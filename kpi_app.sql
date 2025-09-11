-- phpMyAdmin SQL Dump
-- version 5.3.0-dev+20220709.4e08d2933b
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Waktu pembuatan: 11 Sep 2025 pada 04.12
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
(4, 'Marketing', '2025-09-07 20:47:58');

-- --------------------------------------------------------

--
-- Struktur dari tabel `kpi_assignments`
--

CREATE TABLE `kpi_assignments` (
  `id` int(11) NOT NULL,
  `template_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `assigned_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `kpi_assignments`
--

INSERT INTO `kpi_assignments` (`id`, `template_id`, `user_id`, `assigned_at`) VALUES
(6, 3, 6, '2025-09-11 01:25:44');

-- --------------------------------------------------------

--
-- Struktur dari tabel `kpi_items`
--

CREATE TABLE `kpi_items` (
  `id` int(11) NOT NULL,
  `template_id` int(11) NOT NULL,
  `indikator` varchar(255) NOT NULL,
  `bobot` decimal(5,2) NOT NULL,
  `target` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `kpi_items`
--

INSERT INTO `kpi_items` (`id`, `template_id`, `indikator`, `bobot`, `target`, `created_at`) VALUES
(8, 3, 'Omset', '50.00', '100.00', '2025-09-11 01:25:14'),
(9, 3, 'Penjualan', '50.00', '100.00', '2025-09-11 01:25:23');

-- --------------------------------------------------------

--
-- Struktur dari tabel `kpi_realisasi`
--

CREATE TABLE `kpi_realisasi` (
  `id` int(11) NOT NULL,
  `assignment_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `realisasi` decimal(10,2) DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `evidence` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struktur dari tabel `kpi_templates`
--

CREATE TABLE `kpi_templates` (
  `id` int(11) NOT NULL,
  `departemen_id` int(11) NOT NULL,
  `nama` varchar(150) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `periode` year(4) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `kpi_templates`
--

INSERT INTO `kpi_templates` (`id`, `departemen_id`, `nama`, `deskripsi`, `periode`, `created_at`) VALUES
(3, 3, 'KPI Keuangan', 'utk divisi keuangan', 2024, '2025-09-11 01:24:59');

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
(1, 'Manager', 'manager@example.com', '$2y$10$Wf.nfZzFEFMvjLrBcqCjJOdA9hMPTHCYR0Jn6f22OmupxO/rfWSam', 'manager', 3, '2025-09-09 06:41:19'),
(2, 'Admin', 'admin@example.com', '$2y$10$lStNmsNICEMuxbMxgd1iueXbIknRMwd.uaqNrcS.7ZOqmgWVJb.xO', 'admin', NULL, '2025-09-09 06:53:52'),
(6, 'ronaldo', 'ronaldo@gmail.com', '$2y$10$wXM9Gna01fOOqH97V8P7JOd4BNCzru0Mib6elFu5d.HwbDQjRiWRi', 'user', 3, '2025-09-10 04:55:34');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `kpi_assignments`
--
ALTER TABLE `kpi_assignments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `template_id` (`template_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeks untuk tabel `kpi_items`
--
ALTER TABLE `kpi_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `template_id` (`template_id`);

--
-- Indeks untuk tabel `kpi_realisasi`
--
ALTER TABLE `kpi_realisasi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `assignment_id` (`assignment_id`),
  ADD KEY `item_id` (`item_id`);

--
-- Indeks untuk tabel `kpi_templates`
--
ALTER TABLE `kpi_templates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `departemen_id` (`departemen_id`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `kpi_assignments`
--
ALTER TABLE `kpi_assignments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `kpi_items`
--
ALTER TABLE `kpi_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT untuk tabel `kpi_realisasi`
--
ALTER TABLE `kpi_realisasi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `kpi_templates`
--
ALTER TABLE `kpi_templates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `kpi_assignments`
--
ALTER TABLE `kpi_assignments`
  ADD CONSTRAINT `kpi_assignments_ibfk_1` FOREIGN KEY (`template_id`) REFERENCES `kpi_templates` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `kpi_assignments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `kpi_items`
--
ALTER TABLE `kpi_items`
  ADD CONSTRAINT `kpi_items_ibfk_1` FOREIGN KEY (`template_id`) REFERENCES `kpi_templates` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `kpi_realisasi`
--
ALTER TABLE `kpi_realisasi`
  ADD CONSTRAINT `kpi_realisasi_ibfk_1` FOREIGN KEY (`assignment_id`) REFERENCES `kpi_assignments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `kpi_realisasi_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `kpi_items` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `kpi_templates`
--
ALTER TABLE `kpi_templates`
  ADD CONSTRAINT `kpi_templates_ibfk_1` FOREIGN KEY (`departemen_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`departemen_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;



