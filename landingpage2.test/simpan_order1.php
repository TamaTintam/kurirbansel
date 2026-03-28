<?php
require 'config/db.php';

header("Content-Type: application/json");

try {
    $input = json_decode(file_get_contents("php://input"), true);

    $kode_unik = 'KK' . rand(100000, 999999);
    $status = 'pending';
    $created_at = date("Y-m-d H:i:s");

    $stmt = $pdo->prepare("INSERT INTO pesanan 
    (nama, wa, origin, destination, barang, ongkir, kode_unik, status, created_at) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $stmt->execute([
        $input['nama'],
        $input['wa'],
        $input['origin'],
        $input['destination'],
        $input['barang'],
        $input['ongkir'],
        $kode_unik,
        $status,
        $created_at
    ]);

    echo json_encode([
        'status' => 'success',
        'kode' => $kode_unik
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
