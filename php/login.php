<?php
    include 'koneksi.php';
    session_start();

    $error = "";
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = trim($_POST['username']);
        $password = $_POST['password'];

        //prep. statement login
            $stLog = $conn->prepare("SELECT * FROM users WHERE username = ? LIMIT 1");
            $stLog->bind_param("s", $username);
            $stLog->execute();
            $resultLog = $stLog->get_result();

        if ($resultLog->num_rows === 1) {
            $user = $resultLog->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                $_SESSION['username'] = $user['username'];
                $_SESSION['role']     = $user['role'];
            //path by role
                //admin
                    if ($user['role'] === 'admin') {
                        header("Location: admin.php");
                        exit();
                    }
                //mhs
                    elseif ($user['role'] === 'mhs') {
                        $npm = $user['username'];
                        $stMhs = $conn->prepare("SELECT barang FROM peminjaman WHERE npm = ? ORDER BY time DESC LIMIT 1");
                        $stMhs->bind_param("s", $npm);
                        $stMhs->execute();
                        $resultMhs = $stMhs->get_result();
                        $dataMhs = $resultMhs->fetch_assoc();
                        if ($dataMhs && !is_null($dataMhs['barang']) && trim($dataMhs['barang']) !== '') {
                            header("Location: home-status.php");
                        } else {
                            header("Location: form.php");
                        }
                        exit();
                    }
                //dosen
                    elseif ($user['role'] === 'dosen') {
                        $kodeDosen = $user['username'];
                        $stDos = $conn->prepare("SELECT namaDosen FROM userdosen WHERE kodeDosen = ? LIMIT 1");
                        $stDos->bind_param("s", $kodeDosen);
                        $stDos->execute();
                        $resultDos = $stDos->get_result();
                        $dataDosen = $resultDos->fetch_assoc();
                        header("Location: formDosen.php");
                        exit();
                    }
                else {
                    $error = "Role tidak dikenal.";
                }
            } else {
                $error = "Password salah, coba lagi";
            }
        } else {
            $error = "Username tidak terdaftar";
        }
    }
?>

<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="author" content="©2025_10122877">
        <title>Sekretariat Dosen | Login</title>
        <link rel="stylesheet" href="../css/glass-card.css">
        <link rel="icon" type="image/icon" href="icon/IonPerson.svg">
    </head>
    <body>
        <?php include 'loadingA.php'; ?>
        <div class="blur"></div>
        <div class="glass-card">
            <h2>Login</h2>
            <form action="" method="post" onsubmit="lsRembMe()">
                <label for="username">Username</label>
                <input type="text" name="username" id="username" placeholder="username" required>
                <label for="password">Password</label>
                <input type="password" name="password" id="password" placeholder="password" required>
                <div class="rembMe">
                    <input type="checkbox" id="rembMe" value="inget">
                    <label for="rembMe">Ingat Saya</label></div>
                <button type="submit">Masuk</button>
            </form>
            <div class="error">
                <?php if ($error): ?>
                    <p><?= $error ?></p>
                <?php endif; ?></div>
            <a href="../home.php">Home</a>
            <a id="CPR" style="margin-top: 15px; margin-bottom: -10px;"></a>
        </div>
        <script src="../js/rembMe.js" defer></script>
        <script src="../js/copyright.js"></script>
    </body>
</html>
