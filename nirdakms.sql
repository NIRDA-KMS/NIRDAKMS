-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 23, 2025 at 10:54 PM
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
-- Database: `nirdakms`
--

-- --------------------------------------------------------

--
-- Table structure for table `forum_categories`
--

CREATE TABLE `forum_categories` (
  `id` int(11) NOT NULL,
  `category_name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `permission` enum('public','private','restricted') NOT NULL,
  `allowed_user_ids` varchar(255) DEFAULT NULL,
  `allowed_group_ids` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `forum_categories`
--

INSERT INTO `forum_categories` (`id`, `category_name`, `description`, `permission`, `allowed_user_ids`, `allowed_group_ids`, `created_at`, `updated_at`) VALUES
(13, 'General', 'General discussions', 'public', NULL, NULL, '2025-04-23 19:28:01', '2025-04-23 19:28:01'),
(14, 'Announcements', 'Official announcements', 'public', NULL, NULL, '2025-04-23 19:28:01', '2025-04-23 19:28:01'),
(22, 'NIRDA meetings', 'meetings today', 'private', '9,6,8', '', '2025-04-22 10:00:20', '2025-04-22 10:00:20'),
(23, 'Nirdaforum1', 'this is the testing well forum in our system', 'public', '', '', '2025-04-22 21:07:14', '2025-04-22 21:07:14'),
(24, 'Nirda_datamanagement', 'this is the forum discussing in correcting the data a=of the industrial researching', 'private', '9,6,1,11', '', '2025-04-22 23:06:10', '2025-04-22 23:06:10');

-- --------------------------------------------------------

--
-- Table structure for table `forum_replies`
--

CREATE TABLE `forum_replies` (
  `reply_id` int(11) NOT NULL,
  `topic_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `content` text NOT NULL,
  `is_flagged` tinyint(1) DEFAULT 0,
  `flag_reason` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `forum_subscriptions`
--

CREATE TABLE `forum_subscriptions` (
  `subscription_id` int(11) NOT NULL,
  `topic_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `forum_topics`
--

CREATE TABLE `forum_topics` (
  `topic_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `is_pinned` tinyint(1) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `forum_topics`
--

INSERT INTO `forum_topics` (`topic_id`, `category_id`, `user_id`, `title`, `content`, `is_pinned`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 13, 1, 'First Topic', 'Content...', 0, 1, '2025-04-23 19:31:40', '2025-04-23 19:31:40'),
(2, 14, 1, 'Second Topic', 'Content...', 0, 1, '2025-04-23 19:31:40', '2025-04-23 19:31:40'),
(3, 13, 1, 'First Topic', 'Content...', 0, 1, '2025-04-23 19:36:24', '2025-04-23 19:36:24'),
(4, 14, 1, 'Second Topic', 'Content...', 0, 1, '2025-04-23 19:36:24', '2025-04-23 19:36:24'),
(5, 13, 1, 'First Topic', 'Content...', 0, 1, '2025-04-23 19:38:18', '2025-04-23 19:38:18'),
(6, 14, 1, 'Second Topic', 'Content...', 0, 1, '2025-04-23 19:38:18', '2025-04-23 19:38:18');

-- --------------------------------------------------------

--
-- Table structure for table `schedule_events`
--

CREATE TABLE `schedule_events` (
  `event_id` int(11) NOT NULL,
  `eventTitle` varchar(100) NOT NULL,
  `startDateTime` datetime NOT NULL,
  `endingDateTime` datetime NOT NULL,
  `eventLocation` text DEFAULT NULL,
  `eventDescription` varchar(255) DEFAULT NULL,
  `attend` text DEFAULT NULL,
  `Recurrence` text DEFAULT NULL,
  `emailReminder` varchar(50) DEFAULT NULL,
  `appReminder` varchar(100) DEFAULT NULL,
  `reminderTime` int(11) DEFAULT NULL,
  `isActive` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `role_id` int(11) NOT NULL,
  `department` varchar(100) DEFAULT NULL,
  `division` varchar(100) DEFAULT NULL,
  `unit` varchar(100) DEFAULT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `language_preference` enum('English','French','Kinyarwanda') DEFAULT 'English',
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_login` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `email`, `password`, `full_name`, `role_id`, `department`, `division`, `unit`, `phone_number`, `language_preference`, `is_active`, `created_at`, `last_login`) VALUES
(1, 'Mahomet', 'mahofoide@gmail.com', '$2y$10$xQAq50zZilsxp.7O3TZsFu.fRJ3xGpmPx.Opx/r9jn5vzUquDS5W2', 'KAZUBWENGE Mahomet', 1, 'Knowledge Management and Operational monitoring', '0', 'Technology Monitoring and Knowledge Management', '0788359461', 'English', 1, '2024-07-14 02:14:45', '2024-07-14 03:11:18'),
(2, 'Kativire', 'bisinfo.backet@gmail.com', '$2y$10$kTBdYRQoY1dYiz9DC5XENeyVfv4bNdAjdPH/4F3QGbAFRO9XruiCW', 'Mahomet Kativire', 1, 'OP &KM', '0', 'TM&KM', '1788346231', 'Kinyarwanda', 1, '2025-03-11 06:39:02', '2025-03-11 06:44:17'),
(3, 'Aimee', 'aimee.ndoli@example.com', '$2y$10$7UQreEvgM5j3vIj1J2XzjO1e57/4xD/fp5yNaPVOROejLfzHqZmLC', 'NDOLI Aimee', 2, 'Data Analysis', '1', 'Monitoring Unit', '0788888881', 'French', 1, '2025-01-10 02:20:15', '2025-01-11 03:10:11'),
(4, 'Eric', 'eric.ndoli@example.com', '$2y$10$WfPv3j5jNSb5xeKKoxt4XeYX.l7ROKeWZf6jzHgFEXwF0ReUnmZUy', 'NDOLI Eric', 2, 'ICT Department', '1', 'Support & Services', '0788888882', 'English', 1, '2025-02-20 04:00:00', '2025-02-20 04:45:00'),
(5, 'Josiane', 'josiane.m@example.com', '$2y$10$aqaNQ9DnHULt1zYK9ZL/yuLtP6xPu8JZu6wElZ8n51/5P3UVEA77m', 'UMUTONI Josiane', 1, 'Communications', '2', 'Public Relations', '0788888883', 'Kinyarwanda', 1, '2025-03-01 03:00:00', '2025-03-01 03:30:00'),
(6, 'Claude', 'claude.k@example.com', '$2y$10$zL1qfxFqFOAFpLsL4iKZoOlSyk45sjk7SgL5quJ2xIO.PYdDjUpXi', 'KAMANA Claude', 3, 'Research and Development', '3', 'Innovation Unit', '0788888884', 'French', 1, '2025-04-01 01:30:00', '2025-04-01 02:00:00'),
(7, 'Beata', 'beata.r@example.com', '$2y$10$A2FYO1m1TrX3fKYZzM9Z8.xQ2ybBvE9qFZbTI7Egr0nKzL6FuNeyG', 'RUTAYISIRE Beata', 2, 'Project Management', '1', 'Execution Team', '0788888885', 'English', 1, '2025-03-15 05:20:00', '2025-03-15 05:45:00'),
(8, 'Yves', 'yves.niy@example.com', '$2y$10$NpFJz8zR4nZ0bUZK4OFE1u4VBMv88JUNX6YHcRMF5bUJ2r59mjPiG', 'NIYONSENGA Yves', 1, 'Quality Assurance', '2', 'QA Monitoring', '0788888886', 'Kinyarwanda', 1, '2025-04-10 00:50:00', '2025-04-10 01:15:00'),
(9, 'Carine', 'carine.k@example.com', '$2y$10$Re0H7xI5OfUOj7sRZ1UdeOTaLS4rF2Uj5u3YZCQN3vly4MPnAMZJq', 'KABANDA Carine', 3, 'Finance and Planning', '2', 'Budget Unit', '0788888887', 'English', 1, '2025-03-22 02:40:00', '2025-03-22 03:10:00'),
(10, 'Elie', 'elie.m@example.com', '$2y$10$gXoCMUvUM9H1j8jVL3xJHO8uMe98a/1vU8VvZ57qJ6jF2rqL3PzK2', 'MUKIZA Elie', 2, 'Logistics & Operations', '3', 'Fleet Services', '0788888888', 'French', 1, '2025-03-30 04:10:00', '2025-03-30 04:40:00'),
(11, 'rutembeza', 'yvesrutembeza@gmail.com', '$2y$10$0YD.evRN0nXseyxqcHEzn.mMIlG3qmupj3fq8GRKJssp4c9lyBd/.', 'Yves RUTEMBEZA', 3, NULL, NULL, NULL, '0787461999', 'English', 1, '2025-04-22 10:06:40', '2025-04-22 23:58:14');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `forum_categories`
--
ALTER TABLE `forum_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `forum_replies`
--
ALTER TABLE `forum_replies`
  ADD PRIMARY KEY (`reply_id`),
  ADD KEY `topic_id` (`topic_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `forum_topics`
--
ALTER TABLE `forum_topics`
  ADD PRIMARY KEY (`topic_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `forum_categories`
--
ALTER TABLE `forum_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `forum_replies`
--
ALTER TABLE `forum_replies`
  MODIFY `reply_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `forum_topics`
--
ALTER TABLE `forum_topics`
  MODIFY `topic_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `forum_replies`
--
ALTER TABLE `forum_replies`
  ADD CONSTRAINT `fk_reply_topic` FOREIGN KEY (`topic_id`) REFERENCES `forum_topics` (`topic_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_reply_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `forum_topics`
--
ALTER TABLE `forum_topics`
  ADD CONSTRAINT `fk_topic_category_new` FOREIGN KEY (`category_id`) REFERENCES `forum_categories` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_topic_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
