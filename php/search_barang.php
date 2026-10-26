<?php
    include "koneksi.php";

    header('Content-Type: application/json');

    $type = isset($_GET['type']) ? $_GET['type'] : '';
    $query = isset($_GET['query']) ? mysqli_real_escape_string($conn, $_GET['query']) : '';
    $catBarang = '';

    switch ($type) {
        case 'card':
            $catBarang = 'CARD';
            break;
        case 'view':
            $catBarang = 'VIEW';
            break;
        case 'cable':
            $catBarang = 'CABLE';
            break;
        default:
            echo json_encode([]);
            exit;
    }

    $sql = "SELECT namaBarang, kodeBarang FROM barang 
            WHERE catBarang = '$catBarang' 
            AND statusBarang = 'READY' 
            AND (namaBarang LIKE '%$query%' OR kodeBarang LIKE '%$query%')";

    $result = mysqli_query($conn, $sql);
    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }

    echo json_encode($data);
?>
