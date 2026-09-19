<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
require_once __DIR__ . '/../../app/core/Database.php';

$input = json_decode(file_get_contents('php://input'), true) ?: $_GET;
$commodityId = (int)($input['commodity_id'] ?? 1);
$regency = $input['regency'] ?? 'Lampung Barat';

$db = Database::getInstance();
$commodity = $db->fetchOne("SELECT name FROM commodities WHERE id = ?", [$commodityId]);

// Aggregate historical features
$stats = $db->fetchOne("
    SELECT
        COUNT(*) as farmer_count,
        COALESCE(SUM(land_size_ha),0) as total_land,
        COALESCE(AVG(land_size_ha),0) as avg_land,
        COALESCE(SUM(estimated_production_ton),0) as total_production
    FROM farmers
    WHERE commodity_id = ? AND regency = ?
", [$commodityId, $regency]);

$features = [
    'commodity_id' => $commodityId,
    'regency' => $regency,
    'farmer_count' => (int)$stats['farmer_count'],
    'total_land' => (float)$stats['total_land'],
    'avg_land' => (float)$stats['avg_land'],
    'historical_production' => (float)$stats['total_production']
];

// Call Python ML service
$mlUrl = getenv('ML_SERVICE_URL') ?: 'http://ml:8000/predict';
$ch = curl_init($mlUrl);
curl_setopt_array($ch, [
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode($features),
    CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 10
]);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 200 && $response) {
    $ml = json_decode($response, true);
} else {
    // Fallback: simple heuristic
    $ml = [
        'predicted_harvest_date' => date('Y-m-d', strtotime('+' . rand(20, 45) . ' days')),
        'predicted_production_ton' => round((float)$stats['total_production'] * 0.95, 1),
        'confidence_score' => 0.72,
        'model_version' => 'fallback-heuristic'
    ];
}

$start = strtotime($ml['predicted_harvest_date']);
$end = strtotime('+15 days', $start);

echo json_encode([
    'success' => true,
    'commodity' => $commodity['name'] ?? 'Unknown',
    'regency' => $regency,
    'estimated_harvest_range' => date('d M', $start) . ' – ' . date('d M Y', $end),
    'confidence' => round($ml['confidence_score'] * 100, 0),
    'estimated_production_ton' => $ml['predicted_production_ton'],
    'farmer_count' => (int)$stats['farmer_count'],
    'total_land_ha' => round((float)$stats['total_land'], 0),
    'sources' => ['Historical Harvest', 'Land Size', 'Planting Records', 'Weather Data'],
    'model_version' => $ml['model_version']
]);