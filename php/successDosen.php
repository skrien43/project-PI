<?php
    session_start();
    if (!isset($_SESSION['username']) || !isset($_SESSION['role']) || 
        ($_SESSION['role'] !== 'dosen')) {
        header("Location: login.php");
        exit();
    }
    $kodeOTP = isset($_SESSION['OTP']) ? $_SESSION['OTP'] : '';
    unset($_SESSION['OTP']);
?>


<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="author" content="©2025_10122877">
        <title>Sekretariat Dosen | Notifikasi</title>
        <link rel="stylesheet" href="../css/glass-card.css">
        <link rel="icon" type="image/icon" href="icon/StreamlineAnnouncementMegaphoneRemix.svg">
    </head>
    <body>
        <?php include 'loading.php'; ?>
        <div class="blur"></div>
        <div class="card">
            <h2>Success</h2>
            <p style="font-size:small;">Data peminjaman Anda sudah tercatat.</p>
            <p style="font-size:20px;"><strong>Kode OTP:</strong></p>
            <div class="code-box"><?= htmlspecialchars($kodeOTP) ?></div>
            <a href="#" onclick="copyOTP()" style="color:var(--primary-color)">Salin Kode</a>
            <p>
                <strong>Simpan</strong> dan <strong>tunjukkan</strong> kode<br>di atas kepada staf saat
                Anda <strong>mengambil</strong> dan <strong>mengembalikan</strong> 
                alat di ruang Sekretariat Dosen.<br>Terima Kasih!
            </p>
            <p style="font-size: 16px; color:red;"><strong>JANGAN REFRESH HALAMAN INI</strong></p>
            <a href="logout.php">LOGOUT</a>
            <a id="CPR" style="margin-top: 15px; margin-bottom: -10px;"></a>
        </div>
        <script src="../js/copy.js"></script>
        <script src="../js/copyright.js"></script>
    </body>
</html>
