<?php
    session_start();
    if (!isset($_SESSION['username'])) {
        header("Location: login.php");
        exit();
    }
    include "koneksi.php";

    $username = $_SESSION['username'];
    $role = $_SESSION['role'];

    if ($role === 'dosen') {
        $stmt = $conn->prepare("SELECT kodeDosen, namaDosen FROM userdosen WHERE kodeDosen = ? LIMIT 1");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
    } else {
        echo "<script>alert('Role tidak valid');window.location='logout.php';</script>";
        exit();
    }

    //OTP
        function genOTP($conn) {
            do {
                $kodeOTP = str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);
                $stmt = $conn->prepare("SELECT 1 FROM peminjaman WHERE OTP= ?");
                $stmt->bind_param("s", $kodeOTP);
                $stmt->execute();
                $cek = $stmt->get_result();
            } while(mysqli_num_rows($cek) > 0);
            return $kodeOTP;
        }
    
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $kodeDosen  = trim($_POST['kodeD']);
        $namaDosen  = ucwords(strtolower(trim($_POST['dosen'])));
        $ruangan    = strtoupper(trim($_POST['room']));
        $jamKuliah  = trim($_POST['jklh']);
        $no_hp      = trim($_POST['telp']);
        $kodeOTP    = genOTP($conn);

        $kodeBarang = [];
        if (!empty($_POST['kode_card'])) $kodeBarang[]  = trim($_POST['kode_card']);
        if (!empty($_POST['kode_view'])) $kodeBarang[]  = trim($_POST['kode_view']);
        if (!empty($_POST['kode_cable'])) $kodeBarang[] = trim($_POST['kode_cable']);

        if (empty($kodeBarang)) {
            $stmt = $conn->prepare("INSERT INTO peminjaman 
                (kodeDosen, namaDosen, ruangan, jamKuliah, no_hp, OTP) 
                VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssss", $kodeDosen, $namaDosen, $ruangan, $jamKuliah, $no_hp, $kodeOTP);
        } else {
            $barang = [];
            $query_cat = "SELECT catBarang FROM barang WHERE kodeBarang IN ('" . implode("','", $kodeBarang) . "')";
            $res_cat = mysqli_query($conn, $query_cat);
            while($row_cat = mysqli_fetch_assoc($res_cat)){
                $barang[] = $row_cat['catBarang'];
            }
            $barang_str = implode(',', $barang);
            $kodeBarang_str = implode(',', $kodeBarang);
            $stmt = $conn->prepare("INSERT INTO peminjaman 
                (kodeDosen, namaDosen, ruangan, jamKuliah, no_hp, barang, kodeBarang, OTP) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssssss", $kodeDosen, $namaDosen, $ruangan, $jamKuliah, $no_hp, $barang_str, $kodeBarang_str, $kodeOTP);
        }

        if ($stmt->execute()) {
            if (!empty($kodeBarang)) {
                $update_sql = "UPDATE barang SET statusBarang = 'USED' WHERE kodeBarang IN ('" . implode("','", $kodeBarang) . "')";
                mysqli_query($conn, $update_sql);
            }
            //nextpage
            $_SESSION['OTP'] = $kodeOTP;
            header("Location: successDosen.php");
            exit();
        } else {
            echo "<script>alert('GAGAL MENYIMPAN DATA: " . $stmt->error . "');</script>";
        }
    }
?>


<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="author" content="©2025_10122877">
        <title>SekDos | Form Peminjaman Alat</title>
        <link rel="stylesheet" href="../css/form_style.css">
        <link rel="icon" type="image/icon" href="icon/FluentForm48Filled.svg">
    </head>
    <body>
        <?php include 'loading.php'; ?>
        <div class="form-wrapper">
            <div class="form-header">
                <h2>Form Peminjaman Alat Dosen</h2>
                <p>Isi data di bawah ini untuk melakukan peminjaman</p>
                <p style="color:red; font-size:15px;"><a style="font-weight: 600;">*</a> Menunjukkan bagian yang wajib diisi</p>
            </div>
            <!-- form DSN -->
                <form action="" method="post" class="form-body">
                    <div class="form-row">
                        <label for="kodeD">Kode Dosen</label>
                        <input type="text" id="kodeD" name="kodeD" value="<?= ucwords(htmlspecialchars($data['kodeDosen'])) ?>" readonly>
                    </div>
                    <div class="form-row">
                        <label for="dosen">Nama Anda</label>
                        <input type="text" id="dosen" name="dosen" value="<?= ucwords(strtolower(htmlspecialchars($data['namaDosen']))) ?>" readonly>
                    </div>
                    <div class="form-row">
                        <label for="room">Ruangan<a style="color:red"> *</a></label>
                        <input type="text" id="room" name="room" placeholder="Contoh: E316" required autocomplete="off">
                    </div>
                    <div class="form-row">
                        <label for="jklh">Jam Perkuliahan<a style="color:red"> *</a></label>
                        <input type="text" id="jklh" name="jklh" placeholder="Contoh: 1/2/3" required>
                    </div>
                    <div class="form-row">
                        <label for="telp">Nomor HP<a style="color:red"> *</a></label>
                        <input type="text" id="telp" name="telp" placeholder="Masukkan nomor telepon aktif" required>
                    </div>
                    <hr style="border: none; border-top: 1px solid #dee2e6; margin: 10px 0;">
                    <div class="form-row">
                        <div class="subhead">
                            <h2>PILIH BARANG</h2>
                        </div>
                        <div>
                            <p style="color:red; font-weight:500;
                                font-size:15px;">-Kosongkan yang tidak diperlukan.</p>
                        </div>
                    </div>
                    <div class="form-row">
                        <label for="search_card">Kartu Ruangan</label>
                        <div class="search-container">
                            <input type="text" id="search_card" onkeyup="searchBarang('card', this.value)" placeholder="Ketik no. ruangan untuk mencari..." autocomplete="off">
                            <input type="hidden" name="kode_card" id="kode_card">
                            <div id="results_card" class="search-results"></div>
                        </div>
                        <label style="font-size:12.5px; font-weight:500; font-style:oblique; color:red; margin-top:-7px;
                            text-transform:uppercase; margin-left: 5px;"><strong>Pastikan No. Kartu = No. Ruangan</strong></label>
                    </div>
                    <div class="form-row">
                        <label for="search_view">Proyektor</label>
                        <div class="search-container">
                            <input type="text" id="search_view" onkeyup="searchBarang('view', this.value)" placeholder="Ketik 'Proyektor' untuk mencari..." autocomplete="off">
                            <input type="hidden" name="kode_view" id="kode_view">
                            <div id="results_view" class="search-results"></div>
                        </div>
                    </div>
                    <div class="form-row">
                        <label for="search_cable">Kabel HDMI</label>
                        <div class="search-container">
                            <input type="text" id="search_cable" onkeyup="searchBarang('cable', this.value)" placeholder="Ketik 'HDMI' untuk mencari..." autocomplete="off">
                            <input type="hidden" name="kode_cable" id="kode_cable">
                            <div id="results_cable" class="search-results"></div>
                        </div>
                    </div>
                    <button type="submit" class="button-submit">SUBMIT</button>
                </form>
            <div class="form-footer">
                <a href="logout.php">LOGOUT</a>
            </div>
            <a id="CPR" style="margin-top: 15px; margin-bottom: -20px;"></a>
        </div>
        <script src="../js/search.js"></script>
        <script src="../js/copyright.js"></script>
    </body>
</html>
