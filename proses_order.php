<?php
require 'config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = $_POST['nama'];
    $wa = $_POST['wa'];
    $origin = $_POST['origin'];
    $destination = $_POST['destination'];
    $barang = $_POST['barang'];
    $ongkir = $_POST['ongkir'];

    $stmt = $pdo->prepare("INSERT INTO pesanan (nama, wa, origin, destination, barang, ongkir) 
        VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$nama, $wa, $origin, $destination, $barang, $ongkir]);

    // Redirect ke WhatsApp
    $pesan = "Halo Admin Kurir, ada pesanan:\n\nNama: $nama\nWA: $wa\nBarang: $barang\nDari: $origin\nTujuan: $destination\nOngkir: Rp " . number_format($ongkir);
    $url = "https://wa.me/62" . ltrim($wa, "0") . "?text=" . urlencode($pesan);
    header("Location: $url");
    exit;
}
