-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Nov 27, 2026 at 08:11 AM
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
-- Database: `comparo`
--

-- --------------------------------------------------------

--
-- Table structure for table `destination`
--

CREATE TABLE `destination` (
  `id` int NOT NULL,
  `location` varchar(255) NOT NULL,
  `price` int NOT NULL,
  `tour_operator_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `destination`
--

INSERT INTO `destination` (`id`, `location`, `price`, `tour_operator_id`) VALUES
(1, 'Rome', 1650, 2),
(2, 'Londres', 1100, 2),
(3, 'Monaco', 1390, 1),
(4, 'Tunis', 2390, 3),
(5, 'Tokyo', 2000, 2),
(6, 'Marrakech', 1500, 3),
(7, 'Bangkok', 1300, 1),
(8, 'Madrid', 1675, 3),
(9, 'Dubaï', 2100, 3),
(10, 'Istanbul', 500, 1),
(11, 'Lisbonne', 1800, 2),
(12, 'Rome', 1590, 3),
(13, 'Rome', 1720, 5),
(14, 'Londres', 1145, 3),
(15, 'Londres', 1040, 4),
(16, 'Monaco', 1450, 2),
(17, 'Monaco', 1335, 3),
(18, 'Tunis', 2450, 2),
(19, 'Tunis', 2285, 5),
(20, 'Tokyo', 1940, 3),
(21, 'Tokyo', 2130, 4),
(22, 'Marrakech', 1550, 1),
(23, 'Marrakech', 1425, 2),
(24, 'Bangkok', 1245, 3),
(25, 'Bangkok', 1380, 4),
(26, 'Madrid', 1705, 1),
(27, 'Madrid', 1615, 2),
(28, 'Madrid', 1690, 4),
(29, 'Dubaï', 2035, 2),
(30, 'Dubaï', 2240, 4),
(31, 'Istanbul', 535, 2),
(32, 'Istanbul', 475, 5),
(33, 'Lisbonne', 1745, 3),
(34, 'Lisbonne', 1875, 4);

-- --------------------------------------------------------

--
-- Table structure for table `review`
--

CREATE TABLE `review` (
  `id` int NOT NULL,
  `message` varchar(255) NOT NULL,
  `author` varchar(255) NOT NULL,
  `tour_operator_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `review`
--

INSERT INTO `review` (`id`, `message`, `author`, `tour_operator_id`) VALUES
(1, 'Super voyage, prestation au top !!', 'Michel', 2),
(2, 'Tout est inclus dans le prix, c\'est cool, je recommande', 'Paul', 3),
(3, 'Un peu cher, mais ça vaut le coup', 'Paul', 2),
(4, 'arnaque!!!! a fuire!!!', 'René', 2);

-- --------------------------------------------------------

--
-- Table structure for table `tour_operator`
--

CREATE TABLE `tour_operator` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `link` varchar(255) NOT NULL,
  `grade_count` int NOT NULL DEFAULT '0',
  `grade_total` int NOT NULL DEFAULT '0',
  `is_premium` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `tour_operator`
--

INSERT INTO `tour_operator` (`id`, `name`, `link`, `grade_count`, `grade_total`, `is_premium`) VALUES
(1, 'Salaun Holidays', 'https://www.salaun-holidays.com/', 0, 0, 0),
(2, 'Fram', 'https://www.fram.fr/', 2, 6, 1),
(3, 'Heliades', 'https://www.heliades.fr/', 1, 4, 0),
(4, 'Voyageur du monde ', 'https://www.voyageursdumonde.fr/voyage-sur-mesure', 6, 5, 1),
(5, 'Amplitudes', 'https://www.amplitudes.com/', 6, 4, 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `destination`
--
ALTER TABLE `destination`
  ADD PRIMARY KEY (`id`),
  ADD KEY `destination_tour_operator` (`tour_operator_id`);

--
-- Indexes for table `review`
--
ALTER TABLE `review`
  ADD PRIMARY KEY (`id`),
  ADD KEY `review_tour_operator` (`tour_operator_id`);

--
-- Indexes for table `tour_operator`
--
ALTER TABLE `tour_operator`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `destination`
--
ALTER TABLE `destination`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `review`
--
ALTER TABLE `review`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tour_operator`
--
ALTER TABLE `tour_operator`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `destination`
--
ALTER TABLE `destination`
  ADD CONSTRAINT `destination_tour_operator` FOREIGN KEY (`tour_operator_id`) REFERENCES `tour_operator` (`id`);

--
-- Constraints for table `review`
--
ALTER TABLE `review`
  ADD CONSTRAINT `review_tour_operator` FOREIGN KEY (`tour_operator_id`) REFERENCES `tour_operator` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
