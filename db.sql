-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Anamakine: localhost:3306
-- Üretim Zamanı: 09 Ağu 2025, 13:08:50
-- Sunucu sürümü: 8.0.43
-- PHP Sürümü: 8.4.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Web Analytics veritabanı şeması
--

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `api_requests`
--

CREATE TABLE `api_requests` (
  `id` bigint NOT NULL,
  `website_id` int NOT NULL,
  `endpoint` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NOT NULL,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `daily_stats`
--

CREATE TABLE `daily_stats` (
  `id` int NOT NULL,
  `website_id` int NOT NULL,
  `date` date NOT NULL,
  `page_views` int DEFAULT '0',
  `unique_visitors` int DEFAULT '0',
  `sessions` int DEFAULT '0',
  `bounce_rate` decimal(5,2) DEFAULT '0.00',
  `avg_session_duration` int DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `events`
--

CREATE TABLE `events` (
  `id` bigint NOT NULL,
  `website_id` int NOT NULL,
  `visitor_id` int NOT NULL,
  `session_id` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NOT NULL,
  `event_type` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NOT NULL,
  `event_category` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci DEFAULT NULL,
  `event_action` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci DEFAULT NULL,
  `event_label` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci DEFAULT NULL,
  `event_value` decimal(10,2) DEFAULT NULL,
  `page_url` text CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `country` varchar(100) COLLATE utf8mb4_turkish_ci DEFAULT NULL,
  `country_code` varchar(5) COLLATE utf8mb4_turkish_ci DEFAULT NULL,
  `region` varchar(100) COLLATE utf8mb4_turkish_ci DEFAULT NULL,
  `city` varchar(100) COLLATE utf8mb4_turkish_ci DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;

--
--




-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `page_views`
--

CREATE TABLE `page_views` (
  `id` bigint NOT NULL,
  `website_id` int NOT NULL,
  `visitor_id` int NOT NULL,
  `session_id` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci DEFAULT NULL,
  `page_url` text CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NOT NULL,
  `page_title` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci DEFAULT NULL,
  `referrer_url` text CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci,
  `browser` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci DEFAULT NULL,
  `os` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci DEFAULT NULL,
  `device_type` enum('desktop','mobile','tablet','other') CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci DEFAULT NULL,
  `country` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci DEFAULT NULL,
  `city` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci DEFAULT NULL,
  `screen_resolution` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci DEFAULT NULL,
  `viewport_size` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci DEFAULT NULL,
  `language` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `country_code` varchar(5) COLLATE utf8mb4_turkish_ci DEFAULT NULL,
  `region` varchar(100) COLLATE utf8mb4_turkish_ci DEFAULT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `timezone` varchar(50) COLLATE utf8mb4_turkish_ci DEFAULT NULL,
  `isp` varchar(255) COLLATE utf8mb4_turkish_ci DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;

--
--



-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `sessions`
--

CREATE TABLE `sessions` (
  `id` bigint NOT NULL,
  `website_id` int NOT NULL,
  `visitor_id` int NOT NULL,
  `session_id` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NOT NULL,
  `start_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `end_time` timestamp NULL DEFAULT NULL,
  `duration` int DEFAULT '0',
  `page_count` int DEFAULT '1',
  `bounce` tinyint(1) DEFAULT '1'
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `system_logs`
--

CREATE TABLE `system_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `severity` enum('error','warning','info') NOT NULL DEFAULT 'error',
  `source` varchar(100) NOT NULL,
  `message` text NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_system_logs_created` (`created_at`),
  KEY `idx_system_logs_severity_created` (`severity`,`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `login_attempts`
--

CREATE TABLE `login_attempts` (
  `bucket_key` char(64) NOT NULL,
  `failed_attempts` smallint unsigned NOT NULL DEFAULT '0',
  `window_started_at` datetime NOT NULL,
  `locked_until` datetime DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`bucket_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `geo_ip_cache`
--

CREATE TABLE `geo_ip_cache` (
  `ip_address` varchar(45) NOT NULL,
  `country` varchar(100) DEFAULT NULL,
  `country_code` varchar(5) DEFAULT NULL,
  `region` varchar(100) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `timezone` varchar(50) DEFAULT NULL,
  `isp` varchar(255) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ip_address`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `geo_lookup_queue`
--

CREATE TABLE `geo_lookup_queue` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `ip_address` varchar(45) NOT NULL,
  `attempts` smallint unsigned NOT NULL DEFAULT '0',
  `available_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_geo_ip` (`ip_address`),
  KEY `idx_geo_queue_available` (`available_at`,`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `notification_deliveries`
--

CREATE TABLE `notification_deliveries` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `notification_type` varchar(32) NOT NULL,
  `period_key` varchar(100) NOT NULL,
  `website_id` int NOT NULL DEFAULT '0',
  `payload` mediumtext NOT NULL,
  `status` varchar(16) NOT NULL DEFAULT 'pending',
  `attempts` smallint unsigned NOT NULL DEFAULT '0',
  `available_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `sent_at` datetime DEFAULT NULL,
  `last_error` varchar(1000) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_notification_delivery` (`user_id`,`notification_type`,`period_key`,`website_id`),
  KEY `idx_notification_due` (`status`,`available_at`,`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `email` varchar(191) NOT NULL,
  `password` varchar(255) NOT NULL,
  `is_admin` tinyint(1) NOT NULL DEFAULT '0',
  `totp_enabled` tinyint(1) NOT NULL DEFAULT '0',
  `totp_secret_encrypted` text DEFAULT NULL,
  `totp_pending_secret_encrypted` text DEFAULT NULL,
  `totp_recovery_codes` text DEFAULT NULL,
  `totp_last_step` bigint DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
--



-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `visitors`
--

CREATE TABLE `visitors` (
  `id` int NOT NULL,
  `visitor_id` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NOT NULL,
  `first_visit` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `last_visit` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;

--
--



-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `websites`
--

CREATE TABLE `websites` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `domain` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NOT NULL,
  `api_key` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NOT NULL,
  `timezone` varchar(64) NOT NULL DEFAULT 'UTC',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;

--
--



--


-- Dökümü yapılmış tablolar için indeksler
--

--
-- Tablo için indeksler `api_requests`
--
ALTER TABLE `api_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_website_created` (`website_id`,`created_at`),
  ADD KEY `idx_ip_created` (`ip_address`,`created_at`);

--
-- Tablo için indeksler `daily_stats`
--
ALTER TABLE `daily_stats`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_website_date` (`website_id`,`date`),
  ADD KEY `idx_date` (`date`);

--
-- Tablo için indeksler `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`),
  ADD KEY `visitor_id` (`visitor_id`),
  ADD KEY `idx_website_event` (`website_id`,`event_type`,`created_at`),
  ADD KEY `idx_created_at` (`created_at`),
  ADD KEY `idx_website_category_created` (`website_id`,`event_category`,`created_at`);

--
-- Tablo için indeksler `page_views`
--
ALTER TABLE `page_views`
  ADD PRIMARY KEY (`id`),
  ADD KEY `visitor_id` (`visitor_id`),
  ADD KEY `idx_website_session` (`website_id`,`session_id`),
  ADD KEY `idx_website_created` (`website_id`,`created_at`),
  ADD KEY `idx_website_visitor_created` (`website_id`,`visitor_id`,`created_at`),
  ADD KEY `idx_created_at` (`created_at`),
  ADD KEY `idx_country` (`country`),
  ADD KEY `idx_country_code` (`country_code`),
  ADD KEY `idx_city` (`city`);

--
-- Tablo için indeksler `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `visitor_id` (`visitor_id`),
  ADD UNIQUE KEY `unique_website_session` (`website_id`,`session_id`),
  ADD KEY `idx_start_time` (`start_time`),
  ADD KEY `idx_website_start_time` (`website_id`,`start_time`);

--
-- Tablo için indeksler `users`
--
ALTER TABLE `users`
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `idx_email` (`email`);

--
-- Tablo için indeksler `visitors`
--
ALTER TABLE `visitors`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `visitor_id` (`visitor_id`),
  ADD KEY `idx_visitor_id` (`visitor_id`);

--
-- Tablo için indeksler `websites`
--
ALTER TABLE `websites`
  ADD UNIQUE KEY `domain` (`domain`),
  ADD UNIQUE KEY `api_key` (`api_key`),
  ADD KEY `idx_api_key` (`api_key`),
  ADD KEY `idx_websites_user_id` (`user_id`),
  ADD CONSTRAINT `fk_websites_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

CREATE TABLE `website_members` (
  `website_id` int NOT NULL,
  `user_id` int NOT NULL,
  `role` enum('read','write') NOT NULL DEFAULT 'read',
  `invited_by` int NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`website_id`, `user_id`),
  KEY `idx_website_members_user` (`user_id`),
  CONSTRAINT `fk_website_members_website` FOREIGN KEY (`website_id`) REFERENCES `websites` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_website_members_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_website_members_invited_by` FOREIGN KEY (`invited_by`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;

CREATE TABLE `website_invitations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `website_id` int NOT NULL,
  `email` varchar(191) NOT NULL,
  `token_hash` char(64) NOT NULL,
  `role` enum('read','write') NOT NULL DEFAULT 'read',
  `invited_by` int NOT NULL,
  `expires_at` datetime NOT NULL,
  `accepted_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_website_invitation_token` (`token_hash`),
  KEY `idx_website_invitation_email` (`website_id`, `email`, `accepted_at`),
  CONSTRAINT `fk_website_invitations_website` FOREIGN KEY (`website_id`) REFERENCES `websites` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_website_invitations_user` FOREIGN KEY (`invited_by`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;

--
-- AUTO_INCREMENT anahtarlarının ayarlanması
--

--
-- Tablo için AUTO_INCREMENT anahtarı `api_requests`
--
ALTER TABLE `api_requests`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT anahtarı `daily_stats`
--
ALTER TABLE `daily_stats`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT anahtarı `events`
--
ALTER TABLE `events`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT anahtarı `page_views`
--
ALTER TABLE `page_views`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT anahtarı `sessions`
--
ALTER TABLE `sessions`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT;

--
--
-- Tablo için AUTO_INCREMENT anahtarı `visitors`
--
ALTER TABLE `visitors`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
