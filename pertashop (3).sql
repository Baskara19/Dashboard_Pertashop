-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 09, 2025 at 03:42 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `pertashop`
--

-- --------------------------------------------------------

--
-- Table structure for table `penjualan_harian`
--

CREATE TABLE `penjualan_harian` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) DEFAULT NULL,
  `shift` enum('Pagi','Sore') DEFAULT NULL,
  `tanggal` date NOT NULL,
  `odo_awal` int(11) DEFAULT NULL,
  `odo_akhir` int(11) DEFAULT NULL,
  `penjualan_liter` int(11) DEFAULT NULL,
  `penghasilan_rp` decimal(15,2) NOT NULL,
  `ukur_awal` decimal(10,2) NOT NULL,
  `ukur_akhir` decimal(10,2) NOT NULL,
  `hasil_pengukuran` double DEFAULT NULL,
  `stok_hari_ini` decimal(10,2) NOT NULL,
  `harga_pertamax` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `role` enum('admin','operator') NOT NULL DEFAULT 'operator',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `nama_lengkap`, `role`, `created_at`) VALUES
(1, 'Baskara', '$2y$10$kk4RNCaOzWA02/r1OP1QqeHbepvQGCl92EU41s7tYmcpoS1jTsbZG', 'Baskara', 'admin', '2025-08-17 05:53:22'),
(4, 'operator1', '$2y$10$6DwANM7.i.xxV.ooDM0tt.WkPi8Fa.cPSb6SbbfIcDsmwGXjqqnIG', 'operator1', 'operator', '2025-08-17 07:04:41'),
(10, 'admin1', '$2y$10$eIJGUaoTDuOymPU2FgYTXuZlhuBawzBlvg7ipTFCtfV9upu3hXiTy', 'admin1', 'admin', '2025-09-09 01:39:19');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `penjualan_harian`
--
ALTER TABLE `penjualan_harian`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `penjualan_harian`
--
ALTER TABLE `penjualan_harian`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=67;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
