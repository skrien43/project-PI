<?php
    session_start();
    include "koneksi.php";

    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
        header('Location: login.php');
        exit;
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['peminjaman_no'])) {
        $peminjaman_no = intval($_POST['peminjaman_no']);
        $sql_get_kode = "SELECT kodeBarang FROM peminjaman WHERE no = $peminjaman_no";
        $result = mysqli_query($conn, $sql_get_kode);
        $row = mysqli_fetch_assoc($result);
        
        if ($row && !empty($row['kodeBarang'])) {
            $kodeBarang_array = explode(',', $row['kodeBarang']);
            $sanitized_codes = [];
            foreach($kodeBarang_array as $kode) {
                $sanitized_codes[] = mysqli_real_escape_string($conn, trim($kode));
            }
            if (!empty($sanitized_codes)) {
                $update_sql = "UPDATE barang SET statusBarang = 'READY' WHERE kodeBarang IN ('" . implode("','", $sanitized_codes) . "')";
                mysqli_query($conn, $update_sql);
            }
            $sql_update_peminjaman = "UPDATE peminjaman SET barang = NULL, OTP = '------' WHERE no = $peminjaman_no";
            if (mysqli_query($conn, $sql_update_peminjaman)) {
                header("Location: admin.php");
                exit;
            } else {
                echo "<script>alert('Gagal menandai peminjaman sebagai selesai. Error: " . mysqli_error($conn) . "'); window.location='admin.php';</script>";
            }
        } else {
            echo "<script>alert('Data peminjaman tidak ditemukan atau sudah selesai.'); window.location='admin.php';</script>";
        }
    } else {
        header("Location: admin.php");
        exit;
    }
?>
