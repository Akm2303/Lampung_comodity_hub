<?php
$db = new PDO('sqlite:lampung_agri.db');
$regions = [
    ['Lampung Barat', ['Sumber Jaya','Way Tenong','Sekincau','Batu Brak']],
    ['Tanggamus', ['Kota Agung','Pulau Panggung','Wonosobo','Semaka']],
    ['Way Kanan', ['Bumi Agung','Kasui','Blambangan Umpu','Baradatu']],
    ['Pesawaran', ['Gedong Tataan','Natar','Punduh Pidada','Padang Cermin']],
    ['Lampung Selatan', ['Kalianda','Sidomulyo','Natar','Penengahan']],
];
$commodities = [1=>[1,2], 2=>[3], 3=>[4], 4=>[5], 5=>[6]];
$grades = ['A','B','C'];
$statuses = ['Growing','Near Harvest','Harvest','Out of Season'];

$stmt = $db->prepare("INSERT INTO farmers (farmer_code, full_name, phone, village, district, regency, latitude, longitude, land_size_ha, commodity_id, variety_id, planting_date, estimated_harvest_date, estimated_production_ton, grade, land_status, location_consent) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");

for ($i = 11; $i <= 100; $i++) {
    $reg = $regions[array_rand($regions)];
    $village = $reg[1][array_rand($reg[1])];
    $regency = $reg[0];
    $commId = array_rand($commodities);
    $varId = $commodities[$commId][array_rand($commodities[$commId])];
    $plantDate = date('Y-m-d', strtotime('-' . rand(30, 400) . ' days'));
    $harvestDate = date('Y-m-d', strtotime($plantDate . ' + ' . rand(180, 400) . ' days'));
    $landSize = round(rand(5, 100) / 10, 1);
    $production = round($landSize * (rand(8, 25) / 10), 2);
    
    $stmt->execute([
        sprintf('FRM-%04d', $i),
        'Petani ' . $i,
        '0812' . rand(10000000, 99999999),
        $village, $village, $regency,
        -5 + (rand(-100, 100) / 100),
        104 + (rand(0, 200) / 100),
        $landSize, $commId, $varId,
        $plantDate, $harvestDate, $production,
        $grades[array_rand($grades)],
        $statuses[array_rand($statuses)],
        rand(0, 1)
    ]);
}
echo "100 farmers generated!\n";