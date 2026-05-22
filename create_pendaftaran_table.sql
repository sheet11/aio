-- Skrip: hanya menambah tabel `tb_interstudent` ke database yang sudah ada
-- Pastikan database target sudah ada dan aktif

-- USE `db_poltekkes_oia`; -- Uncomment this line for local XAMPP setup if needed

CREATE TABLE IF NOT EXISTS `tb_interstudent` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(50) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `first_name` VARCHAR(50) NOT NULL,
    `last_name` VARCHAR(50) NOT NULL,
    `dob` DATE NOT NULL,
    `gender` VARCHAR(20) NOT NULL,
    `nationality` VARCHAR(100) NOT NULL,
    `passport` VARCHAR(50) NOT NULL,
    `email` VARCHAR(100) NOT NULL,
    `phone` VARCHAR(30) NOT NULL,
    `current_location` VARCHAR(150) DEFAULT NULL,
    `education_level` VARCHAR(100) NOT NULL,
    `gpa` VARCHAR(20) DEFAULT NULL,
    `previous_school` VARCHAR(150) NOT NULL,
    `program1` VARCHAR(100) NOT NULL,
    `program2` VARCHAR(100) DEFAULT NULL,
    `scholarship` VARCHAR(100) DEFAULT NULL,
    `english_proficiency` VARCHAR(100) DEFAULT NULL,
    `sop` TEXT NOT NULL,
    `referral` VARCHAR(150) DEFAULT NULL,
    `passport_file` VARCHAR(255) DEFAULT NULL,
    `english_cert_file` VARCHAR(255) DEFAULT NULL,
    `diploma_file` VARCHAR(255) DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
