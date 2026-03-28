<?php
require 'config/db.php';
$stmt = $pdo->query("SELECT * FROM pesanan ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Riwayat Pesanan</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
  <h3>Riwayat Pesanan</h3>
  <table class="table table-bordered table-striped">
    <thead>
      <tr>
        <th>Waktu</th>
        <th>Nama</th>
        <th>No WA</th>
        <th>Dari</th>
        <th>Tujuan</th>
        <th>Barang</th>
        <th>Ongkir</th>
        <th>Status</th>
      </tr>
    </thead>
    <tbody>
      <?php while ($p = $stmt->fetch()): ?>
        <tr>
          <td><?= $p['created_at'] ?></td>
          <td><?= htmlspecialchars($p['nama']) ?></td>
          <td><?= htmlspecialchars($p['wa']) ?></td>
          <td><?= htmlspecialchars($p['origin']) ?></td>
          <td><?= htmlspecialchars($p['destination']) ?></td>
          <td><?= htmlspecialchars($p['barang']) ?></td>
          <td>Rp <?= number_format($p['ongkir']) ?></td>
          <td><?= $p['status'] ?></td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</body>
</html>
