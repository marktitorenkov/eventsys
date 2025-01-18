-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 17, 2025 at 11:44 PM
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

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

DROP TABLE IF EXISTS `events`;
CREATE TABLE `events` (
  `event_id` int(11) NOT NULL,
  `admin` int(11) NOT NULL,
  `canChange` tinyint(1) DEFAULT 1,
  `name` varchar(50) NOT NULL,
  `date` date NOT NULL,
  `description` varchar(100) NOT NULL,
  `recurring` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `event_groups`
--

DROP TABLE IF EXISTS `event_groups`;
CREATE TABLE `event_groups` (
  `group_id` int(11) NOT NULL,
  `creator_id` int(11) NOT NULL,
  `group_name` varchar(50) NOT NULL,
  `money_goal` int(10) NOT NULL DEFAULT 0,
  `meeting_time` time NOT NULL DEFAULT '09:00:00',
  `meeting_place` varchar(50) DEFAULT NULL,
  `group_description` varchar(250) DEFAULT NULL,
  `group_pass` char(8) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `event_to_group`
--

DROP TABLE IF EXISTS `event_to_group`;
CREATE TABLE `event_to_group` (
  `event_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `year` smallint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `favorite_users`
--

DROP TABLE IF EXISTS `favorite_users`;
CREATE TABLE `favorite_users` (
  `user_id` int(11) NOT NULL,
  `favorite_user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(50) DEFAULT NULL,
  `birthdate` date DEFAULT NULL,
  `password_hash` char(60) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Triggers `users`
--
DROP TRIGGER IF EXISTS `Birthday`;
DELIMITER $$
CREATE TRIGGER `Birthday` AFTER INSERT ON `users` FOR EACH ROW INSERT INTO events
(admin, canChange, name, date, recurring) VALUES (NEW.id, FALSE, CONCAT('Birthday: ', NEW.username), NEW.birthdate, 1)
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `BirthdayUpdate`;
DELIMITER $$
CREATE TRIGGER `BirthdayUpdate` AFTER UPDATE ON `users` FOR EACH ROW UPDATE events e
SET
e.date = NEW.birthdate
WHERE e.admin=NEW.id AND NOT e.canChange
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `user_in_group`
--

DROP TABLE IF EXISTS `user_in_group`;
CREATE TABLE `user_in_group` (
  `user_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`event_id`);

--
-- Indexes for table `event_groups`
--
ALTER TABLE `event_groups`
  ADD PRIMARY KEY (`group_id`),
  ADD KEY `event_groups_ibfk_1` (`creator_id`);

--
-- Indexes for table `event_to_group`
--
ALTER TABLE `event_to_group`
  ADD PRIMARY KEY (`event_id`,`group_id`),
  ADD KEY `event_to_group_ibfk_1` (`event_id`),
  ADD KEY `event_to_group_ibfk_2` (`group_id`);

--
-- Indexes for table `favorite_users`
--
ALTER TABLE `favorite_users`
  ADD UNIQUE KEY `favorite_users_unique` (`user_id`,`favorite_user_id`),
  ADD KEY `favorite_user_id` (`favorite_user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `user_in_group`
--
ALTER TABLE `user_in_group`
  ADD PRIMARY KEY (`user_id`,`group_id`),
  ADD KEY `user_in_group_ibfk_1` (`user_id`),
  ADD KEY `user_in_group_ibfk_2` (`group_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `event_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `event_groups`
--
ALTER TABLE `event_groups`
  MODIFY `group_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `event_groups`
--
ALTER TABLE `event_groups`
  ADD CONSTRAINT `event_groups_ibfk_1` FOREIGN KEY (`creator_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `event_to_group`
--
ALTER TABLE `event_to_group`
  ADD CONSTRAINT `event_to_group_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `events` (`event_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `event_to_group_ibfk_2` FOREIGN KEY (`group_id`) REFERENCES `event_groups` (`group_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `favorite_users`
--
ALTER TABLE `favorite_users`
  ADD CONSTRAINT `favorite_users_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `favorite_users_ibfk_2` FOREIGN KEY (`favorite_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `user_in_group`
--
ALTER TABLE `user_in_group`
  ADD CONSTRAINT `user_in_group_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `user_in_group_ibfk_2` FOREIGN KEY (`group_id`) REFERENCES `event_groups` (`group_id`) ON DELETE CASCADE ON UPDATE CASCADE;
SET FOREIGN_KEY_CHECKS=1;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
