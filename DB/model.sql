-- Database creation using utf8mb4_unicode_520_ci collation as default
CREATE USER IF NOT EXISTS 'bioarineo'@'localhost' IDENTIFIED BY '&Bioarineo1';
CREATE DATABASE IF NOT EXISTS bioarineo COLLATE utf8mb4_unicode_520_ci;
GRANT ALL PRIVILEGES ON bioarineo.* TO 'bioarineo'@'localhost' IDENTIFIED BY '&Bioarineo1';

-- Tables creation using default DB collation and InnoDB engine
CREATE TABLE devices (id SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,  type VARCHAR (63), name VARCHAR(63), address VARCHAR(127), PRIMARY KEY (id)) ENGINE=InnoDB;
