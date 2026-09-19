<?php
require_once __DIR__ . '/../app/core/Auth.php';
require_once __DIR__ . '/../app/models/Commodity.php';
Auth::start();

$slug = $_GET['slug'] ?? 'kopi';
$c = Commodity::find($slug);
if (!$c) { http_response_code(404); exit("Commodity not found"); }
$varieties = Commodity::varieties($c['id']);
$regions = Commodity::regions($c['id']);
$monthly = Commodity::monthlyProduction($c['id']);
?>
<!DOCTYPE html>
<html lang="id"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= htmlspecialchars($c['name']) ?> - Detail</title>
<link rel="stylesheet" href="/assets/css/style.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head><body>
<?php include __DIR__ . '/partials/navbar.php'; ?>

<section class="section">
    <div class="container">
        <a href="/commodities.php" class="back-link">← Kembali ke Katalog</a>
        <div class="detail-hero">
            <div class="detail-image" style="background-image:url('<?= htmlspecialchars($c['image_url']) ?>')"></div>
            <div class="detail-info">
                <span class="badge badge-green">🟢 Ready for Harvest</span>
                <h1><?= htmlspecialchars($c['name']) ?></h1>
                <p class="scientific-large"><?= htmlspecialchars($c['scientific_name']) ?></p>
                <p><?= htmlspecialchars($c['description']) ?></p>

                <div class="variety-tags">
                    <?php foreach ($varieties as $v): ?>
                        <span class="tag"><?= htmlspecialchars($v['variety_name']) ?></span>
                    <?php endforeach; ?>
                </div>

                <div class="detail-stats">
                    <div><strong><?= count($regions) ?></strong><span>Kabupaten</span></div>
                    <div><strong><?= number_format(array_sum(array_column($regions, 'land')), 0) ?></strong><span>Hektare</span></div>
                    <div><strong><?= number_format(array_sum(array_column($regions, 'production')), 0) ?></strong><span>Ton Estimasi</span></div>
                </div>
            </div>
        </div>

        <div class="detail-grid">
            <div class="detail-card">
                <h3>📍 Daerah Produksi</h3>
                <ul class="region-list">
                    <?php foreach ($regions as $r): ?>
                        <li>
                            <strong><?= htmlspecialchars($r['regency']) ?></strong>
                            <span><?= (int)$r['farmers'] ?> petani · <?= number_format($r['land'], 0) ?> Ha · <?= number_format($r['production'], 0) ?> Ton</span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <div class="detail-card">
                <h3>📊 Data Produksi & Karakteristik</h3>
                <table class="info-table">
                    <tr><td>Musim Tanam</td><td>Sepanjang tahun</td></tr>
                    <tr><td>Estimasi Umur Tanaman</td><td>6–12 bulan</td></tr>
                    <tr><td>Periode Panen</td><td>Okt–Des</td></tr>
                    <tr><td>Grade</td><td>A / B / C</td></tr>
                    <tr><td>Bentuk Produk</td><td>Biji kering / segar</td></tr>
                    <tr><td>Potensi Pasar</td><td>Nasional & Internasional</td></tr>
                    <tr><td>Negara Tujuan</td><td>Singapore, Malaysia, China, Europe</td></tr>
                    <tr><td>Metode Pengemasan</td><td>Karung 50 kg / vacuum</td></tr>
                </table>
            </div>
        </div>

        <div class="chart-section">
            <h3>📈 Volume Panen Bulanan (Ton)</h3>
            <canvas id="monthlyChart" height="80"></canvas>
        </div>
    </div>
</section>

<script>
const monthlyData = <?= json_encode($monthly) ?>;
const months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
const labels = monthlyData.map(d => months[(parseInt(d.month)||1)-1]);
const values = monthlyData.map(d => parseFloat(d.volume).toFixed(1));
new Chart(document.getElementById('monthlyChart'), {
    type: 'bar',
    data: { labels, datasets: [{ label: 'Volume (Ton)', data: values, backgroundColor: '#2d6a4f', borderRadius: 8 }] },
    options: { responsive: true, plugins: { legend: { display: false } } }
});
</script>

<?php include __DIR__ . '/partials/footer.php'; ?>
</body></html>