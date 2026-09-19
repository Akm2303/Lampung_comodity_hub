<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../app/models/Commodity.php';
echo json_encode(['success' => true, 'data' => Commodity::all()]);