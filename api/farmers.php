<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
require_once __DIR__ . '/../../app/models/Farmer.php';

$filters = [
    'regency' => $_GET['regency'] ?? '',
    'commodity_id' => $_GET['commodity_id'] ?? '',
    'min_land' => $_GET['min_land'] ?? '',
    'land_status' => $_GET['land_status'] ?? '',
];

$farmers = Farmer::filter($filters);

// Privacy: sembunyikan koordinat jika consent = 0
$consentOnly = isset($_GET['consent']) && $_GET['consent'] == '1';
if ($consentOnly) {
    $farmers = array_filter($farmers, fn($f) => (int)$f['location_consent'] === 1);
    $farmers = array_values($farmers);
}

// Public API: hide exact coordinates for non-consented
foreach ($farmers as &$f) {
    if ((int)$f['location_consent'] !== 1) {
        $f['latitude'] = round($f['latitude'], 1);
        $f['longitude'] = round($f['longitude'], 1);
        $f['phone'] = '***';
    }
}

echo json_encode(['success' => true, 'count' => count($farmers), 'data' => $farmers]);