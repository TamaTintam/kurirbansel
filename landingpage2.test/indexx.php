<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Kurir Kampung (Non-Google Version)</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
  <link rel="manifest" href="manifest.json">
    <meta name="theme-color" content="#0d6efd">

  <style>
    #map { height: 300px; }
  </style>
</head>
<body class="bg-light">

<div class="container mt-4 p-4 bg-white shadow rounded">
  <h4 class="mb-3 text-center">🚚 Kurir Kampung</h4>
  <form id="formOrder">
    <div class="mb-3">
      <label>Lokasi Pengirim (otomatis)</label>
      <input type="text" class="form-control" id="origin" name="origin" readonly>
    </div>
    <div class="mb-3">
      <label>Tujuan</label>
      <input type="text" class="form-control" id="destination" placeholder="Contoh: Pasar Desa, RT 03" required>
    </div>
    <div class="mb-3">
      <label>Deskripsi Barang</label>
      <input type="text" class="form-control" id="barang" placeholder="Contoh: Nasi kotak 10 porsi" required>
    </div>
    <div class="mb-3">
      <label>Nomor WhatsApp</label>
      <input type="text" class="form-control" id="wa" placeholder="08xxxxxxxx" required>
    </div>

    <div class="mb-3">
      <label>Estimasi Ongkir:</label>
      <h5 id="ongkir">Rp 0</h5>
    </div>

    <div id="map" class="mb-3"></div>

    <button type="submit" class="btn btn-success w-100">Pesan via WhatsApp</button>
  </form>
</div>

<!-- JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
  let map, userLatLng, tujuanLatLng;
  const tarifPerKm = 2000;

  // Inisialisasi peta
  map = L.map('map').setView([-7.25, 112.75], 13); // fallback location
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

  // Geolokasi pengguna (asal)
  navigator.geolocation.getCurrentPosition(async function(pos) {
    userLatLng = [pos.coords.latitude, pos.coords.longitude];
    map.setView(userLatLng, 15);
    L.marker(userLatLng).addTo(map).bindPopup("Lokasi Anda").openPopup();

    // Reverse geocoding Nominatim
    const res = await fetch(`https://nominatim.openstreetmap.org/reverse?lat=${userLatLng[0]}&lon=${userLatLng[1]}&format=json`);
    const data = await res.json();
    document.getElementById("origin").value = data.display_name;
  });

  // Klik pada peta untuk ambil tujuan
  let tujuanMarker;
  map.on("click", async function(e) {
    if (tujuanMarker) map.removeLayer(tujuanMarker);
    tujuanLatLng = [e.latlng.lat, e.latlng.lng];
    tujuanMarker = L.marker(tujuanLatLng).addTo(map).bindPopup("Tujuan").openPopup();

    // Reverse geocode tujuan
    const res = await fetch(`https://nominatim.openstreetmap.org/reverse?lat=${tujuanLatLng[0]}&lon=${tujuanLatLng[1]}&format=json`);
    const data = await res.json();
    document.getElementById("destination").value = data.display_name;

    // Hitung jarak via OSRM
    hitungJarakDanBiaya();
  });

  async function hitungJarakDanBiaya() {
    if (!userLatLng || !tujuanLatLng) return;

    const url = `https://router.project-osrm.org/route/v1/driving/${userLatLng[1]},${userLatLng[0]};${tujuanLatLng[1]},${tujuanLatLng[0]}?overview=false`;
    const res = await fetch(url);
    const json = await res.json();

    if (json.routes && json.routes.length > 0) {
      const meter = json.routes[0].distance;
      const km = meter / 1000;
      const biaya = Math.ceil(km) * tarifPerKm;

      document.getElementById("ongkir").innerText = "Rp " + biaya.toLocaleString();
      document.getElementById("ongkir").dataset.biaya = biaya;
    }
  }

  // Kirim WA saat submit
      document.getElementById("formOrder").addEventListener("submit", function(e) {
      e.preventDefault();
      const origin = document.getElementById("origin").value;
      const destination = document.getElementById("destination").value;
      const barang = document.getElementById("barang").value;
      const wa = document.getElementById("wa").value;
      const biaya = document.getElementById("ongkir").dataset.biaya || 0;
    
      // Simpan ke database
      fetch('simpan_order.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          origin, destination, barang, wa,
          ongkir: biaya,
          nama: "Pelanggan"
        })
      });
    
      const pesan = `Halo Admin Kurir,%0ASaya ingin kirim barang:%0A- Barang: ${barang}%0A- Dari: ${origin}%0A- Ke: ${destination}%0AOngkir: Rp ${biaya}`;
      const url = `https://wa.me/62${wa.replace(/^0/, '')}?text=${pesan}`;
      window.open(url, "_blank");
    });

</script>
<script>
  if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('/service-worker.js')
      .then(reg => console.log("Service Worker registered", reg))
      .catch(err => console.error("Service Worker failed", err));
  }
</script>

</body>
</html>
