<?php
require_once __DIR__ . '/../app/core/Auth.php';
Auth::start();
?>
<!DOCTYPE html>
<html lang="id"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Agriculture Map</title>
<link rel="stylesheet" href="/assets/css/style.css">
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.css">
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.Default.css">
</head><body>
<?php include __DIR__ . '/partials/navbar.php'; ?>

<section class="section">
    <div class="container">
        <h1>🗺️ Agriculture Map</h1>
        <div class="map-controls">
            <label><input type="checkbox" class="commodity-filter" value="1" checked> ☕ Kopi</label>
            <label><input type="checkbox" class="commodity-filter" value="2" checked> 🍌 Pisang</label>
            <label><input type="checkbox" class="commodity-filter" value="3" checked> 🥔 Singkong</label>
            <label><input type="checkbox" class="commodity-filter" value="4" checked> 🌽 Jagung</label>
            <label><input type="checkbox" class="commodity-filter" value="5" checked> 🌿 Vanili</label>
        </div>
        <div id="map" style="height:600px;border-radius:12px;"></div>
    </div>
</section>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet.markercluster@1.5.3/dist/leaflet.markercluster.js"></script>
<script>
const map = L.map('map').setView([-5.2, 104.8], 9);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '© OpenStreetMap' }).addTo(map);

const markers = L.markerClusterGroup();
let allFarmers = [];

const commodityColors = { 1:'#8b4513', 2:'#f4d03f', 3:'#c39bd3', 4:'#f39c12', 5:'#27ae60' };
const commodityIcons = { 1:'☕', 2:'🍌', 3:'🥔', 4:'🌽', 5:'🌿' };

fetch('/api/farmers.php?consent=1')
    .then(r => r.json())
    .then(data => {
        allFarmers = data.data || [];
        renderMarkers(allFarmers);
    });

function renderMarkers(farmers) {
    markers.clearLayers();
    farmers.forEach(f => {
        if (!f.latitude || !f.longitude) return;
        const color = commodityColors[f.commodity_id] || '#2d6a4f';
        const icon = L.divIcon({
            html: `<div style="background:${color};width:28px;height:28px;border-radius:50%;color:white;text-align:center;line-height:28px;font-size:14px;border:2px solid white;box-shadow:0 2px 6px rgba(0,0,0,0.3)">${commodityIcons[f.commodity_id]||'📍'}</div>`,
            iconSize: [28, 28], className: 'custom-marker'
        });
        const marker = L.marker([f.latitude, f.longitude], { icon })
            .bindPopup(`
                <div style="min-width:220px">
                    <h4 style="margin:0 0 6px;color:#1b4332">${f.commodity_name}</h4>
                    <p style="margin:2px 0"><b>${f.village}, ${f.district}</b></p>
                    <p style="margin:2px 0">Kab. ${f.regency}</p>
                    <hr style="margin:6px 0;border:none;border-top:1px solid #eee">
                    <p style="margin:2px 0">🌱 Luas: <b>${f.land_size_ha} Ha</b></p>
                    <p style="margin:2px 0">📦 Produksi: <b>${f.estimated_production_ton} Ton</b></p>
                    <p style="margin:2px 0">🗓 Panen: <b>${f.estimated_harvest_date}</b></p>
                    <p style="margin:2px 0">⭐ Grade: <b>${f.grade}</b></p>
                    <p style="margin:2px 0">📊 Status: <b>${f.land_status}</b></p>
                </div>
            `);
        marker._commodityId = f.commodity_id;
        markers.addLayer(marker);
    });
    map.addLayer(markers);
}

document.querySelectorAll('.commodity-filter').forEach(cb => {
    cb.addEventListener('change', () => {
        const active = [...document.querySelectorAll('.commodity-filter:checked')].map(c => parseInt(c.value));
        const filtered = allFarmers.filter(f => active.includes(parseInt(f.commodity_id)));
        renderMarkers(filtered);
    });
});
</script>
<?php include __DIR__ . '/partials/footer.php'; ?>
</body></html>