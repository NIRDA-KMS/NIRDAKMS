!-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 28, 2025 at 04:03 PM
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
-- Table structure for table `attendees`
--

CREATE TABLE `attendees` (
  `attendee_id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `rsvp_status` enum('pending','confirmed','declined') DEFAULT 'pending',
  `reminder_sent` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attendees`
--

INSERT INTO `attendees` (`attendee_id`, `event_id`, `email`, `name`, `rsvp_status`, `reminder_sent`, `created_at`) VALUES
(4, 4, 'test@example.com', NULL, 'pending', 0, '2025-04-24 22:29:34'),
(6, 6, 'yvesrutembeza@gmail.com', NULL, 'pending', 1, '2025-04-24 22:50:17'),
(7, 7, 'zayves111@gmail.com', NULL, 'pending', 1, '2025-04-24 23:18:39'),
(8, 8, 'zayves111@gmail.com', NULL, 'pending', 1, '2025-04-24 23:28:52');

-- --------------------------------------------------------

--
-- Table structure for table `chat_groups`
--

CREATE TABLE `chat_groups` (
  `group_id` int(11) NOT NULL,
  `group_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(24, 'Nirda_datamanagement', 'this is the forum discussing in correcting the data a=of the industrial researching', 'private', '9,6,1,11', '', '2025-04-22 23:06:10', '2025-04-22 23:06:10'),
(0, 'NIRDA meetings2', 'testing forum again if is working properlry', 'private', '1,2', '', '2025-04-28 13:43:59', '2025-04-28 13:43:59');

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

--
-- Dumping data for table `forum_replies`
--

INSERT INTO `forum_replies` (`reply_id`, `topic_id`, `user_id`, `content`, `is_flagged`, `flag_reason`, `created_at`, `updated_at`) VALUES
(1, 7, 11, 'yes welcome', 0, NULL, '2025-04-23 22:33:07', '2025-04-23 22:33:07'),
(2, 7, 11, 'are you ready', 0, NULL, '2025-04-23 22:33:23', '2025-04-23 22:33:23'),
(0, 7, 11, 'welcome why no replies yet', 0, NULL, '2025-04-25 12:50:30', '2025-04-25 12:50:30'),
(0, 7, 11, 'this is work but others dont\\n', 0, NULL, '2025-04-25 12:50:45', '2025-04-25 12:50:45'),
(0, 4, 11, 'murakoze', 0, NULL, '2025-04-25 12:50:56', '2025-04-25 12:50:56'),
(0, 2, 11, 'nice to meet your', 0, NULL, '2025-04-25 12:51:09', '2025-04-25 12:51:09');

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

--
-- Dumping data for table `forum_subscriptions`
--

INSERT INTO `forum_subscriptions` (`subscription_id`, `topic_id`, `user_id`, `created_at`) VALUES
(0, 7, 11, '2025-04-23 22:32:42'),
(0, 8, 11, '2025-04-23 22:38:22'),
(0, 0, 11, '2025-04-25 12:49:43');

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
(6, 14, 1, 'Second Topic', 'Content...', 0, 1, '2025-04-23 19:38:18', '2025-04-23 19:38:18'),
(7, 13, 11, 'welcome', '<p>now now</p>', 0, 1, '2025-04-23 22:32:42', '2025-04-23 22:32:42'),
(8, 13, 11, 'welcome', '<p>amata</p>', 0, 1, '2025-04-23 22:38:22', '2025-04-23 22:38:22'),
(0, 14, 11, 'muraho', '<p>amazina nitwa rutembeza yves</p>', 0, 1, '2025-04-25 12:49:43', '2025-04-25 12:49:43'),
(0, 13, 11, 'narbi', '<p>dksks</p>', 0, 1, '2025-04-25 12:51:43', '2025-04-25 12:51:43'),
(0, 22, 11, 'yego se', '<p>nwmwmw</p>', 0, 1, '2025-04-25 12:52:30', '2025-04-25 12:52:30');

-- --------------------------------------------------------

--
-- Table structure for table `group_members`
--

CREATE TABLE `group_members` (
  `member_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `joined_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_admin` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `message_id` int(11) NOT NULL,
  `conversation_id` int(11) DEFAULT NULL,
  `group_id` int(11) DEFAULT NULL,
  `sender_id` int(11) NOT NULL,
  `content` text NOT NULL,
  `sent_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('sent','delivered','read') DEFAULT 'sent',
  `is_deleted` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `private_conversations`
--

CREATE TABLE `private_conversations` (
  `conversation_id` int(11) NOT NULL,
  `user1_id` int(11) NOT NULL,
  `user2_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_message_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `schedule_events`
--
CREATE TABLE `schedule_events` (
  `event_id` int(11) NOT NULL AUTO_INCREMENT,
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
  `isActive` tinyint(1) DEFAULT 1,
  `rsvp_status` ENUM('pending', 'confirmed', 'declined') DEFAULT 'pending' AFTER `isActive`;
  PRIMARY KEY (`event_id`)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `schedule_events`
--

INSERT INTO `schedule_events` (`event_id`, `eventTitle`, `startDateTime`, `endingDateTime`, `eventLocation`, `eventDescription`, `attend`, `Recurrence`, `emailReminder`, `appReminder`, `reminderTime`, `isActive`) VALUES
(4, 'Test Event', '2025-04-25 00:59:34', '2025-04-25 01:59:34', 'Test Location', NULL, NULL, NULL, NULL, NULL, 15, 1),
(6, 'compainy youth', '2025-04-12 00:49:00', '2025-04-26 00:49:00', 'kigali Rwanda', 'we', 'yvesrutembeza@gmail.com', 'weekly', '1', '0', 60, 1),
(7, 'event 3', '2025-04-04 01:17:00', '2025-04-05 01:17:00', 'KIGALI RWANDA', 'attendees of event3', 'zayves111@gmail.com', 'daily', '1', '1', 15, 1),
(8, 'codebase', '2025-04-30 01:26:00', '2025-05-01 01:27:00', 'kigali Rwanda', 'welcome to code base', 'zayves111@gmail.com', 'daily', '1', '0', 15, 1);

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
(1, 'Mahomet', 'mahofoide@gmail.com', '$2y$10$xQAq50zZilsxp.7O3TZsFu.fRJ3xGpmPx.Opx/r9jn5vzUquDS5W2', 'KAZUBWENGE Mahomet', 1, 'Knowledge Management and Operational monitoring', '0', 'Technology Monitoring and Knowledge Management', '0788359461', 'English', 1, '2024-07-14 00:14:45', '2024-07-14 01:11:18'),
(2, 'Kativire', 'bisinfo.backet@gmail.com', '$2y$10$kTBdYRQoY1dYiz9DC5XENeyVfv4bNdAjdPH/4F3QGbAFRO9XruiCW', 'Mahomet Kativire', 1, 'OP &KM', '0', 'TM&KM', '1788346231', 'Kinyarwanda', 1, '2025-03-11 04:39:02', '2025-03-11 04:44:17'),
(3, 'Aimee', 'aimee.ndoli@example.com', '$2y$10$7UQreEvgM5j3vIj1J2XzjO1e57/4xD/fp5yNaPVOROejLfzHqZmLC', 'NDOLI Aimee', 2, 'Data Analysis', '1', 'Monitoring Unit', '0788888881', 'French', 1, '2025-01-10 00:20:15', '2025-01-11 01:10:11'),
(4, 'Eric', 'eric.ndoli@example.com', '$2y$10$WfPv3j5jNSb5xeKKoxt4XeYX.l7ROKeWZf6jzHgFEXwF0ReUnmZUy', 'NDOLI Eric', 2, 'ICT Department', '1', 'Support & Services', '0788888882', 'English', 1, '2025-02-20 02:00:00', '2025-02-20 02:45:00'),
(5, 'Josiane', 'josiane.m@example.com', '$2y$10$aqaNQ9DnHULt1zYK9ZL/yuLtP6xPu8JZu6wElZ8n51/5P3UVEA77m', 'UMUTONI Josiane', 1, 'Communications', '2', 'Public Relations', '0788888883', 'Kinyarwanda', 1, '2025-03-01 01:00:00', '2025-03-01 01:30:00'),
(6, 'Claude', 'claude.k@example.com', '$2y$10$zL1qfxFqFOAFpLsL4iKZoOlSyk45sjk7SgL5quJ2xIO.PYdDjUpXi', 'KAMANA Claude', 3, 'Research and Development', '3', 'Innovation Unit', '0788888884', 'French', 1, '2025-03-31 23:30:00', '2025-04-01 00:00:00'),
(7, 'Beata', 'beata.r@example.com', '$2y$10$A2FYO1m1TrX3fKYZzM9Z8.xQ2ybBvE9qFZbTI7Egr0nKzL6FuNeyG', 'RUTAYISIRE Beata', 2, 'Project Management', '1', 'Execution Team', '0788888885', 'English', 1, '2025-03-15 03:20:00', '2025-03-15 03:45:00'),
(8, 'Yves', 'yves.niy@example.com', '$2y$10$NpFJz8zR4nZ0bUZK4OFE1u4VBMv88JUNX6YHcRMF5bUJ2r59mjPiG', 'NIYONSENGA Yves', 1, 'Quality Assurance', '2', 'QA Monitoring', '0788888886', 'Kinyarwanda', 1, '2025-04-09 22:50:00', '2025-04-09 23:15:00'),
(9, 'Carine', 'carine.k@example.com', '$2y$10$Re0H7xI5OfUOj7sRZ1UdeOTaLS4rF2Uj5u3YZCQN3vly4MPnAMZJq', 'KABANDA Carine', 3, 'Finance and Planning', '2', 'Budget Unit', '0788888887', 'English', 1, '2025-03-22 00:40:00', '2025-03-22 01:10:00'),
(10, 'Elie', 'elie.m@example.com', '$2y$10$gXoCMUvUM9H1j8jVL3xJHO8uMe98a/1vU8VvZ57qJ6jF2rqL3PzK2', 'MUKIZA Elie', 2, 'Logistics & Operations', '3', 'Fleet Services', '0788888888', 'French', 1, '2025-03-30 02:10:00', '2025-03-30 02:40:00'),
(11, 'rutembeza', 'yvesrutembeza@gmail.com', '$2y$10$0YD.evRN0nXseyxqcHEzn.mMIlG3qmupj3fq8GRKJssp4c9lyBd/.', 'Yves RUTEMBEZA', 3, NULL, NULL, NULL, '0787461999', 'English', 1, '2025-04-22 08:06:40', '2025-04-28 13:32:34'),
(12, 'christian', 'crutembeza@gmail.com', '$2y$10$j4r3jBG.dkLa4EpIRadoTulQiQkg/6qsI8dyDjRiextONZnEVJnMq', 'RUTEMBEZA CHRISTIAN', 3, NULL, NULL, NULL, '0786436066', 'English', 1, '2025-04-28 13:50:51', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_status`
--

CREATE TABLE `user_status` (
  `user_id` int(11) NOT NULL,
  `is_online` tinyint(1) DEFAULT 0,
  `last_seen` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attendees`
--
ALTER TABLE `attendees`
  ADD PRIMARY KEY (`attendee_id`);

--
-- Indexes for table `chat_groups`
--
ALTER TABLE `chat_groups`
  ADD PRIMARY KEY (`group_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `group_members`
--
ALTER TABLE `group_members`
  ADD PRIMARY KEY (`member_id`),
  ADD UNIQUE KEY `unique_membership` (`group_id`,`user_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`message_id`),
  ADD KEY `conversation_id` (`conversation_id`),
  ADD KEY `group_id` (`group_id`),
  ADD KEY `sender_id` (`sender_id`);

--
-- Indexes for table `private_conversations`
--
ALTER TABLE `private_conversations`
  ADD PRIMARY KEY (`conversation_id`),
  ADD UNIQUE KEY `unique_conversation` (`user1_id`,`user2_id`),
  ADD KEY `user2_id` (`user2_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `user_status`
--
ALTER TABLE `user_status`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attendees`
--
ALTER TABLE `attendees`
  MODIFY `attendee_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `chat_groups`
--
ALTER TABLE `chat_groups`
  MODIFY `group_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `group_members`
--
ALTER TABLE `group_members`
  MODIFY `member_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `message_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `private_conversations`
--
ALTER TABLE `private_conversations`
  MODIFY `conversation_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `chat_groups`
--
ALTER TABLE `chat_groups`
  ADD CONSTRAINT `fk_group_creator` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `group_members`
--
ALTER TABLE `group_members`
  ADD CONSTRAINT `fk_group_member` FOREIGN KEY (`group_id`) REFERENCES `chat_groups` (`group_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_member_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `fk_message_conversation` FOREIGN KEY (`conversation_id`) REFERENCES `private_conversations` (`conversation_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_message_group` FOREIGN KEY (`group_id`) REFERENCES `chat_groups` (`group_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_message_sender` FOREIGN KEY (`sender_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `private_conversations`
--
ALTER TABLE `private_conversations`
  ADD CONSTRAINT `fk_conversation_user1` FOREIGN KEY (`user1_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_conversation_user2` FOREIGN KEY (`user2_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `user_status`
--
ALTER TABLE `user_status`
  ADD CONSTRAINT `fk_status_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

DROP TABLE IF EXISTS project_members;
DROP TABLE IF EXISTS project_goals;
DROP TABLE IF EXISTS projects;


CREATE TABLE projects (
    id INT NOT NULL AUTO_INCREMENT,
    project_name VARCHAR(255) NOT NULL,
    description TEXT,
    project_template VARCHAR(255),
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    goal_title VARCHAR(255),
    goal_description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


CREATE TABLE project_members (
    id INT NOT NULL AUTO_INCREMENT,
    project_id INT UNSIGNED NOT NULL,  
    user_id INT UNSIGNED NOT NULL,     
    role VARCHAR(255) NOT NULL,
    joined_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


CREATE TABLE project_goals (
    id INT NOT NULL AUTO_INCREMENT,
    project_id INT UNSIGNED NOT NULL,  
    goal_title VARCHAR(255) NOT NULL,
    goal_description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
