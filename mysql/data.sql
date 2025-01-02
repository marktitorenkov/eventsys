--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `hashed_password`) VALUES
(2, 'radoslav', '$2y$10$h4S7dN6/e8Ze4xmnz.uWP.V2OtWcTSZTrw5esc8wg0mnQAjA/Kaz.');


--
-- Dumping data for table `groups`
--

INSERT INTO `groups` (`group_id`, `creator_id`, `group_name`, `money_goal`, `meeting_time`, `meeting_place`, `group_description`, `group_pass`) VALUES
(1, 2, 'group 2025', 0, '09:00:00', NULL, NULL, NULL),
(2, 2, 'group 2026', 0, '09:00:00', NULL, NULL, NULL),
(3, 2, 'private test', 0, '09:00:00', NULL, NULL, 'faOFxVGt'),
(4, 2, 'public test', 0, '09:00:00', NULL, NULL, NULL);


--
-- Dumping data for table `event_to_group`
--

INSERT INTO `event_to_group` (`event_id`, `group_id`, `year`) VALUES
(1, 1, 2025),
(1, 2, 2026),
(1, 3, 2025),
(1, 4, 2025);