<?php
require_once __DIR__ . '/../app/core/Database.php';
require_once __DIR__ . '/../app/core/Auth.php';
require_once __DIR__ . '/../app/models/Commodity.php';
Auth::start();

$commodities = Commodity::all();
$db = Database::getInstance();
$stats = [
    'farmers' => $db->fetchOne("SELECT COUNT(*) c FROM farmers")['c'],
    'land' => round($db->fetchOne("SELECT SUM(land_size_ha) s FROM farmers")['s'] ?? 0, 0),
    'production' => round($db->fetchOne("SELECT SUM(estimated_production_ton) s FROM farmers")['s'] ?? 0, 0),
    'shipments' => $db->fetchOne("SELECT COUNT(*) c FROM shipments")['c'],
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Lampung Agri Commodity Hub</title>
<link rel="stylesheet" href="/assets/css/style.css">
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
</head>
<body>

<?php include __DIR__ . '/partials/navbar.php'; ?>

<!-- HERO -->
<section class="hero">
    <div class="hero-content">
        <span class="badge">🌾 Digital Agriculture Platform</span>
        <h1>Connecting Lampung Agriculture<br>to National & Global Markets</h1>
        <p>Platform digital untuk memetakan komoditas, memprediksi panen, menghubungkan petani, dan mengelola distribusi hingga ekspor.</p>
        <div class="hero-buttons">
            <a href="/commodities.php" class="btn btn-primary">Explore Commodities</a>
            <a href="/map.php" class="btn btn-outline">View Agriculture Map</a>
        </div>
    </div>
</section>

<!-- STATS -->
<section class="stats-section">
    <div class="container stats-grid">
        <div class="stat-card"><div class="stat-value" data-count="<?= $stats['farmers'] ?>">0</div><div class="stat-label">Registered Farmers</div></div>
        <div class="stat-card"><div class="stat-value" data-count="<?= $stats['land'] ?>">0</div><div class="stat-label">Hectares of Land</div></div>
        <div class="stat-card"><div class="stat-value" data-count="<?= $stats['production'] ?>">0</div><div class="stat-label">Tons Estimated Production</div></div>
        <div class="stat-card"><div class="stat-value" data-count="<?= $stats['shipments'] ?>">0</div><div class="stat-label">Active Shipments</div></div>
    </div>
</section>

<!-- COMMODITIES -->
<section class="section">
    <div class="container">
        <div class="section-header">
            <h2>Komoditas Unggulan</h2>
            <p>Katalog komoditas pertanian premium dari seluruh wilayah Lampung</p>
        </div>
        <div class="commodity-grid">
            <?php foreach ($commodities as $c): ?>
            <div class="commodity-card">
                <div class="card-image" style="background-image:url('<?= htmlspecialchars($c['image_url']) ?>')"></div>
                <div class="card-body">
                    <h3><?= strtoupper(htmlspecialchars($c['name'])) ?></h3>
                    <p class="scientific"><?= htmlspecialchars($c['scientific_name']) ?></p>
                    <div class="card-meta">
                        <span>📍 <?= (int)$c['regions'] ?> Kabupaten</span>
                        <span>🌱 <?= number_format($c['total_land'] ?? 0, 0) ?> Ha</span>
                        <span>📦 <?= number_format($c['total_production'] ?? 0, 0) ?> Ton</span>
                        <span>👨‍🌾 <?= (int)$c['farmer_count'] ?> Petani</span>
                    </div>
                    <a href="/commodity-detail.php?slug=<?= urlencode($c['slug']) ?>" class="btn btn-card">VIEW DETAIL →</a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- SMART ALERTS -->
<section class="section alerts-section">
    <div class="container">
        <h2>🔔 Smart Alerts</h2>
        <div class="alerts-grid">
            <div class="alert-card harvest">
                <h4>🌾 Harvest Alert</h4>
                <p>78 petani kopi dengan total 125 Ha diperkirakan memasuki masa panen dalam 30 hari.</p>
            </div>
            <div class="alert-card logistics">
                <h4>🚛 Logistics Alert</h4>
                <p>Estimasi 450 Ton kopi membutuhkan kapasitas transportasi ±18 truck.</p>
            </div>
            <div class="alert-card export">
                <h4>🌍 Export Alert</h4>
                <p>Volume 450 Ton tersedia untuk proses export.</p>
            </div>
            <div class="alert-card warehouse">
                <h4>🏭 Warehouse Alert</h4>
                <p>Warehouse membutuhkan tambahan kapasitas 200 Ton.</p>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/partials/footer.php'; ?>

<script src="/assets/js/app.js"></script>
</body>
</html>