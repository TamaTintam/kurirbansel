<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

require 'config/db.php';

// Ambil filter
$tgl_awal = $_GET['tgl_awal'] ?? '';
$tgl_akhir = $_GET['tgl_akhir'] ?? '';
$status = $_GET['status'] ?? '';

// Query dinamis
$query = "SELECT * FROM pesanan WHERE 1=1";
$params = [];

if ($tgl_awal && $tgl_akhir) {
    $query .= " AND DATE(created_at) BETWEEN ? AND ?";
    $params[] = $tgl_awal;
    $params[] = $tgl_akhir;
}
if ($status) {
    $query .= " AND status = ?";
    $params[] = $status;
}

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$data = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Laporan Pengiriman</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4 bg-light">
  <div class="container bg-white p-4 rounded shadow">
    <h4 class="mb-4">📊 Laporan Pengiriman</h4>

    <form method="GET" class="row g-3 mb-3">
      <div class="col-md-3">
        <label>Tanggal Awal</label>
        <input type="date" name="tgl_awal" value="<?= $tgl_awal ?>" class="form-control">
      </div>
      <div class="col-md-3">
        <label>Tanggal Akhir</label>
        <input type="date" name="tgl_akhir" value="<?= $tgl_akhir ?>" class="form-control">
      </div>
      <div class="col-md-3">
        <label>Status</label>
        <select name="status" class="form-select">
          <option value="">Semua</option>
          <option <?= $status == 'pending' ? 'selected' : '' ?>>pending</option>
          <option <?= $status == 'diantar' ? 'selected' : '' ?>>diantar</option>
          <option <?= $status == 'selesai' ? 'selected' : '' ?>>selesai</option>
        </select>
      </div>
      <div class="col-md-3 d-flex align-items-end">
        <button class="btn btn-primary me-2">Tampilkan</button>
        <a href="export_excel.php?tgl_awal=<?= $tgl_awal ?>&tgl_akhir=<?= $tgl_akhir ?>&status=<?= $status ?>" class="btn btn-success" target="_blank">Export Excel</a>
      </div>
    </form>

    <table class="table table-bordered">
      <thead>
        <tr>
          <th>Tanggal</th>
          <th>Nama</th>
          <th>Dari - Tujuan</th>
          <th>Barang</th>
          <th>Ongkir</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($data): foreach ($data as $row): ?>
          <tr>
            <td><?= $row['created_at'] ?></td>
            <td><?= htmlspecialchars($row['nama']) ?></td>
            <td><?= htmlspecialchars($row['origin']) ?> → <?= htmlspecialchars($row['destination']) ?></td>
            <td><?= htmlspecialchars($row['barang']) ?></td>
            <td>Rp <?= number_format($row['ongkir']) ?></td>
            <td><?= $row['status'] ?></td>
          </tr>
        <?php endforeach; else: ?>
          <tr><td colspan="6" class="text-center">Tidak ada data</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</body>
</html>
