-- USERS
INSERT INTO users (username, email, password_hash, role, full_name) VALUES
('superadmin', 'admin@lampungagri.id', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHe1Hk6m', 'SUPER_ADMIN', 'Super Admin'),
('farmer01', 'farmer01@mail.id', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHe1Hk6m', 'FARMER', 'Budi Santoso');

-- COMMODITIES
INSERT INTO commodities (name, scientific_name, slug, description, image_url, category) VALUES
('Kopi Lampung', 'Coffea arabica / Coffea canephora', 'kopi', 'Kopi premium dari dataran tinggi Lampung dengan cita rasa khas.', 'https://images.unsplash.com/photo-1447933601403-0c6688de566e?w=800', 'Beverage'),
('Pisang Lampung', 'Musa paradisiaca', 'pisang', 'Pisang unggulan dengan rasa manis dan tekstur lembut.', 'https://images.unsplash.com/photo-1571771894821-ce9b6c11b08e?w=800', 'Fruit'),
('Singkong', 'Manihot esculenta', 'singkong', 'Singkong berkualitas tinggi untuk industri tapioka & pangan.', 'https://images.unsplash.com/photo-1596097635121-14b63b7a0c19?w=800', 'Tuber'),
('Jagung', 'Zea mays', 'jagung', 'Jagung hibrida dengan produktivitas tinggi.', 'https://images.unsplash.com/photo-1551754655-cd27e38d2076?w=800', 'Cereal'),
('Vanili', 'Vanilla planifolia', 'vanili', 'Vanili premium dengan kadar vanillin tinggi.', 'https://images.unsplash.com/photo-1601055903647-ddf1b7b6e7d5?w=800', 'Spice');

-- VARIETIES
INSERT INTO commodity_varieties (commodity_id, variety_name, characteristics) VALUES
(1, 'Robusta', 'Body kuat, aroma khas, kadar kafein tinggi'),
(1, 'Arabica', 'Aroma floral, acidity medium'),
(2, 'Cavendish', 'Manis, tekstur lembut'),
(3, 'UJ-5', 'Produktivitas tinggi, kadar pati tinggi'),
(4, 'Bisi-18', 'Hibrida, tahan hama'),
(5, 'Planifolia', 'Kadar vanillin 2-3%');

-- FARMERS (contoh 10, generate 100 via script)
INSERT INTO farmers (farmer_code, full_name, phone, village, district, regency, latitude, longitude, land_size_ha, commodity_id, variety_id, planting_date, estimated_harvest_date, estimated_production_ton, grade, land_status, location_consent) VALUES
('FRM-0001','Sukirman','081234567890','Sumber Jaya','Sumber Jaya','Lampung Barat',-5.0123,104.5678,2.5,1,1,'2025-01-15','2026-10-15',3.2,'A','Near Harvest',1),
('FRM-0002','Warsito','081234567891','Way Tenong','Way Tenong','Lampung Barat',-5.0234,104.5789,1.8,1,1,'2025-02-10','2026-11-10',2.4,'A','Growing',1),
('FRM-0003','Siti Aminah','081234567892','Kota Agung','Kota Agung','Tanggamus',-5.4567,104.6789,3.2,2,3,'2025-03-01','2026-09-01',5.5,'B','Harvest',1),
('FRM-0004','Bambang P','081234567893','Gedong Tataan','Gedong Tataan','Pesawaran',-5.3789,105.0123,1.2,3,4,'2025-04-12','2026-12-12',4.8,'A','Growing',1),
('FRM-0005','Rusdi','081234567894','Natar','Natar','Lampung Selatan',-5.3012,105.2345,5.5,4,5,'2025-05-20','2026-08-20',8.2,'A','Near Harvest',1),
('FRM-0006','Hendra','081234567895','Bumi Agung','Bumi Agung','Way Kanan',-4.5678,104.4567,2.0,1,2,'2025-01-25','2026-10-25',2.8,'A','Near Harvest',1),
('FRM-0007','Tono','081234567896','Sidomulyo','Sidomulyo','Lampung Selatan',-5.4321,105.3456,1.5,3,4,'2025-06-01','2027-01-01',5.0,'B','Growing',1),
('FRM-0008','Yusuf','081234567897','Pulau Panggung','Pulau Panggung','Tanggamus',-5.5678,104.7890,4.0,1,1,'2025-02-15','2026-11-15',5.2,'A','Growing',1),
('FRM-0009','Dedi','081234567898','Kasui','Kasui','Way Kanan',-4.6789,104.5678,1.0,5,6,'2025-07-01','2026-12-01',0.8,'A','Growing',1),
('FRM-0010','Agus','081234567899','Punduh Pidada','Punduh Pidada','Pesawaran',-5.6789,105.1234,3.5,2,3,'2025-03-15','2026-09-15',6.2,'A','Harvest',1);

-- WAREHOUSES
INSERT INTO warehouses (name, location, regency, capacity_ton, used_capacity_ton, latitude, longitude) VALUES
('Gudang Utama Lampung Barat','Sumber Jaya','Lampung Barat',5000,3200,-5.0123,104.5678),
('Gudang Bandar Lampung','Panjang','Bandar Lampung',8000,4500,-5.4567,105.3234),
('Gudang Tanggamus','Kota Agung','Tanggamus',3000,1800,-5.4567,104.6789);

-- VEHICLES
INSERT INTO vehicles (vehicle_code, vehicle_type, license_plate, driver_name, capacity_ton, current_location, status) VALUES
('VH-001','Truck','BE 1234 AB','Slamet',10,'Lampung Barat','Available'),
('VH-002','Container Truck','BE 5678 CD','Joko',20,'Bandar Lampung','Loading'),
('VH-003','Refrigerated Truck','BE 9012 EF','Rudi',8,'Tanggamus','In Transit'),
('VH-004','Pickup','BE 3456 GH','Anton',2,'Pesawaran','Available'),
('VH-005','Box Truck','BE 7890 IJ','Budi',5,'Way Kanan','Maintenance');

-- BUYERS
INSERT INTO buyers (company_name, country, contact_person, email, phone) VALUES
('Singapore Coffee Trading Pte','Singapore','Mr. Tan','tan@sct.sg','+65 1234 5678'),
('Malaysia Agri Import Sdn Bhd','Malaysia','Ms. Lim','lim@mai.my','+60 1234 5678'),
('China Commodity Co.','China','Mr. Wang','wang@cc.cn','+86 1234 5678'),
('Europe Spice GmbH','Germany','Mr. Schmidt','schmidt@es.de','+49 1234 5678');

-- EXPORT ORDERS
INSERT INTO export_orders (order_code, commodity_id, buyer_id, hs_code, quantity_ton, grade, origin, destination_country, destination_port, incoterms, status) VALUES
('EXP-2026-001',1,1,'0901.11',100,'A','Lampung Barat','Singapore','Port of Singapore','FOB','Ready for Export'),
('EXP-2026-002',5,4,'0905.10',5,'A','Way Kanan','Germany','Hamburg','CIF','Processing'),
('EXP-2026-003',2,2,'0803.90',50,'A','Tanggamus','Malaysia','Port Klang','FOB','Collection');

-- SHIPMENTS
INSERT INTO shipments (shipment_code, origin, destination, commodity_id, weight_ton, vehicle_id, cost_idr, status, eta, shipment_type) VALUES
('SHP-001','Lampung Barat','Pelabuhan Panjang',1,20,1,8500000,'In Transit','2026-09-20 14:00:00','National'),
('SHP-002','Bandar Lampung','Jakarta',1,15,2,12000000,'Loading','2026-09-22 08:00:00','National'),
('SHP-003','Tanggamus','Surabaya',2,10,3,15000000,'On Route','2026-09-25 12:00:00','National');