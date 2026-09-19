<?php
require_once __DIR__ . '/../core/Database.php';

class Farmer {
    public static function filter($filters = []) {
        $db = Database::getInstance();
        $sql = "SELECT f.*, c.name AS commodity_name, v.variety_name
                FROM farmers f
                LEFT JOIN commodities c ON f.commodity_id = c.id
                LEFT JOIN commodity_varieties v ON f.variety_id = v.id
                WHERE 1=1";
        $params = [];

        if (!empty($filters['regency'])) { $sql .= " AND f.regency = ?"; $params[] = $filters['regency']; }
        if (!empty($filters['district'])) { $sql .= " AND f.district = ?"; $params[] = $filters['district']; }
        if (!empty($filters['commodity_id'])) { $sql .= " AND f.commodity_id = ?"; $params[] = $filters['commodity_id']; }
        if (!empty($filters['min_land'])) { $sql .= " AND f.land_size_ha >= ?"; $params[] = $filters['min_land']; }
        if (!empty($filters['land_status'])) { $sql .= " AND f.land_status = ?"; $params[] = $filters['land_status']; }
        if (!empty($filters['min_production'])) { $sql .= " AND f.estimated_production_ton >= ?"; $params[] = $filters['min_production']; }

        return $db->fetchAll($sql, $params);
    }

    public static function stats($filters = []) {
        $rows = self::filter($filters);
        $totalLand = array_sum(array_column($rows, 'land_size_ha'));
        $totalProd = array_sum(array_column($rows, 'estimated_production_ton'));
        return [
            'count' => count($rows),
            'total_land' => round($totalLand, 2),
            'total_production' => round($totalProd, 2)
        ];
    }
}