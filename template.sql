CREATE DATABASE IF NOT EXISTS bimaundangan;
USE bimaundangan;

-- 1. Tabel untuk daftar template mentah (Master)
CREATE TABLE IF NOT EXISTS templates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_template VARCHAR(100),
    isi_html LONGTEXT,
    thumbnail VARCHAR(255)
);

-- 2. Tabel untuk menyimpan hasil editan user
CREATE TABLE IF NOT EXISTS user_projects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    template_id INT,
    html_editaan LONGTEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- 3. Masukkan contoh data template agar tidak kosong
INSERT INTO templates (nama_template, isi_html, thumbnail) VALUES 
('Rustic Wedding', '<div class="hero-section" style="background:pink; height:100vh; text-align:center;"><h1>Undangan Kami</h1><button onclick="openInvitation()">Buka Undangan</button></div><div class="isi-undangan"><h2>Acara Resepsi</h2><p>Lokasi di Gedung Serbaguna</p></div>', 'rustic.jpg');
