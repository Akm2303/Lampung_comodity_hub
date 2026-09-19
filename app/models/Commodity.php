<?php
require_once __DIR__ . '/../core/Database.php';

class Commodity {
    public static function all() {
        $db = Database::getInstance();
        return $db->fetchAll("
            SELECT c.*,
                (SELECT COUNT(DISTINCT f.regency) FROM farmers f WHERE f.commodity_id = c.id) AS regions,
                (SELECT SUM(f.land_size_ha) FROM farmers f WHERE f.commodity_id = c.id) AS total_land,
                (SELECT SUM(f.estimated_production_ton) FROM farmers f WHERE f.commodity_id = c.id) AS total_production,
                (SELECT COUNT(*) FROM farmers f WHERE f.commodity_id = c.id) AS farmer_count
            FROM commodities c
        ");
    }

    public static function find($idOrSlug) {
        $db = Database::getInstance();
        $sql = is_numeric($idOrSlug)
            ? "SELECT * FROM commodities WHERE id = ?"
            : "SELECT * FROM commodities WHERE slug = ?";
        return $db->fetchOne($sql, [$idOrSlug]);
    }

    public static function varieties($commodityId) {
        $db = Database::getInstance();
        return $db->fetchAll("SELECT * FROM commodity_varieties WHERE commodity_id = ?", [$commodityId]);
    }

    public static function regions($commodityId) {
        $db = Database::getInstance();
        return $db->fetchAll("
            SELECT regency, COUNT(*) as farmers, SUM(land_size_ha) as land, SUM(estimated_production_ton) as production
            FROM farmers WHERE commodity_id = ?
            GROUP BY regency ORDER BY production DESC
        ", [$commodityId]);
    }

    public static function monthlyProduction($commodityId) {
        $db = Database::getInstance();
        return $db->fetchAll("
            SELECT strftime('%m', estimated_harvest_date) as month,
                   SUM(estimated_production_ton) as volume
            FROM farmers WHERE commodity_id = ?
            GROUP BY month ORDER BY month
        ", [$commodityId]);
    }
}