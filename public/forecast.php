<?php
require_once __DIR__ . '/../app/core/Auth.php';
Auth::start();
?>
<!DOCTYPE html>
<html lang="id"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>AI Harvest Forecast</title>
<link rel="stylesheet" href="/assets/css/style.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head><body>
<?php include __DIR__ . '/partials/navbar.php'; ?>

<section class="section">
    <div class="container">
        <h1>🤖 AI Harvest Prediction</h1>
        <p class="muted">Prediksi berbasis Machine Learning (XGBoost). Hasil adalah estimasi, bukan kepastian.</p>

        <div class="forecast-controls">
            <select id="forecastCommodity">
                <option value="1">Kopi</option>
                <option value="2">Pisang</option>
                <option value="3">Singkong</option>
                <option value="4">Jagung</option>
                <option value="5">Vanili</option>
            </select>
            <select id="forecastRegency">
                <option value="Lampung Barat">Lampung Barat</option>
                <option value="Tanggamus">Tanggamus</option>
                <option value="Way Kanan">Way Kanan</option>
                <option value="Pesawaran">Pesawaran</option>
                <option value="Lampung Selatan">Lampung Selatan</option>
            </select>
            <button id="btnForecast" class="btn btn-primary">🔮 Predict Harvest</button>
        </div>

        <div id="forecastResult" class="forecast-result" style="display:none">
            <div class="forecast-card">
                <h3 id="fcTitle"></h3>
                <div class="forecast-metrics">
                    <div><span class="lbl">Estimated Harvest</span><strong id="fcDate"></strong></div>
                    <div><span class="lbl">Confidence</span><strong id="fcConf"></strong></div>
                    <div><span class="lbl">Est. Production</span><strong id="fcProd"></strong></div>
                    <div><span class="lbl">Available Farmers</span><strong id="fcFarmers"></strong></div>
                    <div><span class="lbl">Land</span><strong id="fcLand"></strong></div>
                </div>
                <p class="muted small" id="fcNote"></p>
            </div>
        </div>
    </div>
</section>

<script>
document.getElementById('btnForecast').addEventListener('click', async () => {
    const commodityId = document.getElementById('forecastCommodity').value;
    const regency = document.getElementById('forecastRegency').value;
    const btn = document.getElementById('btnForecast');
    btn.disabled = true; btn.textContent = '⏳ Memproses...';

    try {
        const res = await fetch('/api/forecast.php', {
            method: 'POST',
            headers: {'Content-Type':'application/json'},
            body: JSON.stringify({ commodity_id: commodityId, regency })
        });
        const data = await res.json();
        if (data.success) {
            document.getElementById('forecastResult').style.display = 'block';
            document.getElementById('fcTitle').textContent = `${data.commodity} — ${regency}`;
            document.getElementById('fcDate').textContent = data.estimated_harvest_range;
            document.getElementById('fcConf').textContent = data.confidence + '%';
            document.getElementById('fcProd').textContent = data.estimated_production_ton + ' Ton';
            document.getElementById('fcFarmers').textContent = data.farmer_count;
            document.getElementById('fcLand').textContent = data.total_land_ha + ' Ha';
            document.getElementById('fcNote').textContent = 'Sumber data: ' + data.sources.join(', ');
        }
    } catch(e) { alert('Error: ' + e.message); }
    btn.disabled = false; btn.textContent = '🔮 Predict Harvest';
});
</script>
<?php include __DIR__ . '/partials/footer.php'; ?>
</body></html>