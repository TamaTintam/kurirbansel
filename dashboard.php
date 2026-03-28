<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

require 'config/db.php';
$stmt = $pdo->query("SELECT * FROM pesanan ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Dashboard Kurir Kampung</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4>Dashboard Kurir Kampung</h4>
    <a href="logout.php" class="btn btn-danger btn-sm">Logout</a>
  </div>
  
  <table class="table table-bordered table-striped">
    <thead>
      <tr>
        <th>Waktu</th>
        <th>Nama</th>
        <th>Dari - Tujuan</th>
        <th>Barang</th>
        <th>Ongkir</th>
        <th>Status</th>
        <th>Aksi</th>
      </tr>
    </thead>
    <tbody>
      <?php while ($p = $stmt->fetch()): ?>
        <tr>
          <td><?= $p['created_at'] ?></td>
          <td><?= htmlspecialchars($p['nama']) ?></td>
          <td><?= htmlspecialchars($p['origin']) ?> → <?= htmlspecialchars($p['destination']) ?></td>
          <td><?= htmlspecialchars($p['barang']) ?></td>
          <td>Rp <?= number_format($p['ongkir']) ?></td>
          <td><span class="badge bg-info"><?= $p['status'] ?></span></td>
          <td>
            <form method="POST" action="update_status.php" class="d-flex">
              <input type="hidden" name="id" value="<?= $p['id'] ?>">
              <select name="status" class="form-select me-2" required>
                <option <?= $p['status'] == 'pending' ? 'selected' : '' ?>>pending</option>
                <option <?= $p['status'] == 'diantar' ? 'selected' : '' ?>>diantar</option>
                <option <?= $p['status'] == 'selesai' ? 'selected' : '' ?>>selesai</option>
              </select>
              <button class="btn btn-sm btn-primary">Update</button>
            </form>
          </td>
        </tr>
      <?php endwhile; ?>
      
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>

    </tbody>
  </table>

<!--edit tarid-->
<?php
$stmtTarif = $pdo->query("SELECT nilai FROM pengaturan WHERE nama = 'tarif_per_km'");
$tarif = $stmtTarif->fetchColumn();
$stmtTarif5km = $pdo->query("SELECT nilai FROM pengaturan WHERE nama = 'tarif_lebih'");
$tarif5km = $stmtTarif5km->fetchColumn();
?>

<!--tombol edit tarif-->
<div class="d-flex justify-content-between align-items-center mt-4 mb-2">
  <h6>Tarif dibawah 5km: Rp <?= number_format($tarif) ?>/km 
  <br>Tarif setelah 5km: Rp <?= number_format($tarif5km) ?>/km</h6>
  <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalTarif">Edit Tarif</button>
</div>

<!-- Modal Edit Tarif -->
<div class="modal fade" id="modalTarif" tabindex="-1">
  <div class="modal-dialog">
    <form class="modal-content" id="formTarif">
      <div class="modal-header">
        <h5 class="modal-title">Edit Tarif per KM</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <label class="form-label">Tarif kurang 5km (Rp)</label>
        <input type="number" class="form-control" id="inputTarif" value="<?= $tarif ?>" required>
      </div>
      <div class="modal-body">
        <label class="form-label">Tarif setelah 5km (Rp)</label>
        <input type="number" class="form-control" id="inputTarif5km" value="<?= $tarif5km ?>" required>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Simpan</button>
      </div>
    </form>
  </div>
</div>

<script>
document.getElementById("formTarif").addEventListener("submit", function(e) {
  e.preventDefault();
  const tarif = document.getElementById("inputTarif").value;
  const tarif5km = document.getElementById("inputTarif5km").value;

  fetch("simpan_order.php", {
    method: "POST",
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
      action: "simpan_tarif",
      tarif: tarif,
      tarif5km: tarif5km
    })
  })
  .then(res => res.json())
  .then(data => {
    if (data.status === "ok") {
      alert("Tarif berhasil diperbarui!");
      location.reload();
    } else {
      alert("Gagal menyimpan tarif.");
    }
  });
});
</script>

</body>
</html>
