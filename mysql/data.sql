-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 02, 2025 at 06:35 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `eventsys`
--

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`event_id`, `name`, `date`, `recurring`) VALUES
(1, 'D Birthday', '2025-02-04', 1),
(2, 'M Birthday', '2025-05-05', 1),
(3, 'O Birthday', '2025-05-24', 1),
(4, 'I Nameday', '2025-05-05', 1),
(5, 'X+Y Wedding', '2025-08-15', 0);

--
-- Dumping data for table `event_to_group`
--

INSERT INTO `event_to_group` (`event_id`, `group_id`, `year`) VALUES
(1, 1, 2025),
(1, 2, 2026),
(1, 3, 2025),
(1, 4, 2025);

--
-- Dumping data for table `groups`
--

INSERT INTO `groups` (`group_id`, `creator_id`, `group_name`, `money_goal`, `meeting_time`, `meeting_place`, `group_description`, `group_pass`) VALUES
(1, 2, 'group 2025', 0, '09:00:00', NULL, NULL, NULL),
(2, 2, 'group 2026', 0, '09:00:00', NULL, NULL, NULL),
(3, 2, 'private test', 0, '09:00:00', NULL, NULL, 'faOFxVGt'),
(4, 2, 'public test', 0, '09:00:00', NULL, NULL, NULL);

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password_hash`) VALUES
(2, 'radoslav', '$2y$10$h4S7dN6/e8Ze4xmnz.uWP.V2OtWcTSZTrw5esc8wg0mnQAjA/Kaz.'),
(4, 'mark', '$2y$10$EX82855JmY.wJNIIgvcemu/tLG6rSkfbV3wHiulNka8p.q7LuiGwC');
SET FOREIGN_KEY_CHECKS=1;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
