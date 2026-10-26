<?php
    session_start();
    include "koneksi.php";

    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
        header('Location: login.php');
        exit;
    }
    $isViewer = isset($_SESSION['username']) && $_SESSION['username'] === 'viewer';

    //add & drop barang
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_barang'])) {
            $namaBarang = mysqli_real_escape_string($conn, $_POST['namaBarang']);
            $kodeBarang = mysqli_real_escape_string($conn, $_POST['kodeBarang']);
            $catBarang = mysqli_real_escape_string($conn, $_POST['catBarang']);
            $check_sql = "SELECT * FROM barang WHERE kodeBarang = '$kodeBarang'";
            $check_result = mysqli_query($conn, $check_sql);
            if (mysqli_num_rows($check_result) == 0) {
                $sql = "INSERT INTO barang (namaBarang, kodeBarang, catBarang, statusBarang) VALUES
                    ('$namaBarang', '$kodeBarang', '$catBarang', 'READY')";
                mysqli_query($conn, $sql);
            } else {
                echo "<script>alert('Kode barang sudah ada!');</script>";
            }
        }
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_barang'])) {
            $kodeBarang = mysqli_real_escape_string($conn, $_POST['kodeBarang']);
            $sql = "DELETE FROM barang WHERE kodeBarang = '$kodeBarang'";
            mysqli_query($conn, $sql);
        }
    //select * from barang
        $cards = mysqli_query($conn, "SELECT * FROM barang WHERE catBarang = 'CARD' ORDER BY kodeBarang ASC");
        $viewers = mysqli_query($conn, "SELECT * FROM barang WHERE catBarang = 'VIEW' ORDER BY kodeBarang ASC");
        $cables = mysqli_query($conn, "SELECT * FROM barang WHERE catBarang = 'CABLE' ORDER BY kodeBarang ASC");
?>


<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="author" content="©2025_10122877">
        <title>SekDos | Manajemen Alat</title>
        <link rel="stylesheet" href="../css/admin_style.css">
        <link rel="icon" type="image/icon" href="icon/ClarityAdministratorSolid.svg">
    </head>
    <body>
        <?php include 'loadingA.php'; ?>
        <header class="top-nav" style="user-select: none;" id="head">
            <div class="nav-left" style="cursor:default;">
                <div class="logo">Admin Panel</div>
                <span id="time" class="datetime-display" title='Datetime'></span>
            </div>
            <div class="nav-right">
                <nav class="nav-links">
                    <a href="admin.php">Dashboard</a>
                    <a class="active" style="cursor:default; text-decoration:none">Manajemen Alat</a>
                </nav>
                <a href="logout.php" class="logout-button">Logout</a>
            </div>
        </header>
        <main class="main-content">
            <header class="header">
                <h1 style="cursor:default;">Manajemen Alat</h1>
            </header>
            <!-- main list -->
                <?php
                    function render_management_table($title, $category, $result, $conn) {
                        //count barang by status
                            $sql_used  = "SELECT COUNT(*) FROM barang WHERE catBarang = '$category' AND statusBarang = 'USED'";
                            $sql_ready = "SELECT COUNT(*) FROM barang WHERE catBarang = '$category' AND statusBarang = 'READY'";
                            $used  = mysqli_fetch_row(mysqli_query($conn, $sql_used))[0];
                            $ready = mysqli_fetch_row(mysqli_query($conn, $sql_ready))[0];
                            
                        echo "<section class='table-wrapper'>
                            <div class='table-header'>
                                <h2 style='margin-bottom: -10px; cursor:default;'>$title</h2>
                                <div style='cursor: pointer;'>
                                    <button class='button button-primary' style='padding: 8px 16px;'
                                        onClick='window.location.reload();' title='Refresh halaman'>REFRESH</button>
                                </div>
                            </div>";
                        echo "<hr style='border: none; border-top: 0px; margin: 10px 0;'>";
                        echo "<section class='card-container'>
                                <div class='card'>
                                    <h3>Total Barang Sedang Digunakan</h3>
                                    <p style='font-size:30px'>$used</p>
                                </div>
                                <div class='card'>
                                    <h3>Total Barang Tersedia</h3>
                                    <p style='font-size:30px'>$ready</p>
                                </div>
                            </section>";
                        echo "<hr style='border: none; border-top: 1px solid #dee2e6; margin: 20px 0;'>";
                        if ($category !== "CARD") {
                            echo "<div class='form-section'>
                                <form method='POST' action=''>
                                    <input type='text' name='namaBarang' placeholder='Nama Barang Baru' required>
                                    <input type='text' name='kodeBarang' placeholder='Kode Barang Baru' required autocomplete='off'>
                                    <input type='hidden' name='catBarang' value='$category'>
                                    <button type='submit' name='add_barang' class='button button-selesai'
                                        style='padding: 10px 20px;' title='Tambah alat baru'>Tambah</button>
                                </form>
                            </div>";
                            echo "<table>
                                <thead style='user-select: none; cursor:default;'>
                                <tr><th>Nama Barang</th><th>Kode Barang</th>
                                <th>Status</th><th>Aksi</th></tr>
                                </thead>
                                <tbody>";
                        } else {
                            echo "<table>
                            <thead style='user-select: none; cursor:default;'>
                            <tr><th>Nama Barang</th><th>Kode Barang</th>
                            <th>Status</th><th></th></tr>
                            </thead>
                            <tbody>";                            
                        }
                        //merge USED & others
                            $used_items = [];
                            $other_items = [];
                            while($row = mysqli_fetch_assoc($result)) {
                                if ($row['statusBarang'] === "USED") {
                                    $used_items[] = $row;
                                } else {
                                    $other_items[] = $row;
                                }
                            }
                        //USED duluan
                            $sorted_items = array_merge($used_items, $other_items);
                            foreach ($sorted_items as $index => $row) {
                                $kode = htmlspecialchars($row['kodeBarang']);
                                $nama = strtoupper(htmlspecialchars($row['namaBarang']));
                                $status = htmlspecialchars($row['statusBarang']);
                                $extCls = $index >= 10 ? "hide" : "";
                                echo "<tr id='view-row-$kode' class='$extCls'>
                                    <td>$nama</td>
                                    <td>$kode</td>
                                    <td>$status</td>
                                    <td style='width: 150px;'>";
                                if ($category !== "CARD" && $status !== "USED") {
                                    echo "
                                    <div style='user-select:none'>
                                        <button class='button button-edit' onclick=\"toggleEditForm('$kode')\" title='Edit data alat'>Edit</button>
                                            <form method='POST' action='' onsubmit=\"return confirm('Yakin ingin hapus?');\" style='display:inline;'>
                                                <input type='hidden' name='kodeBarang' value='$kode'>
                                                <button type='submit' name='delete_barang' class='button button-hapus' title='Hapus data alat'>Hapus</button>
                                            </form>
                                    </div>";
                                }
                            //edit barang
                                echo "</td>
                                </tr>";
                                if ($category !== "CARD") {
                                    echo "<tr id='edit-row-$kode' class='$extCls' style='display: none;'>
                                        <td colspan='4'>
                                            <form action='edit_barang.php' method='post' style='display:flex; gap:10px;'>
                                                <input type='hidden' name='original_kodeBarang' value='$kode'>
                                                <input type='text' name='namaBarang' value='$nama' placeholder='Nama Barang' required style='flex-grow:2;'>
                                                <input type='text' name='new_kodeBarang' value='$kode' placeholder='Kode Barang' required style='flex-grow:1;' autocomplete='off'>
                                                <button type='submit' class='button button-selesai'>Simpan</button>
                                                <button type='button' class='button button-secondary' onclick=\"toggleEditForm('$kode')\">Batal</button>
                                            </form>
                                        </td>
                                    </tr>";
                                }
                            }
                        echo "</tbody></table>";
                        if (count($sorted_items) > 10) {
                            echo "<div class='show-all-wrapper' style='text-align: center; margin-top: 10px;'>
                                    <a href='#' onclick='toggleRows(this); return false;'
                                        class='toggle-link'; title='Tampilan data' style='user-select:none;'>view more...</a>
                                </div>";
                        }
                        echo "</section>";
                    }
                    render_management_table("Kartu Akses Ruangan", "CARD", $cards, $conn);
                    render_management_table("Proyektor / Viewer", "VIEW", $viewers, $conn);
                    render_management_table("Kabel HDMI", "CABLE", $cables, $conn);
                ?>
            <a id="CPR" style="margin-top: 15px; margin-bottom: -15px;"
                onclick="document.getElementById('head').scrollIntoView({ behavior: 'smooth' })"></a>
        </main>
        <!-- edit barang -->
            <script>
                function toggleEditForm(kode) {
                    const viewRow = document.getElementById('view-row-' + kode);
                    const editRow = document.getElementById('edit-row-' + kode);
                    if (editRow.style.display === 'none') {
                        editRow.style.display = 'table-row';
                        viewRow.style.display = 'none';
                    } else {
                        editRow.style.display = 'none';
                        viewRow.style.display = 'table-row';
                    }
                }
            </script>
            <?php if ($isViewer): ?>
                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        const navbar = document.querySelector('nav') || document.querySelector('.navbar');
                        const navbarButtons = navbar ? navbar.querySelectorAll('button, input[type="submit"], a.btn') : [];
                        document.querySelectorAll('button, input[type="submit"], a.btn').forEach(function (btn) {
                            let insideNavbar = false;
                            navbarButtons.forEach(function (navBtn) {
                                if (btn === navBtn) {
                                    insideNavbar = true;
                                }
                            });
                            if (!insideNavbar) {
                                btn.disabled = true;
                                btn.style.pointerEvents = 'none';
                                btn.style.opacity = '0.6';
                            }
                        });
                    });
                </script>
            <?php endif; ?>
        <script src="../js/toggleView.js"></script>
        <script src="../js/datetime.js"></script>
        <script src="../js/copyright.js"></script>
    </body>
</html>
