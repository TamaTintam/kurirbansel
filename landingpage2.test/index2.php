<!DOCTYPE html>
<html>
<head>
    <title>Pilih Titik di Peta</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
</head>
<body>
    <button onclick="mode = 'A'">Pilih Titik A</button>
    <input id="titikA" readonly><br>

    <button onclick="mode = 'B'">Pilih Titik B</button>
    <input id="titikB" readonly><br>

    <button onclick="hitungJarak()">Hitung Jarak</button>
    <p id="hasil"></p>

    <div id="map" style="height: 400px;"></div>

    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script>
        // Inisialisasi peta
        let map = L.map('map').setView([-6.9, 107.6], 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

        // Variabel global
        let titikA = null, titikB = null, garis = null;
        let markerA = null, markerB = null;
        let mode = 'A';  // Default mode A

        map.on('click', function(e) {
            console.log(`Mode sekarang: ${mode}, Lokasi klik: ${e.latlng.lat}, ${e.latlng.lng}`);
            if (mode === 'A') {
                titikA = e.latlng;
                document.getElementById('titikA').value = `${titikA.lat}, ${titikA.lng}`;

                if (markerA) map.removeLayer(markerA);
                markerA = L.marker(titikA).addTo(map).bindPopup("Titik A").openPopup();
            } else if (mode === 'B') {
                titikB = e.latlng;
                document.getElementById('titikB').value = `${titikB.lat}, ${titikB.lng}`;

                if (markerB) map.removeLayer(markerB);
                markerB = L.marker(titikB).addTo(map).bindPopup("Titik B").openPopup();
            }

            // Gambar garis jika kedua titik sudah ada
            if (garis) {
                map.removeLayer(garis);
            }
            if (titikA && titikB) {
                garis = L.polyline([titikA, titikB], {color: 'red'}).addTo(map);
            }
        });

        function hitungJarak() {
            if (!titikA || !titikB) {
                alert("Pilih kedua titik dulu");
                return;
            }

            const R = 6371;
            const dLat = (titikB.lat - titikA.lat) * Math.PI / 180;
            const dLon = (titikB.lng - titikA.lng) * Math.PI / 180;

            const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                      Math.cos(titikA.lat * Math.PI / 180) * Math.cos(titikB.lat * Math.PI / 180) *
                      Math.sin(dLon/2) * Math.sin(dLon/2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
            const d = R * c;

            document.getElementById('hasil').innerText = `Jarak: ${d.toFixed(2)} km`;
        }
    </script>
</body>
</html>
