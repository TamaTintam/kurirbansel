<?php
require 'config/db.php';

$input = json_decode(file_get_contents("php://input"), true);

// Cek aksi yang diminta
$action = $input['action'] ?? 'simpan_order';

if ($action === 'simpan_tarif') {
    // Validasi dan sanitasi input
    $tarif = isset($input['tarif']) ? (int) $input['tarif'] : 0;
    $tarif5km = isset($input['tarif5km']) ? (int) $input['tarif5km'] : 0;

    // Siapkan query update
    $stmt1 = $pdo->prepare("UPDATE pengaturan SET nilai = ? WHERE nama = 'tarif_per_km'");
    $stmt2 = $pdo->prepare("UPDATE pengaturan SET nilai = ? WHERE nama = 'tarif_lebih'");

    // Eksekusi kedua update
    $success1 = $stmt1->execute([$tarif]);
    $success2 = $stmt2->execute([$tarif5km]);

    echo json_encode([
        "status" => ($success1 && $success2) ? "ok" : "fail"
    ]);
    exit;
}


// --- Proses simpan order seperti biasa ---
$kode_unik = 'KK' . rand(100000, 999999);

$stmt = $pdo->prepare("INSERT INTO pesanan (nama, wa, origin, destination, barang, ongkir, kode_unik) 
VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt->execute([
    $input['nama'],
    $input['wa'],
    $input['origin'],
    $input['destination'],
    $input['barang'],
    $input['ongkir'],
    $kode_unik
]);

echo json_encode([
    'status' => 'success',
    'kode' => $kode_unik
]);

