-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: May 01, 2025 at 06:27 PM
-- Server version: 8.0.30
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_starling`
--

-- --------------------------------------------------------

--
-- Table structure for table `menu`
--

CREATE TABLE `menu` (
  `id_menu` int NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` int NOT NULL,
  `price` int NOT NULL DEFAULT '0',
  `image` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `menu`
--

INSERT INTO `menu` (`id_menu`, `name`, `type`, `price`, `image`, `description`) VALUES
(18, 'Asian Dolce Latte', 1, 59000, 'menu_67e40c7823029.webp', 'Minuman kopi dengan rasa manis dan creamy'),
(19, 'Americano', 1, 40000, 'menu_67e40c8d63452.webp', 'Kopi hitam dengan rasa kuat'),
(20, 'Salted Caramel Latte', 1, 56000, 'menu_67e40ca802a50.webp', 'Latte dengan rasa karamel asin'),
(21, 'Cold Brew', 1, 48000, 'menu_67e40cc16d723.webp', 'Kopi dingin dengan rasa yang halus'),
(22, 'Caramel Latte', 1, 56000, 'menu_67e40cd75aa9a.webp', 'Latte dengan rasa karamel'),
(23, 'Teavana Ice Tea', 2, 30000, 'menu_67e40cf19cac7.webp', 'Es teh dengan rasa segar'),
(24, 'Scarlet Velvet Cake', 3, 43000, 'menu_67e40d1047f72.webp', 'Kue red velvet lembut'),
(25, 'Smoked Beef Mushroom & Cheese Panini', 3, 52000, 'menu_67e40d734cd61.webp', 'Panini dengan isian daging sapi dan jamur'),
(26, 'Greentea Cream Frappuccino', 4, 62000, 'menu_67e40e1ceaa60.webp', 'Frappuccino dengan rasa greentea'),
(27, 'Caramel Java Chip Frappuccino', 4, 62000, 'menu_67e40e464a693.webp', 'Frappuccino dengan rasa karamel dan coklat'),
(28, 'Greentea Latte', 2, 59000, 'menu_67e40e6345e35.webp', 'Latte dengan rasa greentea');

-- --------------------------------------------------------

--
-- Table structure for table `types`
--

CREATE TABLE `types` (
  `id_type` int NOT NULL,
  `nama` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `types`
--

INSERT INTO `types` (`id_type`, `nama`) VALUES
(1, 'Coffee'),
(2, 'Non Coffee'),
(3, 'Food'),
(4, 'Frappuccino');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `idUser` int NOT NULL,
  `fullName` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `userName` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'customer',
  `profilePicture` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'user.jpg'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`idUser`, `fullName`, `userName`, `email`, `password`, `role`, `profilePicture`) VALUES
(1, 'Admin', 'admin', 'admin@gmail.com', '$2y$10$XjAmRgwbHOh61/ICnuVhMOO5rgV4wOclVHxEpBiD0DrJHC7LKBYT6', 'admin', 'user.jpg');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `menu`
--
ALTER TABLE `menu`
  ADD PRIMARY KEY (`id_menu`),
  ADD KEY `type` (`type`);

--
-- Indexes for table `types`
--
ALTER TABLE `types`
  ADD PRIMARY KEY (`id_type`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`idUser`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `menu`
--
ALTER TABLE `menu`
  MODIFY `id_menu` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `types`
--
ALTER TABLE `types`
  MODIFY `id_type` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `idUser` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `menu`
--
ALTER TABLE `menu`
  ADD CONSTRAINT `menu_ibfk_1` FOREIGN KEY (`type`) REFERENCES `types` (`id_type`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
