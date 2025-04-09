-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 08, 2025 at 01:05 PM
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
-- Database: `research_repository`
--

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `comment_id` int(11) NOT NULL,
  `document_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `content` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `content_moderation`
--

CREATE TABLE `content_moderation` (
  `moderation_id` int(11) NOT NULL,
  `content_type` enum('document','forum_topic','forum_reply') NOT NULL,
  `content_id` int(11) NOT NULL,
  `moderation_status` enum('pending','approved','rejected') DEFAULT 'pending',
  `moderated_by` int(11) DEFAULT NULL,
  `moderated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `reason` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `custom_fields`
--

CREATE TABLE `custom_fields` (
  `field_id` int(11) NOT NULL,
  `field_name` varchar(100) NOT NULL,
  `field_type` varchar(50) NOT NULL,
  `is_required` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `documents`
--

CREATE TABLE `documents` (
  `document_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_type` varchar(50) NOT NULL,
  `file_size` int(11) NOT NULL,
  `document_type` varchar(50) NOT NULL,
  `language` enum('English','French','Kinyarwanda') NOT NULL,
  `uploaded_by` int(11) DEFAULT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_modified` timestamp NULL DEFAULT NULL,
  `current_version` int(11) DEFAULT 1,
  `folder_id` int(11) DEFAULT NULL,
  `access_permissions` varchar(50) DEFAULT NULL,
  `creation_date` date DEFAULT NULL,
  `author` varchar(100) DEFAULT NULL,
  `owner` int(11) DEFAULT NULL,
  `version_number` int(11) DEFAULT 1,
  `page_count` int(11) DEFAULT NULL,
  `word_count` int(11) DEFAULT NULL,
  `duration` time DEFAULT NULL,
  `resolution` varchar(20) DEFAULT NULL,
  `publication_status` enum('Draft','Under Review','Published','Archived') DEFAULT 'Draft',
  `scheduled_publication_date` datetime DEFAULT NULL,
  `expiration_date` datetime DEFAULT NULL,
  `review_date` date DEFAULT NULL,
  `last_review_date` date DEFAULT NULL,
  `next_review_date` date DEFAULT NULL,
  `view_count` int(11) DEFAULT 0,
  `download_count` int(11) DEFAULT 0,
  `last_accessed_date` datetime DEFAULT NULL,
  `confidentiality_level` enum('Public','Confidential','Top Secret') DEFAULT 'Public',
  `retention_period` varchar(50) DEFAULT NULL,
  `disposition_instructions` text DEFAULT NULL,
  `file_checksum` varchar(64) DEFAULT NULL,
  `software_used` varchar(100) DEFAULT NULL,
  `hardware_used` varchar(100) DEFAULT NULL,
  `encoding_information` text DEFAULT NULL,
  `color_space` varchar(20) DEFAULT NULL,
  `country_of_origin` varchar(100) DEFAULT NULL,
  `region` varchar(100) DEFAULT NULL,
  `gps_coordinates` varchar(50) DEFAULT NULL,
  `doi` varchar(50) DEFAULT NULL,
  `isbn` varchar(20) DEFAULT NULL,
  `citation_format` text DEFAULT NULL,
  `accessibility_features` text DEFAULT NULL,
  `accessibility_compliance_level` varchar(50) DEFAULT NULL,
  `quality_assurance_status` varchar(50) DEFAULT NULL,
  `quality_check_date` date DEFAULT NULL,
  `quality_checker` int(11) DEFAULT NULL,
  `archival_status` varchar(50) DEFAULT NULL,
  `archive_date` date DEFAULT NULL,
  `archive_location` varchar(255) DEFAULT NULL,
  `current_workflow_step` varchar(100) DEFAULT NULL,
  `next_action_required` text DEFAULT NULL,
  `next_action_due_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `document_approvers`
--

CREATE TABLE `document_approvers` (
  `document_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `approval_status` enum('Pending','Approved','Rejected') DEFAULT NULL,
  `approval_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `document_areas`
--

CREATE TABLE `document_areas` (
  `document_id` int(11) NOT NULL,
  `area_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `document_contributors`
--

CREATE TABLE `document_contributors` (
  `document_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `contribution_type` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `document_custom_fields`
--

CREATE TABLE `document_custom_fields` (
  `document_id` int(11) NOT NULL,
  `field_id` int(11) NOT NULL,
  `field_value` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `document_references`
--

CREATE TABLE `document_references` (
  `document_id` int(11) NOT NULL,
  `referenced_document_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `document_reviewers`
--

CREATE TABLE `document_reviewers` (
  `document_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `review_status` enum('Pending','Approved','Rejected') DEFAULT NULL,
  `review_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `document_tags`
--

CREATE TABLE `document_tags` (
  `document_id` int(11) NOT NULL,
  `tag_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `document_taxonomies`
--

CREATE TABLE `document_taxonomies` (
  `document_id` int(11) NOT NULL,
  `term_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `document_versions`
--

CREATE TABLE `document_versions` (
  `version_id` int(11) NOT NULL,
  `document_id` int(11) DEFAULT NULL,
  `version_number` int(11) DEFAULT NULL,
  `file_path` varchar(255) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `version_notes` text DEFAULT NULL,
  `change_log` text DEFAULT NULL,
  `editor` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `folders`
--

CREATE TABLE `folders` (
  `folder_id` int(11) NOT NULL,
  `folder_name` varchar(50) NOT NULL,
  `parent_folder_id` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `access_permissions` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `folders`
--

INSERT INTO `folders` (`folder_id`, `folder_name`, `parent_folder_id`, `description`, `created_by`, `created_at`, `access_permissions`) VALUES
(4, 'Projects', 4, 'this folder1', NULL, '2024-07-16 17:43:56', 'Public');

-- --------------------------------------------------------

--
-- Table structure for table `folder_taxonomies`
--

CREATE TABLE `folder_taxonomies` (
  `folder_id` int(11) NOT NULL,
  `taxonomy_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `folder_taxonomies`
--

INSERT INTO `folder_taxonomies` (`folder_id`, `taxonomy_id`) VALUES
(4, 11);

-- --------------------------------------------------------

--
-- Table structure for table `forums`
--

CREATE TABLE `forums` (
  `forum_id` int(11) NOT NULL,
  `forum_title` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `associated_project_id` int(11) DEFAULT NULL,
  `access_permissions` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `forum_replies`
--

CREATE TABLE `forum_replies` (
  `reply_id` int(11) NOT NULL,
  `topic_id` int(11) DEFAULT NULL,
  `content` text NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `forum_topics`
--

CREATE TABLE `forum_topics` (
  `topic_id` int(11) NOT NULL,
  `forum_id` int(11) DEFAULT NULL,
  `topic_title` varchar(200) NOT NULL,
  `content` text NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ip`
--

CREATE TABLE `ip` (
  `id` int(11) NOT NULL,
  `document_id` int(11) DEFAULT NULL,
  `ip_type` enum('Patent','Copyright','Trademark','Trade Secret') NOT NULL,
  `registration_number` varchar(50) DEFAULT NULL,
  `filing_date` date DEFAULT NULL,
  `ip_status` enum('Pending','Approved','Rejected') DEFAULT 'Pending',
  `approval_date` date DEFAULT NULL,
  `expiration_date` date DEFAULT NULL,
  `licensing_information` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `knowledge_areas`
--

CREATE TABLE `knowledge_areas` (
  `area_id` int(11) NOT NULL,
  `area_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `parent_area_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notice_messages`
--

CREATE TABLE `notice_messages` (
  `id` int(11) NOT NULL,
  `message` text NOT NULL,
  `is_active` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `notice_messages`
--

INSERT INTO `notice_messages` (`id`, `message`, `is_active`, `created_at`, `updated_at`) VALUES
(33, 'Welcome to NIRDA Knowledge Management System which is Under Development! We are excited to have you here. Check out our new features and do not forget to complete your profile.', 1, '2024-07-14 14:08:05', '2025-03-11 13:02:20');

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `token` varchar(64) NOT NULL,
  `expiry` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `permission_id` int(11) NOT NULL,
  `permission_name` varchar(50) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`permission_id`, `permission_name`, `description`) VALUES
(1, 'repository_upload_file', 'Ability to upload files to the repository'),
(2, 'repository_create_folder', 'Ability to create folders in the repository'),
(3, 'repository_version_history', 'Access to view and manage version history'),
(4, 'repository_manage_knowledge_areas', 'Ability to manage knowledge areas'),
(5, 'repository_manage_documents', 'Ability to manage documents in the repository'),
(6, 'repository_generate_reports', 'Ability to generate reports from the repository'),
(7, 'search_perform_advanced', 'Ability to perform advanced searches'),
(8, 'search_manage_recent', 'Ability to manage recent searches'),
(9, 'search_save', 'Ability to save searches'),
(10, 'search_export_results', 'Ability to export search results'),
(11, 'collaboration_access_forum', 'Access to the discussion forum'),
(12, 'collaboration_use_chat', 'Ability to use the chat feature'),
(13, 'collaboration_manage_projects', 'Ability to manage projects'),
(14, 'collaboration_invite_members', 'Ability to invite new members'),
(15, 'collaboration_schedule_events', 'Ability to schedule events'),
(16, 'analytics_view_usage_reports', 'Ability to view usage reports'),
(17, 'analytics_access_dashboard', 'Access to the analytics dashboard'),
(18, 'analytics_create_custom_reports', 'Ability to create custom reports'),
(19, 'analytics_manage_reports', 'Ability to manage reports'),
(20, 'trending_view_items', 'Ability to view trending items'),
(21, 'trending_manage_items', 'Ability to manage trending items'),
(22, 'trending_share_items', 'Ability to share trending items'),
(23, 'trending_subscribe', 'Ability to subscribe to trends'),
(24, 'user_add', 'Ability to add new users'),
(25, 'user_edit', 'Ability to edit user profiles'),
(26, 'user_delete', 'Ability to delete users'),
(27, 'user_manage_roles', 'Ability to manage user roles'),
(28, 'user_manage_permissions', 'Ability to manage user permissions'),
(29, 'taxonomy_add', 'Ability to add new taxonomy'),
(30, 'taxonomy_edit', 'Ability to edit existing taxonomy'),
(31, 'taxonomy_delete', 'Ability to delete taxonomy'),
(32, 'taxonomy_manage_metadata', 'Ability to manage metadata'),
(33, 'config_modify_general', 'Ability to modify general settings'),
(34, 'config_email_settings', 'Ability to configure email settings'),
(35, 'config_security_settings', 'Ability to adjust security settings'),
(36, 'config_manage_backups', 'Ability to manage system backups'),
(37, 'moderation_review_content', 'Ability to review content'),
(38, 'moderation_manage_flags', 'Ability to manage flagged content'),
(39, 'moderation_configure_auto', 'Ability to configure auto-moderation settings'),
(40, 'integration_configure_api', 'Ability to configure API settings'),
(41, 'integration_manage_webhooks', 'Ability to manage webhooks'),
(42, 'integration_configure_sso', 'Ability to configure Single Sign-On'),
(43, 'integration_manage_active', 'Ability to manage active integrations'),
(44, 'audit_view_logs', 'Ability to view audit logs'),
(45, 'audit_filter_logs', 'Ability to filter audit logs'),
(46, 'audit_export_logs', 'Ability to export audit logs'),
(47, 'audit_configure_retention', 'Ability to configure log retention settings'),
(48, 'home_view_statistics', 'Ability to view overall statistics on the home dashboard'),
(49, 'home_access_quick_links', 'Ability to access quick links on the home dashboard'),
(50, 'global_read', 'Global read/view access across all sections'),
(51, 'global_create', 'Global create/add access across all sections'),
(52, 'global_edit', 'Global edit/update access across all sections'),
(53, 'global_delete', 'Global delete access across all sections'),
(54, 'global_admin', 'Global admin-level access across all sections');

-- --------------------------------------------------------

--
-- Table structure for table `projects`
--

CREATE TABLE `projects` (
  `project_id` int(11) NOT NULL,
  `project_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `project_documents`
--

CREATE TABLE `project_documents` (
  `project_id` int(11) NOT NULL,
  `document_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `project_members`
--

CREATE TABLE `project_members` (
  `project_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `role` varchar(50) DEFAULT NULL,
  `joined_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ratings`
--

CREATE TABLE `ratings` (
  `rating_id` int(11) NOT NULL,
  `document_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `rating` int(11) NOT NULL CHECK (`rating` >= 1 and `rating` <= 5),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `recent_searches`
--

CREATE TABLE `recent_searches` (
  `search_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `search_query` varchar(255) NOT NULL,
  `searched_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reply_attachments`
--

CREATE TABLE `reply_attachments` (
  `attachment_id` int(11) NOT NULL,
  `reply_id` int(11) DEFAULT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_size` int(11) NOT NULL,
  `uploaded_by` int(11) DEFAULT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reports`
--

CREATE TABLE `reports` (
  `report_id` int(11) NOT NULL,
  `report_name` varchar(255) NOT NULL,
  `report_type` varchar(50) DEFAULT NULL,
  `parameters` text DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `role_id` int(11) NOT NULL,
  `role_name` varchar(50) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`role_id`, `role_name`, `description`) VALUES
(1, 'Administrator', 'Has all the privillages'),
(2, 'KM Specialist', 'has all privilleges'),
(3, 'KM Officer', 'limited privilegies'),
(4, 'Manager ', 'Have limited Privileges'),
(5, 'Director', 'Has 3 Previlages');

-- --------------------------------------------------------

--
-- Table structure for table `role_permissions`
--

CREATE TABLE `role_permissions` (
  `role_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `role_permissions`
--

INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES
(1, 1),
(1, 2),
(1, 3),
(1, 4),
(1, 5),
(1, 7),
(1, 8),
(1, 9),
(1, 10),
(2, 1),
(2, 2),
(2, 3),
(2, 4),
(2, 5),
(2, 6),
(2, 7),
(2, 8),
(2, 9),
(2, 10),
(3, 6),
(3, 8),
(5, 2),
(5, 3),
(5, 4),
(5, 5),
(5, 6),
(5, 8);

-- --------------------------------------------------------

--
-- Table structure for table `scheduled_reports`
--

CREATE TABLE `scheduled_reports` (
  `schedule_id` int(11) NOT NULL,
  `report_id` int(11) DEFAULT NULL,
  `frequency` varchar(50) DEFAULT NULL,
  `next_run` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `recipients` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `system_config`
--

CREATE TABLE `system_config` (
  `config_id` int(11) NOT NULL,
  `config_key` varchar(50) NOT NULL,
  `config_value` text DEFAULT NULL,
  `description` text DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tags`
--

CREATE TABLE `tags` (
  `tag_id` int(11) NOT NULL,
  `tag_name` varchar(30) NOT NULL,
  `description` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tags`
--

INSERT INTO `tags` (`tag_id`, `tag_name`, `description`) VALUES
(1, 'knowledge management', 'The best Option to explore the organizational knowledge Assets');

-- --------------------------------------------------------

--
-- Table structure for table `taxonomies`
--

CREATE TABLE `taxonomies` (
  `taxonomy_id` int(11) NOT NULL,
  `taxonomy_name` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `parent_taxonomy_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `taxonomies`
--

INSERT INTO `taxonomies` (`taxonomy_id`, `taxonomy_name`, `description`, `parent_taxonomy_id`, `created_at`) VALUES
(2, '1.	Research and Innovation', '', NULL, '2024-07-15 07:44:53'),
(3, '1.1. Research Areas', '', 2, '2024-07-15 07:46:06'),
(4, '1.1.1. Industrial Technologies', 'Research', 3, '2024-07-15 07:46:48'),
(5, '1.1.2. Agricultural Processing', 'Research', 3, '2024-07-15 07:47:29'),
(6, '1.1.3. Information and Communication Technology', 'ICT', 3, '2024-07-15 07:48:24'),
(7, '1.1.4. Energy and Environment', 'Research', 3, '2024-07-15 07:49:10'),
(8, '1.1.5. Biotechnology and Health', 'Research', 3, '2024-07-15 07:49:47'),
(9, '1.1.6. Natural Sciences', 'Research', 3, '2024-07-15 07:50:22'),
(10, '1.1.7. Social Sciences and Humanities', 'Research', 3, '2024-07-15 07:50:56'),
(11, '1.2. Projects', 'NIRDA Projects Hub', 2, '2024-07-15 07:51:58'),
(12, '1.2.1. Ongoing Research Projects', 'Project', 11, '2024-07-15 07:52:37'),
(13, '1.2.2. Completed Projects', 'Project', 11, '2024-07-15 07:53:18'),
(14, '1.2.3. Open Call Projects', 'Project', 11, '2024-07-15 07:53:51'),
(15, '1.3. Innovation Initiatives', 'NIRDA Innovation Initiatives', 2, '2024-07-15 07:54:38'),
(16, '1.3.1. Technology Incubation Centers', 'incubation center', 15, '2024-07-15 07:55:17'),
(17, '1.3.2. Pilot Plants', 'Pilot', 15, '2024-07-15 07:56:06'),
(18, '1.3.3. Rural Industrialization Initiatives', 'innovation', 15, '2024-07-15 07:56:59'),
(19, '2.	Knowledge Assets', 'NIRDA Knowledge Bank', NULL, '2024-07-15 07:58:20'),
(20, '2.1. Explicit Knowledge', 'Explicit', 19, '2024-07-15 08:03:00'),
(21, '2.1.1. Scientific Publications', 'Scientific Publications', 20, '2024-07-15 08:03:58'),
(22, '2.1.2. Technical Reports', 'Technical Reports', 20, '2024-07-15 08:04:36'),
(23, '2.1.3. Research Papers', 'Research Paper', 20, '2024-07-15 08:05:28'),
(24, '2.1.4. Patents', 'Intellectual Property Right', 20, '2024-07-15 08:06:22'),
(25, '2.1.5. Documentation and Records', 'Records Management', 20, '2024-07-15 08:07:17'),
(26, '2.2. Tacit Knowledge', 'Tacit', 19, '2024-07-15 08:08:14'),
(27, '2.2.1. Expertise and Experience', 'Expertise and Experience', 26, '2024-07-15 08:09:04'),
(28, '2.2.2. Skills and Know-how', 'Skills and Know-how', 26, '2024-07-15 08:09:48'),
(29, '2.2.3. Contextual Understanding', 'Contextual Understanding', 26, '2024-07-15 08:10:41'),
(30, '2.2.4. Networks and Relationships', 'knowledge acquired through Networks and Relationships', 26, '2024-07-15 08:11:36'),
(31, '2.3. Historical Documents', 'Historical Documents that are preserved for future use or references', 19, '2024-07-15 08:12:45'),
(32, '2.3.1. IRSAC Documents', 'Documents which were created, receives or maintained by IRSAC', 31, '2024-07-15 08:14:04'),
(33, '2.3.2. INRS Documents', 'Documents which were created, received or maintained by INRS', 31, '2024-07-15 08:15:22'),
(34, '2.3.3. IRST Documents', 'Document which were created, Received aor maintained by IRST', 31, '2024-07-15 08:16:32'),
(35, '3.	Industrial Sectors', 'Industrial Sector Based Knowledge Bank', NULL, '2024-07-15 08:17:51'),
(36, '3.1. Manufacturing', 'Manufacturing Related Knowledge', 35, '2024-07-15 08:18:54'),
(37, '3.2. Agriculture', 'Agriculture', 35, '2024-07-15 08:19:44'),
(38, '3.3. Mining and Minerals', 'Mining and Minerals Sector', 35, '2024-07-15 08:20:33'),
(39, '3.4. Textiles and Apparel', 'Textiles and Apparel Sector', 35, '2024-07-15 08:21:14'),
(40, '3.5. Food Processing', 'Food Processing Sector', 35, '2024-07-15 08:21:59'),
(42, '3.6. Emerging Industries', 'Emerging Industries', 35, '2024-07-15 09:23:51'),
(43, '4.	Technology and Innovation Management', 'Technology and Innovation Management', NULL, '2024-07-16 21:04:08'),
(44, '4.1. Technology Transfer', 'Technology Transfer', 43, '2024-07-16 21:04:50'),
(45, '4.2. Intellectual Property Rights Management', 'Intellectual Property Rights', 43, '2024-07-16 21:06:05'),
(46, '4.2.1. Patents', 'Patents Right', 45, '2024-07-16 21:06:48'),
(47, '4.2.2. Trademarks', 'Trademarks', 45, '2024-07-16 21:07:26'),
(48, '4.2.3. Copyright', 'Copyright', 45, '2024-07-16 21:08:07'),
(49, '4.3. Technology Audits', 'Technology Audits', 43, '2024-07-16 21:09:02'),
(50, '4.4. Value Chain Analyses', 'Value Chain Analyses', 43, '2024-07-16 21:09:45'),
(51, '4.5. Prototype Development', 'Prototype Development', 43, '2024-07-16 21:10:23'),
(52, '4.6. Reverse Engineering', 'Reverse Engineering', 43, '2024-07-16 21:10:59'),
(53, '5.	Partnerships and Collaborations', 'Partnerships and Collaborations Documents', NULL, '2024-07-16 21:11:38'),
(54, '5.1. Government Agencies', 'Government Agencies', 53, '2024-07-16 21:12:13'),
(55, '5.2. Academic Institutions', 'Academic Institutions', 53, '2024-07-16 21:12:49'),
(56, '5.3. International Collaborations', 'International Collaborations', 53, '2024-07-16 21:13:26'),
(57, '5.4. Industry Associations', 'Industry Associations', 53, '2024-07-16 21:14:05'),
(58, '5.5. Research Institutions', 'Research Institutions', 53, '2024-07-16 21:14:41'),
(59, '6.	Capacity Building and Training', 'Capacity Building and Training Resources', NULL, '2024-07-16 21:15:22'),
(60, '6.1. Training Programs', 'Training Programs', 59, '2024-07-16 21:15:59'),
(61, '6.2. Workshops and Seminars', 'Workshops and Seminars', 59, '2024-07-16 21:16:38'),
(62, '6.3. Mentoring Initiatives', 'Mentoring Initiatives', 59, '2024-07-16 21:17:13'),
(63, '6.4. Skills Development', 'Skills Development', 59, '2024-07-16 21:17:52'),
(64, '6.5. SME Support and Incubation', 'SME Support and Incubation', 59, '2024-07-16 21:18:28'),
(65, '7.	Policy and Regulatory Framework', 'Policy and Regulatory Framework Documents', NULL, '2024-07-16 21:19:23'),
(66, '7.1. National Industrial Development Policy', 'National Industrial Development Policy', 65, '2024-07-16 21:20:08'),
(67, '7.2. Research and Innovation Policies', 'Research and Innovation Policies', 65, '2024-07-16 21:20:46'),
(68, '7.3. Intellectual Property Regulations', 'Intellectual Property Regulations', 65, '2024-07-16 21:21:25'),
(69, '7.4. Industry-Specific Regulations', 'Industry-Specific Regulations', 65, '2024-07-16 21:21:59'),
(70, '8.	Organizational Structure and Culture', 'Organizational Structure and Culture', NULL, '2024-07-16 21:22:58'),
(71, '8.1. Directorate Office', 'Leadership Team', 70, '2024-07-16 21:23:37'),
(72, '8.2. Departments', 'Departmental Document', 70, '2024-07-16 21:26:04'),
(73, '8.3. Divisions', 'Divisions', 70, '2024-07-16 21:26:48'),
(74, '8.4. Units', 'Unit\'s Document', 70, '2024-07-16 21:27:36'),
(75, '8.5. Organizational Culture', 'Organizational Culture', 70, '2024-07-16 21:28:08'),
(76, '8.5.1. Collaboration and Teamwork', 'Collaboration and Teamwork', 75, '2024-07-16 21:28:51'),
(77, '8.5.2. Continuous Learning', 'Continuous Learning', 75, '2024-07-16 21:29:32'),
(78, '8.5.3. Knowledge Sharing Initiatives', 'Knowledge Sharing Initiatives', 75, '2024-07-16 21:30:27'),
(79, '9.	Resources and Facilities', 'Resources and Facilities', NULL, '2024-07-16 21:31:25'),
(80, '9.1. Library', 'Library Resources', 79, '2024-07-16 21:32:20'),
(81, '9.1.1. Books', 'Books', 80, '2024-07-16 21:32:59'),
(82, '9.1.2. Journals', 'Journals', 80, '2024-07-16 21:33:39'),
(83, '9.1.3. Magazines', 'Magazines', 80, '2024-07-16 21:34:28'),
(84, '9.2. Laboratories', 'Laboratories', 79, '2024-07-16 21:35:13'),
(85, '9.2.1. STEM Laboratory', 'STEM Laboratory', 84, '2024-07-16 21:36:52'),
(86, '9.2.2. R&D Laboratory', 'R&D Laboratory', 84, '2024-07-16 21:37:30'),
(87, '9.3. Research Equipment', 'Research Equipment(Materials)', 79, '2024-07-16 21:38:25'),
(88, '9.4. NIRDA Management supporting Systems', 'Management supporting Systems', 79, '2024-07-16 21:40:29'),
(89, '9.4.1. Projects management System (PMS)', '(PMS)', 88, '2024-07-16 21:45:30'),
(90, '9.4.2. Lab. Management Information System (LMIS)', 'Laboratory Management Information System (LMIS)', 88, '2024-07-16 21:46:21'),
(91, '9.4.3. Knowledge Management system (KMS)', 'Knowledge Management system (KMS)', 88, '2024-07-16 21:46:58'),
(92, '9.4.4. Webmail (Exchange)', 'Webmail (Exchange)', 88, '2024-07-16 21:47:43'),
(93, '9.4.5. OpenCall Portal', 'OpenCall Portal', 88, '2024-07-16 21:48:21'),
(94, '10.	Sustainable Development', 'Sustainable Development', NULL, '2024-07-16 21:48:58'),
(95, '10.1. Environmental Impact Assessments', 'Environmental Impact Assessments', 94, '2024-07-16 21:50:05'),
(96, '10.2. Sustainable Technologies', 'Sustainable Technologies', 94, '2024-07-16 21:50:43'),
(97, '10.3. Green Manufacturing Practices', 'Green Manufacturing Practices', 94, '2024-07-16 21:51:24'),
(98, '10.4. Circular Economy Initiatives', 'Circular Economy Initiatives', 94, '2024-07-16 21:51:55'),
(99, '11.	Publications and Dissemination', 'NIRDA Publications and Dissemination', NULL, '2024-07-16 21:52:35'),
(100, '11.1. Annual Reports', 'NIRDA Annual Reports', 99, '2024-07-16 21:53:17'),
(101, '11.2. Research Findings', 'Research Findings', 99, '2024-07-16 21:54:03'),
(102, '11.3. Case Studies', 'Case Studies', 99, '2024-07-16 21:54:40'),
(103, '11.4. Best Practices', 'Best Practices', 99, '2024-07-16 21:55:15'),
(104, '11.5. Industry Insights', 'Industry Insights', 99, '2024-07-16 21:55:46');

-- --------------------------------------------------------

--
-- Table structure for table `taxonomy_terms`
--

CREATE TABLE `taxonomy_terms` (
  `term_id` int(11) NOT NULL,
  `taxonomy_id` int(11) DEFAULT NULL,
  `term_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `taxonomy_terms`
--

INSERT INTO `taxonomy_terms` (`term_id`, `taxonomy_id`, `term_name`) VALUES
(6, 4, 'Research'),
(7, 5, 'Research'),
(8, 6, 'Research'),
(10, 8, 'Research'),
(11, 9, 'Research'),
(12, 10, 'Research'),
(15, 13, 'Project'),
(16, 14, 'Project'),
(18, 16, 'innovation'),
(20, 18, 'innovation'),
(21, 19, 'Knowledge'),
(23, 11, 'Project'),
(24, 15, 'innovation'),
(25, 20, 'Explicit'),
(26, 21, 'Publication'),
(27, 22, 'Report'),
(28, 23, 'Research'),
(29, 24, 'IPR'),
(30, 25, 'Records'),
(31, 26, 'Tacit'),
(32, 27, 'Tacit'),
(33, 28, 'Skills'),
(34, 29, 'Understanding'),
(35, 30, 'Tacit'),
(36, 31, 'Historical Documents '),
(38, 33, 'Historical Documents '),
(39, 34, 'Historical Documents '),
(40, 35, 'Industrial Sector'),
(41, 36, 'Manufacturing Sector'),
(42, 37, 'Agriculture '),
(43, 38, 'Mining and Minerals '),
(44, 39, 'Textiles and Apparel '),
(45, 40, 'Food Processing '),
(47, 3, 'Research'),
(48, 32, 'IRSAC'),
(50, 12, 'Project'),
(51, 17, 'innovation'),
(52, 2, 'NIRDA Research and Innovation contents Banks'),
(53, 7, 'Research'),
(54, 43, 'Technology and Innovation '),
(55, 44, 'Technology Transfer '),
(56, 45, 'Intellectual Property Rights'),
(57, 46, 'Patent '),
(58, 47, 'Trademarks '),
(59, 48, 'Copyright '),
(60, 49, 'Technology Audit '),
(61, 50, 'Value Chain Analysis '),
(62, 51, 'Prototype Development '),
(63, 52, 'Reverse Engineering '),
(64, 53, 'Partnerships and Collaborations '),
(65, 54, 'Government Agencies '),
(66, 55, 'Academic Institutions '),
(67, 56, 'International Collaborations '),
(68, 57, 'Industry Associations '),
(69, 58, 'Research Institutions '),
(70, 59, 'Capacity Building and Training '),
(71, 60, 'Training Programs'),
(72, 61, 'Workshops and Seminars '),
(73, 62, 'Mentoring Initiatives '),
(74, 63, 'Skills Development '),
(75, 64, 'SME Support and Incubation '),
(76, 65, 'Policy and Regulatory Framework '),
(77, 66, 'National Industrial Development Policy '),
(78, 67, 'Research and Innovation Policies '),
(79, 68, 'Intellectual Property Regulations '),
(80, 69, 'Industry-Specific Regulations '),
(81, 70, 'Organizational Structure and Culture '),
(83, 72, 'Department  '),
(84, 73, 'Division\'s Document'),
(85, 74, 'Unit '),
(86, 75, 'Organizational Culture '),
(87, 76, 'Collaboration and Teamwork '),
(88, 77, 'Continuous Learning '),
(89, 78, 'Knowledge Sharing Initiatives '),
(90, 79, 'Resources and Facilities '),
(91, 80, 'Library Resources'),
(92, 81, 'Book'),
(93, 82, 'Journal '),
(94, 83, 'Magazine'),
(95, 84, 'Laboratory'),
(96, 85, 'STEM'),
(97, 86, 'R&D '),
(98, 87, 'Research Equipment '),
(99, 88, 'Supporting Systems '),
(100, 89, 'PMS'),
(102, 91, '(KMS)'),
(103, 92, 'Webmail (Exchange)'),
(104, 93, 'Open Call Portal'),
(105, 94, 'Sustainable Development '),
(106, 95, 'Environmental Impact Assessments '),
(107, 96, 'Sustainable Technologies '),
(108, 97, 'Green Manufacturing Practices '),
(109, 98, 'Circular Economy Initiatives '),
(110, 99, 'Publications and Dissemination '),
(111, 100, ' Annual Report '),
(112, 101, 'Research Finding '),
(113, 102, 'Case Studies '),
(114, 103, ' Best Practices '),
(115, 104, 'Industry Insights '),
(117, 71, 'Leadership Team '),
(118, 42, 'Emerging Industries '),
(119, 90, 'LMIS');

-- --------------------------------------------------------

--
-- Table structure for table `topic_attachments`
--

CREATE TABLE `topic_attachments` (
  `attachment_id` int(11) NOT NULL,
  `topic_id` int(11) DEFAULT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_size` int(11) NOT NULL,
  `uploaded_by` int(11) DEFAULT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `trending_items`
--

CREATE TABLE `trending_items` (
  `item_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `icon` varchar(50) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
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
(1, 'Mahomet', 'mahofoide@gmail.com', '$2y$10$xQAq50zZilsxp.7O3TZsFu.fRJ3xGpmPx.Opx/r9jn5vzUquDS5W2', 'KAZUBWENGE Mahomet', 1, 'Knowledge Management and Operational monitoring', '0', 'Technology Monitoring and Knowledge Management', '0788359461', 'English', 1, '2024-07-14 08:14:45', '2024-07-14 09:11:18'),
(2, 'Kativire', 'bisinfo.backet@gmail.com', '$2y$10$kTBdYRQoY1dYiz9DC5XENeyVfv4bNdAjdPH/4F3QGbAFRO9XruiCW', 'Mahomet Kativire', 1, 'OP &KM', '0', 'TM&KM', '1788346231', 'Kinyarwanda', 1, '2025-03-11 12:39:02', '2025-03-11 12:44:17');

-- --------------------------------------------------------

--
-- Table structure for table `user_activity_log`
--

CREATE TABLE `user_activity_log` (
  `log_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `user_name` varchar(100) DEFAULT NULL,
  `activity_type` varchar(50) NOT NULL,
  `activity_details` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_activity_log`
--

INSERT INTO `user_activity_log` (`log_id`, `user_id`, `user_name`, `activity_type`, `activity_details`, `ip_address`, `timestamp`) VALUES
(1, 0, 'guest', 'User Logout', 'User logged out successfully', '::1', '2024-07-07 18:53:07'),
(2, 1, 'Mahomet', 'User Login', 'User logged in successfully', '::1', '2024-07-14 08:18:20'),
(3, 1, 'Mahomet', 'User Login', 'User logged in successfully', '::1', '2024-07-14 08:21:31'),
(4, 1, 'Mahomet', 'User Login', 'User logged in successfully', '::1', '2024-07-14 08:27:37'),
(5, 1, 'Mahomet', 'User Login', 'User logged in successfully', '::1', '2024-07-14 08:28:58'),
(6, 1, 'Mahomet', 'User Login', 'User logged in successfully', '::1', '2024-07-14 08:29:13'),
(7, 1, 'Mahomet', 'User Login', 'User logged in successfully', '::1', '2024-07-14 08:38:47'),
(8, 1, 'Mahomet', 'User Login', 'User logged in successfully', '::1', '2024-07-14 08:39:25'),
(9, 1, 'Mahomet', 'User Login', 'User logged in successfully', '::1', '2024-07-14 08:40:10'),
(10, 1, 'Mahomet', 'User Login', 'User logged in successfully', '::1', '2024-07-14 08:46:42'),
(11, 1, 'Mahomet', 'User Login', 'User logged in successfully', '::1', '2024-07-14 08:47:23'),
(12, 1, 'Mahomet', 'User Login', 'User logged in successfully', '::1', '2024-07-14 08:48:34'),
(13, 1, 'Mahomet', 'User Login', 'User logged in successfully', '::1', '2024-07-14 08:49:26'),
(14, 1, 'Mahomet', 'User Login', 'User logged in successfully', '::1', '2024-07-14 09:10:22'),
(15, 1, 'Mahomet', 'User Login', 'User logged in successfully', '::1', '2024-07-14 09:10:42'),
(16, 1, 'Mahomet', 'User Login', 'User logged in successfully', '::1', '2024-07-14 09:11:18'),
(17, 1, NULL, 'Created folder', 'Created folder: Repo', '::1', '2024-07-14 16:12:13'),
(18, 1, NULL, 'Created folder', 'Created folder: Knowledge', '::1', '2024-07-14 16:18:24'),
(19, 1, NULL, 'Updated folder', 'Updated folder: Repo', '::1', '2024-07-14 18:26:36'),
(20, 1, NULL, 'Updated folder', 'Updated folder: Knowledge', '::1', '2024-07-14 18:34:33'),
(21, 1, NULL, 'Updated folder', 'Updated folder: Knowledge', '::1', '2024-07-14 18:51:57'),
(22, 1, NULL, 'Updated folder', 'Updated folder: Knowledge', '::1', '2024-07-14 19:12:00'),
(23, 1, NULL, 'Updated folder', 'Updated folder: Repo', '::1', '2024-07-14 19:12:13'),
(24, 1, NULL, 'Updated folder', 'Updated folder: Knowledge', '::1', '2024-07-14 19:12:43'),
(25, 1, NULL, 'Updated folder', 'Updated folder: Knowledge', '::1', '2024-07-14 19:19:12'),
(26, 1, NULL, 'Moved folder', 'Moved folder ID: 2 to new parent ID: 1', '::1', '2024-07-14 19:19:40'),
(27, 1, NULL, 'Moved folder', 'Moved folder ID: 2 to new parent ID: ', '::1', '2024-07-14 19:20:09'),
(28, 1, NULL, 'Moved folder', 'Moved folder ID: 2 to new parent ID: 1', '::1', '2024-07-14 19:20:24'),
(29, 1, NULL, 'Updated folder', 'Updated folder: Repo', '::1', '2024-07-14 19:22:10'),
(30, 1, NULL, 'Updated folder', 'Updated folder: Knowledge', '::1', '2024-07-14 19:23:17'),
(31, 1, NULL, 'Updated folder', 'Updated folder: Knowledge', '::1', '2024-07-14 19:39:49'),
(32, 1, NULL, 'Updated folder', 'Updated folder: Knowledge', '::1', '2024-07-14 19:53:29'),
(33, 1, NULL, 'Updated folder', 'Updated folder: Knowledge', '::1', '2024-07-14 19:55:15'),
(34, 1, NULL, 'Updated folder', 'Updated folder: Knowledge', '::1', '2024-07-14 19:56:14'),
(35, 1, NULL, 'Updated folder', 'Updated folder: Repo', '::1', '2024-07-14 20:08:29'),
(36, 1, NULL, 'Updated folder', 'Updated folder: Knowledge', '::1', '2024-07-14 20:09:05'),
(37, 1, NULL, 'Updated folder', 'Updated folder: Knowledge', '::1', '2024-07-14 20:10:00'),
(38, 1, NULL, 'Updated folder', 'Updated folder: Knowledge', '::1', '2024-07-14 20:10:39'),
(39, 1, NULL, 'Updated folder', 'Updated folder: ', '::1', '2024-07-14 20:13:17'),
(40, 2, 'Kativire', 'User Login', 'User logged in successfully', '::1', '2025-03-11 12:44:18'),
(41, 2, 'Kativire', 'User Logout', 'User logged out successfully', '::1', '2025-03-11 12:45:18');

-- --------------------------------------------------------

--
-- Table structure for table `workflow_status`
--

CREATE TABLE `workflow_status` (
  `status_id` int(11) NOT NULL,
  `document_id` int(11) DEFAULT NULL,
  `current_step` varchar(100) DEFAULT NULL,
  `next_step` varchar(100) DEFAULT NULL,
  `assigned_to` int(11) DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `status` enum('Pending','In Progress','Completed') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`comment_id`),
  ADD KEY `document_id` (`document_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `content_moderation`
--
ALTER TABLE `content_moderation`
  ADD PRIMARY KEY (`moderation_id`),
  ADD KEY `moderated_by` (`moderated_by`);

--
-- Indexes for table `custom_fields`
--
ALTER TABLE `custom_fields`
  ADD PRIMARY KEY (`field_id`);

--
-- Indexes for table `documents`
--
ALTER TABLE `documents`
  ADD PRIMARY KEY (`document_id`),
  ADD KEY `uploaded_by` (`uploaded_by`),
  ADD KEY `folder_id` (`folder_id`),
  ADD KEY `fk_document_owner` (`owner`),
  ADD KEY `fk_document_quality_checker` (`quality_checker`);

--
-- Indexes for table `document_approvers`
--
ALTER TABLE `document_approvers`
  ADD PRIMARY KEY (`document_id`,`user_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `document_areas`
--
ALTER TABLE `document_areas`
  ADD PRIMARY KEY (`document_id`,`area_id`),
  ADD KEY `area_id` (`area_id`);

--
-- Indexes for table `document_contributors`
--
ALTER TABLE `document_contributors`
  ADD PRIMARY KEY (`document_id`,`user_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `document_custom_fields`
--
ALTER TABLE `document_custom_fields`
  ADD PRIMARY KEY (`document_id`,`field_id`),
  ADD KEY `field_id` (`field_id`);

--
-- Indexes for table `document_references`
--
ALTER TABLE `document_references`
  ADD PRIMARY KEY (`document_id`,`referenced_document_id`),
  ADD KEY `referenced_document_id` (`referenced_document_id`);

--
-- Indexes for table `document_reviewers`
--
ALTER TABLE `document_reviewers`
  ADD PRIMARY KEY (`document_id`,`user_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `document_tags`
--
ALTER TABLE `document_tags`
  ADD PRIMARY KEY (`document_id`,`tag_id`),
  ADD KEY `tag_id` (`tag_id`);

--
-- Indexes for table `document_taxonomies`
--
ALTER TABLE `document_taxonomies`
  ADD PRIMARY KEY (`document_id`,`term_id`),
  ADD KEY `term_id` (`term_id`);

--
-- Indexes for table `document_versions`
--
ALTER TABLE `document_versions`
  ADD PRIMARY KEY (`version_id`),
  ADD KEY `document_id` (`document_id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `editor` (`editor`);

--
-- Indexes for table `folders`
--
ALTER TABLE `folders`
  ADD PRIMARY KEY (`folder_id`),
  ADD KEY `parent_folder_id` (`parent_folder_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `folder_taxonomies`
--
ALTER TABLE `folder_taxonomies`
  ADD PRIMARY KEY (`folder_id`,`taxonomy_id`),
  ADD KEY `taxonomy_id` (`taxonomy_id`);

--
-- Indexes for table `forums`
--
ALTER TABLE `forums`
  ADD PRIMARY KEY (`forum_id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `associated_project_id` (`associated_project_id`);

--
-- Indexes for table `forum_replies`
--
ALTER TABLE `forum_replies`
  ADD PRIMARY KEY (`reply_id`),
  ADD KEY `topic_id` (`topic_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `forum_topics`
--
ALTER TABLE `forum_topics`
  ADD PRIMARY KEY (`topic_id`),
  ADD KEY `forum_id` (`forum_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `ip`
--
ALTER TABLE `ip`
  ADD PRIMARY KEY (`id`),
  ADD KEY `document_id` (`document_id`);

--
-- Indexes for table `knowledge_areas`
--
ALTER TABLE `knowledge_areas`
  ADD PRIMARY KEY (`area_id`),
  ADD UNIQUE KEY `area_name` (`area_name`),
  ADD KEY `parent_area_id` (`parent_area_id`);

--
-- Indexes for table `notice_messages`
--
ALTER TABLE `notice_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`permission_id`),
  ADD UNIQUE KEY `permission_name` (`permission_name`);

--
-- Indexes for table `projects`
--
ALTER TABLE `projects`
  ADD PRIMARY KEY (`project_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `project_documents`
--
ALTER TABLE `project_documents`
  ADD PRIMARY KEY (`project_id`,`document_id`),
  ADD KEY `document_id` (`document_id`);

--
-- Indexes for table `project_members`
--
ALTER TABLE `project_members`
  ADD PRIMARY KEY (`project_id`,`user_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `ratings`
--
ALTER TABLE `ratings`
  ADD PRIMARY KEY (`rating_id`),
  ADD KEY `document_id` (`document_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `recent_searches`
--
ALTER TABLE `recent_searches`
  ADD PRIMARY KEY (`search_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `reply_attachments`
--
ALTER TABLE `reply_attachments`
  ADD PRIMARY KEY (`attachment_id`),
  ADD KEY `reply_id` (`reply_id`),
  ADD KEY `uploaded_by` (`uploaded_by`);

--
-- Indexes for table `reports`
--
ALTER TABLE `reports`
  ADD PRIMARY KEY (`report_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`role_id`),
  ADD UNIQUE KEY `role_name` (`role_name`);

--
-- Indexes for table `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD PRIMARY KEY (`role_id`,`permission_id`),
  ADD KEY `permission_id` (`permission_id`);

--
-- Indexes for table `scheduled_reports`
--
ALTER TABLE `scheduled_reports`
  ADD PRIMARY KEY (`schedule_id`),
  ADD KEY `report_id` (`report_id`);

--
-- Indexes for table `system_config`
--
ALTER TABLE `system_config`
  ADD PRIMARY KEY (`config_id`),
  ADD UNIQUE KEY `config_key` (`config_key`);

--
-- Indexes for table `tags`
--
ALTER TABLE `tags`
  ADD PRIMARY KEY (`tag_id`),
  ADD UNIQUE KEY `tag_name` (`tag_name`);

--
-- Indexes for table `taxonomies`
--
ALTER TABLE `taxonomies`
  ADD PRIMARY KEY (`taxonomy_id`),
  ADD UNIQUE KEY `taxonomy_name` (`taxonomy_name`),
  ADD KEY `parent_taxonomy_id` (`parent_taxonomy_id`);

--
-- Indexes for table `taxonomy_terms`
--
ALTER TABLE `taxonomy_terms`
  ADD PRIMARY KEY (`term_id`),
  ADD KEY `taxonomy_id` (`taxonomy_id`);

--
-- Indexes for table `topic_attachments`
--
ALTER TABLE `topic_attachments`
  ADD PRIMARY KEY (`attachment_id`),
  ADD KEY `topic_id` (`topic_id`),
  ADD KEY `uploaded_by` (`uploaded_by`);

--
-- Indexes for table `trending_items`
--
ALTER TABLE `trending_items`
  ADD PRIMARY KEY (`item_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `role_id` (`role_id`);

--
-- Indexes for table `user_activity_log`
--
ALTER TABLE `user_activity_log`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `workflow_status`
--
ALTER TABLE `workflow_status`
  ADD PRIMARY KEY (`status_id`),
  ADD KEY `document_id` (`document_id`),
  ADD KEY `assigned_to` (`assigned_to`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `comment_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `content_moderation`
--
ALTER TABLE `content_moderation`
  MODIFY `moderation_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `custom_fields`
--
ALTER TABLE `custom_fields`
  MODIFY `field_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `documents`
--
ALTER TABLE `documents`
  MODIFY `document_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `document_versions`
--
ALTER TABLE `document_versions`
  MODIFY `version_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `folders`
--
ALTER TABLE `folders`
  MODIFY `folder_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `forums`
--
ALTER TABLE `forums`
  MODIFY `forum_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `forum_replies`
--
ALTER TABLE `forum_replies`
  MODIFY `reply_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `forum_topics`
--
ALTER TABLE `forum_topics`
  MODIFY `topic_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ip`
--
ALTER TABLE `ip`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `knowledge_areas`
--
ALTER TABLE `knowledge_areas`
  MODIFY `area_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notice_messages`
--
ALTER TABLE `notice_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `permission_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;

--
-- AUTO_INCREMENT for table `projects`
--
ALTER TABLE `projects`
  MODIFY `project_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ratings`
--
ALTER TABLE `ratings`
  MODIFY `rating_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `recent_searches`
--
ALTER TABLE `recent_searches`
  MODIFY `search_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reply_attachments`
--
ALTER TABLE `reply_attachments`
  MODIFY `attachment_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reports`
--
ALTER TABLE `reports`
  MODIFY `report_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `role_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `scheduled_reports`
--
ALTER TABLE `scheduled_reports`
  MODIFY `schedule_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `system_config`
--
ALTER TABLE `system_config`
  MODIFY `config_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tags`
--
ALTER TABLE `tags`
  MODIFY `tag_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `taxonomies`
--
ALTER TABLE `taxonomies`
  MODIFY `taxonomy_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=105;

--
-- AUTO_INCREMENT for table `taxonomy_terms`
--
ALTER TABLE `taxonomy_terms`
  MODIFY `term_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=120;

--
-- AUTO_INCREMENT for table `topic_attachments`
--
ALTER TABLE `topic_attachments`
  MODIFY `attachment_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `trending_items`
--
ALTER TABLE `trending_items`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `user_activity_log`
--
ALTER TABLE `user_activity_log`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `workflow_status`
--
ALTER TABLE `workflow_status`
  MODIFY `status_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`document_id`) REFERENCES `documents` (`document_id`),
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `content_moderation`
--
ALTER TABLE `content_moderation`
  ADD CONSTRAINT `content_moderation_ibfk_1` FOREIGN KEY (`moderated_by`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `documents`
--
ALTER TABLE `documents`
  ADD CONSTRAINT `documents_ibfk_1` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `documents_ibfk_2` FOREIGN KEY (`folder_id`) REFERENCES `folders` (`folder_id`),
  ADD CONSTRAINT `fk_document_owner` FOREIGN KEY (`owner`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `fk_document_quality_checker` FOREIGN KEY (`quality_checker`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `document_approvers`
--
ALTER TABLE `document_approvers`
  ADD CONSTRAINT `document_approvers_ibfk_1` FOREIGN KEY (`document_id`) REFERENCES `documents` (`document_id`),
  ADD CONSTRAINT `document_approvers_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `document_areas`
--
ALTER TABLE `document_areas`
  ADD CONSTRAINT `document_areas_ibfk_1` FOREIGN KEY (`document_id`) REFERENCES `documents` (`document_id`),
  ADD CONSTRAINT `document_areas_ibfk_2` FOREIGN KEY (`area_id`) REFERENCES `knowledge_areas` (`area_id`);

--
-- Constraints for table `document_contributors`
--
ALTER TABLE `document_contributors`
  ADD CONSTRAINT `document_contributors_ibfk_1` FOREIGN KEY (`document_id`) REFERENCES `documents` (`document_id`),
  ADD CONSTRAINT `document_contributors_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `document_custom_fields`
--
ALTER TABLE `document_custom_fields`
  ADD CONSTRAINT `document_custom_fields_ibfk_1` FOREIGN KEY (`document_id`) REFERENCES `documents` (`document_id`),
  ADD CONSTRAINT `document_custom_fields_ibfk_2` FOREIGN KEY (`field_id`) REFERENCES `custom_fields` (`field_id`);

--
-- Constraints for table `document_references`
--
ALTER TABLE `document_references`
  ADD CONSTRAINT `document_references_ibfk_1` FOREIGN KEY (`document_id`) REFERENCES `documents` (`document_id`),
  ADD CONSTRAINT `document_references_ibfk_2` FOREIGN KEY (`referenced_document_id`) REFERENCES `documents` (`document_id`);

--
-- Constraints for table `document_reviewers`
--
ALTER TABLE `document_reviewers`
  ADD CONSTRAINT `document_reviewers_ibfk_1` FOREIGN KEY (`document_id`) REFERENCES `documents` (`document_id`),
  ADD CONSTRAINT `document_reviewers_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `document_tags`
--
ALTER TABLE `document_tags`
  ADD CONSTRAINT `document_tags_ibfk_1` FOREIGN KEY (`document_id`) REFERENCES `documents` (`document_id`),
  ADD CONSTRAINT `document_tags_ibfk_2` FOREIGN KEY (`tag_id`) REFERENCES `tags` (`tag_id`);

--
-- Constraints for table `document_taxonomies`
--
ALTER TABLE `document_taxonomies`
  ADD CONSTRAINT `document_taxonomies_ibfk_1` FOREIGN KEY (`document_id`) REFERENCES `documents` (`document_id`),
  ADD CONSTRAINT `document_taxonomies_ibfk_2` FOREIGN KEY (`term_id`) REFERENCES `taxonomy_terms` (`term_id`);

--
-- Constraints for table `folder_taxonomies`
--
ALTER TABLE `folder_taxonomies`
  ADD CONSTRAINT `folder_taxonomies_ibfk_1` FOREIGN KEY (`folder_id`) REFERENCES `folders` (`folder_id`),
  ADD CONSTRAINT `folder_taxonomies_ibfk_2` FOREIGN KEY (`taxonomy_id`) REFERENCES `taxonomies` (`taxonomy_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
