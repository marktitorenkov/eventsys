-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 18, 2025 at 03:50 PM
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

INSERT INTO `events` (`event_id`, `creator_id`, `name`, `date`, `description`, `recurring`) VALUES
(1, NULL, 'D Birthday', '2025-02-04', '', 1),
(2, NULL, 'M Birthday', '2025-05-05', '', 1),
(3, NULL, 'O Birthday', '2025-05-24', '', 1),
(4, NULL, 'I Nameday', '2025-05-05', '', 1),
(5, NULL, 'X+Y Wedding', '2025-08-15', '', 0),
(6, 2, 'Birthday: radoslav', '2002-09-26', '', 1),
(7, 4, 'Birthday: mark', '1999-05-05', '', 1),
(9, 6, 'Birthday: petya', '2001-02-13', '', 1),
(10, 7, 'Birthday: messi', '1987-06-24', '', 1),
(11, 8, 'Birthday: ronaldo', '1985-02-05', '', 1),
(12, 9, 'Birthday: LeoDiCaprio', '1974-11-11', '', 1),
(13, 10, 'Birthday: BradPitt', '1963-12-18', '', 1),
(14, 11, 'Birthday: TomCruise', '1962-07-03', '', 1),
(15, 12, 'Birthday: TaylorSwift', '1989-12-13', '', 1),
(16, 13, 'Birthday: Beyonce', '1981-09-04', '', 1),
(17, 14, 'Birthday: KanyeWest', '1977-06-08', '', 1);

--
-- Dumping data for table `event_groups`
--

INSERT INTO `event_groups` (`group_id`, `creator_id`, `group_name`, `money_goal`, `meeting_time`, `meeting_place`, `group_description`, `group_pass`) VALUES
(1, 2, 'radoslavs group', 0, '09:00:00', 'MEETING PALCE TO MEET', NULL, NULL),
(2, 2, 'private test', 0, '09:00:00', NULL, NULL, 'dCvtDaJW'),
(3, 2, 'next years public group', 0, '09:00:00', NULL, NULL, NULL),
(4, 2, 'some random thing', 0, '09:00:00', NULL, NULL, NULL),
(5, 4, 'other group', 0, '09:00:00', NULL, NULL, NULL);

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
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `birthday_event`, `password_hash`) VALUES
(2, 'radoslav', NULL, 6, '$2y$10$wDUBuVFZn/pMeWo7SaiV/OehsJc66NFdHEbgRpRMSqsjgUoBOYohW'),
(4, 'mark', 'mark.titorenkov@outlook.com', 7, '$2y$10$EX82855JmY.wJNIIgvcemu/tLG6rSkfbV3wHiulNka8p.q7LuiGwC'),
(6, 'petya', NULL, 9, '$2y$10$S6zkYQajUEh51bzqHUZmOumqU23cktVTSwwt13DVTjYlmjj7.djJO'),
(7, 'messi', NULL, 10, '$2y$10$Fbgyav1H1Dy7zItBpWzoRuwzteR4XbwVwp9AxwPi0Ly8zDwm.EqSW'),
(8, 'ronaldo', NULL, 11, '$2y$10$31I05JV7TIH0AwECz/jH7.ZfDV.r7j7NLIa7CrmYA6TV8gcUoGOym'),
(9, 'LeoDiCaprio', NULL, 12, '$2y$10$aGPPV3qBZd54V6Qijd40NOSdf2lDK/o5huS1AlUIf0SXWoxAfs47u'),
(10, 'BradPitt', NULL, 13, '$2y$10$87qU/J13K19JY/Q7Gl8c3uDUbEL7tfWX6gaJwXSj48Qs/xlqhVvKS'),
(11, 'TomCruise', NULL, 14, '$2y$10$5Am/V9UJGjdBICgW0N9CUOkPQlSQFxu2hpnKAqBmtcXNLv5a4tw/O'),
(12, 'TaylorSwift', NULL, 15, '$2y$10$BzOrCahDfLUGYQ1eZfI6BOjByN9yzsbFEpI4ytVyaHy9KDeizVsOO'),
(13, 'Beyonce', NULL, 16, '$2y$10$LMHMCy8rKKJNTcGDsP4jVeTVhz1bjNsqckBbi9jalBhQ98THgxuKG'),
(14, 'KanyeWest', NULL, 17, '$2y$10$QMm.iiG5LPpgwvG56KWVy.tD6/3ThTnTDd/Z7.7o0.Nm0Fko5zr6m');

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
