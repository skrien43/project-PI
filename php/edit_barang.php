<?php
    session_start();
    include "koneksi.php";

    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
        header('Location: login.php');
        exit;
    }
    
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['original_kodeBarang'], $_POST['new_kodeBarang'], $_POST['namaBarang'])) {
        $original_kode = mysqli_real_escape_string($conn, $_POST['original_kodeBarang']);
        $new_kode = mysqli_real_escape_string($conn, $_POST['new_kodeBarang']);
        $nama_barang = mysqli_real_escape_string($conn, $_POST['namaBarang']);
        if ($original_kode !== $new_kode) {
            $check_sql = "SELECT * FROM barang WHERE kodeBarang = ?";
            $stmt_check = mysqli_prepare($conn, $check_sql);
            mysqli_stmt_bind_param($stmt_check, "s", $new_kode);
            mysqli_stmt_execute($stmt_check);
            $result = mysqli_stmt_get_result($stmt_check);
            if (mysqli_num_rows($result) > 0) {
                echo "<script>alert('Gagal: Kode barang \"$new_kode\" sudah digunakan oleh barang lain.'); window.location='manage_barang.php';</script>";
                exit;
            }
            mysqli_stmt_close($stmt_check);
        }
        $sql_update = "UPDATE barang SET kodeBarang = ?, namaBarang = ? WHERE kodeBarang = ?";
        $stmt_update = mysqli_prepare($conn, $sql_update);
        
        if ($stmt_update) {
            mysqli_stmt_bind_param($stmt_update, "sss", $new_kode, $nama_barang, $original_kode);
            if (mysqli_stmt_execute($stmt_update)) {
                header("Location: manage_barang.php");
                exit;
            } else {
                echo "<script>alert('Gagal mengupdate data. Error: " . mysqli_stmt_error($stmt_update) . "'); window.location='manage_barang.php';</script>";
            }
            mysqli_stmt_close($stmt_update);
        } else {
            echo "<script>alert('Gagal mempersiapkan statement. Error: " . mysqli_error($conn) . "'); window.location='manage_barang.php';</script>";
        }
    } else {
        header("Location: manage_barang.php");
        exit;
    }
?>
