-- SQL Script to set up the new database for Poltekkes Bengkulu International Admissions
-- Create the database if it does not exist
CREATE DATABASE IF NOT EXISTS `db_poltekkes_oia` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE `db_poltekkes_oia`;

-- Create the tb_interstudent table
CREATE TABLE IF NOT EXISTS `tb_interstudent` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
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
    `english_proficiency` VARCHAR(100) DEFAULT NULL,
    `sop` TEXT NOT NULL,
    `referral` VARCHAR(150) DEFAULT NULL,
    `passport_file` VARCHAR(255) DEFAULT NULL,
    `english_cert_file` VARCHAR(255) DEFAULT NULL,
    `diploma_file` VARCHAR(255) DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
