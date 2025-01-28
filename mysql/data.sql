-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: db
-- Generation Time: Jan 28, 2025 at 03:13 PM
-- Server version: 10.5.27-MariaDB-ubu2004
-- PHP Version: 8.2.27

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
USE `eventsys`;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`event_id`, `creator_id`, `name`, `date`, `description`, `recurring`) VALUES
(1, 1, 'Birthday: radoslav', '2002-09-26', '', 1),
(2, 2, 'Birthday: mark', '1999-05-05', '', 1),
(3, 3, 'Birthday: petya', '2001-02-13', '', 1),
(4, 4, 'Birthday: messi', '1987-06-24', '', 1),
(5, 5, 'Birthday: ronaldo', '1985-02-05', '', 1),
(6, 6, 'Birthday: LeoDiCaprio', '1974-11-11', '', 1),
(7, 7, 'Birthday: BradPitt', '1963-12-18', '', 1),
(8, 8, 'Birthday: TomCruise', '1962-07-03', '', 1),
(9, 9, 'Birthday: TaylorSwift', '1989-12-13', '', 1),
(10, 10, 'Birthday: Beyonce', '1981-09-04', '', 1),
(11, 11, 'Birthday: KanyeWest', '1977-06-08', '', 1),
(12, 5, 'ronaldos pizza party', '2025-01-30', '', 0),
(13, 12, 'Birthday: NiccoloPaganini', '1782-10-27', '', 1),
(14, 13, 'Birthday: WaltDisney', '1901-12-05', '', 1),
(15, 14, 'Birthday: JimCarrey', '1962-01-17', '', 1);

--
-- Dumping data for table `event_groups`
--

INSERT INTO `event_groups` (`group_id`, `creator_id`, `group_name`, `money_goal`, `meeting_time`, `meeting_place`, `group_description`, `group_pass`) VALUES
(1, 1, 'Radoslavs private group', 50, '09:00:00', 'Some place to meet', 'Превеждайте парите по paypal.', 'peJY1bYM'),
(2, 5, 'ronaldos group', 0, '09:00:00', NULL, NULL, NULL),
(3, 2, 'my awsome pizza party', 100, '09:00:00', NULL, NULL, NULL),
(7, 1, 'radoslavs group', 0, '09:00:00', NULL, NULL, NULL);

--
-- Dumping data for table `event_to_group`
--

INSERT INTO `event_to_group` (`event_id`, `group_id`, `year`) VALUES
(1, 2, 2025),
(5, 1, 2025),
(12, 3, 2025),
(12, 7, 2025);

--
-- Dumping data for table `favorite_users`
--

INSERT INTO `favorite_users` (`user_id`, `favorite_user_id`) VALUES
(1, 2),
(1, 3),
(1, 5),
(2, 5);

--
-- Dumping data for table `group_messages`
--

INSERT INTO `group_messages` (`group_id`, `sender_id`, `time`, `content`) VALUES
(3, 1, '2025-01-28 13:10:35.888', 'hello chat'),
(3, 1, '2025-01-28 13:10:56.230', 'are we ready for some pizza?'),
(3, 1, '2025-01-28 13:43:02.971', 'hello?'),
(3, 2, '2025-01-28 13:10:43.763', 'hi all');

--
-- Dumping data for table `polls`
--

INSERT INTO `polls` (`poll_id`, `group_id`, `creator_id`, `poll_title`) VALUES
(1, 3, 2, 'Whats your favorite pizza?'),
(2, 3, 1, 'Where should we go?');

--
-- Dumping data for table `poll_options`
--

INSERT INTO `poll_options` (`option_id`, `poll_id`, `option_title`) VALUES
(4, 1, 'Hawaiian '),
(5, 1, 'Margarita'),
(1, 1, 'Peperoni'),
(2, 2, 'Home pizza'),
(6, 2, 'Pizzaria Pizza Palaze'),
(3, 2, 'Tavarn Bulgarina');

--
-- Dumping data for table `poll_votes`
--

INSERT INTO `poll_votes` (`option_id`, `user_id`) VALUES
(1, 1),
(1, 2),
(3, 1),
(4, 2),
(5, 1);

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `birthday_event`, `password_hash`) VALUES
(1, 'radoslav', 'radoslavbotov1@gmail.com', 1, '$2y$10$wDUBuVFZn/pMeWo7SaiV/OehsJc66NFdHEbgRpRMSqsjgUoBOYohW'),
(2, 'mark', 'mark.titorenkov@outlook.com', 2, '$2y$10$EX82855JmY.wJNIIgvcemu/tLG6rSkfbV3wHiulNka8p.q7LuiGwC'),
(3, 'petya', NULL, 3, '$2y$10$S6zkYQajUEh51bzqHUZmOumqU23cktVTSwwt13DVTjYlmjj7.djJO'),
(4, 'messi', NULL, 4, '$2y$10$Fbgyav1H1Dy7zItBpWzoRuwzteR4XbwVwp9AxwPi0Ly8zDwm.EqSW'),
(5, 'ronaldo', NULL, 5, '$2y$10$31I05JV7TIH0AwECz/jH7.ZfDV.r7j7NLIa7CrmYA6TV8gcUoGOym'),
(6, 'LeoDiCaprio', NULL, 6, '$2y$10$aGPPV3qBZd54V6Qijd40NOSdf2lDK/o5huS1AlUIf0SXWoxAfs47u'),
(7, 'BradPitt', NULL, 7, '$2y$10$87qU/J13K19JY/Q7Gl8c3uDUbEL7tfWX6gaJwXSj48Qs/xlqhVvKS'),
(8, 'TomCruise', NULL, 8, '$2y$10$5Am/V9UJGjdBICgW0N9CUOkPQlSQFxu2hpnKAqBmtcXNLv5a4tw/O'),
(9, 'TaylorSwift', NULL, 9, '$2y$10$BzOrCahDfLUGYQ1eZfI6BOjByN9yzsbFEpI4ytVyaHy9KDeizVsOO'),
(10, 'Beyonce', NULL, 10, '$2y$10$LMHMCy8rKKJNTcGDsP4jVeTVhz1bjNsqckBbi9jalBhQ98THgxuKG'),
(11, 'KanyeWest', NULL, 11, '$2y$10$QMm.iiG5LPpgwvG56KWVy.tD6/3ThTnTDd/Z7.7o0.Nm0Fko5zr6m'),
(12, 'NiccoloPaganini', NULL, 13, '$2y$12$t8ayLxQ6zCV7Dfbd0zK.muyfdn5iQV0DjoA1IPTwoeimLUl.CoT7y'),
(13, 'WaltDisney', NULL, 14, '$2y$12$bY8PYThaJ2XAEmg1iOacY.MuVqPIbHlB/V4fmnNCwPuJjuNlMrzB2'),
(14, 'JimCarrey', NULL, 15, '$2y$12$BTrGxbQjeSfPJWi.5MdCFeLq1Jb6rtnGMqWbRIOHy3.zoYjpOrk.K');

--
-- Dumping data for table `user_hidden_event`
--

INSERT INTO `user_hidden_event` (`event_id`, `user_id`) VALUES
(9, 11),
(11, 9);

--
-- Dumping data for table `user_in_group`
--

INSERT INTO `user_in_group` (`user_id`, `group_id`) VALUES
(1, 1),
(1, 3),
(1, 7),
(2, 3),
(3, 3),
(4, 3),
(5, 2),
(5, 3);
SET FOREIGN_KEY_CHECKS=1;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
