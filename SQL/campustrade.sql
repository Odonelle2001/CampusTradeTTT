-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 03, 2025 at 06:24 AM
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
-- Database: `campustrade`
--

-- --------------------------------------------------------

--
-- Table structure for table `accounts`
--

CREATE TABLE `accounts` (
  `id` int(10) UNSIGNED NOT NULL,
  `email` varchar(190) NOT NULL,
  `password` varchar(255) NOT NULL,
  `first_name` varchar(80) NOT NULL,
  `last_name` varchar(80) NOT NULL,
  `school_name` varchar(160) NOT NULL,
  `major` varchar(120) NOT NULL,
  `acad_role` enum('Student','Alumni','Admin') NOT NULL,
  `city_state` varchar(80) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `accounts`
--

INSERT INTO `accounts` (`id`, `email`, `password`, `first_name`, `last_name`, `school_name`, `major`, `acad_role`, `city_state`, `created_at`) VALUES
(17, 'qj9341wf@go.minnstate.edu', '$2y$10$fRzUNkaB91wn5ZAqUgTJgetlZlP/zaDpYg1IbgVhBjMR5lGmCK5yC', 'Marthe', 'Lab', 'Metropolitan State University', 'Computer Science', 'Student', '', '2025-10-29 03:51:58'),
(18, 'hk8756oo@go.minnstate.edu', '$2y$10$gdU7Fq7/YHPw7ESs9NAuXONN.yJN8tSDdiaIEVDT6Ciqo.jZPn5Fm', 'Joab', 'Nyabuto', 'Metropolitan State University', 'Computer Science', 'Student', 'Saint Paul', '2025-11-03 03:49:05');

-- --------------------------------------------------------

--
-- Table structure for table `booklistings`
--

CREATE TABLE `booklistings` (
  `id` int(10) UNSIGNED NOT NULL,
  `seller_id` int(10) UNSIGNED NOT NULL,
  `title` varchar(200) NOT NULL,
  `isbn` varchar(32) DEFAULT NULL,
  `image_path` varchar(500) DEFAULT NULL,
  `price` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `book_state` enum('New','Used') NOT NULL DEFAULT 'New',
  `status` enum('Active','Sold','Archived') NOT NULL DEFAULT 'Active',
  `course_id` varchar(40) DEFAULT NULL,
  `contact_info` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `userprofile`
--

CREATE TABLE `userprofile` (
  `user_id` int(11) NOT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `preferred_pay` enum('Venmo','PayPal','CashApp','Zelle','Cash') NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accounts`
--
ALTER TABLE `accounts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `booklistings`
--
ALTER TABLE `booklistings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `seller_id` (`seller_id`);

--
-- Indexes for table `userprofile`
--
ALTER TABLE `userprofile`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `accounts`
--
ALTER TABLE `accounts`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `booklistings`
--
ALTER TABLE `booklistings`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `booklistings`
--
ALTER TABLE `booklistings`
  ADD CONSTRAINT `booklistings_ibfk_1` FOREIGN KEY (`seller_id`) REFERENCES `accounts` (`id`) ON DELETE CASCADE;
COMMIT;


--reset password
ALTER TABLE accounts
  ADD COLUMN must_change_password TINYINT(1) NOT NULL DEFAULT 0 AFTER created_at,
  ADD COLUMN reset_token_hash CHAR(64) NULL AFTER must_change_password,
  ADD COLUMN reset_expires_at DATETIME NULL AFTER reset_token_hash;




/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;


--
-- Table structure for table `tickets`
--

CREATE TABLE `tickets` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tickets`
--

INSERT INTO `tickets` (`id`, `name`, `email`, `message`, `created_at`) VALUES
(1, 'Anthony Chang', 'fz7416lx@go.minnstate.edu', 'Test', '2025-11-25 22:48:07'),
(2, 'Anthony Chang', 'fz7416lx@go.minnstate.edu', 'Test', '2025-11-25 22:48:18'),
(3, 'Anthony', 'changanthony2000@gmail.com', 'Test123', '2025-11-26 00:14:01'),
(4, 'Sim Chang', 'Anthony@gmail.com', 'THIS MESSAGH', '2025-11-27 01:16:47');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tickets`
--
ALTER TABLE `tickets`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tickets`
--
ALTER TABLE `tickets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;