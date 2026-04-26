-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 26, 2026 at 12:29 PM
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
-- Database: `school_project`
--

-- --------------------------------------------------------

--
-- Table structure for table `folders`
--

CREATE TABLE `folders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `folder_name` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `folders`
--

INSERT INTO `folders` (`id`, `user_id`, `folder_name`, `created_at`) VALUES
(1, 7, 'testing', '2026-03-06 15:14:20'),
(2, 7, 'qwerty', '2026-03-11 14:24:10'),
(4, 12, 'aszdxfgh', '2026-04-17 14:23:39'),
(5, 12, 'awsdfgf', '2026-04-17 14:32:27');

-- --------------------------------------------------------

--
-- Table structure for table `login`
--

CREATE TABLE `login` (
  `id` int(11) NOT NULL,
  `username` text NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `login`
--

INSERT INTO `login` (`id`, `username`, `email`, `password`, `created_at`, `updated_at`) VALUES
(1, 'test', '1@1', '$2y$10$nxIeSYvnT38DpnAZA4WvH.enmMX63314q7yJ87hebD4Gs/C5NTdIG', '2026-02-01 17:01:23', '2026-02-01 17:01:23'),
(5, 'qwerty', 'qwer@ty.com', '$2y$10$hpNMAnNzePwwwbPeuy8LAO3ca4TyuSsTYbh6bLmF0jCJltyyFysma', '2026-03-03 18:56:17', '2026-03-03 18:56:17'),
(7, '12qw', 'q@w.lv', '$2y$10$hM/iz.YeUGAITJCvYZlBIeEBmIwxEqqdqnqEmPi2Mry0wNMN7ZVNy', '2026-03-04 19:13:33', '2026-03-04 19:13:33'),
(12, '13qw', '1@4.com', '$2y$10$T05Ge9gv/DqRsOQbHSLj2eDMmjJiu1s0yymSXfajfz6btvZmlkIoW', '2026-04-10 11:12:41', '2026-04-10 11:12:41');

-- --------------------------------------------------------

--
-- Table structure for table `passwords`
--

CREATE TABLE `passwords` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `login_data` text NOT NULL,
  `password` varchar(255) NOT NULL,
  `userID` int(11) DEFAULT NULL,
  `folder_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `passwords`
--

INSERT INTO `passwords` (`id`, `name`, `login_data`, `password`, `userID`, `folder_id`) VALUES
(1, 'test', 'qwerty', '12345', 1, NULL),
(2, 'dati', 'qwerty', '123456', 1, NULL),
(3, 'weeds', 'qwerty1', '1234567', 1, NULL),
(6, 'testds', 'dooods', 'qwerty1', 5, NULL),
(7, 'pass1', '1123', '1234', 7, NULL),
(8, 'pass2', '1234rt', '56789o', 7, 1),
(9, 'pass3', 'qwedf', '12345t', 7, 1),
(11, 'sazxdsa', 'axszx', 'xasz', 12, 5);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `folders`
--
ALTER TABLE `folders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `login`
--
ALTER TABLE `login`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `passwords`
--
ALTER TABLE `passwords`
  ADD PRIMARY KEY (`id`),
  ADD KEY `userID` (`userID`),
  ADD KEY `folder_id` (`folder_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `folders`
--
ALTER TABLE `folders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `login`
--
ALTER TABLE `login`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `passwords`
--
ALTER TABLE `passwords`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `folders`
--
ALTER TABLE `folders`
  ADD CONSTRAINT `folders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `login` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `passwords`
--
ALTER TABLE `passwords`
  ADD CONSTRAINT `passwords_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `login` (`id`),
  ADD CONSTRAINT `passwords_ibfk_2` FOREIGN KEY (`folder_id`) REFERENCES `folders` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
