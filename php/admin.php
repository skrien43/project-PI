<?php
    session_start();
    include "koneksi.php";
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
        header('Location: login.php');
        exit;
    }
    $isViewer = isset($_SESSION['username']) && $_SESSION['username'] === 'viewer';

    //count items
        $peminjaman_aktif = mysqli_fetch_assoc(mysqli_query($conn, 
            "SELECT COUNT(*) as total FROM peminjaman WHERE barang IS NOT NULL AND kodeBarang <> ''"))['total'];
        $peminjaman_selesai = mysqli_fetch_assoc(mysqli_query($conn, 
            "SELECT COUNT(*) as total FROM peminjaman WHERE OTP = '------'"))['total'];
        $barang_tersedia = mysqli_fetch_assoc(mysqli_query($conn, 
            "SELECT COUNT(*) as total FROM barang WHERE statusBarang = 'READY'"))['total'];

    //select * from mhs
        $sql_mhs = "SELECT p.*, u.username, u.role, m.namaMhs
                    FROM peminjaman p
                    LEFT JOIN users u ON p.npm = u.username
                    LEFT JOIN usermhs m ON p.npm = m.npm
                    WHERE u.role = 'mhs'
                    ORDER BY (OTP = '------') ASC, p.time DESC";
        $result_mhs = mysqli_query($conn, $sql_mhs);

    //select * from dosen
        $sql_dosen = "SELECT p.*, u.username, u.role, d.namaDosen
                    FROM peminjaman p
                    LEFT JOIN users u ON p.kodeDosen = u.username
                    LEFT JOIN userdosen d ON p.kodeDosen = d.kodeDosen
                    WHERE u.role = 'dosen'
                    ORDER BY (OTP = '------') ASC, p.time DESC";
        $result_dosen = mysqli_query($conn, $sql_dosen);

    //format barang
        function formatBarangDipinjam($conn, $barang_str, $kode_str) {
            if (empty($kode_str)) return 'N/A';
            $kode_arr = explode(',', $kode_str);
            $display_items = [];
            for ($i = 0; $i < count($kode_arr); $i++) {
                $kategori = isset($kategori_arr[$i]) ? trim($kategori_arr[$i]) : '';
                $kode = trim($kode_arr[$i]);
                // select namaBarang from barang
                    $stmt = mysqli_prepare($conn, "SELECT namaBarang FROM barang WHERE kodeBarang = ?");
                    mysqli_stmt_bind_param($stmt, "s", $kode);
                    mysqli_stmt_execute($stmt);
                    mysqli_stmt_bind_result($stmt, $namaBarang);
                    mysqli_stmt_fetch($stmt);
                    mysqli_stmt_close($stmt);
                $namaBarang = $namaBarang ?? '-';
                $display_items[] ='(' . htmlspecialchars($kode) . ')-' . htmlspecialchars($namaBarang);
            }
            return implode('<br>', $display_items);
        }

    //format WIB
        function tglID($datetime) {
            $waktu = strtotime($datetime);
            $idMo = [
                'January' => 'Januari', 'February' => 'Februari', 'March' => 'Maret',
                'April' => 'April', 'May' => 'Mei', 'June' => 'Juni',
                'July' => 'Juli', 'August' => 'Agustus', 'September' => 'September',
                'October' => 'Oktober', 'November' => 'November', 'December' => 'Desember'
            ];
            $tgl = date('d', $waktu);
            $mo  = $idMo[date('F', $waktu)];
            $thn  = date('Y', $waktu);
            return "$tgl $mo";
        }   function timeID($datetime) {
            $waktu = strtotime($datetime);
            $jam  = date('H:i', $waktu);
            return "$jam WIB";
        }
?>


<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="author" content="©2025_10122877">
        <title>SekDos | Dashboard Peminjaman</title>
        <link rel="stylesheet" href="../css/admin_style.css">
        <link rel="icon" type="image/icon" href="icon/ClarityAdministratorSolid.svg">
    </head>
    <body data-role="<?= htmlspecialchars($_SESSION['role']) ?>">
        <?php include 'loadingA.php'; ?>
        <header class="top-nav" style="user-select: none;" id="head">
            <div class="nav-left" style="cursor:default;">
                <div class="logo">Admin Panel</div>
                <span id="time" class="datetime-display" title='Datetime'></span>
            </div>
            <div class="nav-right">
                <nav class="nav-links">
                    <a class="active" style="cursor:default; text-decoration:none">Dashboard</a>
                    <a href="manage_barang.php">Manajemen Alat</a>
                </nav>
                <a href="logout.php" class="logout-button">Logout</a>
            </div>
        </header>
        <main class="main-content">
            <header class="header">
                <h1 style="cursor:default;">Dashboard Peminjaman</h1>
            </header>
        <!-- card count -->
            <section class="card-container">
                <div class="card">
                    <h3>Peminjaman Aktif</h3>
                    <p><?php echo $peminjaman_aktif; ?></p>
                </div>
                <div class="card">
                    <h3>Peminjaman Selesai</h3>
                    <p><?php echo $peminjaman_selesai; ?></p>
                </div>
                <div class="card">
                    <h3>Total Keseluruhan Alat Tersedia</h3>
                    <p><?php echo $barang_tersedia; ?></p>
                </div>
            </section>
        <!-- MHS -->
            <section class="table-wrapper" id="teleportM">
                <div class="table-header">
                    <h2 style="margin-bottom: -10px; cursor:default;">Peminjaman Mahasiswa</h2>
                    <div style="display: flex; gap: 15px;">
                        <button class="button button-primary" title='Peminjaman Dosen'
                            onclick="document.getElementById('teleportD').scrollIntoView({ behavior: 'smooth' })">Dosen</button>
                        <button class="button button-primary" style="padding: 8px 16px; cursor:pointer;"
                            onClick="window.location.reload();" title='Refresh halaman' id="ref">REFRESH</button>
                        <button class="button button-selesai" style="padding: 8px 16px; cursor:pointer;"
                            id="btnExpM" title="Ekspor data">EXPORT</button>
                    </div>
                </div>
                <hr style="border: none; border-top: 1px solid #dee2e6; margin-top: 17px; margin-bottom: 3px;">
                <a style="font-size:14px; font-weight:400;
                            font-style:oblique; color: #a4a4a8ff;
                            margin-left:5px; letter-spacing:1px;">*gunakan shift + scroll untuk slide tabel</a>
                <hr style="border: none; margin: 3px 0;">
                <div style="overflow-x: scroll;" id="refScr">
                    <table id="table-mhs">
                        <thead>
                            <tr style="user-select: none;">
                                <th>No</th><th>NPM</th><th>Nama</th><th>Dosen Pengajar</th><th>Kelas</th>
                                <th>Ruangan</th><th>Jam Kuliah</th><th>No. HP</th><th>Alat</th>
                                <th>Jaminan</th><th>Kode OTP</th><th>Timestamp</th><th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; while ($row = mysqli_fetch_assoc($result_mhs)): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= htmlspecialchars($row['username']) ?></td>
                                <td><?= ucwords(strtolower(htmlspecialchars($row['namaMhs']))) ?></td>
                                <td><?= ucwords(strtolower(htmlspecialchars($row['namaDosen']))) ?></td>
                                <td><?= htmlspecialchars($row['kelas']) ?></td>
                                <td><?= htmlspecialchars($row['ruangan']) ?></td>
                                <td><?= htmlspecialchars($row['jamKuliah']) ?></td>
                                <td><?= htmlspecialchars($row['no_hp']) ?></td>
                                <td><?= formatBarangDipinjam($conn, $row['barang'], $row['kodeBarang']) ?></td>
                                <td><?= htmlspecialchars($row['jaminan']) ?></td>
                                <td><?= isset($row['OTP']) ? htmlspecialchars($row['OTP']) : '-' ?></td>
                                <td><?= tglID($row['time']) ?>, <?= timeID($row['time']) ?></td>
                                <td>
                                    <?php if (!empty($row['barang'])): ?>
                                        <form action="return_barang.php" method="post">
                                            <input type="hidden" name="peminjaman_no" value="<?= $row['no'] ?>">
                                            <button type="submit" class="button button-selesai"
                                                onclick="sessionStorage.setItem('tpM', 'true')">Selesai</button>
                                        </form>
                                    <?php else: ?>
                                        <form action="delete_peminjaman.php" method="post" onsubmit="return confirm('Hapus data tersebut?');">
                                            <input type="hidden" name="peminjaman_no" value="<?= $row['no'] ?>">
                                            <button type="submit" class="button button-hapus"
                                                onclick="sessionStorage.setItem('tpM', 'true')">Hapus</button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        <!-- DOSEN -->
            <section class="table-wrapper" id="teleportD">
                <div class="table-header">
                    <h2 style="margin-bottom: -10px; cursor:default;">Peminjaman Dosen</h2>
                    <div style="display: flex; gap: 15px;">
                        <button class="button button-primary" title='Peminjaman Mahasiswa'
                            onclick="document.getElementById('teleportM').scrollIntoView({ behavior: 'smooth' })">MHS</button>
                        <button class="button button-primary" style="padding: 8px 16px; cursor:pointer;"
                            onClick="window.location.reload();" title='Refresh halaman'>REFRESH</button>
                        <button class="button button-selesai" style="padding: 8px 16px; cursor: pointer;"
                            id="btnExpD" title="Ekspor data">EXPORT</button>
                    </div>
                </div>
                <hr style="border: none; border-top: 1px solid #dee2e6; margin: 20px 0;">
                <table id="table-dosen">
                    <thead>
                        <tr style="user-select: none;">
                            <th>No</th><th>Kode Dosen</th><th>Nama</th><th>Ruangan</th>
                            <th>Jam Kuliah</th><th>No. HP</th><th>Alat</th>
                            <th>Kode OTP</th><th>Timestamp</th><th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; while ($row = mysqli_fetch_assoc($result_dosen)): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= htmlspecialchars($row['username']) ?></td>
                            <td><?= ucwords(strtolower(htmlspecialchars($row['namaDosen']))) ?></td>
                            <td><?= htmlspecialchars($row['ruangan']) ?></td>
                            <td><?= htmlspecialchars($row['jamKuliah']) ?></td>
                            <td><?= htmlspecialchars($row['no_hp']) ?></td>
                            <td><?= formatBarangDipinjam($conn, $row['barang'], $row['kodeBarang']) ?></td>
                            <td><?= isset($row['OTP']) ? htmlspecialchars($row['OTP']) : '-' ?></td>
                            <td><?= tglID($row['time']) ?>, <?= timeID($row['time']) ?></td>
                            <td>
                                <?php if (!empty($row['barang'])): ?>
                                    <form action="return_barang.php" method="post">
                                        <input type="hidden" name="peminjaman_no" value="<?= $row['no'] ?>">
                                        <button type="submit" class="button button-selesai"
                                            onclick="sessionStorage.setItem('tpD', 'true')">Selesai</button>
                                    </form>
                                <?php else: ?>
                                    <form action="delete_peminjaman.php" method="post" onsubmit="return confirm('Hapus data tersebut?');">
                                        <input type="hidden" name="peminjaman_no" value="<?= $row['no'] ?>">
                                        <button type="submit" class="button button-hapus"
                                            onclick="sessionStorage.setItem('tpD', 'true')">Hapus</button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </section>
        <a id="CPR" style="margin-top: 15px; margin-bottom: -15px;"
            onclick="document.getElementById('head').scrollIntoView({ behavior: 'smooth' })"></a>
        </main>
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
        <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
        <script src="../js/teleportADM.js"></script>
        <script src="../js/export.js"></script>
        <script src="../js/datetime.js"></script>
        <script src="../js/warning.js"></script>
        <script src="../js/copyright.js"></script>
    </body>
</html>
