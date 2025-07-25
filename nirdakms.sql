-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 18, 2025 at 12:42 PM
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
-- Table structure for table `activities`
--

CREATE TABLE `activities` (
  `activity_id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `action_type` varchar(50) NOT NULL COMMENT 'e.g., created, updated, deleted, commented',
  `description` text NOT NULL,
  `timestamp` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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

-- -- Dumping data for table `attendees`
--

INSERT INTO `attendees` (`attendee_id`, `event_id`, `email`, `name`, `rsvp_status`, `reminder_sent`, `created_at`) VALUES
(4, 4, 'test@example.com', NULL, 'pending', 0, '2025-04-24 22:29:34'),
(6, 6, 'yvesrutembeza@gmail.com', NULL, 'pending', 1, '2025-04-24 22:50:17'),
(7, 7, 'zayves111@gmail.com', NULL, 'pending', 1, '2025-04-24 23:18:39'),
(8, 8, 'zayves111@gmail.com', NULL, 'pending', 1, '2025-04-24 23:28:52'),
(10, 0, 'mahofoide@gmail.com', NULL, 'pending', 0, '2025-05-13 10:29:31'),
(11, 0, 'yvesrutembeza@gmail.com', NULL, 'pending', 0, '2025-05-13 13:07:46');

-- --------------------------------------------------------

--
-- Table structure for table `blocked_users`
--

CREATE TABLE `blocked_users` (
  `block_id` int(11) NOT NULL,
  `blocker_id` int(11) NOT NULL,
  `blocked_id` int(11) NOT NULL,
  `blocked_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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

-- -- Dumping data for table `chat_groups`
--

INSERT INTO `chat_groups` (`group_id`, `group_name`, `description`, `created_by`, `created_at`, `updated_at`, `is_active`) VALUES
(1, 'welcome', 'group welcome', 11, '2025-04-28 18:46:40', '2025-04-28 20:50:15', 1),
(2, 'nice', 'its good', 11, '2025-04-28 18:47:05', '2025-04-28 18:47:14', 1),
(3, 'family', 'family friends', 11, '2025-04-28 20:24:49', '2025-05-07 00:16:34', 1),
(4, 'YB_CREATIVITY', 'FOUNDATION', 11, '2025-04-28 20:25:14', '2025-04-28 20:25:14', 1),
(5, 'family group', 'welcome home', 11, '2025-04-28 22:15:46', '2025-04-28 22:15:46', 1),
(6, 'welcome home', 'wl', 11, '2025-04-28 22:16:29', '2025-04-29 21:08:36', 1),
(7, 'e', '', 11, '2025-04-29 21:31:41', '2025-04-29 21:31:41', 1),
(8, 'FAMILY', 'family chats', 11, '2025-04-29 21:38:04', '2025-04-29 21:38:04', 1),
(9, 'welcome', 'this is inv', 11, '2025-04-29 21:42:07', '2025-04-29 21:42:07', 1),
(10, 'ss', 'sns', 12, '2025-04-29 23:09:35', '2025-04-29 23:09:35', 1),
(11, 'mhhh', 'mg', 12, '2025-04-30 01:23:14', '2025-05-08 09:41:51', 1),
(12, 'welcome', '', 11, '2025-04-30 01:38:25', '2025-04-30 01:38:25', 1),
(13, 'NIRDA GROUP', 'NIRDA TEAM WORK', 11, '2025-04-30 07:52:19', '2025-05-13 11:37:08', 1),
(14, 'shsjs', '', 11, '2025-04-30 08:08:06', '2025-05-07 17:31:43', 1),
(15, 'ssss', 'ss', 11, '2025-05-04 20:14:18', '2025-05-07 00:15:09', 1),
(16, 'sksksks', 'gg', 11, '2025-05-13 11:28:25', '2025-05-13 11:29:07', 1),
(17, 'yes', 'welcome', 12, '2025-05-17 22:59:08', '2025-05-17 23:15:46', 1),
(18, 'imaba', 'wele', 12, '2025-05-18 09:44:09', '2025-05-18 09:44:55', 1);

-- --------------------------------------------------------

--
-- Table structure for table `files`
--

CREATE TABLE `files` (
  `id` int(11) NOT NULL,
  `filename` varchar(255) NOT NULL,
  `filepath` varchar(255) NOT NULL,
  `size` int(11) NOT NULL,
  `type` varchar(100) NOT NULL,
  `uploaded` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- -- Dumping data for table `files`
--

INSERT INTO `files` (`id`, `filename`, `filepath`, `size`, `type`, `uploaded`) VALUES
(44, '1267-UKWITEGETSE00024520250428101123.pdf', 'storage/6815ef6e65dfd.pdf', 371009, 'application/pdf', '2025-05-03 10:26:54'),
(55, 'ben.pdf', 'storage/681645a02dea5.pdf', 34230, 'application/pdf', '2025-05-03 16:34:40'),
(57, 'malnutrition app.pdf', 'storage/681660b085c61.pdf', 196556, 'application/pdf', '2025-05-03 18:30:08'),
(58, 'GRACEDOCUMENTS.pdf', 'storage/6817a6658cf4b.pdf', 407992, 'application/pdf', '2025-05-04 17:39:49'),
(59, '52-professional-foundations-certificate-yves-rutembeza.png', 'storage/681a3b5217a19.png', 244293, 'image/png', '2025-05-06 16:39:46'),
(60, 'congz.jpeg', 'storage/681c7b5669c5c.jpg', 81295, 'image/jpeg', '2025-05-08 09:37:26');

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

-- -- Dumping data for table `forum_categories`
--

INSERT INTO `forum_categories` (`id`, `category_name`, `description`, `permission`, `allowed_user_ids`, `allowed_group_ids`, `created_at`, `updated_at`) VALUES
(13, 'General', 'General discussions', 'public', NULL, NULL, '2025-04-23 19:28:01', '2025-04-23 19:28:01'),
(14, 'Announcements', 'Official announcements', 'public', NULL, NULL, '2025-04-23 19:28:01', '2025-04-23 19:28:01'),
(22, 'NIRDA meetings', 'meetings today', 'private', '9,6,8', '', '2025-04-22 10:00:20', '2025-04-22 10:00:20'),
(23, 'Nirdaforum1', 'this is the testing well forum in our system', 'public', '', '', '2025-04-22 21:07:14', '2025-04-22 21:07:14'),
(24, 'Nirda_datamanagement', 'this is the forum discussing in correcting the data a=of the industrial researching', 'private', '9,6,1,11', '', '2025-04-22 23:06:10', '2025-04-22 23:06:10'),
(25, 'NIRDA meetings2', 'testing forum again if is working properlry', 'private', '1,2', '', '2025-04-28 13:43:59', '2025-04-28 13:43:59'),
(26, 'chech next', 'now now', 'private', '12,11', '', '2025-04-30 00:03:19', '2025-04-30 00:03:19'),
(27, 'nice', 'welcome', 'private', '6,1', '', '2025-04-30 01:16:53', '2025-04-30 01:16:53'),
(28, 'RUTEMBEZA YVES', 'yyyyyyyyyyyyyy', 'public', '', '', '2025-05-13 10:50:49', '2025-05-13 10:50:49'),
(29, 'NIRDA meetings finally', 'this is the end task meeting ', 'private', '8,12,11', '', '2025-05-17 21:17:30', '2025-05-17 21:17:30');

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

-- -- Dumping data for table `forum_replies`
--

INSERT INTO `forum_replies` (`reply_id`, `topic_id`, `user_id`, `content`, `is_flagged`, `flag_reason`, `created_at`, `updated_at`) VALUES
(5, 4, 11, 'murakoze', 0, NULL, '2025-04-25 12:50:56', '2025-04-25 12:50:56'),
(6, 2, 11, 'nice to meet your', 0, NULL, '2025-04-25 12:51:09', '2025-04-25 12:51:09'),
(7, 4, 11, 'now its time to work together', 0, NULL, '2025-04-28 17:29:43', '2025-04-28 17:29:43'),
(8, 4, 11, 'ese nibyo koko', 0, NULL, '2025-04-28 17:29:50', '2025-04-28 17:29:50'),
(9, 3, 11, 'murakoze', 0, NULL, '2025-04-28 17:30:00', '2025-04-28 17:30:00'),
(16, 15, 11, 'hvgvh', 0, NULL, '2025-05-12 13:13:25', '2025-05-12 13:13:25'),
(26, 30, 12, 'sksks', 0, NULL, '2025-05-18 07:38:46', '2025-05-18 07:38:46'),
(27, 31, 12, 'jj\\n', 1, 'spam', '2025-05-18 07:39:22', '2025-05-18 08:15:21'),
(29, 31, 11, 'plagiarizm', 0, NULL, '2025-05-18 08:05:34', '2025-05-18 08:05:34'),
(32, 31, 11, 'mkkk', 0, NULL, '2025-05-18 08:15:13', '2025-05-18 08:15:13'),
(34, 5, 11, 'sis', 0, NULL, '2025-05-18 08:20:33', '2025-05-18 08:20:33'),
(37, 16, 11, 'ss', 0, NULL, '2025-05-18 08:48:58', '2025-05-18 08:48:58'),
(38, 34, 11, 'djdkd', 0, NULL, '2025-05-18 09:18:26', '2025-05-18 09:18:26'),
(39, 37, 11, 'nice again', 0, NULL, '2025-05-18 09:20:18', '2025-05-18 09:20:18'),
(40, 37, 11, 'merci cyane', 1, 'spam', '2025-05-18 09:20:27', '2025-05-18 09:23:35'),
(41, 37, 12, 'thanks again rutembeza', 1, 'inappropriate', '2025-05-18 09:22:46', '2025-05-18 09:23:07');

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

-- -- Dumping data for table `forum_subscriptions`
--

INSERT INTO `forum_subscriptions` (`subscription_id`, `topic_id`, `user_id`, `created_at`) VALUES
(0, 7, 11, '2025-04-23 22:32:42'),
(0, 8, 11, '2025-04-23 22:38:22'),
(0, 0, 11, '2025-04-25 12:49:43'),
(0, 0, 12, '2025-04-29 23:39:29'),
(0, 17, 11, '2025-05-13 09:01:29'),
(0, 27, 11, '2025-05-13 10:17:23'),
(0, 28, 11, '2025-05-13 10:58:55'),
(0, 5, 11, '2025-05-18 08:20:34'),
(0, 12, 11, '2025-05-18 08:23:59'),
(0, 30, 11, '2025-05-18 08:30:28'),
(0, 31, 11, '2025-05-18 08:30:37'),
(0, 35, 12, '2025-05-18 09:14:44'),
(0, 36, 12, '2025-05-18 09:15:04');

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

-- -- Dumping data for table `forum_topics`
--

INSERT INTO `forum_topics` (`topic_id`, `category_id`, `user_id`, `title`, `content`, `is_pinned`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 13, 1, 'First Topic', 'Content...', 0, 1, '2025-04-23 19:31:40', '2025-04-23 19:31:40'),
(2, 14, 1, 'Second Topic', 'Content...', 0, 1, '2025-04-23 19:31:40', '2025-04-23 19:31:40'),
(3, 13, 1, 'First Topic', 'Content...', 0, 1, '2025-04-23 19:36:24', '2025-04-23 19:36:24'),
(4, 14, 1, 'Second Topic', 'Content...', 0, 1, '2025-04-23 19:36:24', '2025-04-23 19:36:24'),
(5, 13, 1, 'First Topic', 'Content...', 0, 1, '2025-04-23 19:38:18', '2025-04-23 19:38:18'),
(6, 14, 1, 'Second Topic', 'Content...', 0, 1, '2025-04-23 19:38:18', '2025-04-23 19:38:18'),
(15, 0, 12, 'welcome', '<p>this is the goode day</p>', 0, 1, '2025-04-29 23:39:29', '2025-04-29 23:39:29'),
(16, 0, 12, 'amakuru', '<p>murakoze</p>', 0, 1, '2025-04-30 00:05:45', '2025-04-30 00:05:45'),
(30, 0, 12, 'amata', '<p>jhjk</p>', 0, 1, '2025-05-17 22:45:25', '2025-05-17 22:45:25'),
(31, 0, 12, 'welcome 4', '<p>thanks for watching</p>', 0, 1, '2025-05-18 07:34:31', '2025-05-18 07:34:31'),
(34, 0, 11, 'amazing', '<p>skksks</p>', 1, 1, '2025-05-18 08:51:18', '2025-05-18 09:18:37'),
(35, 0, 12, 'amazing ideas', '<p>thanks alot</p>', 0, 1, '2025-05-18 09:14:44', '2025-05-18 09:14:44'),
(36, 0, 12, 'hello', '<p>welcome</p>', 0, 1, '2025-05-18 09:15:04', '2025-05-18 09:15:04'),
(37, 0, 11, 'welcome', '<p>now days thanks very much</p>', 0, 1, '2025-05-18 09:19:56', '2025-05-18 09:19:56');

-- --------------------------------------------------------

--
-- Table structure for table `group_invitations`
--

CREATE TABLE `group_invitations` (
  `invitation_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `inviter_id` int(11) NOT NULL,
  `invitee_id` int(11) NOT NULL,
  `status` enum('pending','accepted','rejected') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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

-- -- Dumping data for table `group_members`
--

INSERT INTO `group_members` (`member_id`, `group_id`, `user_id`, `joined_at`, `is_admin`) VALUES
(1, 1, 11, '2025-04-28 18:46:40', 1),
(2, 2, 11, '2025-04-28 18:47:05', 1),
(3, 3, 11, '2025-04-28 20:24:49', 1),
(4, 4, 11, '2025-04-28 20:25:14', 1),
(5, 5, 11, '2025-04-28 22:15:46', 1),
(6, 6, 11, '2025-04-28 22:16:29', 1),
(7, 7, 11, '2025-04-29 21:31:41', 1),
(8, 8, 11, '2025-04-29 21:38:04', 1),
(9, 9, 11, '2025-04-29 21:42:07', 1),
(10, 10, 12, '2025-04-29 23:09:35', 1),
(11, 11, 12, '2025-04-30 01:23:14', 1),
(12, 12, 11, '2025-04-30 01:38:25', 1),
(13, 13, 11, '2025-04-30 07:52:19', 1),
(14, 14, 11, '2025-04-30 08:08:06', 1),
(15, 15, 11, '2025-05-04 20:14:18', 1),
(16, 15, 15, '2025-05-07 00:15:09', 0),
(22, 14, 12, '2025-05-07 15:04:03', 0),
(24, 11, 15, '2025-05-08 09:41:51', 0),
(26, 16, 11, '2025-05-13 11:28:25', 1),
(27, 16, 9, '2025-05-13 11:28:25', 0),
(29, 16, 2, '2025-05-13 11:28:55', 0),
(30, 13, 14, '2025-05-13 11:37:08', 0),
(31, 17, 12, '2025-05-17 22:59:08', 1),
(32, 17, 9, '2025-05-17 22:59:08', 0),
(33, 17, 6, '2025-05-17 22:59:08', 0),
(34, 18, 12, '2025-05-18 09:44:09', 1),
(35, 18, 9, '2025-05-18 09:44:09', 0),
(36, 18, 6, '2025-05-18 09:44:09', 0),
(37, 18, 1, '2025-05-18 09:44:55', 0),
(38, 18, 2, '2025-05-18 09:44:55', 0);

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

-- -- Dumping data for table `messages`
--

INSERT INTO `messages` (`message_id`, `conversation_id`, `group_id`, `sender_id`, `content`, `sent_at`, `status`, `is_deleted`) VALUES
(1, NULL, 1, 11, 'welcome', '2025-04-28 18:46:47', 'sent', 0),
(2, NULL, 1, 11, 'hello', '2025-04-28 18:46:52', 'sent', 0),
(3, NULL, 2, 11, 'jdjd', '2025-04-28 18:47:13', 'sent', 0),
(4, NULL, 2, 11, 'kdkd', '2025-04-28 18:47:14', 'sent', 0),
(5, 1, NULL, 1, 'Hello Yves, how are you?', '2025-04-28 20:30:05', 'read', 0),
(6, 1, NULL, 11, 'Hi Mahomet, I\'m doing well!', '2025-04-28 20:35:05', 'read', 0),
(7, 1, NULL, 1, 'Can we meet tomorrow?', '2025-04-28 20:40:05', 'delivered', 0),
(8, 1, NULL, 11, 'no tomorrow i will not be available let\'s talk to next tomorrow', '2025-04-28 20:41:37', 'sent', 0),
(9, 2, NULL, 11, 'Hi Christian!', '2025-04-28 20:29:45', 'read', 0),
(10, 2, NULL, 12, 'Hello Yves!', '2025-04-28 20:34:45', 'read', 0),
(11, 2, NULL, 11, 'How is the project going?', '2025-04-28 20:44:45', 'delivered', 0),
(12, NULL, 1, 11, 'no tomorrow i will not be available let\'s talk to next tomorrow', '2025-04-28 20:50:15', 'sent', 0),
(13, 2, NULL, 11, 'are you okk', '2025-04-28 20:59:04', 'sent', 0),
(14, NULL, 6, 11, 'bethher', '2025-04-28 22:20:02', 'sent', 0),
(15, 2, NULL, 11, 'nice', '2025-04-28 22:20:27', 'sent', 0),
(16, 2, NULL, 11, 'muraho neza', '2025-04-29 21:08:23', 'sent', 0),
(17, NULL, 6, 11, 'murakoze', '2025-04-29 21:08:36', 'sent', 0),
(18, 2, NULL, 12, 'aho se ni amahoro', '2025-04-30 01:23:30', 'sent', 0),
(19, 2, NULL, 11, 'yego aha ni amahoro', '2025-04-30 01:23:45', 'sent', 0),
(20, 2, NULL, 12, 'nice two meet you', '2025-04-30 01:26:01', 'sent', 0),
(21, 2, NULL, 11, 'nice two meet you to', '2025-04-30 01:26:28', 'sent', 0),
(22, 2, NULL, 12, 'yeah nice', '2025-04-30 01:26:39', 'sent', 0),
(23, 1, NULL, 11, 'umeze neza se', '2025-04-30 01:37:03', 'sent', 0),
(24, NULL, 13, 11, 'WELCOME', '2025-04-30 07:52:34', 'sent', 0),
(25, 2, NULL, 11, 'hello', '2025-04-30 07:53:19', 'sent', 0),
(26, NULL, 14, 11, 'WELCOME', '2025-04-30 08:08:17', 'sent', 0),
(27, 5, NULL, 15, 'hello', '2025-05-05 13:36:36', 'sent', 0),
(28, 6, NULL, 14, 'hello', '2025-05-05 13:37:20', 'sent', 0),
(29, 6, NULL, 15, 'umezute', '2025-05-05 13:37:45', 'sent', 0),
(30, 6, NULL, 14, 'saw wowe x', '2025-05-05 13:38:05', 'sent', 0),
(31, 6, NULL, 15, 'fresh 2', '2025-05-05 13:38:16', 'sent', 0),
(32, 6, NULL, 15, 'good', '2025-05-05 13:38:22', 'sent', 0),
(33, NULL, 14, 11, 'kjhgf', '2025-05-07 17:31:42', 'sent', 0),
(34, NULL, 14, 11, 'bhgv', '2025-05-07 17:31:43', 'sent', 0),
(35, 2, NULL, 11, 'jsjsks', '2025-05-13 08:55:56', 'sent', 0),
(36, 9, NULL, 11, 'welcpome', '2025-05-13 09:19:38', 'sent', 0),
(37, NULL, 16, 11, 'uhuhj', '2025-05-13 11:29:06', 'sent', 0),
(38, NULL, 16, 11, 'mkknk', '2025-05-13 11:29:07', 'sent', 0),
(39, 10, NULL, 16, 'hello', '2025-05-13 11:34:02', 'sent', 0),
(40, 10, NULL, 11, 'umezute x', '2025-05-13 11:34:20', 'sent', 0),
(41, 10, NULL, 16, 'fresh uratangaza iki', '2025-05-13 11:34:41', 'sent', 0),
(42, 2, NULL, 12, 'jbhg', '2025-05-17 20:58:14', 'sent', 0),
(43, NULL, 17, 12, 'welcome', '2025-05-17 23:01:42', 'sent', 0),
(44, 8, NULL, 12, 'smsms', '2025-05-18 09:43:58', 'sent', 0);

-- --------------------------------------------------------

--
-- Table structure for table `message_attachments`
--

CREATE TABLE `message_attachments` (
  `attachment_id` int(11) NOT NULL,
  `message_id` int(11) NOT NULL,
  `file_id` int(11) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_type` varchar(100) NOT NULL,
  `file_size` int(11) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `moderation_logs`
--

CREATE TABLE `moderation_logs` (
  `log_id` int(11) NOT NULL AUTO_INCREMENT,
  `admin_id` int(11) NOT NULL,
  `action_type` varchar(50) NOT NULL,
  `content_type` varchar(50) NOT NULL,
  `content_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `details` text DEFAULT NULL,
  `content_snapshot` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`log_id`),
  KEY `admin_id` (`admin_id`),
  KEY `user_id` (`user_id`)
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

-- -- Dumping data for table `private_conversations`
--

INSERT INTO `private_conversations` (`conversation_id`, `user1_id`, `user2_id`, `created_at`, `last_message_at`) VALUES
(1, 1, 11, '2025-04-28 20:40:05', '2025-04-30 01:37:03'),
(2, 11, 12, '2025-04-28 20:44:45', '2025-05-17 20:58:14'),
(3, 8, 11, '2025-04-29 23:45:19', NULL),
(4, 14, 7, '2025-05-05 13:34:07', NULL),
(5, 15, 13, '2025-05-05 13:36:31', '2025-05-05 13:36:36'),
(6, 14, 15, '2025-05-05 13:37:11', '2025-05-05 13:38:22'),
(8, 12, 9, '2025-05-07 00:21:38', '2025-05-18 09:43:58'),
(9, 11, 9, '2025-05-13 09:19:34', '2025-05-13 09:19:38'),
(10, 16, 11, '2025-05-13 11:33:59', '2025-05-13 11:34:41');

-- --------------------------------------------------------

--
-- Table structure for table `projects`
--

CREATE TABLE `projects` (
  `project_id` int(11) NOT NULL,
  `project_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `project_template` varchar(100) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `goal_title` varchar(255) DEFAULT NULL,
  `goal_description` text DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- -- Dumping data for table `projects`
--

INSERT INTO `projects` (`project_id`, `project_name`, `description`, `project_template`, `start_date`, `end_date`, `goal_title`, `goal_description`, `created_by`, `created_at`) VALUES
(1, 'CALLING PEOPLE WITH NEW PROJECT', 'qwertyuio', 'Team Collaboration', '2025-05-30', '2025-06-28', 'UI/UX DESIGN', 'asdfghjklqwertyui', NULL, '2025-05-04 11:11:08'),
(2, 'CALLING PEOPLE WITH NEW PROJECT', 'qwertyuio', 'Team Collaboration', '2025-05-30', '2025-06-28', 'UI/UX DESIGN', 'asdfghjklqwertyui', NULL, '2025-05-04 11:11:15'),
(3, 'CALLING PEOPLE WITH NEW PROJECT', 'qwertyuio', 'Team Collaboration', '2025-05-30', '2025-06-28', 'UI/UX DESIGN', 'asdfghjklqwertyui', NULL, '2025-05-04 11:12:48'),
(4, 'MOBILE DEVELOPING APP', 'qwertyuiop[', 'Research Project', '2025-05-29', '2025-06-08', 'backkend ', 'qwertyuiop', NULL, '2025-05-04 12:32:27'),
(5, 'database training', 'qwertyuio', 'Research Project', '2025-06-08', '2025-06-08', 'UI/UX DESIGN', 'qwertyui', NULL, '2025-05-04 14:46:36'),
(6, 'Tutor Connect ', 'this project aims at connecting teachers and students', 'Team Collaboration', '2025-05-05', '2025-06-11', 'UI/UX DESIGN', 'hvgdhxvcfcf', NULL, '2025-05-04 18:45:51'),
(7, 'Tutor Connect ', 'this project aims at connecting teachers and students', 'Team Collaboration', '2025-05-05', '2025-06-11', 'UI/UX DESIGN', 'hvgdhxvcfcf', NULL, '2025-05-04 18:48:53'),
(8, 'Access to Financetyhj', 'ggg', 'Team Collaboration', '2025-05-14', '2025-05-22', 'this and this', 'ggggg', NULL, '2025-05-05 14:04:58'),
(9, 'nirda collaboration', 'collaboration ends to next week', 'Team Collaboration', '2025-05-01', '2025-05-08', 'certificate', 'today is the day', NULL, '2025-05-06 16:39:10'),
(10, 'projectTest', 'EJEIDKDK\r\n', '', '2025-05-16', '2025-05-30', 'DKDKD', 'DKKDKD', NULL, '2025-05-08 09:36:17'),
(11, 'nirdat collaboration forum', 'dfft', 'Team Collaboration', '2025-05-02', '2025-05-10', 'the goals of this project is to provide effectives action to all ', 'ggcv', NULL, '2025-05-08 10:06:12'),
(12, 'nirdat collaboration forum', 'ggggghjj', 'Research Project', '2025-03-28', '2025-05-14', 'the goals of this project is to provide effectives action to all ', 'yyyyhhhh', NULL, '2025-05-13 11:15:06');

-- --------------------------------------------------------

--
-- Table structure for table `project_members`
--

CREATE TABLE `project_members` (
  `member_id` int(12) NOT NULL,
  `project_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `role` varchar(50) DEFAULT NULL,
  `joined_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- -- Dumping data for table `project_members`
--

INSERT INTO `project_members` (`member_id`, `project_id`, `user_id`, `role`, `joined_at`) VALUES
(11, 2, 6, 'digital_transformation_lead', '2025-05-04 11:11:15'),
(14, 3, 6, 'digital_transformation_lead', '2025-05-04 11:12:48'),
(17, 4, 6, 'digital_transformation_lead', '2025-05-04 12:32:27'),
(19, 5, 6, 'digital_transformation_lead', '2025-05-04 14:46:36'),
(21, 5, 3, 'governance_officer', '2025-05-04 16:28:35'),
(22, 1, 2, 'data_scientist', '2025-05-04 16:39:36'),
(23, 1, 3, 'department_head', '2025-05-04 17:31:09'),
(24, 4, 3, 'network_engineer', '2025-05-04 17:36:45'),
(25, 6, 2, 'data_scientist', '2025-05-04 18:45:51'),
(26, 7, 2, 'data_scientist', '2025-05-04 18:48:53'),
(27, 2, 15, 'contributor', '2025-05-05 13:42:26'),
(29, 5, 13, 'software_developer', '2025-05-05 13:54:51'),
(32, 8, 2, 'data_scientist', '2025-05-05 14:04:58'),
(33, 8, 4, 'cio', '2025-05-05 14:04:58'),
(34, 9, 12, 'media_specialist', '2025-05-06 16:39:10'),
(35, 10, 12, 'contributor', '2025-05-08 09:36:17'),
(36, 11, 9, 'contributor', '2025-05-08 10:06:12'),
(37, 11, 2, 'data_scientist', '2025-05-08 10:06:12'),
(38, 11, 11, 'contributor', '2025-05-08 10:08:23'),
(39, 12, 4, 'team_lead', '2025-05-13 11:15:06');

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

-- -- Dumping data for table `schedule_events`
--

INSERT INTO `schedule_events` (`event_id`, `eventTitle`, `startDateTime`, `endingDateTime`, `eventLocation`, `eventDescription`, `attend`, `Recurrence`, `emailReminder`, `appReminder`, `reminderTime`, `isActive`) VALUES
(4, 'Test Event', '2025-04-25 00:59:34', '2025-04-25 01:59:34', 'Test Location', NULL, NULL, NULL, NULL, NULL, 15, 1),
(0, 'NIRDA TEAM MEATING PROJECT', '2025-05-13 12:25:00', '2025-05-16 12:25:00', 'Huye', 'dfsgvbn bvcxz', 'mahofoide@gmail.com', 'daily', '0', '1', 15, 1),
(0, 'WELCOME2', '2025-05-03 15:07:00', '2025-05-07 15:07:00', 'KIGALI RWANDA', 'WELCOME', 'yvesrutembeza@gmail.com', 'daily', '1', '1', 15, 1);

-- --------------------------------------------------------

--
-- Table structure for table `starred_conversations`
--

CREATE TABLE `starred_conversations` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `conversation_id` int(11) DEFAULT NULL,
  `group_id` int(11) DEFAULT NULL,
  `is_starred` tinyint(1) DEFAULT 1,
  `starred_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `unstarred_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- -- Dumping data for table `starred_conversations`
--

INSERT INTO `starred_conversations` (`id`, `user_id`, `conversation_id`, `group_id`, `is_starred`, `starred_at`, `unstarred_at`) VALUES
(2, 11, 2, NULL, 0, '2025-05-07 00:25:28', '2025-05-13 09:16:56'),
(3, 11, 2, NULL, 1, '2025-05-13 09:16:58', NULL),
(6, 12, 2, NULL, 1, '2025-05-17 23:02:21', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tasks`
--

CREATE TABLE `tasks` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `status` enum('backlog','todo','in_progress','done') DEFAULT 'backlog',
  `priority` enum('low','medium','high') DEFAULT 'medium',
  `assignee_id` int(11) DEFAULT NULL,
  `deadline` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `project_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- -- Dumping data for table `tasks`
--

INSERT INTO `tasks` (`id`, `title`, `description`, `status`, `priority`, `assignee_id`, `deadline`, `created_at`, `updated_at`, `project_id`) VALUES
(22, 'Chriss ', '', 'backlog', 'low', 3, '0000-00-00', '2025-04-30 09:33:07', '2025-04-30 09:33:07', NULL),
(23, 'Chriss ', 'wertyuifdgghjnbb', 'done', 'medium', 2, '2025-05-09', '2025-04-30 09:33:48', '2025-04-30 09:33:48', NULL),
(24, 'Chriss ', 'ffwrr', 'todo', 'medium', 2, '2025-05-02', '2025-04-30 10:07:22', '2025-04-30 10:07:22', NULL),
(25, 'Chriss ', 'qwweryt', 'todo', 'low', 2, '2025-05-02', '2025-04-30 10:10:03', '2025-04-30 10:10:03', NULL),
(26, 'Chriss ', 'wertyu', 'in_progress', 'medium', 2, '2025-05-10', '2025-04-30 10:11:10', '2025-04-30 10:11:10', NULL),
(27, 'Chriss ', 'wertyu', 'in_progress', 'low', 2, '2025-05-28', '2025-05-03 10:11:43', '2025-05-03 10:11:43', NULL),
(28, 'Chriss ', 'cv', 'in_progress', 'medium', 3, '2025-05-03', '2025-05-03 10:12:18', '2025-05-03 10:12:18', NULL),
(29, 'Chriss Easy ', 'qwertyu', 'todo', 'low', 3, '2025-04-29', '2025-05-03 12:04:59', '2025-05-03 12:04:59', NULL),
(30, 'Chriss Easy ', '', 'backlog', 'medium', 0, '0000-00-00', '2025-05-03 12:05:32', '2025-05-03 12:05:32', NULL),
(31, 'authentication', 'allow user to input personal information', 'in_progress', 'high', 0, '2025-05-11', '2025-05-03 16:36:30', '2025-05-03 16:36:30', NULL),
(32, 'authentication', 'qwerty', 'done', 'low', 2, '2025-05-31', '2025-05-03 18:19:59', '2025-05-03 18:19:59', NULL),
(33, 'authentication', 'dfg', 'done', 'high', 3, '2025-06-08', '2025-05-03 18:21:25', '2025-05-03 18:21:25', NULL),
(34, 'authentication', '', 'in_progress', 'medium', 3, '2025-06-05', '2025-05-04 13:31:03', '2025-05-04 13:31:03', NULL),
(35, 'kwiga', 'ertyuio', 'backlog', 'high', 3, '2025-06-08', '2025-05-04 13:59:11', '2025-05-04 13:59:11', NULL),
(36, 'kwiga', 'asdfghjk.qwertyuio', 'in_progress', 'low', 2, '2025-05-31', '2025-05-04 17:42:54', '2025-05-04 17:42:54', NULL),
(37, 'kkkk', 'nvcg ', 'backlog', 'high', 12, '0000-00-00', '2025-05-06 16:40:20', '2025-05-06 16:40:20', NULL);

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

-- -- Dumping data for table `users`
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
(11, 'rutembeza', 'yvesrutembeza@gmail.com', '$2y$10$0YD.evRN0nXseyxqcHEzn.mMIlG3qmupj3fq8GRKJssp4c9lyBd/.', 'Yves RUTEMBEZA', 3, NULL, NULL, NULL, '0787461999', 'English', 1, '2025-04-22 08:06:40', '2025-05-18 07:22:20'),
(12, 'christian', 'crutembeza@gmail.com', '$2y$10$j4r3jBG.dkLa4EpIRadoTulQiQkg/6qsI8dyDjRiextONZnEVJnMq', 'RUTEMBEZA CHRISTIAN', 3, NULL, NULL, NULL, '0786436066', 'English', 1, '2025-04-28 13:50:51', '2025-05-18 09:21:17'),
(13, 'ndoli', 'ndolijeandamascene@gmail.com', '12Damasce12@', 'NDOLI Jean Damascene', 0, NULL, NULL, NULL, NULL, 'English', 1, '2025-05-05 13:28:10', NULL),
(14, 'ndolijean', 'jdamascene.ndoli@nirda.gov.rw', '$2y$10$H/5oDAJiGj/crJZsJ2oiq.eKv1XZlJd31FsQ3cj1uwbTJBq2GpYA2', 'NDOLI Jean Damascene', 3, NULL, NULL, NULL, '0789312765', 'English', 1, '2025-05-05 13:30:56', '2025-05-05 13:31:06'),
(15, 'Umutabyi', 'umutambyicompanyltd@gmail.com', '$2y$10$MzIi1E.mpJSUrI1Ydq7/7.2w5fb1kmY0xxQmm6kF5IkHOEn4Nmr3u', 'UMUTABYI', 3, NULL, NULL, NULL, '07884645328', 'English', 1, '2025-05-05 13:36:08', '2025-05-05 13:36:19'),
(16, 'ndolijeand', 'damasce@gmail.com', '$2y$10$xRu6yXuR1n3.w0N/aiLR6.uimi60l7xc92sQgQNCeM1yW6.1F.deO', 'ndoli', 3, NULL, NULL, NULL, 'ndoli', 'English', 1, '2025-05-13 11:33:08', '2025-05-13 11:33:16');

-- --------------------------------------------------------

--
-- Table structure for table `user_profiles`
--

CREATE TABLE `user_profiles` (
  `profile_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `status_message` varchar(255) DEFAULT NULL,
  `status` enum('online','away','busy','offline') DEFAULT 'offline',
  `theme_preference` varchar(50) DEFAULT 'light',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_status`
--

CREATE TABLE `user_status` (
  `user_id` int(11) NOT NULL,
  `is_online` tinyint(1) DEFAULT 0,
  `last_seen` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_warnings`
--

CREATE TABLE `user_warnings` (
  `warning_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `warning_type` varchar(50) NOT NULL,
  `content_id` int(11) NOT NULL,
  `content_type` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`warning_id`),
  KEY `user_id` (`user_id`),
  KEY `admin_id` (`admin_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- -- Dumping data for table `user_warnings`
--

INSERT INTO `user_warnings` (`warning_id`, `user_id`, `admin_id`, `warning_type`, `content_id`, `content_type`, `created_at`) VALUES
(1, 11, 1, 'spam', 0, 'post', '2025-04-25 12:50:56'),
(2, 12, 1, 'inappropriate', 0, 'post', '2025-04-25 12:51:09'),
(3, 11, 1, 'spam', 0, 'post', '2025-04-28 17:29:43'),
(4, 11, 1, 'spam', 0, 'post', '2025-04-28 17:29:50'),
(5, 11, 1, 'spam', 0, 'post', '2025-04-28 17:30:00'),
(6, 11, 1, 'spam', 0, 'post', '2025-05-12 13:13:25'),
(7, 12, 1, 'spam', 0, 'post', '2025-05-18 07:38:46'),
(8, 12, 1, 'spam', 1, 'post', '2025-05-18 07:39:22'),
(9, 11, 1, 'spam', 0, 'post', '2025-05-18 08:05:34'),
(10, 11, 1, 'spam', 0, 'post', '2025-05-18 08:15:13'),
(11, 11, 1, 'spam', 0, 'post', '2025-05-18 08:20:33'),
(12, 11, 1, 'spam', 0, 'post', '2025-05-18 08:48:58'),
(13, 11, 1, 'spam', 0, 'post', '2025-05-18 09:18:26'),
(14, 11, 1, 'spam', 0, 'post', '2025-05-18 09:20:18'),
(15, 11, 1, 'spam', 1, 'post', '2025-05-18 09:20:27'),
(16, 12, 1, 'spam', 1, 'post', '2025-05-18 09:22:46'),
(17, 11, 1, 'spam', 1, 'post', '2025-05-18 09:23:07');

-- --------------------------------------------------------

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activities`
--
ALTER TABLE `activities`
  ADD PRIMARY KEY (`activity_id`),
  ADD KEY `project_id` (`project_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `attendees`
--
ALTER TABLE `attendees`
  ADD PRIMARY KEY (`attendee_id`);

--
-- Indexes for table `blocked_users`
--
ALTER TABLE `blocked_users`
  ADD PRIMARY KEY (`block_id`),
  ADD UNIQUE KEY `unique_block` (`blocker_id`,`blocked_id`),
  ADD KEY `blocked_id` (`blocked_id`);

--
-- Indexes for table `chat_groups`
--
ALTER TABLE `chat_groups`
  ADD PRIMARY KEY (`group_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `files`
--
ALTER TABLE `files`
  ADD PRIMARY KEY (`id`);

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
  ADD KEY `idx_topic` (`topic_id`),
  ADD KEY `idx_user` (`user_id`);

--
-- Indexes for table `forum_topics`
--
ALTER TABLE `forum_topics`
  ADD PRIMARY KEY (`topic_id`),
  ADD KEY `idx_category` (`category_id`),
  ADD KEY `idx_user` (`user_id`);

--
-- Indexes for table `group_invitations`
--
ALTER TABLE `group_invitations`
  ADD PRIMARY KEY (`invitation_id`),
  ADD KEY `group_id` (`group_id`),
  ADD KEY `inviter_id` (`inviter_id`),
  ADD KEY `invitee_id` (`invitee_id`);

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
-- Indexes for table `message_attachments`
--
ALTER TABLE `message_attachments`
  ADD PRIMARY KEY (`attachment_id`),
  ADD KEY `message_id` (`message_id`);

--
-- Indexes for table `moderation_logs`
--
ALTER TABLE `moderation_logs`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `admin_id` (`admin_id`);

--
-- Indexes for table `private_conversations`
--
ALTER TABLE `private_conversations`
  ADD PRIMARY KEY (`conversation_id`),
  ADD UNIQUE KEY `unique_conversation` (`user1_id`,`user2_id`),
  ADD KEY `user2_id` (`user2_id`);

--
-- Indexes for table `projects`
--
ALTER TABLE `projects`
  ADD PRIMARY KEY (`project_id`);

--
-- Indexes for table `project_members`
--
ALTER TABLE `project_members`
  ADD PRIMARY KEY (`member_id`);

--
-- Indexes for table `starred_conversations`
--
ALTER TABLE `starred_conversations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_star` (`user_id`,`conversation_id`,`group_id`),
  ADD KEY `conversation_id` (`conversation_id`),
  ADD KEY `group_id` (`group_id`);

--
-- Indexes for table `tasks`
--
ALTER TABLE `tasks`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `user_profiles`
--
ALTER TABLE `user_profiles`
  ADD PRIMARY KEY (`profile_id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- Indexes for table `user_status`
--
ALTER TABLE `user_status`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `user_warnings`
--
ALTER TABLE `user_warnings`
  ADD PRIMARY KEY (`warning_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `admin_id` (`admin_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activities`
--
ALTER TABLE `activities`
  MODIFY `activity_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `attendees`
--
ALTER TABLE `attendees`
  MODIFY `attendee_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `blocked_users`
--
ALTER TABLE `blocked_users`
  MODIFY `block_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `chat_groups`
--
ALTER TABLE `chat_groups`
  MODIFY `group_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `files`
--
ALTER TABLE `files`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT for table `forum_categories`
--
ALTER TABLE `forum_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `forum_replies`
--
ALTER TABLE `forum_replies`
  MODIFY `reply_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `forum_topics`
--
ALTER TABLE `forum_topics`
  MODIFY `topic_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `group_invitations`
--
ALTER TABLE `group_invitations`
  MODIFY `invitation_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `group_members`
--
ALTER TABLE `group_members`
  MODIFY `member_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `message_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT for table `message_attachments`
--
ALTER TABLE `message_attachments`
  MODIFY `attachment_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `moderation_logs`
--
ALTER TABLE `moderation_logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `private_conversations`
--
ALTER TABLE `private_conversations`
  MODIFY `conversation_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `projects`
--
ALTER TABLE `projects`
  MODIFY `project_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `project_members`
--
ALTER TABLE `project_members`
  MODIFY `member_id` int(12) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `starred_conversations`
--
ALTER TABLE `starred_conversations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `tasks`
--
ALTER TABLE `tasks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `user_profiles`
--
ALTER TABLE `user_profiles`
  MODIFY `profile_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_warnings`
--
ALTER TABLE `user_warnings`
  MODIFY `warning_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activities`
--
ALTER TABLE `activities`
  ADD CONSTRAINT `activities_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `projects` (`project_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `activities_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `blocked_users`
--
ALTER TABLE `blocked_users`
  ADD CONSTRAINT `fk_blocked_user` FOREIGN KEY (`blocked_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_blocker_user` FOREIGN KEY (`blocker_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `chat_groups`
--
ALTER TABLE `chat_groups`
  ADD CONSTRAINT `fk_group_creator` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `group_invitations`
--
ALTER TABLE `group_invitations`
  ADD CONSTRAINT `fk_invitation_group` FOREIGN KEY (`group_id`) REFERENCES `chat_groups` (`group_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_invitation_invitee` FOREIGN KEY (`invitee_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_invitation_inviter` FOREIGN KEY (`inviter_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

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
-- Constraints for table `message_attachments`
--
ALTER TABLE `message_attachments`
  ADD CONSTRAINT `fk_attachment_message` FOREIGN KEY (`message_id`) REFERENCES `messages` (`message_id`) ON DELETE CASCADE;

--
-- Constraints for table `moderation_logs`
--
ALTER TABLE `moderation_logs`
  ADD CONSTRAINT `moderation_logs_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `private_conversations`
--
ALTER TABLE `private_conversations`
  ADD CONSTRAINT `fk_conversation_user1` FOREIGN KEY (`user1_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_conversation_user2` FOREIGN KEY (`user2_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `starred_conversations`
--
ALTER TABLE `starred_conversations`
  ADD CONSTRAINT `fk_starred_conversation` FOREIGN KEY (`conversation_id`) REFERENCES `private_conversations` (`conversation_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_starred_group` FOREIGN KEY (`group_id`) REFERENCES `chat_groups` (`group_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_starred_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `user_profiles`
--
ALTER TABLE `user_profiles`
  ADD CONSTRAINT `fk_profile_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `user_status`
--
ALTER TABLE `user_status`
  ADD CONSTRAINT `fk_status_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `user_warnings`
--
ALTER TABLE `user_warnings`
  ADD CONSTRAINT `user_warnings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `user_warnings_ibfk_2` FOREIGN KEY (`admin_id`) REFERENCES `users` (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
