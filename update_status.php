<?php
require 'config/db.php';
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$id = $_POST['id'];
$status = $_POST['status'];

// Ambil data pesanan untuk WA
$stmt = $pdo->prepare("SELECT * FROM pesanan WHERE id = ?");
$stmt->execute([$id]);
$pesanan = $stmt->fetch();

// Update status
$update = $pdo->prepare("UPDATE pesanan SET status = ? WHERE id = ?");
$update->execute([$status, $id]);

// Kirim notifikasi WA otomatis
$nama = $pesanan['nama'];
$no_wa = $pesanan['wa'];
$barang = $pesanan['barang'];

if ($status == 'diantar') {
    $pesan = "Halo $nama, pesanan Anda *sedang diantar*. Barang: $barang.\nTerima kasih telah menggunakan Kurir Bansel.";
} elseif ($status == 'selesai') {
    $pesan = "Halo $nama, pesanan Anda *telah sampai*.\nTerima kasih telah menggunakan Kurir Bansel!";
} else {
    $pesan = "";
}

if (!empty($pesan)) {
    // Format URL ke WhatsApp
    $url = "https://wa.me/62" . ltrim($no_wa, "0") . "?text=" . urlencode($pesan);

    // Arahkan langsung ke WA (opsional: bisa pakai API WA gateway juga)
    echo "<script>window.open('$url', '_blank'); window.location.href='dashboard.php';</script>";
    exit;
} else {
    header("Location: dashboard.php");
    exit;
}
