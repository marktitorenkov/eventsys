-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 11, 2025 at 08:56 PM
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
(1, 2, 2025),
(1, 3, 2026),
(1, 5, 2025),
(2, 4, 2025);

--
-- Dumping data for table `groups`
--

INSERT INTO `groups` (`group_id`, `creator_id`, `group_name`, `money_goal`, `meeting_time`, `meeting_place`, `group_description`, `group_pass`) VALUES
(1, 2, 'radoslavs group', 0, '09:00:00', 'MEETING PALCE TO MEET', NULL, NULL),
(2, 2, 'private test', 0, '09:00:00', NULL, NULL, 'dCvtDaJW'),
(3, 2, 'next years public group', 0, '09:00:00', NULL, NULL, NULL),
(4, 2, 'some random thing', 0, '09:00:00', NULL, NULL, NULL),
(5, 4, 'other group', 0, '09:00:00', NULL, NULL, NULL);

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password_hash`) VALUES
(2, 'radoslav', '$2y$10$wDUBuVFZn/pMeWo7SaiV/OehsJc66NFdHEbgRpRMSqsjgUoBOYohW'),
(4, 'mark', '$2y$10$EX82855JmY.wJNIIgvcemu/tLG6rSkfbV3wHiulNka8p.q7LuiGwC');

--
-- Dumping data for table `user_in_group`
--

INSERT INTO `user_in_group` (`user_id`, `group_id`) VALUES
(2, 1),
(2, 2);
SET FOREIGN_KEY_CHECKS=1;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
