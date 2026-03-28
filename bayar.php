<?php
require 'config/db.php';

$kode = $_GET['kode'] ?? null;
$data = null;

if ($kode) {
    $stmt = $pdo->prepare("SELECT * FROM pesanan WHERE kode_unik = ?");
    $stmt->execute([$kode]);
    $data = $stmt->fetch();
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Pembayaran Pesanan</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5 p-4 bg-white shadow rounded text-center">
  <h4 class="mb-3">💳 Pembayaran via QRIS</h4>

  <?php if ($data): ?>
    <p>Kode Pesanan: <strong><?= htmlspecialchars($data['kode_unik']) ?></strong></p>
    <p>Total Ongkir: <strong>Rp <?= number_format($data['ongkir']) ?></strong></p>

    <img src="qris.png" alt="QRIS" style="max-width:250px" class="my-3"><br>
    <p>Silakan scan QRIS di atas menggunakan aplikasi dompet digital Anda (DANA, OVO, Gopay, dll).</p>
    <p>Setelah membayar, kirim bukti via WhatsApp ke admin.</p>

    <a href="https://wa.me/62<?= ltrim($data['wa'], '0') ?>?text=Halo, saya sudah membayar ongkir untuk pesanan <?= $data['kode_unik'] ?>. Berikut bukti pembayarannya." class="btn btn-success mt-2">Kirim Bukti Pembayaran</a>
  <?php else: ?>
    <div class="alert alert-danger">Kode pesanan tidak ditemukan.</div>
  <?php endif; ?>
</div>
</body>
</html>
