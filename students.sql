-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 22, 2024 at 05:28 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `student_directory`
--

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `student_id` int(11) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `birth_date` date NOT NULL,
  `student_name` varchar(255) NOT NULL,
  `contact_number` varchar(20) NOT NULL,
  `address` text NOT NULL,
  `id_number` varchar(50) NOT NULL,
  `user_type` enum('user','admin') NOT NULL DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`student_id`, `first_name`, `last_name`, `email`, `password`, `birth_date`, `student_name`, `contact_number`, `address`, `id_number`, `user_type`) VALUES
(1, 'Skyler', 'Kan', 'randyescartin20@gmail.com', '$2y$10$vHVkiSQiW4uThXu2xuEPaODJmi6hO96UvGf/Efv/p4SJsyGbrUYIS', '2005-02-25', '', '09223333333', 'Day-as, Cordova, Cebu', '20243', 'user'),
(2, 'Mark', 'Inoc', 'markinoc@gmail.com', '$2y$10$EKclvDTCSZZLjoRVmW23Mec6g8kIqi35jzq.GEn1w2YZ.enF2im6u', '2005-02-25', '', '092231303131', 'Poblacion, Cordova, Cebu', '20244', 'user'),
(15, 'Randy', 'Escartin', 'randyescartin2004@gmail.com', '$2y$10$VWkdp51aP6cSavHj82OVsOuGgA48PHwK8.5IbXA9wsufq96SzDjhi', '2004-01-25', '', '092231303131', 'Poblacion, Cordova, Cebu', '3', 'admin'),
(16, 'Kaleon', 'Kan', 'kaleonkan@gmail.com', '$2y$10$p0DyjGNEVs5c3ZxgKxQh3utfE6iheyHEL3HIKXACUIzwYhCCwzF9S', '2005-01-25', '', '09073308248', 'Poblacion, Cordova, Cebu', '5', 'admin'),
(17, 'Marchel', 'Ngujo', 'marchelngujo30@gmail.com', '$2y$10$P7FjO0S/zbu5M7D3t6ap0u5St1Bbn3o.AIFA9gbOhqzUA/8i7wrQW', '2004-03-30', '', '09073308249', 'Poblacion, Cordova, Cebu', '20241', 'user'),
(19, 'jerald', 'anderson', 'jeraldanderson@gmail.com', '$2y$10$8Q3lFtosABmLxjZIrUmT9uQzcww/Tn3ileaQPUzzcb3yQcngm/iOy', '2003-12-13', '', '0956545465', 'DAY-AS CORDOVA CEBUY', '2', 'admin');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`student_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `id_number` (`id_number`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `student_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
