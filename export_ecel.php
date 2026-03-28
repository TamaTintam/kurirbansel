<?php
require 'config/db.php';

$tgl_awal = $_GET['tgl_awal'] ?? '';
$tgl_akhir = $_GET['tgl_akhir'] ?? '';
$status = $_GET['status'] ?? '';

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

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=laporan_pengiriman.xls");
?>

<table border="1">
  <thead>
    <tr>
      <th>Tanggal</th>
      <th>Nama</th>
      <th>Asal - Tujuan</th>
      <th>Barang</th>
      <th>Ongkir</th>
      <th>Status</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($data as $row): ?>
      <tr>
        <td><?= $row['created_at'] ?></td>
        <td><?= $row['nama'] ?></td>
        <td><?= $row['origin'] ?> → <?= $row['destination'] ?></td>
        <td><?= $row['barang'] ?></td>
        <td><?= $row['ongkir'] ?></td>
        <td><?= $row['status'] ?></td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>
