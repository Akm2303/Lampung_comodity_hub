-- ========================================
-- LAMPUNG AGRI COMMODITY HUB - SCHEMA
-- ========================================
PRAGMA foreign_keys = ON;

-- USERS & AUTH
CREATE TABLE users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username TEXT UNIQUE NOT NULL,
    email TEXT UNIQUE NOT NULL,
    password_hash TEXT NOT NULL,
    role TEXT NOT NULL CHECK(role IN ('SUPER_ADMIN','ADMIN','FARMER','AGGREGATOR','WAREHOUSE','LOGISTICS','EXPORTER','BUYER')),
    full_name TEXT,
    phone TEXT,
    is_active INTEGER DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- COMMODITIES
CREATE TABLE commodities (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    scientific_name TEXT,
    slug TEXT UNIQUE NOT NULL,
    description TEXT,
    image_url TEXT,
    category TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE commodity_varieties (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    commodity_id INTEGER NOT NULL,
    variety_name TEXT NOT NULL,
    characteristics TEXT,
    FOREIGN KEY (commodity_id) REFERENCES commodities(id) ON DELETE CASCADE
);

-- FARMERS
CREATE TABLE farmers (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    farmer_code TEXT UNIQUE NOT NULL,
    full_name TEXT NOT NULL,
    phone TEXT,
    village TEXT,
    district TEXT,
    regency TEXT,
    province TEXT DEFAULT 'Lampung',
    latitude REAL,
    longitude REAL,
    land_size_ha REAL,
    commodity_id INTEGER,
    variety_id INTEGER,
    planting_date DATE,
    estimated_harvest_date DATE,
    estimated_production_ton REAL,
    grade TEXT,
    land_status TEXT CHECK(land_status IN ('Growing','Near Harvest','Harvest','Out of Season')),
    harvest_status TEXT,
    location_consent INTEGER DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (commodity_id) REFERENCES commodities(id),
    FOREIGN KEY (variety_id) REFERENCES commodity_varieties(id)
);

-- PRODUCTION RECORDS
CREATE TABLE production_records (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    farmer_id INTEGER NOT NULL,
    harvest_date DATE,
    volume_ton REAL,
    grade TEXT,
    notes TEXT,
    FOREIGN KEY (farmer_id) REFERENCES farmers(id) ON DELETE CASCADE
);

-- WEATHER DATA
CREATE TABLE weather_data (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    regency TEXT,
    record_date DATE,
    rainfall_mm REAL,
    temperature_c REAL,
    humidity_pct REAL,
    altitude_m REAL,
    ndvi REAL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- ML PREDICTIONS
CREATE TABLE ml_predictions (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    farmer_id INTEGER,
    commodity_id INTEGER,
    predicted_harvest_date DATE,
    predicted_production_ton REAL,
    confidence_score REAL,
    model_version TEXT,
    inputs_json TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (farmer_id) REFERENCES farmers(id),
    FOREIGN KEY (commodity_id) REFERENCES commodities(id)
);

-- LOGISTICS
CREATE TABLE warehouses (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    location TEXT,
    regency TEXT,
    capacity_ton REAL,
    used_capacity_ton REAL DEFAULT 0,
    latitude REAL,
    longitude REAL
);

CREATE TABLE vehicles (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    vehicle_code TEXT UNIQUE,
    vehicle_type TEXT CHECK(vehicle_type IN ('Pickup','Truck','Box Truck','Refrigerated Truck','Container Truck','Ship','Air Cargo')),
    license_plate TEXT,
    driver_name TEXT,
    capacity_ton REAL,
    current_location TEXT,
    status TEXT CHECK(status IN ('Available','Loading','In Transit','Delivered','Maintenance'))
);

CREATE TABLE shipments (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    shipment_code TEXT UNIQUE,
    origin TEXT,
    destination TEXT,
    commodity_id INTEGER,
    weight_ton REAL,
    vehicle_id INTEGER,
    cost_idr REAL,
    status TEXT,
    eta DATETIME,
    shipment_type TEXT CHECK(shipment_type IN ('National','International')),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (commodity_id) REFERENCES commodities(id),
    FOREIGN KEY (vehicle_id) REFERENCES vehicles(id)
);

-- EXPORT
CREATE TABLE buyers (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    company_name TEXT,
    country TEXT,
    contact_person TEXT,
    email TEXT,
    phone TEXT
);

CREATE TABLE export_orders (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    order_code TEXT UNIQUE,
    commodity_id INTEGER,
    buyer_id INTEGER,
    hs_code TEXT,
    quantity_ton REAL,
    grade TEXT,
    origin TEXT,
    destination_country TEXT,
    destination_port TEXT,
    incoterms TEXT,
    status TEXT CHECK(status IN ('Production','Collection','Processing','QC','Ready for Export','Customs','Port','On Vessel','Arrived','Delivered')),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (commodity_id) REFERENCES commodities(id),
    FOREIGN KEY (buyer_id) REFERENCES buyers(id)
);

-- INDEXES
CREATE INDEX idx_farmers_regency ON farmers(regency);
CREATE INDEX idx_farmers_commodity ON farmers(commodity_id);
CREATE INDEX idx_farmers_status ON farmers(land_status);
CREATE INDEX idx_shipments_status ON shipments(status);