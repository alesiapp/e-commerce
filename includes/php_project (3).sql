-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 21, 2024 at 10:35 PM
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `php_project`
--

-- --------------------------------------------------------

--
-- Table structure for table `administrator`
--

CREATE TABLE `administrator` (
  `admin_id` int(11) NOT NULL,
  `admin_name` varchar(100) DEFAULT NULL,
  `admin_email` text DEFAULT NULL,
  `admin_password` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `administrator`
--

INSERT INTO `administrator` (`admin_id`, `admin_name`, `admin_email`, `admin_password`) VALUES
(1, 'alesia', 'admin@gmail.com', '$2y$10$h2lLwQVCkvNBYN35khS08OyukjZSoDCop6OkFEHqNeNxRQkYDyhp.');

-- --------------------------------------------------------

--
-- Table structure for table `kategoria`
--

CREATE TABLE `kategoria` (
  `kategori_id` int(11) NOT NULL,
  `emri_kategorise` varchar(100) DEFAULT NULL,
  `detaje` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kategoria`
--

INSERT INTO `kategoria` (`kategori_id`, `emri_kategorise`, `detaje`) VALUES
(1, 'sunscreen', NULL),
(2, 'serum', NULL),
(3, 'cleanser', NULL),
(4, 'moisturizer', NULL),
(6, 'Masks', 'Description');

-- --------------------------------------------------------

--
-- Table structure for table `pagesa`
--

CREATE TABLE `pagesa` (
  `id` int(11) NOT NULL,
  `id_porosi` int(11) DEFAULT NULL,
  `transaksion_id` varchar(255) DEFAULT NULL,
  `data` datetime DEFAULT NULL,
  `id_user` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-
--
-- Table structure for table `perdorues`
--

CREATE TABLE `perdorues` (
  `id` int(11) NOT NULL,
  `emri` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `statusi` tinyint(1) DEFAULT NULL,
  `data_krijimit` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- --------------------------------------------------------

--
-- Table structure for table `porosi`
--

CREATE TABLE `porosi` (
  `id` int(11) NOT NULL,
  `statusi` varchar(50) DEFAULT NULL,
  `kosto` decimal(10,2) DEFAULT NULL,
  `id_perdoruesi` int(11) DEFAULT NULL,
  `nr_tel` varchar(30) DEFAULT NULL,
  `qyteti` varchar(255) DEFAULT NULL,
  `shteti` int(11) DEFAULT NULL,
  `data` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `porosi`
--


--
-- Table structure for table `produkte`
--

CREATE TABLE `produkte` (
  `id` int(11) NOT NULL,
  `emri` varchar(100) DEFAULT NULL,
  `kategoria` int(11) DEFAULT NULL,
  `pershkrimi` text DEFAULT NULL,
  `foto1` varchar(255) DEFAULT NULL,
  `foto2` varchar(255) DEFAULT NULL,
  `foto3` varchar(255) DEFAULT NULL,
  `foto4` varchar(255) DEFAULT NULL,
  `cmimi` decimal(10,2) DEFAULT NULL,
  `gjendja` int(11) DEFAULT NULL,
  `krijuar` date DEFAULT curdate()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



--
-- Table structure for table `produkte_porosi`
--

CREATE TABLE `produkte_porosi` (
  `id_porosi` int(11) DEFAULT NULL,
  `id_produkti` int(11) DEFAULT NULL,
  `sasi_produkti` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


CREATE TABLE `produkte_shporta` (
  `id_shporte` int(11) DEFAULT NULL,
  `id_produkti` int(11) DEFAULT NULL,
  `sasi_produkti` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `id_produkti` int(11) DEFAULT NULL,
  `id_porosi` int(11) DEFAULT NULL,
  `vleresimi` int(11) DEFAULT NULL,
  `pershkrimi` text DEFAULT NULL,
  `data` datetime DEFAULT current_timestamp(),
  `id_perdoruesi` int(11) DEFAULT NULL
) ;



--
-- Table structure for table `shporta`
--

CREATE TABLE `shporta` (
  `id` int(11) NOT NULL,
  `id_perdoruesi` int(11) DEFAULT NULL,
  `totali` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


--
-- Table structure for table `shteti`
--

CREATE TABLE `shteti` (
  `emri_shtetit` varchar(100) DEFAULT NULL,
  `kosto_shipping` decimal(10,2) DEFAULT NULL,
  `id_shtetit` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



--
-- Indexes for table `administrator`
--
ALTER TABLE `administrator`
  ADD PRIMARY KEY (`admin_id`);

--
-- Indexes for table `kategoria`
--
ALTER TABLE `kategoria`
  ADD PRIMARY KEY (`kategori_id`),
  ADD UNIQUE KEY `kategoria_produktit_unik` (`emri_kategorise`);

--
-- Indexes for table `pagesa`
--
ALTER TABLE `pagesa`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_pagesa_porosi` (`id_porosi`),
  ADD KEY `fk_pagesa_perdorues` (`id_user`);

--
-- Indexes for table `perdorues`
--
ALTER TABLE `perdorues`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_email` (`email`);

--
-- Indexes for table `porosi`
--
ALTER TABLE `porosi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_porosi_perdorues` (`id_perdoruesi`),
  ADD KEY `fk_porosi_shteti` (`shteti`);

--
-- Indexes for table `produkte`
--
ALTER TABLE `produkte`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `emri_produktit_unik` (`emri`),
  ADD KEY `fk_produkte_kategoria` (`kategoria`);

--
-- Indexes for table `produkte_porosi`
--
ALTER TABLE `produkte_porosi`
  ADD KEY `fk_produkte_porosi_porosi` (`id_porosi`),
  ADD KEY `fk_produkte_porosi_produkte` (`id_produkti`);

--
-- Indexes for table `produkte_shporta`
--
ALTER TABLE `produkte_shporta`
  ADD KEY `fk_produkte_shporta_shporta` (`id_shporte`),
  ADD KEY `fk_produkte_shporta_produkte` (`id_produkti`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_reviews_porosi` (`id_porosi`),
  ADD KEY `fk_reviews_produkte` (`id_produkti`),
  ADD KEY `fk_reviews_perdorues` (`id_perdoruesi`);

--
-- Indexes for table `shporta`
--
ALTER TABLE `shporta`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_shporta_perdorues` (`id_perdoruesi`);

--
-- Indexes for table `shteti`
--
ALTER TABLE `shteti`
  ADD PRIMARY KEY (`id_shtetit`),
  ADD UNIQUE KEY `unq_shteti_emri_shtetit` (`emri_shtetit`);


--
-- AUTO_INCREMENT for table `administrator`
--
ALTER TABLE `administrator`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT for table `kategoria`
--
ALTER TABLE `kategoria`
  MODIFY `kategori_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT for table `pagesa`
--
ALTER TABLE `pagesa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT for table `perdorues`
--
ALTER TABLE `perdorues`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT for table `porosi`
--
ALTER TABLE `porosi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT for table `produkte`
--
ALTER TABLE `produkte`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `shporta`
--
ALTER TABLE `shporta`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT for table `shteti`
--
ALTER TABLE `shteti`
  MODIFY `id_shtetit` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;



--
-- Constraints for table `pagesa`
--
ALTER TABLE `pagesa`
  ADD CONSTRAINT `fk_pagesa_perdorues` FOREIGN KEY (`id_user`) REFERENCES `perdorues` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_pagesa_porosi` FOREIGN KEY (`id_porosi`) REFERENCES `porosi` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `porosi`
--
ALTER TABLE `porosi`
  ADD CONSTRAINT `fk_porosi_perdorues` FOREIGN KEY (`id_perdoruesi`) REFERENCES `perdorues` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_porosi_shteti` FOREIGN KEY (`shteti`) REFERENCES `shteti` (`id_shtetit`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `produkte`
--
ALTER TABLE `produkte`
  ADD CONSTRAINT `fk_produkte_kategoria` FOREIGN KEY (`kategoria`) REFERENCES `kategoria` (`kategori_id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `produkte_porosi`
--
ALTER TABLE `produkte_porosi`
  ADD CONSTRAINT `fk_produkte_porosi_porosi` FOREIGN KEY (`id_porosi`) REFERENCES `porosi` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_produkte_porosi_produkte` FOREIGN KEY (`id_produkti`) REFERENCES `produkte` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `produkte_shporta`
--
ALTER TABLE `produkte_shporta`
  ADD CONSTRAINT `fk_produkte_shporta_produkte` FOREIGN KEY (`id_produkti`) REFERENCES `produkte` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_produkte_shporta_shporta` FOREIGN KEY (`id_shporte`) REFERENCES `shporta` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `fk_reviews_perdorues` FOREIGN KEY (`id_perdoruesi`) REFERENCES `perdorues` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_reviews_porosi` FOREIGN KEY (`id_porosi`) REFERENCES `porosi` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_reviews_produkte` FOREIGN KEY (`id_produkti`) REFERENCES `produkte` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `shporta`
--
ALTER TABLE `shporta`
  ADD CONSTRAINT `fk_shporta_perdorues` FOREIGN KEY (`id_perdoruesi`) REFERENCES `perdorues` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
