<?php
$host = "localhost";
$username = "...";
$password = "...";
$dbname = "...";

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Koneksi gagal." . $conn->connect_error);
}
