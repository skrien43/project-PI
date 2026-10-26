<?php
$host = "localhost";
$username = "root";
$password = "bAsis-daTa8767";
$dbname = "database2";

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Koneksi gagal." . $conn->connect_error);
}
