-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Dec 28, 2024 at 12:23 AM
-- Server version: 8.3.0
-- PHP Version: 8.2.18

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `task_prac`
--

-- --------------------------------------------------------

--
-- Table structure for table `sections`
--

DROP TABLE IF EXISTS `sections`;
CREATE TABLE IF NOT EXISTS `sections` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `user_id` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=39 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sections`
--

INSERT INTO `sections` (`id`, `name`, `user_id`) VALUES
(29, 'Project Management', 2),
(21, 'excercise', 1),
(22, 'Food', 1),
(23, 'Reports', 2),
(24, 'Meetings', 2),
(38, 'Studies1', 5),
(35, 'Reports', 1),
(34, 'Meetings2', 2);

-- --------------------------------------------------------

--
-- Table structure for table `tasks`
--

DROP TABLE IF EXISTS `tasks`;
CREATE TABLE IF NOT EXISTS `tasks` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `section_id` int NOT NULL,
  `title` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `description` text COLLATE utf8mb4_general_ci,
  `due_date` date NOT NULL,
  `status` enum('read','processing','done') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'read',
  `created_date` datetime DEFAULT CURRENT_TIMESTAMP,
  `completed_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `section_id` (`section_id`)
) ENGINE=MyISAM AUTO_INCREMENT=40 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tasks`
--

INSERT INTO `tasks` (`id`, `user_id`, `section_id`, `title`, `description`, `due_date`, `status`, `created_date`, `completed_date`) VALUES
(27, 1, 21, 'gym', 'gyming', '2024-12-30', 'done', '2024-12-06 17:06:42', '0000-00-00 00:00:00'),
(28, 1, 21, 'Swimming', '', '2024-12-24', 'done', '2024-12-07 11:00:57', '0000-00-00 00:00:00'),
(29, 1, 22, 'eating', 'make it balance', '2024-12-27', 'done', '2024-12-07 17:32:56', '2024-12-07 17:33:22'),
(36, 1, 35, 'read', 'reading', '2024-12-31', 'read', '2024-12-21 06:36:05', NULL),
(39, 5, 38, 'write1', 'writing1', '2024-12-31', 'done', '2024-12-24 01:25:22', '2024-12-24 01:27:27');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `profile_image` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `profile_image`) VALUES
(1, 'Goodness', 'info@gmail.com', '$2y$10$dQWR947QMiSSr7qq5LwSmu3MXXTkaaOkbz2hVVg9KocT5c73dyhZS', 'uploads/profile_images/674a2a3de2856_FB_IMG_1601668592160.jpg'),
(2, 'LadyT', 'info2@gmail.com', '$2y$10$O0yuvlB2pnj1wSOd9mQECuKy9HHG9ck0g7BpcLkoevq6ioSq0vA6e', 'uploads/profile_images/675f2b8894b8f_IMG_20231013_133544_977 - Copy.jpg'),
(3, 'GoodyCode', 'goodycode@gmail.com', '$2y$10$/FAzmdsiZbOg30UcwhoOsO4aP0gqu3PFkzFVMTdGdt9BDKgbd3C/a', 'uploads/profile_images/675236c16c8ff_IMG_20231117_230051_896.jpg'),
(4, 'Mark', 'mark@gmail.com', '$2y$10$dU20cKB.kYlLtGMYX.AS6eicJLU4ej1SWCxfdrgOvSrwBQ6WGaNJ.', 'uploads/profile_images/default-profile.png'),
(5, 'Joy', 'joy@gmail.com', '$2y$10$M6CVRa1PuhDGtkH4BDKdh..pz1aylgpILJJ6d4UPqxl3AmNS8i9dq', 'uploads/profile_images/6769ff0645a93_FB_IMG_1622695327017.jpg');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
