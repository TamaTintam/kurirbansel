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
  <title>Cek Pesanan</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5 p-4 bg-white shadow rounded">
  <h4 class="mb-3">🔍 Cek Status Pesanan</h4>
  <form method="GET" class="mb-4">
    <input type="text" name="kode" class="form-control" placeholder="Masukkan kode pesanan (contoh: KK123456)" required value="<?= htmlspecialchars($kode) ?>">
    <button class="btn btn-primary mt-2">Cek</button>
  </form>

  <?php if ($data): ?>
    <div class="alert alert-info">
      <strong>Status:</strong> <?= htmlspecialchars($data['status']) ?><br>
      <strong>Barang:</strong> <?= htmlspecialchars($data['barang']) ?><br>
      <strong>Tujuan:</strong> <?= htmlspecialchars($data['destination']) ?><br>
      <strong>Ongkir:</strong> Rp <?= number_format($data['ongkir']) ?>
    </div>
  <?php elseif ($kode): ?>
    <div class="alert alert-danger">Pesanan tidak ditemukan!</div>
  <?php endif; ?>
</div>
</body>
</html>
