<?php
require_once __DIR__ . '/../app/core/Auth.php';
require_once __DIR__ . '/../app/models/Commodity.php';
Auth::start();
$commodities = Commodity::all();
?>
<!DOCTYPE html>
<html lang="id"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Commodities - Lampung Agri Hub</title>
<link rel="stylesheet" href="/assets/css/style.css"></head>
<body>
<?php include __DIR__ . '/partials/navbar.php'; ?>
<section class="section">
    <div class="container">
        <div class="section-header">
            <h1>Komoditas Pertanian Lampung</h1>
            <p>Katalog lengkap komoditas unggulan dari seluruh kabupaten di Lampung</p>
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
<?php include __DIR__ . '/partials/footer.php'; ?>
<script src="/assets/js/app.js"></script>
</body></html>