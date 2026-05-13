-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 13 Bulan Mei 2026 pada 09.45
-- Versi server: 10.4.24-MariaDB
-- Versi PHP: 7.4.29

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_perpustakaan`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `anggota`
--

CREATE TABLE `anggota` (
  `id` int(11) NOT NULL,
  `nis` varchar(20) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `kelas` varchar(20) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `telepon` varchar(15) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `anggota`
--

INSERT INTO `anggota` (`id`, `nis`, `nama`, `kelas`, `alamat`, `telepon`, `user_id`, `created_at`) VALUES
(1, '2024001', 'Budi Santoso', 'XII RPL 1', 'Jl. Merdeka No.1', '081234567890', 2, '2026-05-11 05:38:47'),
(2, '2024002', 'Siti Rahayu', 'XII RPL 2', 'Jl. Pahlawan No.5', '082345678901', 3, '2026-05-11 05:38:47'),
(3, '2024003', 'Ahmad Fauzi', 'XI RPL 1', 'Jl. Sudirman No.10', '083456789012', NULL, '2026-05-11 05:38:47'),
(4, '2024004', 'Dewi Lestari', 'XI RPL 2', 'Jl. Gatot Subroto No.3', '084567890123', NULL, '2026-05-11 05:38:47');

-- --------------------------------------------------------

--
-- Struktur dari tabel `buku`
--

CREATE TABLE `buku` (
  `id` int(11) NOT NULL,
  `kode_buku` varchar(20) NOT NULL,
  `judul` varchar(200) NOT NULL,
  `pengarang` varchar(100) DEFAULT NULL,
  `penerbit` varchar(100) DEFAULT NULL,
  `tahun_terbit` year(4) DEFAULT NULL,
  `kategori` varchar(50) DEFAULT NULL,
  `stok` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `buku`
--

INSERT INTO `buku` (`id`, `kode_buku`, `judul`, `pengarang`, `penerbit`, `tahun_terbit`, `kategori`, `stok`, `created_at`) VALUES
(1, 'BK001', 'Pemrograman Web dengan PHP', 'Ahmad Syukri', 'Informatika', 2022, 'Teknologi', 6, '2026-05-11 05:38:47'),
(2, 'BK002', 'Basis Data MySQL', 'Budi Raharjo', 'Informatika', 2021, 'Teknologi', 3, '2026-05-11 05:38:47'),
(3, 'BK003', 'Algoritma dan Pemrograman', 'Rinaldi Munir', 'Erlangga', 2020, 'Teknologi', 4, '2026-05-11 05:38:47'),
(4, 'BK004', 'Matematika Diskrit', 'Feri Sulianta', 'Andi Offset', 2019, 'Sains', 6, '2026-05-11 05:38:47'),
(5, 'BK005', 'Jaringan Komputer', 'Iwan Sofana', 'Informatika', 2023, 'Teknologi', 2, '2026-05-11 05:38:47'),
(6, 'BK006', 'Bahasa Indonesia Kelas XII', 'Kemendikbud', 'Kemendikbud', 2022, 'Bahasa', 8, '2026-05-11 05:38:47');

-- --------------------------------------------------------

--
-- Struktur dari tabel `peminjaman`
--

CREATE TABLE `peminjaman` (
  `id` int(11) NOT NULL,
  `kode_pinjam` varchar(20) NOT NULL,
  `anggota_id` int(11) NOT NULL,
  `buku_id` int(11) NOT NULL,
  `tgl_pinjam` date NOT NULL,
  `tgl_kembali` date NOT NULL,
  `tgl_pengembalian` date DEFAULT NULL,
  `status` enum('dipinjam','dikembalikan','terlambat') DEFAULT 'dipinjam',
  `denda` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `peminjaman`
--

INSERT INTO `peminjaman` (`id`, `kode_pinjam`, `anggota_id`, `buku_id`, `tgl_pinjam`, `tgl_kembali`, `tgl_pengembalian`, `status`, `denda`, `created_at`) VALUES
(1, 'PJM-001', 1, 1, '2026-05-01', '2026-05-08', '2026-05-12', 'dikembalikan', 4000, '2026-05-11 05:38:47'),
(2, 'PJM-002', 2, 3, '2026-04-25', '2026-05-02', '2026-05-12', 'dikembalikan', 10000, '2026-05-11 05:38:47'),
(3, 'PJM-9283', 3, 3, '2026-05-11', '2026-05-11', '2026-05-11', 'dikembalikan', 0, '2026-05-11 05:44:34'),
(4, 'PJM-9209', 1, 6, '2026-05-12', '2026-05-19', '2026-05-12', 'dikembalikan', 0, '2026-05-12 00:38:53'),
(5, 'PJM-4517', 1, 3, '2026-05-12', '2026-05-19', NULL, 'dipinjam', 0, '2026-05-12 00:47:23');

-- --------------------------------------------------------

--
-- Struktur dari tabel `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `level` enum('admin','user') DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `user`
--

INSERT INTO `user` (`id`, `nama`, `username`, `password`, `level`, `created_at`) VALUES
(1, 'Rian Dika Rangga Raditia', 'admin', 'admin123', 'admin', '2026-05-11 05:38:47'),
(2, 'Budi Santoso', 'user', 'user123', 'user', '2026-05-11 05:38:47'),
(3, 'Siti Rahayu', 'siti', 'user123', 'user', '2026-05-11 05:38:47');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `anggota`
--
ALTER TABLE `anggota`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nis` (`nis`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeks untuk tabel `buku`
--
ALTER TABLE `buku`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kode_buku` (`kode_buku`);

--
-- Indeks untuk tabel `peminjaman`
--
ALTER TABLE `peminjaman`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kode_pinjam` (`kode_pinjam`),
  ADD KEY `anggota_id` (`anggota_id`),
  ADD KEY `buku_id` (`buku_id`);

--
-- Indeks untuk tabel `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `anggota`
--
ALTER TABLE `anggota`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `buku`
--
ALTER TABLE `buku`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `peminjaman`
--
ALTER TABLE `peminjaman`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `anggota`
--
ALTER TABLE `anggota`
  ADD CONSTRAINT `anggota_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `peminjaman`
--
ALTER TABLE `peminjaman`
  ADD CONSTRAINT `peminjaman_ibfk_1` FOREIGN KEY (`anggota_id`) REFERENCES `anggota` (`id`),
  ADD CONSTRAINT `peminjaman_ibfk_2` FOREIGN KEY (`buku_id`) REFERENCES `buku` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
