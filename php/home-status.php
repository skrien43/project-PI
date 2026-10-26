<?php
    session_start();
    include "koneksi.php";

    if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'mhs') {
        header("Location: login.php");
        exit;
    }

    $npm = $_SESSION['username'];
    $sql = "SELECT p.*, m.namaMhs FROM peminjaman p
            JOIN usermhs m ON p.npm = m.npm
            WHERE p.npm = '$npm' AND p.kodeBarang IS NOT NULL
            ORDER BY p.time DESC LIMIT 1";
    $result = $conn->query($sql);
    $data = $result->fetch_assoc();
    if (!$data) {
        header("Location: form.php");
        exit;
    }
    
    $nama       = $data['namaMhs'];
    $kodeBarang = $data['kodeBarang'];
    //time-fmt
        $waktu = strtotime($data['time']);
        $idMo = [
            'January' => 'Januari', 'February' => 'Februari', 'March' => 'Maret',
            'April' => 'April', 'May' => 'Mei', 'June' => 'Juni',
            'July' => 'Juli', 'August' => 'Agustus', 'September' => 'September',
            'October' => 'Oktober', 'November' => 'November', 'December' => 'Desember'
        ];
        $tgl = date('d', $waktu);
        $mo = $idMo[date('F', $waktu)];
        $jam = date('H:i', $waktu);
    $dt   = "$tgl $mo";
    $clc  = "$jam WIB";
    $ruangan    = $data['ruangan'];

    //parse kodeBarang
        $kodeBarangArr = array_map('trim', explode(',', $kodeBarang));
        $namaBarangArr = [];

    foreach ($kodeBarangArr as $kode) {
        $q = mysqli_query($conn, "SELECT namaBarang FROM barang WHERE kodeBarang = '$kode' LIMIT 1");
        $row = mysqli_fetch_assoc($q);
        $namaBarangArr[] = ($row) ? $row['namaBarang'] . " ($kode)" : "";
    }
    $namaBarangDisplay = implode(', ', $namaBarangArr);
?>


<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="author" content="©2025_10122877">
        <title>Sekretariat Dosen UXYZ</title>
        <link rel="stylesheet" href="../css/SDstyle.css">
        <link rel="icon" type="image/icon" href="icon/FaSolidSchool.svg">
    </head>
    <body>
        <?php include 'loading.php'; ?>
        <div class="blur"></div>
        <div class="navbar" style="user-select: none;">
            <span id="time" class="time"></span>
            <a class="on" href="logout.php">Logout</a>
        </div>
        <div class="isi">
            <h1><br>Selamat Datang!<br>di-<em>website</em>
            Sekretariat Dosen Universitas XYZ</h1>
        </div>
        <div class="status">
            <h2 style="margin-bottom: -10px;">Halo, <?= ucwords(strtolower(htmlspecialchars($nama))) ?>!</h2>
            <br><p>Saat ini Anda sedang meminjam <strong><?= htmlspecialchars($namaBarangDisplay) ?></strong>, sejak tanggal <strong><?= $dt?></strong>,
            pukul: <strong><?= $clc?></strong> di ruangan <strong><?= htmlspecialchars($ruangan) ?></strong>.</p>
            <p>Selesaikan pinjaman tersebut pada ruangan <b>Sekretariat Dosen</b> jika Anda ingin meminjam kembali.</p>
            <p style="margin-bottom: -10px;">Terima Kasih.</p><br>
            <pre style="font-size: 14px; margin-bottom: -15px;">
TTD.
Staf Sekretariat Dosen
Universitas XYZ.
            </pre>
        </div>
        <a id="CPR"></a>
        <script src="../js/datetime.js"></script>
        <script src="../js/copyright.js"></script>
    </body>
</html>
