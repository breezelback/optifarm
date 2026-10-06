-- OptiFarm database. Import via phpMyAdmin. Demo password for all accounts: password123
CREATE DATABASE IF NOT EXISTS optifarm CHARACTER SET utf8mb4; USE optifarm;
DROP TABLE IF EXISTS activity_log, field_reports, reports, stock_movements, production, tasks, inventory, fields, users;
CREATE TABLE users(id INT AUTO_INCREMENT PRIMARY KEY,name VARCHAR(100),email VARCHAR(120) UNIQUE,password VARCHAR(255),role ENUM('admin','bod','staff'),status ENUM('Active','Inactive') DEFAULT 'Active',created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP);
CREATE TABLE fields(id INT AUTO_INCREMENT PRIMARY KEY,name VARCHAR(80),crop VARCHAR(60),status ENUM('Active','Maintenance','Inactive') DEFAULT 'Active');
CREATE TABLE inventory(id INT AUTO_INCREMENT PRIMARY KEY,fertilizer VARCHAR(80),type VARCHAR(40),qty DECIMAL(10,2),unit VARCHAR(10) DEFAULT 'kg',min_qty DECIMAL(10,2) DEFAULT 50,expiry DATE,batch VARCHAR(40),cost DECIMAL(10,2) DEFAULT 0,supplier VARCHAR(100));
CREATE TABLE tasks(id INT AUTO_INCREMENT PRIMARY KEY,field_id INT,fertilizer VARCHAR(80),app_date DATE,app_time TIME,assigned_to INT,note TEXT,app_type VARCHAR(40),qty DECIMAL(10,2),repeat_every VARCHAR(20) DEFAULT 'None',priority ENUM('Low','Medium','High') DEFAULT 'Medium',status ENUM('Pending','In Progress','Completed') DEFAULT 'Pending');
CREATE TABLE production(id INT AUTO_INCREMENT PRIMARY KEY,field_id INT,month_date DATE,tons DECIMAL(8,2));
CREATE TABLE stock_movements(id INT AUTO_INCREMENT PRIMARY KEY,month_date DATE UNIQUE,stock_in DECIMAL(10,2) DEFAULT 0,stock_out DECIMAL(10,2) DEFAULT 0);
CREATE TABLE reports(id INT AUTO_INCREMENT PRIMARY KEY,name VARCHAR(120),type VARCHAR(40),generated_by INT,status VARCHAR(20) DEFAULT 'Completed',created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP);
CREATE TABLE field_reports(id INT AUTO_INCREMENT PRIMARY KEY,user_id INT,field_id INT,note TEXT,created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP);
CREATE TABLE activity_log(id INT AUTO_INCREMENT PRIMARY KEY,user_id INT NULL,action VARCHAR(255),created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP);
INSERT INTO users(name,email,password,role) VALUES
('System Administrator','admin@augfarm.com','$2y$10$Uds1SXGFMXpuiCOUIZyGPOBPMG1OZ6kB.GFyaSeGrRjoLBGVJaTWi','admin'),
('Board Member','bod@augfarm.com','$2y$10$Uds1SXGFMXpuiCOUIZyGPOBPMG1OZ6kB.GFyaSeGrRjoLBGVJaTWi','bod'),
('Juan Dela Cruz','staff@augfarm.com','$2y$10$Uds1SXGFMXpuiCOUIZyGPOBPMG1OZ6kB.GFyaSeGrRjoLBGVJaTWi','staff'),
('Maria Santos','maria@augfarm.com','$2y$10$Uds1SXGFMXpuiCOUIZyGPOBPMG1OZ6kB.GFyaSeGrRjoLBGVJaTWi','staff');
INSERT INTO fields(name,crop,status) VALUES('Field A - Lipa','Rambutan','Active'),('Field B - Lipa','Corn','Active'),('Field C - Lipa','Dragon Fruit','Active'),('Field D - Lipa','Tomatoes','Maintenance'),('Field E - Lipa','Rice','Inactive');
INSERT INTO inventory(fertilizer,type,qty,unit,min_qty,expiry,batch,cost,supplier) VALUES
('NPK 14-14-14','Chemical',420,'kg',100,'2027-06-30','B-1001',38,'AgriSupply Co.'),
('Urea 46-0-0','Chemical',65,'kg',100,'2027-03-15','B-1002',32,'AgriSupply Co.'),
('Phosphate','Chemical',300,'kg',80,'2027-01-20','B-1003',45,'FarmChem Inc.'),
('Organic Compost','Organic',520,'kg',150,'2026-12-31','B-2001',12,'AUG Production'),
('Foliar Fertilizer','Foliar',40,'L',50,'2027-02-10','B-3001',210,'AUG Production'),
('Vermicast','Organic',180,'kg',100,'2026-08-01','B-2002',15,'Local Supplier');
INSERT INTO tasks(field_id,fertilizer,app_date,app_time,assigned_to,note,app_type,qty,repeat_every,priority,status) VALUES
(1,'NPK 14-14-14','2026-10-07','07:00',3,'Apply around tree base','Broadcast',40,'Monthly','High','Pending'),
(2,'Urea 46-0-0','2026-10-08','06:30',3,'Side dressing','Side-dress',25,'None','Medium','Pending'),
(3,'Organic Compost','2026-10-05','08:00',4,'','Basal',100,'None','Low','Completed'),
(1,'Foliar Fertilizer','2026-10-06','09:00',4,'Spray in the morning','Foliar spray',10,'Weekly','Medium','In Progress'),
(2,'Phosphate','2026-10-12','07:30',3,'','Broadcast',30,'None','Medium','Pending');
INSERT INTO activity_log(user_id,action) VALUES(1,'System initialized'),(1,'Inventory batch B-1001 added');
INSERT INTO production(field_id,month_date,tons) VALUES(1,'2026-04-01',18),(1,'2026-05-01',19),(1,'2026-06-01',21),(1,'2026-07-01',20),(1,'2026-08-01',23),(1,'2026-09-01',24),(2,'2026-04-01',30),(2,'2026-05-01',28),(2,'2026-06-01',33),(2,'2026-07-01',35),(2,'2026-08-01',34),(2,'2026-09-01',38),(3,'2026-04-01',10),(3,'2026-05-01',11),(3,'2026-06-01',12),(3,'2026-07-01',12),(3,'2026-08-01',14),(3,'2026-09-01',15);
INSERT INTO stock_movements(month_date,stock_in,stock_out) VALUES('2026-04-01',300,210),('2026-05-01',260,240),('2026-06-01',340,260),('2026-07-01',310,250),('2026-08-01',380,300),('2026-09-01',350,330);
