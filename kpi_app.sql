-- phpMyAdmin SQL Dump
-- version 5.3.0-dev+20220709.4e08d2933b
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Waktu pembuatan: 02 Okt 2025 pada 03.41
-- Versi server: 10.4.24-MariaDB
-- Versi PHP: 8.0.18

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `kpi_app`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `departments`
--

CREATE TABLE `departments` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `departments`
--

INSERT INTO `departments` (`id`, `name`, `created_at`) VALUES
(3, 'Keuangan', '2025-09-07 20:47:19'),
(7, 'Marketing', '2025-09-20 01:42:39');

-- --------------------------------------------------------

--
-- Struktur dari tabel `kpi_item`
--

CREATE TABLE `kpi_item` (
  `id` int(11) NOT NULL,
  `template_id` int(11) NOT NULL,
  `indikator` varchar(255) NOT NULL,
  `target` decimal(10,2) NOT NULL,
  `satuan` varchar(50) DEFAULT NULL,
  `bobot` decimal(5,2) NOT NULL,
  `tipe` enum('normal','inverse') DEFAULT 'normal'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `kpi_item`
--

INSERT INTO `kpi_item` (`id`, `template_id`, `indikator`, `target`, `satuan`, `bobot`, `tipe`) VALUES
(4, 2, 'Penjualan', '1000000.00', 'Rp', '50.00', 'normal'),
(6, 2, 'Pengeluaran', '1500000.00', 'Rp', '30.00', 'inverse'),
(7, 4, 'Omset', '1000000.00', 'Rp', '50.00', 'normal'),
(8, 5, 'Pengeluaran', '2000000.00', 'Rp', '50.00', 'inverse');

-- --------------------------------------------------------

--
-- Struktur dari tabel `kpi_realisasi`
--

CREATE TABLE `kpi_realisasi` (
  `id` int(11) NOT NULL,
  `assignment_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `template_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `realisasi` decimal(10,2) DEFAULT 0.00,
  `evidence` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `kpi_realisasi`
--

INSERT INTO `kpi_realisasi` (`id`, `assignment_id`, `user_id`, `template_id`, `item_id`, `realisasi`, `evidence`, `created_at`, `updated_at`) VALUES
(3, 3, 8, 2, 4, '800000.00', '../user/upload/evidence_4_68d78c717671f.jpg', '2025-09-27 07:03:15', '2025-09-27 07:04:17'),
(4, 3, 8, 2, 6, '1200000.00', NULL, '2025-09-27 07:03:15', '2025-09-27 07:03:15'),
(5, 6, 11, 5, 8, '1000000.00', '../user/upload/evidence_8_68d78d78e2e8a.jpg', '2025-09-27 07:08:09', '2025-09-27 07:08:40');

-- --------------------------------------------------------

--
-- Struktur dari tabel `kpi_template`
--

CREATE TABLE `kpi_template` (
  `id` int(11) NOT NULL,
  `departemen_id` int(11) NOT NULL,
  `nama_template` varchar(255) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `dibuat_oleh` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `kpi_template`
--

INSERT INTO `kpi_template` (`id`, `departemen_id`, `nama_template`, `deskripsi`, `dibuat_oleh`, `created_at`) VALUES
(2, 3, 'KPI Keuangan', 'Template KPI utk divisi keuangan', 2, '2025-09-24 06:47:23'),
(4, 7, 'KPI Marketing', 'Template KPI utk divisi marketing', 12, '2025-09-26 02:14:28'),
(5, 7, 'KPI Marketing 2', 'KPI marketing 2', 12, '2025-09-26 07:14:26');

-- --------------------------------------------------------

--
-- Struktur dari tabel `kpi_user`
--

CREATE TABLE `kpi_user` (
  `id` int(11) NOT NULL,
  `template_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `tahun` year(4) NOT NULL,
  `periode` enum('bulanan','triwulan','tahunan') DEFAULT 'bulanan',
  `bulan` tinyint(4) DEFAULT NULL,
  `assigned_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `kpi_user`
--

INSERT INTO `kpi_user` (`id`, `template_id`, `user_id`, `tahun`, `periode`, `bulan`, `assigned_at`) VALUES
(3, 2, 8, 2025, 'bulanan', 9, '2025-09-25 08:07:15'),
(4, 2, 9, 2025, 'bulanan', 9, '2025-09-25 08:07:15'),
(5, 4, 11, 2025, 'bulanan', 9, '2025-09-26 03:25:03'),
(6, 5, 11, 2025, 'bulanan', 9, '2025-09-26 07:16:12');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','manager','user') NOT NULL DEFAULT 'user',
  `departemen_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `nama`, `email`, `password`, `role`, `departemen_id`, `created_at`) VALUES
(1, 'Admin', 'admin@example.com', '$2y$10$mIBKX3i1P8psqi.b1Dv0W.BRR.ZcPNygqio6bqzkLugj9ZTnSmuy6', 'admin', NULL, '2025-09-19 08:03:15'),
(2, 'manager', 'manager@example.com', '$2y$10$DDp.CK3wHL3OnPoR0iwE4u1VDENi33L8AsVxHqwH9fUCAIh1z2LMu', 'manager', 3, '2025-09-19 08:08:14'),
(8, 'messi', 'messi@gmail.com', '$2y$10$aD58w0ij9a6pOHICOtPnD.dms9VnI4FyIrUf4in5/R4DPpZL4BdRe', 'user', 3, '2025-09-20 01:54:40'),
(9, 'neymar', 'neymar@gmail.com', '$2y$10$psyXsZz0JUqRgiVD4oZNZ.dUa1Sy6sLJ.wMTrfjkgF7DnOtV1IAZ.', 'user', 3, '2025-09-20 01:56:40'),
(11, 'ronaldo', 'ronaldo@gmail.com', '$2y$10$7khZXiMWtxgDxQAMYZUTtOLjFpxwsiwQGCWHPRa8s44Piv.w/Wwq.', 'user', 7, '2025-09-26 01:17:05'),
(12, 'manager 2', 'manager2@example.com', '$2y$10$ZIRcWpy9X//5owCRHTlJleXzmWeBKE9v9xZ9pgWGnAVwc0LaKOiLe', 'manager', 7, '2025-09-26 01:17:50');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `kpi_item`
--
ALTER TABLE `kpi_item`
  ADD PRIMARY KEY (`id`),
  ADD KEY `template_id` (`template_id`);

--
-- Indeks untuk tabel `kpi_realisasi`
--
ALTER TABLE `kpi_realisasi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `item_id` (`item_id`),
  ADD KEY `fk_realisasi_template` (`template_id`);

--
-- Indeks untuk tabel `kpi_template`
--
ALTER TABLE `kpi_template`
  ADD PRIMARY KEY (`id`),
  ADD KEY `departemen_id` (`departemen_id`),
  ADD KEY `dibuat_oleh` (`dibuat_oleh`);

--
-- Indeks untuk tabel `kpi_user`
--
ALTER TABLE `kpi_user`
  ADD PRIMARY KEY (`id`),
  ADD KEY `template_id` (`template_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `departemen_id` (`departemen_id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `departments`
--
ALTER TABLE `departments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT untuk tabel `kpi_item`
--
ALTER TABLE `kpi_item`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT untuk tabel `kpi_realisasi`
--
ALTER TABLE `kpi_realisasi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `kpi_template`
--
ALTER TABLE `kpi_template`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `kpi_user`
--
ALTER TABLE `kpi_user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `kpi_item`
--
ALTER TABLE `kpi_item`
  ADD CONSTRAINT `kpi_item_ibfk_1` FOREIGN KEY (`template_id`) REFERENCES `kpi_template` (`id`);

--
-- Ketidakleluasaan untuk tabel `kpi_realisasi`
--
ALTER TABLE `kpi_realisasi`
  ADD CONSTRAINT `fk_realisasi_template` FOREIGN KEY (`template_id`) REFERENCES `kpi_template` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `kpi_realisasi_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `kpi_realisasi_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `kpi_item` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `kpi_template`
--
ALTER TABLE `kpi_template`
  ADD CONSTRAINT `kpi_template_ibfk_1` FOREIGN KEY (`departemen_id`) REFERENCES `departments` (`id`),
  ADD CONSTRAINT `kpi_template_ibfk_2` FOREIGN KEY (`dibuat_oleh`) REFERENCES `users` (`id`);

--
-- Ketidakleluasaan untuk tabel `kpi_user`
--
ALTER TABLE `kpi_user`
  ADD CONSTRAINT `kpi_user_ibfk_1` FOREIGN KEY (`template_id`) REFERENCES `kpi_template` (`id`),
  ADD CONSTRAINT `kpi_user_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Ketidakleluasaan untuk tabel `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`departemen_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;



