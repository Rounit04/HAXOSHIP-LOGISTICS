-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 10, 2025 at 01:19 PM
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
-- Database: `haxo_shipping`
--

-- --------------------------------------------------------

--
-- Table structure for table `blogs`
--

CREATE TABLE `blogs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `author` varchar(255) NOT NULL DEFAULT 'Admin Agent',
  `views` int(11) NOT NULL DEFAULT 0,
  `published_at` timestamp NULL DEFAULT NULL,
  `status` enum('published','draft') NOT NULL DEFAULT 'published',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `booking_categories`
--

CREATE TABLE `booking_categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `type` enum('Single','Bulk') NOT NULL DEFAULT 'Single',
  `requires_awb` tinyint(1) NOT NULL DEFAULT 0,
  `status` enum('Active','In-active') NOT NULL DEFAULT 'Active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `frontend_settings`
--

CREATE TABLE `frontend_settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `banner` varchar(255) DEFAULT NULL,
  `primary_color` varchar(255) NOT NULL DEFAULT '#FF750F',
  `secondary_color` varchar(255) NOT NULL DEFAULT '#ff8c3a',
  `text_color` varchar(255) NOT NULL DEFAULT '#1b1b18',
  `gdpr_cookie_enabled` tinyint(1) NOT NULL DEFAULT 0,
  `gdpr_cookie_message` text DEFAULT NULL,
  `gdpr_cookie_button_text` varchar(255) NOT NULL DEFAULT 'Accept All',
  `gdpr_cookie_decline_text` varchar(255) NOT NULL DEFAULT 'Decline',
  `gdpr_cookie_settings_text` varchar(255) NOT NULL DEFAULT 'Settings',
  `gdpr_cookie_position` varchar(255) NOT NULL DEFAULT 'bottom',
  `gdpr_cookie_bg_color` varchar(255) NOT NULL DEFAULT '#ffffff',
  `gdpr_cookie_text_color` varchar(255) NOT NULL DEFAULT '#1b1b18',
  `gdpr_cookie_button_color` varchar(255) NOT NULL DEFAULT '#FF750F',
  `gdpr_cookie_expiry_days` int(11) NOT NULL DEFAULT 365,
  `contact_email` varchar(255) DEFAULT NULL,
  `contact_phone` varchar(255) DEFAULT NULL,
  `contact_address` varchar(255) DEFAULT NULL,
  `hero_title` text DEFAULT NULL,
  `hero_subtitle` text DEFAULT NULL,
  `hero_button_text` varchar(255) NOT NULL DEFAULT 'Track Now',
  `footer_logo` varchar(255) DEFAULT NULL,
  `footer_description` text DEFAULT NULL,
  `footer_facebook_url` varchar(255) DEFAULT NULL,
  `footer_instagram_url` varchar(255) DEFAULT NULL,
  `footer_twitter_url` varchar(255) DEFAULT NULL,
  `footer_skype_url` varchar(255) DEFAULT NULL,
  `footer_google_play_url` varchar(255) DEFAULT NULL,
  `footer_app_store_url` varchar(255) DEFAULT NULL,
  `footer_copyright_text` text DEFAULT NULL,
  `about_us_content` text DEFAULT NULL,
  `services_section_title` varchar(255) DEFAULT NULL,
  `services_section_content` text DEFAULT NULL,
  `why_haxo_section_title` varchar(255) DEFAULT NULL,
  `why_haxo_section_content` text DEFAULT NULL,
  `pricing_section_title` varchar(255) DEFAULT NULL,
  `pricing_section_content` text DEFAULT NULL,
  `stats_section_content` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `frontend_settings`
--

INSERT INTO `frontend_settings` (`id`, `logo`, `banner`, `primary_color`, `secondary_color`, `text_color`, `gdpr_cookie_enabled`, `gdpr_cookie_message`, `gdpr_cookie_button_text`, `gdpr_cookie_decline_text`, `gdpr_cookie_settings_text`, `gdpr_cookie_position`, `gdpr_cookie_bg_color`, `gdpr_cookie_text_color`, `gdpr_cookie_button_color`, `gdpr_cookie_expiry_days`, `contact_email`, `contact_phone`, `contact_address`, `hero_title`, `hero_subtitle`, `hero_button_text`, `footer_logo`, `footer_description`, `footer_facebook_url`, `footer_instagram_url`, `footer_twitter_url`, `footer_skype_url`, `footer_google_play_url`, `footer_app_store_url`, `footer_copyright_text`, `about_us_content`, `services_section_title`, `services_section_content`, `why_haxo_section_title`, `why_haxo_section_content`, `pricing_section_title`, `pricing_section_content`, `stats_section_content`, `created_at`, `updated_at`) VALUES
(1, 'frontend/rpas6kOgeZlQ9wRlH07T34Flr9TfF9aQr4mK7JG2.png', 'frontend/gseulmPvtEQ9bYkpQcWOMm4Tu8ng3lNa7GkOXSbn.png', '#FF750F', '#ff8c3a', '#1b1b18', 0, NULL, 'Accept All', 'Decline', 'Settings', 'bottom', '#ffffff', '#1b1b18', '#FF750F', 365, NULL, NULL, NULL, 'Hassle Free Fastest Delivery', 'We Committed to delivery - Make easy Efficient and quality delivery.', 'Track Now', NULL, 'Fastest platform with all courier service features. Help you start, run and grow your courier service.', NULL, NULL, NULL, NULL, NULL, NULL, 'Copyright © All rights reserved. Development by Hexoship', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-04 13:16:38', '2025-11-04 13:22:03');

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2025_11_03_193705_add_role_to_users_table', 1),
(5, '2025_11_03_201138_add_banned_at_to_users_table', 1),
(6, '2025_11_03_201558_create_frontend_settings_table', 1),
(7, '2025_11_04_064825_create_booking_categories_table', 1),
(8, '2025_11_04_080448_add_footer_fields_to_frontend_settings_table', 1),
(9, '2025_11_04_094444_create_blogs_table', 1),
(10, '2025_11_04_101111_add_about_us_content_to_frontend_settings_table', 1),
(11, '2025_11_04_114056_add_landing_page_sections_to_frontend_settings_table', 1),
(12, '2025_11_04_125928_create_notifications_table', 1),
(13, '2025_11_04_130505_create_notification_settings_table', 1),
(14, '2025_11_04_131206_add_gdpr_cookie_fields_to_frontend_settings_table', 1),
(15, '2025_11_04_133930_add_contact_fields_to_frontend_settings_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `type` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `title` varchar(255) NOT NULL,
  `data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`data`)),
  `read` tinyint(1) NOT NULL DEFAULT 0,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `type`, `message`, `title`, `data`, `read`, `read_at`, `created_at`, `updated_at`) VALUES
(1, 'user_login', 'Test User (Admin) logged in to the admin panel', 'Admin Login', '{\"user_id\":1,\"user_name\":\"Test User\",\"user_email\":\"test@example.com\"}', 1, '2025-11-05 07:21:46', '2025-11-04 13:20:33', '2025-11-05 07:21:46'),
(2, 'role_assigned', 'Role \"Admin\" has been assigned to Test User', 'Role Assigned', '{\"user_id\":1,\"user_name\":\"Test User\",\"old_role\":null,\"new_role\":\"Admin\"}', 1, '2025-11-05 07:21:46', '2025-11-04 13:20:51', '2025-11-05 07:21:46'),
(3, 'user_login', 'New user Rounit singh registered and logged in', 'New User Registration', '{\"user_id\":2,\"user_name\":\"Rounit singh\",\"user_email\":\"Rounitsingh3204@gmail.com\"}', 1, '2025-11-05 07:21:46', '2025-11-04 13:28:28', '2025-11-05 07:21:46'),
(4, 'role_assigned', 'Role \"User\" has been assigned to Rounit singh', 'Role Assigned', '{\"user_id\":2,\"user_name\":\"Rounit singh\",\"old_role\":null,\"new_role\":\"User\"}', 1, '2025-11-05 07:21:46', '2025-11-04 13:28:51', '2025-11-05 07:21:46'),
(5, 'user_login', 'Test User (Admin) logged in to the admin panel', 'Admin Login', '{\"user_id\":1,\"user_name\":\"Test User\",\"user_email\":\"test@example.com\"}', 1, '2025-11-05 07:21:46', '2025-11-05 01:23:34', '2025-11-05 07:21:46'),
(6, 'user_login', 'Test User (Admin) logged in to the admin panel', 'Admin Login', '{\"user_id\":1,\"user_name\":\"Test User\",\"user_email\":\"test@example.com\"}', 1, '2025-11-05 07:21:46', '2025-11-05 05:21:20', '2025-11-05 07:21:46'),
(7, 'user_login', 'Test User (Admin) logged in to the admin panel', 'Admin Login', '{\"user_id\":1,\"user_name\":\"Test User\",\"user_email\":\"test@example.com\"}', 1, '2025-11-05 07:21:46', '2025-11-05 05:21:33', '2025-11-05 07:21:46');

-- --------------------------------------------------------

--
-- Table structure for table `notification_settings`
--

CREATE TABLE `notification_settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `key` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT 1,
  `show_popup` tinyint(1) NOT NULL DEFAULT 1,
  `show_dropdown` tinyint(1) NOT NULL DEFAULT 1,
  `polling_interval` int(11) NOT NULL DEFAULT 30,
  `additional_settings` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`additional_settings`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `notification_settings`
--

INSERT INTO `notification_settings` (`id`, `key`, `title`, `description`, `enabled`, `show_popup`, `show_dropdown`, `polling_interval`, `additional_settings`, `created_at`, `updated_at`) VALUES
(1, 'user_login', 'User Login Notifications', 'Receive notifications when users log in to the system', 1, 1, 1, 30, NULL, '2025-11-05 07:22:32', '2025-11-05 07:22:32'),
(2, 'role_assigned', 'Role Assignment Notifications', 'Receive notifications when roles are assigned or updated', 1, 1, 1, 30, NULL, '2025-11-05 07:22:32', '2025-11-05 07:22:32'),
(3, 'order_updated', 'Order/Booking Update Notifications', 'Receive notifications when orders or bookings are updated', 1, 1, 1, 30, NULL, '2025-11-05 07:22:32', '2025-11-05 07:22:32');

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `role` varchar(255) DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `banned_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `role`, `email_verified_at`, `banned_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Test User', 'test@example.com', 'Admin', '2025-11-04 13:19:55', NULL, '$2y$12$VGeZX/rRbKToeljrM.UcQumRA51USBytYrGBsgVZz/kFdF1KnqcEu', '8LkCkFq20U', '2025-11-04 13:19:55', '2025-11-04 13:20:51'),
(2, 'Rounit singh', 'Rounitsingh3204@gmail.com', 'User', '2025-11-04 13:28:57', NULL, '$2y$12$O2jhOOSPYIhvlo5kq2J/5OG3apwPWArCgfsi0AbXW6JeZqmQxEToa', NULL, '2025-11-04 13:28:28', '2025-11-04 13:28:57');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `blogs`
--
ALTER TABLE `blogs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `booking_categories`
--
ALTER TABLE `booking_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `frontend_settings`
--
ALTER TABLE `frontend_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notifications_type_index` (`type`),
  ADD KEY `notifications_read_index` (`read`),
  ADD KEY `notifications_created_at_index` (`created_at`);

--
-- Indexes for table `notification_settings`
--
ALTER TABLE `notification_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `notification_settings_key_unique` (`key`),
  ADD KEY `notification_settings_key_index` (`key`),
  ADD KEY `notification_settings_enabled_index` (`enabled`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `blogs`
--
ALTER TABLE `blogs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `booking_categories`
--
ALTER TABLE `booking_categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `frontend_settings`
--
ALTER TABLE `frontend_settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `notification_settings`
--
ALTER TABLE `notification_settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
