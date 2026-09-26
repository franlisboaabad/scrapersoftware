-- Scraper de Empresas Turísticas
-- Ejecutar en phpMyAdmin o en la consola MySQL de Laragon.
-- Ejemplo: mysql -u root < docs/database.sql

CREATE DATABASE IF NOT EXISTS scrapersoftware
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE scrapersoftware;

CREATE TABLE IF NOT EXISTS busquedas (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    keyword VARCHAR(150) NOT NULL,
    location VARCHAR(150) NOT NULL,
    limite INT UNSIGNED NOT NULL,
    total_encontradas INT UNSIGNED NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS empresas (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    place_id VARCHAR(255) NOT NULL,
    nombre VARCHAR(255) NOT NULL,
    website VARCHAR(500) NULL,
    telefono VARCHAR(50) NULL,
    direccion VARCHAR(500) NULL,
    rating DECIMAL(2,1) NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uk_empresas_place_id (place_id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS busqueda_empresa (
    busqueda_id INT UNSIGNED NOT NULL,
    empresa_id INT UNSIGNED NOT NULL,
    PRIMARY KEY (busqueda_id, empresa_id),
    CONSTRAINT fk_busqueda_empresa_busqueda
        FOREIGN KEY (busqueda_id) REFERENCES busquedas (id)
        ON DELETE CASCADE,
    CONSTRAINT fk_busqueda_empresa_empresa
        FOREIGN KEY (empresa_id) REFERENCES empresas (id)
        ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS empresa_emails (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    empresa_id INT UNSIGNED NOT NULL,
    email VARCHAR(255) NOT NULL,
    es_filtrado TINYINT(1) NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uk_empresa_email (empresa_id, email),
    CONSTRAINT fk_empresa_emails_empresa
        FOREIGN KEY (empresa_id) REFERENCES empresas (id)
        ON DELETE CASCADE
) ENGINE=InnoDB;
