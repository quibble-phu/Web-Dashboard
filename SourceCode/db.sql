CREATE DATABASE IF NOT EXISTS cmms_db;
USE cmms_db;

-- 🧍 User (Employee) Table
CREATE TABLE employee (
    user_id VARCHAR(10) PRIMARY KEY,
    username VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL,
    profile_image VARCHAR(255) NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    team VARCHAR(50) NOT NULL,
    position VARCHAR(50) NOT NULL,
    role VARCHAR(50) NOT NULL
);

-- 🔐 User session table
CREATE TABLE user_sessions (
    user_id VARCHAR(10),
    session_id VARCHAR(255),
    ip_address VARCHAR(45),
    user_agent TEXT,
    last_activity DATETIME,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (user_id, session_id),
    FOREIGN KEY (user_id) REFERENCES employee(user_id) ON DELETE CASCADE
);
CREATE INDEX idx_last_activity ON user_sessions(last_activity);

-- 🏭 Machine table
CREATE TABLE machine (
    machine_id VARCHAR(15) PRIMARY KEY,
    line VARCHAR(30) NOT NULL,
    machine_code VARCHAR(30) NOT NULL,
    machine_name VARCHAR(100),
    serial_number VARCHAR(100),
    status VARCHAR(30)
);

-- 🧩 Part table
CREATE TABLE part (
    part_id VARCHAR(20) PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    model VARCHAR(50) NOT NULL,
    brand VARCHAR(50) NOT NULL,
    cost INT,
    supplier VARCHAR(100) NOT NULL
);

-- 🛠️ Maintenance Job table
CREATE TABLE maintenance_job (
    maintenance_id VARCHAR(20) PRIMARY KEY,
    machine_id VARCHAR(15) NOT NULL,
    description VARCHAR(255) NOT NULL,
    man_hour INT NOT NULL,
    period VARCHAR(10) NOT NULL,
    inhouse_outsource VARCHAR(15) NOT NULL,
    maker VARCHAR(100),
    CONSTRAINT fk_maintenance_machine FOREIGN KEY (machine_id)
        REFERENCES machine(machine_id) ON DELETE CASCADE,
    CONSTRAINT chk_in_or_out CHECK (inhouse_outsource IN ('inhouse', 'outsource'))
);

-- 🔧 Many-to-many: Maintenance ↔ Parts
CREATE TABLE mn_part (
    maintenance_id VARCHAR(20) NOT NULL,
    part_id VARCHAR(20) NOT NULL,
    amount INT NOT NULL,
    PRIMARY KEY (maintenance_id, part_id),
    CONSTRAINT fk_mnpart_maintenance FOREIGN KEY (maintenance_id)
        REFERENCES maintenance_job(maintenance_id) ON DELETE CASCADE,
    CONSTRAINT fk_mnpart_part FOREIGN KEY (part_id)
        REFERENCES part(part_id) ON DELETE CASCADE
);

-- 👷 Work assignment table
CREATE TABLE work (
    work_id VARCHAR(20) PRIMARY KEY,
    maintenance_id VARCHAR(20) NOT NULL,
    user_id VARCHAR(10) NOT NULL,
    assign_date DATE,
    start_date DATE,
    end_date DATE,
    CONSTRAINT fk_work_maintenance FOREIGN KEY (maintenance_id)
        REFERENCES maintenance_job(maintenance_id) ON DELETE CASCADE,
    CONSTRAINT fk_work_user FOREIGN KEY (user_id)
        REFERENCES employee(user_id) ON DELETE CASCADE
);

-- 📝 History (งานซ่อมที่เสร็จแล้ว)
CREATE TABLE history (
    history_id VARCHAR(20) PRIMARY KEY,
    work_id VARCHAR(20) NOT NULL,
    date_complete DATE NOT NULL,
    abnormal_record VARCHAR(255),
    abnormal_corrective VARCHAR(255),
    eva VARCHAR(50),
    CONSTRAINT fk_history_work FOREIGN KEY (work_id)
        REFERENCES work(work_id) ON DELETE CASCADE
);

--  token reset pass
CREATE TABLE password_resets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL,
    token VARCHAR(255) NOT NULL,
    expires_at DATETIME NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);
