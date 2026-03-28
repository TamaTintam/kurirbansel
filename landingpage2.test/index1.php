<?php
// Tidak perlu koneksi database di sini karena hanya form
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Kurir Kampung - Pemesanan</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_GOOGLE_MAPS_API_KEY&libraries=places"></script>
  <style>
    body {
      background: #f7f7f7;
    }
    .container {
      max-width: 600px;
      background: white;
      padding: 25px;
      border-radius: 12px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
  </style>
</head>
<body>
<div class="container mt-5">
  <h3 class="text-center mb-4">🚚 Kurir Kampung</h3>
  <form action="proses_order.php" method="POST" id="orderForm">
    <div class="mb-3">
      <label for="origin" class="form-label">Lokasi Pengirim</label>
      <input type="text" class="form-control" id="origin" name="origin" required>
    </div>
    <div class="mb-3">
      <label for="destination" class="form-label">Lokasi Tujuan</label>
      <input type="text" class="form-control" id="destination" name="destination" required>
    </div>
    <div class="mb-3">
      <label for="nama" class="form-label">Nama Pemesan</label>
      <input type="text" class="form-control" name="nama" id="nama" required>
    </div>
    <div class="mb-3">
      <label for="wa" class="form-label">Nomor WhatsApp</label>
      <input type="text" class="form-control" name="wa" id="wa" required>
    </div>
    <div class="mb-3">
      <label for="barang" class="form-label">Deskripsi Barang</label>
      <textarea class="form-control" name="barang" id="barang" rows="2" required></textarea>
    </div>
    <div class="mb-3">
      <label class="form-label">Estimasi Ongkir:</label>
      <h5 id="biaya" class="text-success">Rp 0</h5>
      <input type="hidden" name="ongkir" id="ongkir">
    </div>
    <button type="submit" class="btn btn-success w-100">Pesan Sekarang</button>
  </form>
</div>

<script>
  const tarifPerKm = 2000;
  let directionsService = new google.maps.DirectionsService();

  document.getElementById("origin").addEventListener("change", hitungOngkir);
  document.getElementById("destination").addEventListener("change", hitungOngkir);

  function hitungOngkir() {
    const origin = document.getElementById("origin").value;
    const destination = document.getElementById("destination").value;

    if (!origin || !destination) return;

    directionsService.route({
      origin: origin,
      destination: destination,
      travelMode: google.maps.TravelMode.DRIVING
    }, function (response, status) {
      if (status === google.maps.DirectionsStatus.OK) {
        const distanceInMeters = response.routes[0].legs[0].distance.value;
        const distanceInKm = distanceInMeters / 1000;
        const biaya = Math.ceil(distanceInKm) * tarifPerKm;

        document.getElementById("biaya").innerText = `Rp ${biaya.toLocaleString()}`;
        document.getElementById("ongkir").value = biaya;
      } else {
        alert("Gagal menghitung jarak: " + status);
      }
    });
  }
  
  
  window.onload = function () {
  if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(showPosition, showError);
  } else {
    alert("Browser tidak mendukung lokasi.");
  }
};

function showPosition(position) {
  const lat = position.coords.latitude;
  const lng = position.coords.longitude;

  const geocoder = new google.maps.Geocoder();
  const latlng = { lat: parseFloat(lat), lng: parseFloat(lng) };

  geocoder.geocode({ location: latlng }, function (results, status) {
    if (status === "OK") {
      if (results[0]) {
        document.getElementById("origin").value = results[0].formatted_address;
        hitungOngkir(); // langsung hitung ongkir
      } else {
        alert("Alamat tidak ditemukan.");
      }
    } else {
      alert("Geocoder gagal: " + status);
    }
  });
}

function showError(error) {
  switch (error.code) {
    case error.PERMISSION_DENIED:
      alert("Izin lokasi ditolak.");
      break;
    case error.POSITION_UNAVAILABLE:
      alert("Informasi lokasi tidak tersedia.");
      break;
    case error.TIMEOUT:
      alert("Permintaan lokasi habis waktu.");
      break;
    case error.UNKNOWN_ERROR:
      alert("Terjadi kesalahan lokasi.");
      break;
  }
}

</script>

</body>
</html>
