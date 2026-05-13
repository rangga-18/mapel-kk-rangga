-- ============================================
-- DATABASE: Aplikasi Peminjaman Buku
-- UKK RPL 2025/2026 - Paket 4
-- ============================================

CREATE DATABASE IF NOT EXISTS db_perpustakaan;
USE db_perpustakaan;

-- Tabel user (admin & siswa)
CREATE TABLE IF NOT EXISTS user (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    level ENUM('admin','user') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel anggota (siswa)
CREATE TABLE IF NOT EXISTS anggota (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nis VARCHAR(20) NOT NULL UNIQUE,
    nama VARCHAR(100) NOT NULL,
    kelas VARCHAR(20),
    alamat TEXT,
    telepon VARCHAR(15),
    user_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES user(id) ON DELETE SET NULL
);

-- Tabel buku
CREATE TABLE IF NOT EXISTS buku (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kode_buku VARCHAR(20) NOT NULL UNIQUE,
    judul VARCHAR(200) NOT NULL,
    pengarang VARCHAR(100),
    penerbit VARCHAR(100),
    tahun_terbit YEAR,
    kategori VARCHAR(50),
    stok INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel peminjaman
CREATE TABLE IF NOT EXISTS peminjaman (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kode_pinjam VARCHAR(20) NOT NULL UNIQUE,
    anggota_id INT NOT NULL,
    buku_id INT NOT NULL,
    tgl_pinjam DATE NOT NULL,
    tgl_kembali DATE NOT NULL,
    tgl_pengembalian DATE NULL,
    status ENUM('dipinjam','dikembalikan','terlambat') DEFAULT 'dipinjam',
    denda INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (anggota_id) REFERENCES anggota(id),
    FOREIGN KEY (buku_id) REFERENCES buku(id)
);

-- ============================================
-- DATA AWAL
-- ============================================

-- User: admin (password: admin123) & siswa (password: siswa123)
INSERT INTO user (nama, username, password, level) VALUES
('Administrator', 'admin', 'admin123', 'admin'),
('Budi Santoso', 'budi', 'siswa123', 'user'),
('Siti Rahayu', 'siti', 'siswa123', 'user');

-- Anggota
INSERT INTO anggota (nis, nama, kelas, alamat, telepon, user_id) VALUES
('2024001', 'Budi Santoso', 'XII RPL 1', 'Jl. Merdeka No.1', '081234567890', 2),
('2024002', 'Siti Rahayu', 'XII RPL 2', 'Jl. Pahlawan No.5', '082345678901', 3),
('2024003', 'Ahmad Fauzi', 'XI RPL 1', 'Jl. Sudirman No.10', '083456789012', NULL),
('2024004', 'Dewi Lestari', 'XI RPL 2', 'Jl. Gatot Subroto No.3', '084567890123', NULL);

-- Buku
INSERT INTO buku (kode_buku, judul, pengarang, penerbit, tahun_terbit, kategori, stok) VALUES
('BK001', 'Pemrograman Web dengan PHP', 'Ahmad Syukri', 'Informatika', 2022, 'Teknologi', 5),
('BK002', 'Basis Data MySQL', 'Budi Raharjo', 'Informatika', 2021, 'Teknologi', 3),
('BK003', 'Algoritma dan Pemrograman', 'Rinaldi Munir', 'Erlangga', 2020, 'Teknologi', 4),
('BK004', 'Matematika Diskrit', 'Feri Sulianta', 'Andi Offset', 2019, 'Sains', 6),
('BK005', 'Jaringan Komputer', 'Iwan Sofana', 'Informatika', 2023, 'Teknologi', 2),
('BK006', 'Bahasa Indonesia Kelas XII', 'Kemendikbud', 'Kemendikbud', 2022, 'Bahasa', 8);

-- Peminjaman contoh
INSERT INTO peminjaman (kode_pinjam, anggota_id, buku_id, tgl_pinjam, tgl_kembali, status) VALUES
('PJM-001', 1, 1, '2026-05-01', '2026-05-08', 'dipinjam'),
('PJM-002', 2, 3, '2026-04-25', '2026-05-02', 'terlambat');
