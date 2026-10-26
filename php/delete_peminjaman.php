<?php
session_start();
include "koneksi.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['peminjaman_no'])) {
    $peminjaman_no = intval($_POST['peminjaman_no']);
    $sql = "DELETE FROM peminjaman WHERE no = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $peminjaman_no);

    if (mysqli_stmt_execute($stmt)) {
        
        header("Location: admin.php");
        exit;
    } else {
        echo "<script>alert('Gagal menghapus data. Error: " . mysqli_error($conn) . "'); window.location='admin.php';</script>";
    }
} else {
    
    header("Location: admin.php");
    exit;
}
?>
