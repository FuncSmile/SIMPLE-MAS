-- SIMPEL-MAS Database Schema
-- MySQL 8.0+ with Spatial Support

CREATE DATABASE IF NOT EXISTS simpel_mas CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE simpel_mas;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    phone VARCHAR(20) DEFAULT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('warga', 'admin_instansi', 'super_admin') NOT NULL DEFAULT 'warga',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Categories table
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT DEFAULT NULL,
    agency VARCHAR(255) DEFAULT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Complaints table with spatial POINT column
CREATE TABLE IF NOT EXISTS complaints (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    category_id INT NOT NULL,
    description TEXT NOT NULL,
    photo_before VARCHAR(255) DEFAULT NULL,
    photo_after VARCHAR(255) DEFAULT NULL,
    lat DOUBLE NOT NULL,
    lng DOUBLE NOT NULL,
    location POINT NOT NULL SRID 4326,
    status ENUM('pending', 'in_progress', 'resolved', 'rejected') NOT NULL DEFAULT 'pending',
    upvotes INT NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE,
    SPATIAL INDEX idx_location (location)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Upvotes tracking table
CREATE TABLE IF NOT EXISTS upvotes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    complaint_id INT NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (complaint_id) REFERENCES complaints(id) ON DELETE CASCADE,
    UNIQUE KEY unique_upvote (user_id, complaint_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default categories
INSERT INTO categories (name, description, agency) VALUES
('Infrastruktur', 'Kerusakan jalan, jembatan, trotoar, dan fasilitas umum lainnya', 'Dinas Pekerjaan Umum'),
('Sampah', 'Penumpukan sampah, TPS liar, dan masalah kebersihan lingkungan', 'Dinas Lingkungan Hidup'),
('Penerangan Jalan', 'Lampu jalan mati, kerusakan PJU, dan area gelap', 'Dinas Perhubungan'),
('Banjir', 'Genangan air, saluran tersumbat, dan luapan sungai', 'Dinas Pengairan'),
('Keamanan', 'Gangguan ketertiban, vandalisme, dan potensi kriminalitas', 'Satpol PP'),
('Lainnya', 'Pengaduan lain di luar kategori yang tersedia', 'Administrasi Umum');

-- Insert default super admin (password: admin123)
INSERT INTO users (name, email, phone, password, role) VALUES
('Super Admin', 'superadmin@simpelmas.id', '081234567890', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'super_admin');

-- Insert sample admin instansi
INSERT INTO users (name, email, phone, password, role) VALUES
('Admin Dinas PU', 'admin_pu@simpelmas.id', '081234567891', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin_instansi'),
('Admin Lingkungan', 'admin_lingkungan@simpelmas.id', '081234567892', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin_instansi');
