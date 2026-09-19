<?php
require_once __DIR__ . '/../app/core/Auth.php';
require_once __DIR__ . '/../app/models/Farmer.php';
require_once __DIR__ . '/../app/models/Commodity.php';
Auth::start();

$filters = [
    'regency' => $_GET['regency'] ?? '',
    'commodity_id' => $_GET['commodity_id'] ?? '',
    'min_land' => $_GET['min_land'] ?? '',
    'land_status' => $_GET['land_status'] ?? '',
];
$farmers = Farmer::filter($filters);
$stats = Farmer::stats($filters);
$commodities = Commodity::all();
$db = Database::getInstance();
$regencies = $db->fetchAll("SELECT DISTINCT regency FROM farmers ORDER BY regency");
?>
<!DOCTYPE html>
<html lang="id"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Farmer Database</title>
<link rel="stylesheet" href="/assets/css/style.css"></head><body>
<?php include __DIR__ . '/partials/navbar.php'; ?>

<section class="section">
    <div class="container">
        <h1>👨‍🌾 Farmer Database</h1>

        <form method="GET" class="filter-bar">
            <select name="commodity_id">
                <option value="">Semua Komoditas</option>
                <?php foreach ($commodities as $c): ?>
                    <option value="<?= $c['id'] ?>" <?= $filters['commodity_id'] == $c['id'] ? 'selected' : '' ?>><?= htmlspecialchars($c['name']) ?></option>
                <?php endforeach; ?>
            </select>
            <select name="regency">
                <option value="">Semua Kabupaten</option>
                <?php foreach ($regencies as $r): ?>
                    <option value="<?= htmlspecialchars($r['regency']) ?>" <?= $filters['regency']==$r['regency']?'selected':'' ?>><?= htmlspecialchars($r['regency']) ?></option>
                <?php endforeach; ?>
            </select>
            <select name="min_land">
                <option value="">Semua Luas</option>
                <option value="1" <?= $filters['min_land']==1?'selected':'' ?>>≥ 1 Ha</option>
                <option value="5" <?= $filters['min_land']==5?'selected':'' ?>>≥ 5 Ha</option>
                <option value="10" <?= $filters['min_land']==10?'selected':'' ?>>≥ 10 Ha</option>
            </select>
            <select name="land_status">
                <option value="">Semua Status</option>
                <?php foreach (['Growing','Near Harvest','Harvest','Out of Season'] as $s): ?>
                    <option value="<?= $s ?>" <?= $filters['land_status']==$s?'selected':'' ?>><?= $s ?></option>
                <?php endforeach; ?>
            </select>
            <button class="btn btn-primary" type="submit">Filter</button>
        </form>

        <div class="stats-grid compact">
            <div class="stat-card"><div class="stat-value"><?= $stats['count'] ?></div><div class="stat-label">Petani</div></div>
            <div class="stat-card"><div class="stat-value"><?= number_format($stats['total_land'], 0) ?></div><div class="stat-label">Hektare</div></div>
            <div class="stat-card"><div class="stat-value"><?= number_format($stats['total_production'], 0) ?></div><div class="stat-label">Ton Estimasi</div></div>
        </div>

        <div class="table-wrapper">
            <table class="data-table">
                <thead><tr>
                    <th>ID</th><th>Nama</th><th>Kontak</th><th>Kabupaten</th>
                    <th>Komoditas</th><th>Luas (Ha)</th><th>Produksi (Ton)</th>
                    <th>Status</th><th>Est. Panen</th>
                </tr></thead>
                <tbody>
                <?php foreach (array_slice($farmers, 0, 50) as $f): ?>
                    <tr>
                        <td><?= htmlspecialchars($f['farmer_code']) ?></td>
                        <td><?= htmlspecialchars($f['full_name']) ?></td>
                        <td><?= htmlspecialchars($f['phone']) ?></td>
                        <td><?= htmlspecialchars($f['regency']) ?></td>
                        <td><?= htmlspecialchars($f['commodity_name']) ?></td>
                        <td><?= $f['land_size_ha'] ?></td>
                        <td><?= $f['estimated_production_ton'] ?></td>
                        <td><span class="status-pill status-<?= strtolower(str_replace(' ','-',$f['land_status'])) ?>"><?= $f['land_status'] ?></span></td>
                        <td><?= $f['estimated_harvest_date'] ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <p class="muted">Menampilkan 50 dari <?= count($farmers) ?> petani (koordinat GPS disembunyikan sesuai kebijakan privasi)</p>
    </div>
</section>

<?php include __DIR__ . '/partials/footer.php'; ?>
</body></html>