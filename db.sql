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
-- Veritabanı: `networktoolkit_networktoolkit`
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
-- Tablo döküm verisi `events`
--

INSERT INTO `events` (`id`, `website_id`, `visitor_id`, `session_id`, `event_type`, `event_category`, `event_action`, `event_label`, `event_value`, `page_url`, `created_at`, `country`, `country_code`, `region`, `city`) VALUES
(1, 5, 1, 'session-eqjucbh5h-1754698907567', 'timing', 'time-on-page', 'ping', '/', 270.00, 'https://ondernet.net/', '2025-08-09 00:26:17', NULL, NULL, NULL, NULL),
(2, 5, 1, 'session-eqjucbh5h-1754698907567', 'timing', 'time-on-page', 'ping', '/', 300.00, 'https://ondernet.net/', '2025-08-09 00:26:47', NULL, NULL, NULL, NULL),
(3, 5, 1, 'session-eqjucbh5h-1754698907567', 'timing', 'time-on-page', 'ping', '/', 330.00, 'https://ondernet.net/', '2025-08-09 00:27:17', NULL, NULL, NULL, NULL),
(4, 5, 1, 'session-eqjucbh5h-1754698907567', 'timing', 'time-on-page', 'ping', '/', 360.00, 'https://ondernet.net/', '2025-08-09 00:27:47', NULL, NULL, NULL, NULL),
(5, 5, 1, 'session-eqjucbh5h-1754698907567', 'timing', 'time-on-page', 'ping', '/', 390.00, 'https://ondernet.net/', '2025-08-09 00:28:17', NULL, NULL, NULL, NULL),
(6, 1, 2, 'session-t2yphpy4h-1754699572667', 'performance', 'page-load', 'load-time', '/quiz', 747.00, 'https://quiz-test.online/quiz', '2025-08-09 00:32:53', NULL, NULL, NULL, NULL),
(7, 1, 2, 'session-t2yphpy4h-1754699572667', 'performance', 'paint', 'first-paint', '/quiz', 548.00, 'https://quiz-test.online/quiz', '2025-08-09 00:32:53', NULL, NULL, NULL, NULL),
(8, 1, 2, 'session-t2yphpy4h-1754699572667', 'performance', 'paint', 'first-contentful-paint', '/quiz', 548.00, 'https://quiz-test.online/quiz', '2025-08-09 00:32:53', NULL, NULL, NULL, NULL),
(9, 1, 2, 'session-t2yphpy4h-1754699572667', 'performance', 'dom-ready', 'time', '/quiz', 528.00, 'https://quiz-test.online/quiz', '2025-08-09 00:32:53', NULL, NULL, NULL, NULL),
(10, 1, 2, 'session-t2yphpy4h-1754699572667', 'timing', 'time-on-page', 'duration', '/quiz', 16.00, 'https://quiz-test.online/quiz', '2025-08-09 00:33:09', NULL, NULL, NULL, NULL),
(11, 1, 2, 'session-t2yphpy4h-1754699572667', 'performance', 'dom-ready', 'time', '/quiz/setup', 343.00, 'https://quiz-test.online/quiz/setup', '2025-08-09 00:33:10', NULL, NULL, NULL, NULL),
(12, 1, 2, 'session-t2yphpy4h-1754699572667', 'performance', 'page-load', 'load-time', '/quiz/setup', 693.00, 'https://quiz-test.online/quiz/setup', '2025-08-09 00:33:10', NULL, NULL, NULL, NULL),
(13, 1, 2, 'session-t2yphpy4h-1754699572667', 'performance', 'paint', 'first-paint', '/quiz/setup', 364.00, 'https://quiz-test.online/quiz/setup', '2025-08-09 00:33:10', NULL, NULL, NULL, NULL),
(14, 1, 2, 'session-t2yphpy4h-1754699572667', 'performance', 'paint', 'first-contentful-paint', '/quiz/setup', 364.00, 'https://quiz-test.online/quiz/setup', '2025-08-09 00:33:10', NULL, NULL, NULL, NULL),
(15, 1, 2, 'session-t2yphpy4h-1754699572667', 'scroll', 'scroll-depth', 'reached', '25%', 25.00, 'https://quiz-test.online/quiz/setup', '2025-08-09 00:33:17', NULL, NULL, NULL, NULL),
(16, 1, 2, 'session-t2yphpy4h-1754699572667', 'scroll', 'scroll-depth', 'reached', '50%', 50.00, 'https://quiz-test.online/quiz/setup', '2025-08-09 00:33:18', NULL, NULL, NULL, NULL),
(17, 1, 2, 'session-t2yphpy4h-1754699572667', 'scroll', 'scroll-depth', 'reached', '75%', 75.00, 'https://quiz-test.online/quiz/setup', '2025-08-09 00:33:18', NULL, NULL, NULL, NULL),
(18, 1, 2, 'session-t2yphpy4h-1754699572667', 'scroll', 'scroll-depth', 'reached', '100%', 100.00, 'https://quiz-test.online/quiz/setup', '2025-08-09 00:33:18', NULL, NULL, NULL, NULL),
(19, 1, 2, 'session-t2yphpy4h-1754699572667', 'timing', 'time-on-page', 'duration', '/quiz/setup', 11.00, 'https://quiz-test.online/quiz/setup', '2025-08-09 00:33:20', NULL, NULL, NULL, NULL),
(20, 1, 2, 'session-t2yphpy4h-1754699572667', 'performance', 'paint', 'first-paint', '/quiz/play', 396.00, 'https://quiz-test.online/quiz/play', '2025-08-09 00:33:21', NULL, NULL, NULL, NULL),
(21, 1, 2, 'session-t2yphpy4h-1754699572667', 'performance', 'dom-ready', 'time', '/quiz/play', 373.00, 'https://quiz-test.online/quiz/play', '2025-08-09 00:33:21', NULL, NULL, NULL, NULL),
(22, 1, 2, 'session-t2yphpy4h-1754699572667', 'performance', 'page-load', 'load-time', '/quiz/play', 751.00, 'https://quiz-test.online/quiz/play', '2025-08-09 00:33:21', NULL, NULL, NULL, NULL),
(23, 1, 2, 'session-t2yphpy4h-1754699572667', 'performance', 'paint', 'first-contentful-paint', '/quiz/play', 396.00, 'https://quiz-test.online/quiz/play', '2025-08-09 00:33:21', NULL, NULL, NULL, NULL),
(24, 1, 2, 'session-t2yphpy4h-1754699572667', 'timing', 'time-on-page', 'duration', '/quiz/play', 6.00, 'https://quiz-test.online/quiz/play', '2025-08-09 00:33:27', NULL, NULL, NULL, NULL),
(25, 2, 3, 'session-m19h56i66-1754699703945', 'performance', 'dom-ready', 'time', '/', 1203.00, 'https://ondernet.net/', '2025-08-09 00:40:23', NULL, NULL, NULL, NULL),
(26, 2, 3, 'session-m19h56i66-1754699703945', 'performance', 'page-load', 'load-time', '/', 2366.00, 'https://ondernet.net/', '2025-08-09 00:40:23', NULL, NULL, NULL, NULL),
(27, 2, 3, 'session-m19h56i66-1754699703945', 'performance', 'first-paint', 'time', '/', 1016.00, 'https://ondernet.net/', '2025-08-09 00:40:23', NULL, NULL, NULL, NULL),
(28, 2, 3, 'session-m19h56i66-1754699703945', 'timing', 'time-on-page', 'duration', '/', 9.00, 'https://ondernet.net/', '2025-08-09 00:40:31', NULL, NULL, NULL, NULL),
(29, 3, 4, 'session-tuxxzhlmm-1754700109751', 'performance', 'page-load', 'load-time', '/', 288.00, 'https://haaberler.com/', '2025-08-09 00:41:50', NULL, NULL, NULL, NULL),
(30, 3, 4, 'session-tuxxzhlmm-1754700109751', 'performance', 'dom-ready', 'time', '/', 183.00, 'https://haaberler.com/', '2025-08-09 00:41:50', NULL, NULL, NULL, NULL),
(31, 3, 4, 'session-tuxxzhlmm-1754700109751', 'performance', 'paint', 'first-paint', '/', 140.00, 'https://haaberler.com/', '2025-08-09 00:41:50', NULL, NULL, NULL, NULL),
(32, 3, 4, 'session-tuxxzhlmm-1754700109751', 'performance', 'paint', 'first-contentful-paint', '/', 140.00, 'https://haaberler.com/', '2025-08-09 00:41:50', NULL, NULL, NULL, NULL),
(33, 3, 4, 'session-tuxxzhlmm-1754700109751', 'timing', 'time-on-page', 'duration', '/', 5.00, 'https://haaberler.com/', '2025-08-09 00:41:54', NULL, NULL, NULL, NULL),
(34, 4, 5, 'session-shk5urrmt-1754700185386', 'performance', 'page-load', 'load-time', '/', 1014.00, 'https://cmd-test.site/', '2025-08-09 00:43:06', NULL, NULL, NULL, NULL),
(35, 4, 5, 'session-shk5urrmt-1754700185386', 'performance', 'dom-ready', 'time', '/', 512.00, 'https://cmd-test.site/', '2025-08-09 00:43:06', NULL, NULL, NULL, NULL),
(36, 4, 5, 'session-shk5urrmt-1754700185386', 'performance', 'paint', 'first-paint', '/', 464.00, 'https://cmd-test.site/', '2025-08-09 00:43:06', NULL, NULL, NULL, NULL),
(37, 4, 5, 'session-shk5urrmt-1754700185386', 'performance', 'paint', 'first-contentful-paint', '/', 464.00, 'https://cmd-test.site/', '2025-08-09 00:43:06', NULL, NULL, NULL, NULL),
(38, 4, 5, 'session-shk5urrmt-1754700185386', 'scroll', 'scroll-depth', 'reached', '25%', 25.00, 'https://cmd-test.site/', '2025-08-09 00:43:07', NULL, NULL, NULL, NULL),
(39, 4, 5, 'session-shk5urrmt-1754700185386', 'scroll', 'scroll-depth', 'reached', '75%', 75.00, 'https://cmd-test.site/', '2025-08-09 00:43:07', NULL, NULL, NULL, NULL),
(40, 4, 5, 'session-shk5urrmt-1754700185386', 'scroll', 'scroll-depth', 'reached', '50%', 50.00, 'https://cmd-test.site/', '2025-08-09 00:43:07', NULL, NULL, NULL, NULL),
(41, 4, 5, 'session-shk5urrmt-1754700185386', 'scroll', 'scroll-depth', 'reached', '100%', 100.00, 'https://cmd-test.site/', '2025-08-09 00:43:07', NULL, NULL, NULL, NULL),
(42, 4, 5, 'session-shk5urrmt-1754700185386', 'timing', 'time-on-page', 'duration', '/', 20.00, 'https://cmd-test.site/', '2025-08-09 00:43:25', NULL, NULL, NULL, NULL),
(43, 5, 6, 'session-6cbrutzgr-1754700274483', 'performance', 'page-load', 'load-time', '/', 1745.00, 'https://tekne-sepeti.com/', '2025-08-09 00:44:35', NULL, NULL, NULL, NULL),
(44, 5, 6, 'session-6cbrutzgr-1754700274483', 'performance', 'dom-ready', 'time', '/', 957.00, 'https://tekne-sepeti.com/', '2025-08-09 00:44:35', NULL, NULL, NULL, NULL),
(45, 5, 6, 'session-6cbrutzgr-1754700274483', 'performance', 'paint', 'first-paint', '/', 920.00, 'https://tekne-sepeti.com/', '2025-08-09 00:44:35', NULL, NULL, NULL, NULL),
(46, 5, 6, 'session-6cbrutzgr-1754700274483', 'performance', 'paint', 'first-contentful-paint', '/', 920.00, 'https://tekne-sepeti.com/', '2025-08-09 00:44:35', NULL, NULL, NULL, NULL),
(47, 5, 6, 'session-6cbrutzgr-1754700274483', 'timing', 'time-on-page', 'duration', '/', 5.00, 'https://tekne-sepeti.com/', '2025-08-09 00:44:40', NULL, NULL, NULL, NULL),
(48, 6, 7, 'session-6ckmk2dm9-1754700348158', 'performance', 'dom-ready', 'time', '/', 729.00, 'https://webustasipro.com/', '2025-08-09 00:45:49', NULL, NULL, NULL, NULL),
(49, 6, 7, 'session-6ckmk2dm9-1754700348158', 'performance', 'page-load', 'load-time', '/', 1855.00, 'https://webustasipro.com/', '2025-08-09 00:45:49', NULL, NULL, NULL, NULL),
(50, 6, 7, 'session-6ckmk2dm9-1754700348158', 'performance', 'paint', 'first-paint', '/', 588.00, 'https://webustasipro.com/', '2025-08-09 00:45:49', NULL, NULL, NULL, NULL),
(51, 6, 7, 'session-6ckmk2dm9-1754700348158', 'performance', 'paint', 'first-contentful-paint', '/', 588.00, 'https://webustasipro.com/', '2025-08-09 00:45:49', NULL, NULL, NULL, NULL),
(52, 6, 7, 'session-6ckmk2dm9-1754700348158', 'scroll', 'scroll-depth', 'reached', '25%', 25.00, 'https://webustasipro.com/', '2025-08-09 00:45:57', NULL, NULL, NULL, NULL),
(53, 6, 7, 'session-6ckmk2dm9-1754700348158', 'scroll', 'scroll-depth', 'reached', '50%', 50.00, 'https://webustasipro.com/', '2025-08-09 00:45:57', NULL, NULL, NULL, NULL),
(54, 6, 7, 'session-6ckmk2dm9-1754700348158', 'scroll', 'scroll-depth', 'reached', '75%', 75.00, 'https://webustasipro.com/', '2025-08-09 00:45:57', NULL, NULL, NULL, NULL),
(55, 6, 7, 'session-6ckmk2dm9-1754700348158', 'scroll', 'scroll-depth', 'reached', '100%', 100.00, 'https://webustasipro.com/', '2025-08-09 00:46:00', NULL, NULL, NULL, NULL),
(56, 6, 7, 'session-6ckmk2dm9-1754700348158', 'timing', 'time-on-page', 'duration', '/', 15.00, 'https://webustasipro.com/', '2025-08-09 00:46:03', NULL, NULL, NULL, NULL),
(57, 7, 8, 'session-n0jwmscv4-1754700463277', 'performance', 'page-load', 'load-time', '/', 1960.00, 'https://kocaenerji.com/', '2025-08-09 00:47:43', NULL, NULL, NULL, NULL),
(58, 7, 8, 'session-n0jwmscv4-1754700463277', 'performance', 'dom-ready', 'time', '/', 1534.00, 'https://kocaenerji.com/', '2025-08-09 00:47:43', NULL, NULL, NULL, NULL),
(59, 7, 8, 'session-n0jwmscv4-1754700463277', 'performance', 'paint', 'first-contentful-paint', '/', 1304.00, 'https://kocaenerji.com/', '2025-08-09 00:47:43', NULL, NULL, NULL, NULL),
(60, 7, 8, 'session-n0jwmscv4-1754700463277', 'performance', 'paint', 'first-paint', '/', 1304.00, 'https://kocaenerji.com/', '2025-08-09 00:47:43', NULL, NULL, NULL, NULL),
(61, 7, 8, 'session-n0jwmscv4-1754700463277', 'timing', 'time-on-page', 'duration', '/', 5.00, 'https://kocaenerji.com/', '2025-08-09 00:47:48', NULL, NULL, NULL, NULL),
(62, 8, 9, 'session-gajtri4ys-1754700533920', 'performance', 'page-load', 'load-time', '/', 914.00, 'https://hedefsolar.com/', '2025-08-09 00:48:54', NULL, NULL, NULL, NULL),
(63, 8, 9, 'session-gajtri4ys-1754700533920', 'performance', 'dom-ready', 'time', '/', 631.00, 'https://hedefsolar.com/', '2025-08-09 00:48:54', NULL, NULL, NULL, NULL),
(64, 8, 9, 'session-gajtri4ys-1754700533920', 'performance', 'paint', 'first-paint', '/', 524.00, 'https://hedefsolar.com/', '2025-08-09 00:48:54', NULL, NULL, NULL, NULL),
(65, 8, 9, 'session-gajtri4ys-1754700533920', 'performance', 'paint', 'first-contentful-paint', '/', 524.00, 'https://hedefsolar.com/', '2025-08-09 00:48:54', NULL, NULL, NULL, NULL),
(66, 8, 9, 'session-gajtri4ys-1754700533920', 'timing', 'time-on-page', 'duration', '/', 19.00, 'https://hedefsolar.com/', '2025-08-09 00:49:13', NULL, NULL, NULL, NULL),
(67, 2, 1, 'session-zywtbzbqp-1754701196524', 'performance', 'page-load', 'load-time', '/', 6307.00, 'https://ondernet.net/', '2025-08-09 00:59:58', NULL, NULL, NULL, NULL),
(68, 2, 1, 'session-zywtbzbqp-1754701196524', 'performance', 'dom-ready', 'time', '/', 2642.00, 'https://ondernet.net/', '2025-08-09 00:59:58', NULL, NULL, NULL, NULL),
(69, 2, 1, 'session-zywtbzbqp-1754701196524', 'timing', 'time-on-page', 'ping', '/', 30.00, 'https://ondernet.net/', '2025-08-09 01:00:26', NULL, NULL, NULL, NULL),
(70, 2, 1, 'session-zywtbzbqp-1754701196524', 'timing', 'time-on-page', 'ping', '/', 60.00, 'https://ondernet.net/', '2025-08-09 01:00:56', NULL, NULL, NULL, NULL),
(71, 2, 1, 'session-zywtbzbqp-1754701196524', 'timing', 'time-on-page', 'ping', '/', 90.00, 'https://ondernet.net/', '2025-08-09 01:01:26', NULL, NULL, NULL, NULL),
(72, 2, 1, 'session-zywtbzbqp-1754701196524', 'timing', 'time-on-page', 'ping', '/', 120.00, 'https://ondernet.net/', '2025-08-09 01:01:56', NULL, NULL, NULL, NULL),
(73, 2, 10, 'session-vfkr4w57u-1754702827937', 'performance', 'dom-ready', 'time', '/active-directoryde-folder-redirection-kullanimi/', 2339.00, 'https://ondernet.net/active-directoryde-folder-redirection-kullanimi/', '2025-08-09 01:27:10', NULL, NULL, NULL, NULL),
(74, 2, 10, 'session-vfkr4w57u-1754702827937', 'performance', 'paint', 'first-paint', '/active-directoryde-folder-redirection-kullanimi/', 2348.00, 'https://ondernet.net/active-directoryde-folder-redirection-kullanimi/', '2025-08-09 01:27:10', NULL, NULL, NULL, NULL),
(75, 2, 10, 'session-vfkr4w57u-1754702827937', 'performance', 'page-load', 'load-time', '/active-directoryde-folder-redirection-kullanimi/', 3747.00, 'https://ondernet.net/active-directoryde-folder-redirection-kullanimi/', '2025-08-09 01:27:10', NULL, NULL, NULL, NULL),
(76, 2, 10, 'session-vfkr4w57u-1754702827937', 'performance', 'paint', 'first-contentful-paint', '/active-directoryde-folder-redirection-kullanimi/', 2348.00, 'https://ondernet.net/active-directoryde-folder-redirection-kullanimi/', '2025-08-09 01:27:11', NULL, NULL, NULL, NULL),
(77, 2, 11, 'session-qc7jhwoq5-1754703015871', 'performance', 'page-load', 'load-time', '/category/misconfiguration/', 1555.00, 'https://ondernet.net/category/misconfiguration/', '2025-08-09 01:30:16', NULL, NULL, NULL, NULL),
(78, 2, 11, 'session-qc7jhwoq5-1754703015871', 'performance', 'paint', 'first-paint', '/category/misconfiguration/', 1260.00, 'https://ondernet.net/category/misconfiguration/', '2025-08-09 01:30:16', NULL, NULL, NULL, NULL),
(79, 2, 11, 'session-qc7jhwoq5-1754703015871', 'performance', 'dom-ready', 'time', '/category/misconfiguration/', 1168.00, 'https://ondernet.net/category/misconfiguration/', '2025-08-09 01:30:16', NULL, NULL, NULL, NULL),
(80, 2, 11, 'session-qc7jhwoq5-1754703015871', 'performance', 'paint', 'first-contentful-paint', '/category/misconfiguration/', 1260.00, 'https://ondernet.net/category/misconfiguration/', '2025-08-09 01:30:16', NULL, NULL, NULL, NULL),
(81, 2, 11, 'session-qc7jhwoq5-1754703015871', 'timing', 'time-on-page', 'duration', '/category/misconfiguration/', 1.00, 'https://ondernet.net/category/misconfiguration/', '2025-08-09 01:30:17', NULL, NULL, NULL, NULL),
(82, 2, 11, 'session-qc7jhwoq5-1754703015871', 'performance', 'dom-ready', 'time', '/', 898.00, 'https://ondernet.net/', '2025-08-09 01:30:18', NULL, NULL, NULL, NULL),
(83, 2, 11, 'session-qc7jhwoq5-1754703015871', 'performance', 'paint', 'first-paint', '/', 936.00, 'https://ondernet.net/', '2025-08-09 01:30:18', NULL, NULL, NULL, NULL),
(84, 2, 11, 'session-qc7jhwoq5-1754703015871', 'performance', 'page-load', 'load-time', '/', 1078.00, 'https://ondernet.net/', '2025-08-09 01:30:18', NULL, NULL, NULL, NULL),
(85, 2, 11, 'session-qc7jhwoq5-1754703015871', 'performance', 'paint', 'first-contentful-paint', '/', 936.00, 'https://ondernet.net/', '2025-08-09 01:30:18', NULL, NULL, NULL, NULL),
(86, 2, 11, 'session-qc7jhwoq5-1754703015871', 'timing', 'time-on-page', 'duration', '/', 6.00, 'https://ondernet.net/', '2025-08-09 01:30:24', NULL, NULL, NULL, NULL),
(87, 2, 11, 'session-qc7jhwoq5-1754703015871', 'timing', 'time-on-page', 'duration', '/', 6.00, 'https://ondernet.net/', '2025-08-09 01:30:24', NULL, NULL, NULL, NULL),
(88, 2, 12, 'session-ejhkbfeg5-1754697600083', 'performance', 'page-load', 'load-time', '/exchange-online-powershell-e-nasil-baglanilir/', 80.00, 'https://ondernet.net/exchange-online-powershell-e-nasil-baglanilir/', '2025-08-09 01:33:41', NULL, NULL, NULL, NULL),
(89, 2, 12, 'session-ejhkbfeg5-1754697600083', 'performance', 'dom-ready', 'time', '/exchange-online-powershell-e-nasil-baglanilir/', 10.00, 'https://ondernet.net/exchange-online-powershell-e-nasil-baglanilir/', '2025-08-09 01:33:41', NULL, NULL, NULL, NULL),
(90, 5, 13, 'session-g60zmndfw-1754703363072', 'performance', 'page-load', 'load-time', '/', 3662.00, 'https://tekne-sepeti.com/', '2025-08-09 01:36:03', NULL, NULL, NULL, NULL),
(91, 5, 13, 'session-g60zmndfw-1754703363072', 'performance', 'paint', 'first-paint', '/', 1936.00, 'https://tekne-sepeti.com/', '2025-08-09 01:36:03', NULL, NULL, NULL, NULL),
(92, 5, 13, 'session-g60zmndfw-1754703363072', 'performance', 'paint', 'first-contentful-paint', '/', 1936.00, 'https://tekne-sepeti.com/', '2025-08-09 01:36:03', NULL, NULL, NULL, NULL),
(93, 5, 13, 'session-g60zmndfw-1754703363072', 'performance', 'dom-ready', 'time', '/', 2231.00, 'https://tekne-sepeti.com/', '2025-08-09 01:36:03', NULL, NULL, NULL, NULL),
(94, 6, 14, 'session-guq1arkao-1754704678204', 'performance', 'paint', 'first-paint', '/', 1444.00, 'https://webustasipro.com/', '2025-08-09 01:57:59', NULL, NULL, NULL, NULL),
(95, 6, 14, 'session-guq1arkao-1754704678204', 'performance', 'dom-ready', 'time', '/', 1839.00, 'https://webustasipro.com/', '2025-08-09 01:57:59', NULL, NULL, NULL, NULL),
(96, 6, 14, 'session-guq1arkao-1754704678204', 'performance', 'paint', 'first-contentful-paint', '/', 1444.00, 'https://webustasipro.com/', '2025-08-09 01:57:59', NULL, NULL, NULL, NULL),
(97, 8, 15, 'session-mwr6vtya9-1754704899460', 'performance', 'paint', 'first-paint', '/', 1189.00, 'https://www.hedefsolar.com/', '2025-08-09 02:01:40', NULL, NULL, NULL, NULL),
(98, 8, 15, 'session-mwr6vtya9-1754704899460', 'performance', 'dom-ready', 'time', '/', 1260.00, 'https://www.hedefsolar.com/', '2025-08-09 02:01:40', NULL, NULL, NULL, NULL),
(99, 8, 15, 'session-mwr6vtya9-1754704899460', 'performance', 'page-load', 'load-time', '/', 1882.00, 'https://www.hedefsolar.com/', '2025-08-09 02:01:40', NULL, NULL, NULL, NULL),
(100, 8, 15, 'session-mwr6vtya9-1754704899460', 'performance', 'paint', 'first-contentful-paint', '/', 1189.00, 'https://www.hedefsolar.com/', '2025-08-09 02:01:40', NULL, NULL, NULL, NULL),
(101, 8, 15, 'session-mwr6vtya9-1754704899460', 'timing', 'time-on-page', 'duration', '/', 1.00, 'https://www.hedefsolar.com/', '2025-08-09 02:01:40', NULL, NULL, NULL, NULL),
(102, 8, 16, 'session-qtqm1jtnf-1754704903811', 'performance', 'page-load', 'load-time', '/blog', 1628.00, 'https://www.hedefsolar.com/blog', '2025-08-09 02:01:44', NULL, NULL, NULL, NULL),
(103, 8, 16, 'session-qtqm1jtnf-1754704903811', 'performance', 'dom-ready', 'time', '/blog', 1137.00, 'https://www.hedefsolar.com/blog', '2025-08-09 02:01:44', NULL, NULL, NULL, NULL),
(104, 8, 16, 'session-qtqm1jtnf-1754704903811', 'performance', 'paint', 'first-paint', '/blog', 1145.00, 'https://www.hedefsolar.com/blog', '2025-08-09 02:01:44', NULL, NULL, NULL, NULL),
(105, 8, 16, 'session-qtqm1jtnf-1754704903811', 'performance', 'paint', 'first-contentful-paint', '/blog', 1145.00, 'https://www.hedefsolar.com/blog', '2025-08-09 02:01:44', NULL, NULL, NULL, NULL),
(106, 8, 16, 'session-qtqm1jtnf-1754704903811', 'timing', 'time-on-page', 'duration', '/blog', 1.00, 'https://www.hedefsolar.com/blog', '2025-08-09 02:01:45', NULL, NULL, NULL, NULL),
(107, 8, 17, 'session-v4vuwmpeu-1754704906983', 'performance', 'paint', 'first-contentful-paint', '/contact', 1183.00, 'https://www.hedefsolar.com/contact', '2025-08-09 02:01:47', NULL, NULL, NULL, NULL),
(108, 8, 17, 'session-v4vuwmpeu-1754704906983', 'performance', 'page-load', 'load-time', '/contact', 1644.00, 'https://www.hedefsolar.com/contact', '2025-08-09 02:01:47', NULL, NULL, NULL, NULL),
(109, 8, 17, 'session-v4vuwmpeu-1754704906983', 'performance', 'dom-ready', 'time', '/contact', 1175.00, 'https://www.hedefsolar.com/contact', '2025-08-09 02:01:47', NULL, NULL, NULL, NULL),
(110, 8, 17, 'session-v4vuwmpeu-1754704906983', 'performance', 'paint', 'first-paint', '/contact', 1183.00, 'https://www.hedefsolar.com/contact', '2025-08-09 02:01:47', NULL, NULL, NULL, NULL),
(111, 8, 17, 'session-v4vuwmpeu-1754704906983', 'timing', 'time-on-page', 'duration', '/contact', 1.00, 'https://www.hedefsolar.com/contact', '2025-08-09 02:01:48', NULL, NULL, NULL, NULL),
(112, 8, 18, 'session-ozhfpalkg-1754704910119', 'performance', 'dom-ready', 'time', '/about', 1172.00, 'https://www.hedefsolar.com/about', '2025-08-09 02:01:50', NULL, NULL, NULL, NULL),
(113, 8, 18, 'session-ozhfpalkg-1754704910119', 'performance', 'page-load', 'load-time', '/about', 1635.00, 'https://www.hedefsolar.com/about', '2025-08-09 02:01:50', NULL, NULL, NULL, NULL),
(114, 8, 18, 'session-ozhfpalkg-1754704910119', 'performance', 'paint', 'first-paint', '/about', 1178.00, 'https://www.hedefsolar.com/about', '2025-08-09 02:01:50', NULL, NULL, NULL, NULL),
(115, 8, 18, 'session-ozhfpalkg-1754704910119', 'performance', 'paint', 'first-contentful-paint', '/about', 1178.00, 'https://www.hedefsolar.com/about', '2025-08-09 02:01:50', NULL, NULL, NULL, NULL),
(116, 8, 18, 'session-ozhfpalkg-1754704910119', 'timing', 'time-on-page', 'duration', '/about', 1.00, 'https://www.hedefsolar.com/about', '2025-08-09 02:01:51', NULL, NULL, NULL, NULL),
(117, 2, 19, 'session-zh3sqhy20-1754704800051', 'performance', 'dom-ready', 'time', '/tag/microsoft/', 20.00, 'https://ondernet.net/tag/microsoft/', '2025-08-09 02:28:35', NULL, NULL, NULL, NULL),
(118, 2, 19, 'session-zh3sqhy20-1754704800051', 'performance', 'page-load', 'load-time', '/tag/microsoft/', 50.00, 'https://ondernet.net/tag/microsoft/', '2025-08-09 02:28:36', NULL, NULL, NULL, NULL),
(119, 2, 19, 'session-zh3sqhy20-1754704800051', 'performance', 'page-load', 'load-time', '/category/cyber-security/', 50.00, 'https://ondernet.net/category/cyber-security/', '2025-08-09 02:50:33', NULL, NULL, NULL, NULL),
(120, 2, 19, 'session-zh3sqhy20-1754704800051', 'performance', 'dom-ready', 'time', '/category/cyber-security/', 20.00, 'https://ondernet.net/category/cyber-security/', '2025-08-09 02:50:35', NULL, NULL, NULL, NULL),
(121, 2, 20, 'session-zh3sqhy20-1754704800061', 'performance', 'dom-ready', 'time', '/tag/siem/', 10.00, 'https://ondernet.net/tag/siem/', '2025-08-09 02:58:14', NULL, NULL, NULL, NULL),
(122, 2, 20, 'session-zh3sqhy20-1754704800061', 'performance', 'page-load', 'load-time', '/tag/siem/', 60.00, 'https://ondernet.net/tag/siem/', '2025-08-09 02:58:15', NULL, NULL, NULL, NULL),
(123, 7, 21, 'session-uhw4svxql-1754708400137', 'performance', 'dom-ready', 'time', '/', 100.00, 'http://kocaenerji.com/', '2025-08-09 03:06:17', NULL, NULL, NULL, NULL),
(124, 7, 21, 'session-uhw4svxql-1754708400137', 'performance', 'page-load', 'load-time', '/', 140.00, 'http://kocaenerji.com/', '2025-08-09 03:06:18', NULL, NULL, NULL, NULL),
(125, 2, 22, 'session-301l3c8p5-1754697600021', 'performance', 'page-load', 'load-time', '/tag/soc-analyst/', 90.00, 'https://ondernet.net/tag/soc-analyst/', '2025-08-09 03:56:41', NULL, NULL, NULL, NULL),
(126, 2, 22, 'session-301l3c8p5-1754697600021', 'performance', 'dom-ready', 'time', '/tag/soc-analyst/', 80.00, 'https://ondernet.net/tag/soc-analyst/', '2025-08-09 03:56:42', NULL, NULL, NULL, NULL),
(127, 2, 23, 'session-301l3c8p5-1754712000021', 'performance', 'dom-ready', 'time', '/tag/incident-response/', 10.00, 'https://ondernet.net/tag/incident-response/', '2025-08-09 04:07:22', NULL, NULL, NULL, NULL),
(128, 2, 23, 'session-301l3c8p5-1754712000021', 'performance', 'page-load', 'load-time', '/tag/incident-response/', 60.00, 'https://ondernet.net/tag/incident-response/', '2025-08-09 04:07:23', NULL, NULL, NULL, NULL),
(129, 2, 24, 'session-7wzr1nhi3-1754712520076', 'performance', 'paint', 'first-contentful-paint', '/2025-yilinda-kullanabileceginiz-web-tarayicilari/', 236.00, 'https://ondernet.net/2025-yilinda-kullanabileceginiz-web-tarayicilari/', '2025-08-09 04:08:41', NULL, NULL, NULL, NULL),
(130, 2, 24, 'session-7wzr1nhi3-1754712520076', 'performance', 'paint', 'first-paint', '/2025-yilinda-kullanabileceginiz-web-tarayicilari/', 236.00, 'https://ondernet.net/2025-yilinda-kullanabileceginiz-web-tarayicilari/', '2025-08-09 04:08:41', NULL, NULL, NULL, NULL),
(131, 2, 24, 'session-7wzr1nhi3-1754712520076', 'performance', 'page-load', 'load-time', '/2025-yilinda-kullanabileceginiz-web-tarayicilari/', 327.00, 'https://ondernet.net/2025-yilinda-kullanabileceginiz-web-tarayicilari/', '2025-08-09 04:08:41', NULL, NULL, NULL, NULL),
(132, 2, 24, 'session-7wzr1nhi3-1754712520076', 'performance', 'dom-ready', 'time', '/2025-yilinda-kullanabileceginiz-web-tarayicilari/', 294.00, 'https://ondernet.net/2025-yilinda-kullanabileceginiz-web-tarayicilari/', '2025-08-09 04:08:41', NULL, NULL, NULL, NULL),
(133, 2, 25, 'session-gfmt2f8et-1754712000024', 'performance', 'dom-ready', 'time', '/category/elek/', 10.00, 'https://ondernet.net/category/elek/', '2025-08-09 04:42:14', NULL, NULL, NULL, NULL),
(134, 2, 25, 'session-gfmt2f8et-1754712000024', 'performance', 'page-load', 'load-time', '/category/elek/', 50.00, 'https://ondernet.net/category/elek/', '2025-08-09 04:42:16', NULL, NULL, NULL, NULL),
(135, 8, 26, 'session-301l3c8p5-1754697600041', 'performance', 'dom-ready', 'time', '/references/detail/4', 10.00, 'https://www.hedefsolar.com/references/detail/4', '2025-08-09 04:53:29', NULL, NULL, NULL, NULL),
(136, 8, 26, 'session-301l3c8p5-1754697600041', 'performance', 'page-load', 'load-time', '/references/detail/4', 50.00, 'https://www.hedefsolar.com/references/detail/4', '2025-08-09 04:53:29', NULL, NULL, NULL, NULL),
(137, 1, 27, 'session-lec6tn7bd-1754697600011', 'performance', 'page-load', 'load-time', '/login', 50.00, 'http://www.quiz-test.online/login', '2025-08-09 05:22:21', NULL, NULL, NULL, NULL),
(138, 1, 27, 'session-lec6tn7bd-1754697600011', 'performance', 'dom-ready', 'time', '/login', 10.00, 'http://www.quiz-test.online/login', '2025-08-09 05:22:21', NULL, NULL, NULL, NULL),
(139, 2, 28, 'session-gfmt2f8et-1754719200021', 'performance', 'page-load', 'load-time', '/', 50.00, 'https://ondernet.net/', '2025-08-09 06:31:47', NULL, NULL, NULL, NULL),
(140, 2, 28, 'session-gfmt2f8et-1754719200021', 'performance', 'dom-ready', 'time', '/', 10.00, 'https://ondernet.net/', '2025-08-09 06:31:48', NULL, NULL, NULL, NULL),
(141, 2, 29, 'session-zh3sqhy20-1754719200060', 'performance', 'dom-ready', 'time', '/tag/linux/', 20.00, 'https://ondernet.net/tag/linux/', '2025-08-09 06:41:44', NULL, NULL, NULL, NULL),
(142, 2, 29, 'session-zh3sqhy20-1754719200060', 'performance', 'page-load', 'load-time', '/tag/linux/', 60.00, 'https://ondernet.net/tag/linux/', '2025-08-09 06:41:45', NULL, NULL, NULL, NULL),
(143, 2, 30, 'session-zh3sqhy20-1754719200031', 'performance', 'dom-ready', 'time', '/category/sistem/', 20.00, 'https://ondernet.net/category/sistem/', '2025-08-09 06:51:47', NULL, NULL, NULL, NULL),
(144, 2, 30, 'session-zh3sqhy20-1754719200031', 'performance', 'page-load', 'load-time', '/category/sistem/', 50.00, 'https://ondernet.net/category/sistem/', '2025-08-09 06:51:47', NULL, NULL, NULL, NULL),
(145, 2, 31, 'session-ejhkbfeg5-1754697600080', 'performance', 'dom-ready', 'time', '/', 60.00, 'https://ondernet.net/?page=6', '2025-08-09 06:59:20', NULL, NULL, NULL, NULL),
(146, 2, 31, 'session-ejhkbfeg5-1754697600080', 'performance', 'page-load', 'load-time', '/', 80.00, 'https://ondernet.net/?page=6', '2025-08-09 06:59:21', NULL, NULL, NULL, NULL),
(147, 1, 27, 'session-lec6tn7bd-1754697600011', 'performance', 'dom-ready', 'time', '/login', 10.00, 'https://www.quiz-test.online/login', '2025-08-09 07:15:26', NULL, NULL, NULL, NULL),
(148, 1, 27, 'session-lec6tn7bd-1754697600011', 'performance', 'page-load', 'load-time', '/login', 50.00, 'https://www.quiz-test.online/login', '2025-08-09 07:15:27', NULL, NULL, NULL, NULL),
(149, 2, 32, 'session-zehhxsjrw-1754723988554', 'performance', 'dom-ready', 'time', '/', 2678.00, 'https://ondernet.net/', '2025-08-09 07:19:51', NULL, NULL, NULL, NULL),
(150, 2, 32, 'session-zehhxsjrw-1754723988554', 'performance', 'paint', 'first-paint', '/', 2683.00, 'https://ondernet.net/', '2025-08-09 07:19:52', NULL, NULL, NULL, NULL),
(151, 2, 32, 'session-zehhxsjrw-1754723988554', 'performance', 'paint', 'first-contentful-paint', '/', 2683.00, 'https://ondernet.net/', '2025-08-09 07:19:52', NULL, NULL, NULL, NULL),
(152, 2, 32, 'session-zehhxsjrw-1754723988554', 'performance', 'page-load', 'load-time', '/', 6224.00, 'https://ondernet.net/', '2025-08-09 07:19:52', NULL, NULL, NULL, NULL),
(153, 2, 33, 'session-dhudtues7-1754724000816', 'performance', 'dom-ready', 'time', '/', 2440.00, 'https://ondernet.net/', '2025-08-09 07:20:03', NULL, NULL, NULL, NULL),
(154, 2, 33, 'session-dhudtues7-1754724000816', 'performance', 'paint', 'first-contentful-paint', '/', 2253.00, 'https://ondernet.net/', '2025-08-09 07:20:03', NULL, NULL, NULL, NULL),
(155, 2, 33, 'session-dhudtues7-1754724000816', 'performance', 'page-load', 'load-time', '/', 5171.00, 'https://ondernet.net/', '2025-08-09 07:20:03', NULL, NULL, NULL, NULL),
(156, 2, 33, 'session-dhudtues7-1754724000816', 'performance', 'paint', 'first-paint', '/', 2253.00, 'https://ondernet.net/', '2025-08-09 07:20:04', NULL, NULL, NULL, NULL),
(157, 2, 34, 'session-t099o2svb-1754697600034', 'performance', 'dom-ready', 'time', '/tag/topoloji/', 10.00, 'https://ondernet.net/tag/topoloji/', '2025-08-09 07:21:40', NULL, NULL, NULL, NULL),
(158, 2, 34, 'session-t099o2svb-1754697600034', 'performance', 'page-load', 'load-time', '/tag/topoloji/', 70.00, 'https://ondernet.net/tag/topoloji/', '2025-08-09 07:21:41', NULL, NULL, NULL, NULL),
(159, 1, 27, 'session-lec6tn7bd-1754697600011', 'performance', 'dom-ready', 'time', '/login', 10.00, 'https://www.quiz-test.online/login', '2025-08-09 07:50:02', NULL, NULL, NULL, NULL),
(160, 1, 27, 'session-lec6tn7bd-1754697600011', 'performance', 'page-load', 'load-time', '/login', 50.00, 'https://www.quiz-test.online/login', '2025-08-09 07:50:03', NULL, NULL, NULL, NULL),
(161, 1, 27, 'session-lec6tn7bd-1754697600011', 'performance', 'dom-ready', 'time', '/login', 10.00, 'https://www.quiz-test.online/login', '2025-08-09 08:02:19', NULL, NULL, NULL, NULL),
(162, 1, 27, 'session-lec6tn7bd-1754697600011', 'performance', 'page-load', 'load-time', '/login', 50.00, 'https://www.quiz-test.online/login', '2025-08-09 08:02:20', NULL, NULL, NULL, NULL),
(163, 2, 3, 'session-07q07u7ej-1754728283283', 'performance', 'dom-ready', 'time', '/', 1429.00, 'https://ondernet.net/', '2025-08-09 08:31:25', NULL, NULL, NULL, NULL),
(164, 2, 3, 'session-07q07u7ej-1754728283283', 'performance', 'page-load', 'load-time', '/', 2628.00, 'https://ondernet.net/', '2025-08-09 08:31:25', NULL, NULL, NULL, NULL),
(165, 2, 3, 'session-07q07u7ej-1754728283283', 'performance', 'paint', 'first-paint', '/', 1276.00, 'https://ondernet.net/', '2025-08-09 08:31:25', NULL, NULL, NULL, NULL),
(166, 2, 3, 'session-07q07u7ej-1754728283283', 'performance', 'paint', 'first-contentful-paint', '/', 1276.00, 'https://ondernet.net/', '2025-08-09 08:31:25', NULL, NULL, NULL, NULL),
(167, 2, 3, 'session-07q07u7ej-1754728283283', 'timing', 'time-on-page', 'duration', '/', 18.00, 'https://ondernet.net/', '2025-08-09 08:31:42', NULL, NULL, NULL, NULL),
(168, 2, 3, 'session-07q07u7ej-1754728283283', 'performance', 'page-load', 'load-time', '/', 1769.00, 'https://ondernet.net/?s=dork', '2025-08-09 08:31:43', NULL, NULL, NULL, NULL),
(169, 2, 3, 'session-07q07u7ej-1754728283283', 'performance', 'dom-ready', 'time', '/', 931.00, 'https://ondernet.net/?s=dork', '2025-08-09 08:31:43', NULL, NULL, NULL, NULL),
(170, 2, 3, 'session-07q07u7ej-1754728283283', 'performance', 'paint', 'first-paint', '/', 940.00, 'https://ondernet.net/?s=dork', '2025-08-09 08:31:44', NULL, NULL, NULL, NULL),
(171, 2, 3, 'session-07q07u7ej-1754728283283', 'performance', 'paint', 'first-contentful-paint', '/', 940.00, 'https://ondernet.net/?s=dork', '2025-08-09 08:31:44', NULL, NULL, NULL, NULL),
(172, 2, 3, 'session-07q07u7ej-1754728283283', 'timing', 'time-on-page', 'duration', '/', 8.00, 'https://ondernet.net/?s=dork', '2025-08-09 08:31:51', NULL, NULL, NULL, NULL),
(173, 2, 3, 'session-07q07u7ej-1754728283283', 'performance', 'dom-ready', 'time', '/web-sitenizdeki-guvenlik-aciklarini-google-dorking-ile-test-etme-rehberi/', 866.00, 'https://ondernet.net/web-sitenizdeki-guvenlik-aciklarini-google-dorking-ile-test-etme-rehberi/', '2025-08-09 08:31:53', NULL, NULL, NULL, NULL),
(174, 2, 3, 'session-07q07u7ej-1754728283283', 'performance', 'paint', 'first-paint', '/web-sitenizdeki-guvenlik-aciklarini-google-dorking-ile-test-etme-rehberi/', 864.00, 'https://ondernet.net/web-sitenizdeki-guvenlik-aciklarini-google-dorking-ile-test-etme-rehberi/', '2025-08-09 08:31:53', NULL, NULL, NULL, NULL),
(175, 2, 3, 'session-07q07u7ej-1754728283283', 'performance', 'page-load', 'load-time', '/web-sitenizdeki-guvenlik-aciklarini-google-dorking-ile-test-etme-rehberi/', 1923.00, 'https://ondernet.net/web-sitenizdeki-guvenlik-aciklarini-google-dorking-ile-test-etme-rehberi/', '2025-08-09 08:31:53', NULL, NULL, NULL, NULL),
(176, 2, 3, 'session-07q07u7ej-1754728283283', 'performance', 'paint', 'first-contentful-paint', '/web-sitenizdeki-guvenlik-aciklarini-google-dorking-ile-test-etme-rehberi/', 864.00, 'https://ondernet.net/web-sitenizdeki-guvenlik-aciklarini-google-dorking-ile-test-etme-rehberi/', '2025-08-09 08:31:53', NULL, NULL, NULL, NULL),
(177, 2, 3, 'session-07q07u7ej-1754728283283', 'scroll', 'scroll-depth', 'reached', '25%', 25.00, 'https://ondernet.net/web-sitenizdeki-guvenlik-aciklarini-google-dorking-ile-test-etme-rehberi/', '2025-08-09 08:32:05', NULL, NULL, NULL, NULL),
(178, 2, 3, 'session-07q07u7ej-1754728283283', 'timing', 'time-on-page', 'ping', '/web-sitenizdeki-guvenlik-aciklarini-google-dorking-ile-test-etme-rehberi/', 30.00, 'https://ondernet.net/web-sitenizdeki-guvenlik-aciklarini-google-dorking-ile-test-etme-rehberi/', '2025-08-09 08:32:21', NULL, NULL, NULL, NULL),
(179, 2, 3, 'session-07q07u7ej-1754728283283', 'scroll', 'scroll-depth', 'reached', '50%', 50.00, 'https://ondernet.net/web-sitenizdeki-guvenlik-aciklarini-google-dorking-ile-test-etme-rehberi/', '2025-08-09 08:32:21', NULL, NULL, NULL, NULL),
(180, 2, 3, 'session-07q07u7ej-1754728283283', 'scroll', 'scroll-depth', 'reached', '75%', 75.00, 'https://ondernet.net/web-sitenizdeki-guvenlik-aciklarini-google-dorking-ile-test-etme-rehberi/', '2025-08-09 08:32:27', NULL, NULL, NULL, NULL),
(181, 2, 3, 'session-07q07u7ej-1754728283283', 'timing', 'time-on-page', 'ping', '/web-sitenizdeki-guvenlik-aciklarini-google-dorking-ile-test-etme-rehberi/', 30.00, 'https://ondernet.net/web-sitenizdeki-guvenlik-aciklarini-google-dorking-ile-test-etme-rehberi/', '2025-08-09 08:32:56', NULL, NULL, NULL, NULL),
(182, 2, 3, 'session-07q07u7ej-1754728283283', 'timing', 'time-on-page', 'ping', '/web-sitenizdeki-guvenlik-aciklarini-google-dorking-ile-test-etme-rehberi/', 60.00, 'https://ondernet.net/web-sitenizdeki-guvenlik-aciklarini-google-dorking-ile-test-etme-rehberi/', '2025-08-09 08:33:26', NULL, NULL, NULL, NULL),
(183, 2, 3, 'session-07q07u7ej-1754728283283', 'timing', 'time-on-page', 'ping', '/web-sitenizdeki-guvenlik-aciklarini-google-dorking-ile-test-etme-rehberi/', 90.00, 'https://ondernet.net/web-sitenizdeki-guvenlik-aciklarini-google-dorking-ile-test-etme-rehberi/', '2025-08-09 08:33:56', NULL, NULL, NULL, NULL),
(184, 2, 3, 'session-07q07u7ej-1754728283283', 'timing', 'time-on-page', 'duration', '/web-sitenizdeki-guvenlik-aciklarini-google-dorking-ile-test-etme-rehberi/', 6.00, 'https://ondernet.net/web-sitenizdeki-guvenlik-aciklarini-google-dorking-ile-test-etme-rehberi/', '2025-08-09 08:35:29', NULL, NULL, NULL, NULL),
(185, 2, 3, 'session-07q07u7ej-1754728283283', 'performance', 'page-load', 'load-time', '/web-sitenizdeki-guvenlik-aciklarini-google-dorking-ile-test-etme-rehberi/', 1912.00, 'https://ondernet.net/web-sitenizdeki-guvenlik-aciklarini-google-dorking-ile-test-etme-rehberi/', '2025-08-09 08:38:53', NULL, NULL, NULL, NULL),
(186, 2, 3, 'session-07q07u7ej-1754728283283', 'performance', 'paint', 'first-paint', '/web-sitenizdeki-guvenlik-aciklarini-google-dorking-ile-test-etme-rehberi/', 888.00, 'https://ondernet.net/web-sitenizdeki-guvenlik-aciklarini-google-dorking-ile-test-etme-rehberi/', '2025-08-09 08:38:53', NULL, NULL, NULL, NULL),
(187, 2, 3, 'session-07q07u7ej-1754728283283', 'performance', 'paint', 'first-contentful-paint', '/web-sitenizdeki-guvenlik-aciklarini-google-dorking-ile-test-etme-rehberi/', 888.00, 'https://ondernet.net/web-sitenizdeki-guvenlik-aciklarini-google-dorking-ile-test-etme-rehberi/', '2025-08-09 08:38:54', NULL, NULL, NULL, NULL),
(188, 2, 3, 'session-07q07u7ej-1754728283283', 'performance', 'dom-ready', 'time', '/web-sitenizdeki-guvenlik-aciklarini-google-dorking-ile-test-etme-rehberi/', 1116.00, 'https://ondernet.net/web-sitenizdeki-guvenlik-aciklarini-google-dorking-ile-test-etme-rehberi/', '2025-08-09 08:38:54', NULL, NULL, NULL, NULL),
(189, 2, 3, 'session-07q07u7ej-1754728283283', 'scroll', 'scroll-depth', 'reached', '25%', 25.00, 'https://ondernet.net/web-sitenizdeki-guvenlik-aciklarini-google-dorking-ile-test-etme-rehberi/', '2025-08-09 08:38:58', NULL, NULL, NULL, NULL),
(190, 2, 3, 'session-07q07u7ej-1754728283283', 'timing', 'time-on-page', 'ping', '/web-sitenizdeki-guvenlik-aciklarini-google-dorking-ile-test-etme-rehberi/', 30.00, 'https://ondernet.net/web-sitenizdeki-guvenlik-aciklarini-google-dorking-ile-test-etme-rehberi/', '2025-08-09 08:39:23', NULL, NULL, NULL, NULL),
(191, 2, 3, 'session-07q07u7ej-1754728283283', 'timing', 'time-on-page', 'ping', '/web-sitenizdeki-guvenlik-aciklarini-google-dorking-ile-test-etme-rehberi/', 60.00, 'https://ondernet.net/web-sitenizdeki-guvenlik-aciklarini-google-dorking-ile-test-etme-rehberi/', '2025-08-09 08:39:53', NULL, NULL, NULL, NULL),
(192, 2, 3, 'session-07q07u7ej-1754728283283', 'timing', 'time-on-page', 'ping', '/web-sitenizdeki-guvenlik-aciklarini-google-dorking-ile-test-etme-rehberi/', 90.00, 'https://ondernet.net/web-sitenizdeki-guvenlik-aciklarini-google-dorking-ile-test-etme-rehberi/', '2025-08-09 08:40:23', NULL, NULL, NULL, NULL),
(193, 2, 3, 'session-07q07u7ej-1754728283283', 'timing', 'time-on-page', 'ping', '/web-sitenizdeki-guvenlik-aciklarini-google-dorking-ile-test-etme-rehberi/', 120.00, 'https://ondernet.net/web-sitenizdeki-guvenlik-aciklarini-google-dorking-ile-test-etme-rehberi/', '2025-08-09 08:40:53', NULL, NULL, NULL, NULL),
(194, 2, 3, 'session-07q07u7ej-1754728283283', 'timing', 'time-on-page', 'ping', '/web-sitenizdeki-guvenlik-aciklarini-google-dorking-ile-test-etme-rehberi/', 150.00, 'https://ondernet.net/web-sitenizdeki-guvenlik-aciklarini-google-dorking-ile-test-etme-rehberi/', '2025-08-09 08:41:23', NULL, NULL, NULL, NULL),
(195, 2, 3, 'session-07q07u7ej-1754728283283', 'timing', 'time-on-page', 'ping', '/web-sitenizdeki-guvenlik-aciklarini-google-dorking-ile-test-etme-rehberi/', 180.00, 'https://ondernet.net/web-sitenizdeki-guvenlik-aciklarini-google-dorking-ile-test-etme-rehberi/', '2025-08-09 08:41:53', NULL, NULL, NULL, NULL),
(196, 2, 35, 'session-qb8n6dknq-1754729411211', 'performance', 'paint', 'first-paint', '/ondernet-net/', 2032.00, 'https://ondernet.net/ondernet-net/', '2025-08-09 08:50:13', NULL, NULL, NULL, NULL),
(197, 2, 35, 'session-qb8n6dknq-1754729411211', 'performance', 'paint', 'first-contentful-paint', '/ondernet-net/', 2032.00, 'https://ondernet.net/ondernet-net/', '2025-08-09 08:50:13', NULL, NULL, NULL, NULL),
(198, 2, 35, 'session-qb8n6dknq-1754729411211', 'performance', 'page-load', 'load-time', '/ondernet-net/', 3368.00, 'https://ondernet.net/ondernet-net/', '2025-08-09 08:50:13', NULL, NULL, NULL, NULL),
(199, 2, 35, 'session-qb8n6dknq-1754729411211', 'performance', 'dom-ready', 'time', '/ondernet-net/', 2020.00, 'https://ondernet.net/ondernet-net/', '2025-08-09 08:50:13', NULL, NULL, NULL, NULL),
(200, 2, 36, 'session-ejhkbfeg5-1754697600051', 'performance', 'page-load', 'load-time', '/category/misconfiguration/', 60.00, 'https://ondernet.net/category/misconfiguration/', '2025-08-09 09:18:43', NULL, NULL, NULL, NULL),
(201, 2, 36, 'session-ejhkbfeg5-1754697600051', 'performance', 'dom-ready', 'time', '/category/misconfiguration/', 40.00, 'https://ondernet.net/category/misconfiguration/', '2025-08-09 09:18:44', NULL, NULL, NULL, NULL),
(202, 2, 3, 'session-07q07u7ej-1754728283283', 'timing', 'time-on-page', 'ping', '/web-sitenizdeki-guvenlik-aciklarini-google-dorking-ile-test-etme-rehberi/', 30.00, 'https://ondernet.net/web-sitenizdeki-guvenlik-aciklarini-google-dorking-ile-test-etme-rehberi/', '2025-08-09 09:26:29', NULL, NULL, NULL, NULL),
(203, 2, 3, 'session-07q07u7ej-1754728283283', 'timing', 'time-on-page', 'duration', '/web-sitenizdeki-guvenlik-aciklarini-google-dorking-ile-test-etme-rehberi/', 2.00, 'https://ondernet.net/web-sitenizdeki-guvenlik-aciklarini-google-dorking-ile-test-etme-rehberi/', '2025-08-09 09:29:39', NULL, NULL, NULL, NULL),
(204, 2, 3, 'session-07q07u7ej-1754728283283', 'performance', 'page-load', 'load-time', '/hassas-veriler/', 1878.00, 'https://ondernet.net/hassas-veriler/', '2025-08-09 09:33:13', NULL, NULL, NULL, NULL),
(205, 2, 3, 'session-07q07u7ej-1754728283283', 'performance', 'dom-ready', 'time', '/hassas-veriler/', 995.00, 'https://ondernet.net/hassas-veriler/', '2025-08-09 09:33:13', NULL, NULL, NULL, NULL),
(206, 2, 3, 'session-07q07u7ej-1754728283283', 'performance', 'paint', 'first-contentful-paint', '/hassas-veriler/', 876.00, 'https://ondernet.net/hassas-veriler/', '2025-08-09 09:33:13', NULL, NULL, NULL, NULL),
(207, 2, 3, 'session-07q07u7ej-1754728283283', 'performance', 'paint', 'first-paint', '/hassas-veriler/', 876.00, 'https://ondernet.net/hassas-veriler/', '2025-08-09 09:33:13', NULL, NULL, NULL, NULL),
(208, 2, 3, 'session-07q07u7ej-1754728283283', 'timing', 'time-on-page', 'duration', '/hassas-veriler/', 5.00, 'https://ondernet.net/hassas-veriler/', '2025-08-09 09:33:17', NULL, NULL, NULL, NULL),
(209, 2, 3, 'session-hx7wnf1uq-1754732170159', 'performance', 'page-load', 'load-time', '/', 2584.00, 'https://ondernet.net/', '2025-08-09 09:36:11', NULL, NULL, NULL, NULL),
(210, 2, 3, 'session-hx7wnf1uq-1754732170159', 'performance', 'dom-ready', 'time', '/', 1652.00, 'https://ondernet.net/', '2025-08-09 09:36:11', NULL, NULL, NULL, NULL),
(211, 2, 3, 'session-hx7wnf1uq-1754732170159', 'performance', 'paint', 'first-contentful-paint', '/', 1432.00, 'https://ondernet.net/', '2025-08-09 09:36:11', NULL, NULL, NULL, NULL),
(212, 2, 3, 'session-hx7wnf1uq-1754732170159', 'performance', 'paint', 'first-paint', '/', 1432.00, 'https://ondernet.net/', '2025-08-09 09:36:11', NULL, NULL, NULL, NULL),
(213, 2, 3, 'session-hx7wnf1uq-1754732170159', 'scroll', 'scroll-depth', 'reached', '25%', 25.00, 'https://ondernet.net/', '2025-08-09 09:36:14', NULL, NULL, NULL, NULL),
(214, 2, 3, 'session-hx7wnf1uq-1754732170159', 'timing', 'time-on-page', 'duration', '/', 6.00, 'https://ondernet.net/', '2025-08-09 09:36:16', NULL, NULL, NULL, NULL),
(215, 2, 3, 'session-hx7wnf1uq-1754732170159', 'performance', 'page-load', 'load-time', '/category/misconfiguration/', 1765.00, 'https://ondernet.net/category/misconfiguration/', '2025-08-09 09:36:18', NULL, NULL, NULL, NULL),
(216, 2, 3, 'session-hx7wnf1uq-1754732170159', 'performance', 'paint', 'first-paint', '/category/misconfiguration/', 988.00, 'https://ondernet.net/category/misconfiguration/', '2025-08-09 09:36:18', NULL, NULL, NULL, NULL),
(217, 2, 3, 'session-hx7wnf1uq-1754732170159', 'performance', 'paint', 'first-contentful-paint', '/category/misconfiguration/', 988.00, 'https://ondernet.net/category/misconfiguration/', '2025-08-09 09:36:18', NULL, NULL, NULL, NULL),
(218, 2, 3, 'session-hx7wnf1uq-1754732170159', 'performance', 'dom-ready', 'time', '/category/misconfiguration/', 1126.00, 'https://ondernet.net/category/misconfiguration/', '2025-08-09 09:36:18', NULL, NULL, NULL, NULL),
(219, 2, 3, 'session-hx7wnf1uq-1754732170159', 'timing', 'time-on-page', 'duration', '/category/misconfiguration/', 4.00, 'https://ondernet.net/category/misconfiguration/', '2025-08-09 09:36:40', NULL, NULL, NULL, NULL),
(220, 2, 3, 'session-hx7wnf1uq-1754732170159', 'performance', 'paint', 'first-paint', '/', 1096.00, 'https://ondernet.net/?s=siber+sald%C4%B1r%C4%B1', '2025-08-09 09:36:42', NULL, NULL, NULL, NULL),
(221, 2, 3, 'session-hx7wnf1uq-1754732170159', 'performance', 'page-load', 'load-time', '/', 1662.00, 'https://ondernet.net/?s=siber+sald%C4%B1r%C4%B1', '2025-08-09 09:36:42', NULL, NULL, NULL, NULL),
(222, 2, 3, 'session-hx7wnf1uq-1754732170159', 'performance', 'paint', 'first-contentful-paint', '/', 1096.00, 'https://ondernet.net/?s=siber+sald%C4%B1r%C4%B1', '2025-08-09 09:36:42', NULL, NULL, NULL, NULL),
(223, 2, 3, 'session-hx7wnf1uq-1754732170159', 'performance', 'dom-ready', 'time', '/', 1098.00, 'https://ondernet.net/?s=siber+sald%C4%B1r%C4%B1', '2025-08-09 09:36:42', NULL, NULL, NULL, NULL),
(224, 2, 3, 'session-hx7wnf1uq-1754732170159', 'timing', 'time-on-page', 'duration', '/', 4.00, 'https://ondernet.net/?s=siber+sald%C4%B1r%C4%B1', '2025-08-09 09:36:45', NULL, NULL, NULL, NULL),
(225, 2, 3, 'session-hx7wnf1uq-1754732170159', 'performance', 'page-load', 'load-time', '/kucuk-isletmeler-buyuk-butce-olmadan-siber-saldirilardan-nasil-korunur/', 1475.00, 'https://ondernet.net/kucuk-isletmeler-buyuk-butce-olmadan-siber-saldirilardan-nasil-korunur/', '2025-08-09 09:36:47', NULL, NULL, NULL, NULL),
(226, 2, 3, 'session-hx7wnf1uq-1754732170159', 'performance', 'paint', 'first-contentful-paint', '/kucuk-isletmeler-buyuk-butce-olmadan-siber-saldirilardan-nasil-korunur/', 948.00, 'https://ondernet.net/kucuk-isletmeler-buyuk-butce-olmadan-siber-saldirilardan-nasil-korunur/', '2025-08-09 09:36:47', NULL, NULL, NULL, NULL),
(227, 2, 3, 'session-hx7wnf1uq-1754732170159', 'performance', 'dom-ready', 'time', '/kucuk-isletmeler-buyuk-butce-olmadan-siber-saldirilardan-nasil-korunur/', 975.00, 'https://ondernet.net/kucuk-isletmeler-buyuk-butce-olmadan-siber-saldirilardan-nasil-korunur/', '2025-08-09 09:36:47', NULL, NULL, NULL, NULL),
(228, 2, 3, 'session-hx7wnf1uq-1754732170159', 'performance', 'paint', 'first-paint', '/kucuk-isletmeler-buyuk-butce-olmadan-siber-saldirilardan-nasil-korunur/', 948.00, 'https://ondernet.net/kucuk-isletmeler-buyuk-butce-olmadan-siber-saldirilardan-nasil-korunur/', '2025-08-09 09:36:47', NULL, NULL, NULL, NULL),
(229, 2, 31, 'session-ejhkbfeg5-1754697600080', 'performance', 'page-load', 'load-time', '/tag/linux/', 80.00, 'https://ondernet.net/tag/linux/', '2025-08-09 09:38:27', NULL, NULL, NULL, NULL),
(230, 2, 31, 'session-ejhkbfeg5-1754697600080', 'performance', 'dom-ready', 'time', '/tag/linux/', 60.00, 'https://ondernet.net/tag/linux/', '2025-08-09 09:38:28', NULL, NULL, NULL, NULL),
(231, 2, 3, 'session-n64l547vj-1754732358369', 'performance', 'page-load', 'load-time', '/google-dorking/', 1948.00, 'https://ondernet.net/google-dorking/', '2025-08-09 09:39:19', NULL, NULL, NULL, NULL),
(232, 2, 3, 'session-n64l547vj-1754732358369', 'performance', 'dom-ready', 'time', '/google-dorking/', 1341.00, 'https://ondernet.net/google-dorking/', '2025-08-09 09:39:19', NULL, NULL, NULL, NULL),
(233, 2, 3, 'session-n64l547vj-1754732358369', 'performance', 'paint', 'first-paint', '/google-dorking/', 1072.00, 'https://ondernet.net/google-dorking/', '2025-08-09 09:39:19', NULL, NULL, NULL, NULL),
(234, 2, 3, 'session-n64l547vj-1754732358369', 'performance', 'paint', 'first-contentful-paint', '/google-dorking/', 1072.00, 'https://ondernet.net/google-dorking/', '2025-08-09 09:39:19', NULL, NULL, NULL, NULL),
(235, 2, 3, 'session-n64l547vj-1754732358369', 'timing', 'time-on-page', 'duration', '/google-dorking/', 11.00, 'https://ondernet.net/google-dorking/', '2025-08-09 09:39:30', NULL, NULL, NULL, NULL),
(236, 2, 3, 'session-n64l547vj-1754732358369', 'performance', 'paint', 'first-paint', '/google-dorking/', 1588.00, 'https://ondernet.net/google-dorking/', '2025-08-09 09:39:49', NULL, NULL, NULL, NULL),
(237, 2, 3, 'session-n64l547vj-1754732358369', 'performance', 'dom-ready', 'time', '/google-dorking/', 1745.00, 'https://ondernet.net/google-dorking/', '2025-08-09 09:39:49', NULL, NULL, NULL, NULL),
(238, 2, 3, 'session-n64l547vj-1754732358369', 'performance', 'paint', 'first-contentful-paint', '/google-dorking/', 1588.00, 'https://ondernet.net/google-dorking/', '2025-08-09 09:39:49', NULL, NULL, NULL, NULL),
(239, 2, 3, 'session-n64l547vj-1754732358369', 'performance', 'page-load', 'load-time', '/google-dorking/', 2746.00, 'https://ondernet.net/google-dorking/', '2025-08-09 09:39:49', NULL, NULL, NULL, NULL),
(240, 2, 3, 'session-n64l547vj-1754732358369', 'timing', 'time-on-page', 'ping', '/google-dorking/', 30.00, 'https://ondernet.net/google-dorking/', '2025-08-09 09:40:18', NULL, NULL, NULL, NULL),
(241, 2, 3, 'session-n64l547vj-1754732358369', 'timing', 'time-on-page', 'duration', '/google-dorking/', 54.00, 'https://ondernet.net/google-dorking/', '2025-08-09 09:40:42', NULL, NULL, NULL, NULL),
(242, 2, 3, 'session-n64l547vj-1754732358369', 'performance', 'page-load', 'load-time', '/google-dorking/', 1780.00, 'https://ondernet.net/google-dorking/', '2025-08-09 09:42:10', NULL, NULL, NULL, NULL),
(243, 2, 3, 'session-n64l547vj-1754732358369', 'performance', 'dom-ready', 'time', '/google-dorking/', 1075.00, 'https://ondernet.net/google-dorking/', '2025-08-09 09:42:10', NULL, NULL, NULL, NULL),
(244, 2, 3, 'session-n64l547vj-1754732358369', 'performance', 'paint', 'first-paint', '/google-dorking/', 956.00, 'https://ondernet.net/google-dorking/', '2025-08-09 09:42:10', NULL, NULL, NULL, NULL),
(245, 2, 3, 'session-n64l547vj-1754732358369', 'performance', 'paint', 'first-contentful-paint', '/google-dorking/', 956.00, 'https://ondernet.net/google-dorking/', '2025-08-09 09:42:10', NULL, NULL, NULL, NULL);
INSERT INTO `events` (`id`, `website_id`, `visitor_id`, `session_id`, `event_type`, `event_category`, `event_action`, `event_label`, `event_value`, `page_url`, `created_at`, `country`, `country_code`, `region`, `city`) VALUES
(246, 2, 3, 'session-n64l547vj-1754732358369', 'timing', 'time-on-page', 'duration', '/google-dorking/', 8.00, 'https://ondernet.net/google-dorking/', '2025-08-09 09:42:17', NULL, NULL, NULL, NULL),
(247, 2, 3, 'session-n64l547vj-1754732358369', 'performance', 'page-load', 'load-time', '/', 1881.00, 'https://ondernet.net/', '2025-08-09 09:42:19', NULL, NULL, NULL, NULL),
(248, 2, 3, 'session-n64l547vj-1754732358369', 'performance', 'paint', 'first-contentful-paint', '/', 1192.00, 'https://ondernet.net/', '2025-08-09 09:42:19', NULL, NULL, NULL, NULL),
(249, 2, 3, 'session-n64l547vj-1754732358369', 'performance', 'dom-ready', 'time', '/', 1184.00, 'https://ondernet.net/', '2025-08-09 09:42:19', NULL, NULL, NULL, NULL),
(250, 2, 3, 'session-n64l547vj-1754732358369', 'performance', 'paint', 'first-paint', '/', 1192.00, 'https://ondernet.net/', '2025-08-09 09:42:19', NULL, NULL, NULL, NULL),
(251, 2, 3, 'session-n64l547vj-1754732358369', 'timing', 'time-on-page', 'duration', '/', 6.00, 'https://ondernet.net/', '2025-08-09 09:42:25', NULL, NULL, NULL, NULL),
(252, 2, 3, 'session-n64l547vj-1754732358369', 'performance', 'dom-ready', 'time', '/', 887.00, 'https://ondernet.net/?s=veri', '2025-08-09 09:42:26', NULL, NULL, NULL, NULL),
(253, 2, 3, 'session-n64l547vj-1754732358369', 'performance', 'paint', 'first-contentful-paint', '/', 892.00, 'https://ondernet.net/?s=veri', '2025-08-09 09:42:26', NULL, NULL, NULL, NULL),
(254, 2, 3, 'session-n64l547vj-1754732358369', 'performance', 'page-load', 'load-time', '/', 1407.00, 'https://ondernet.net/?s=veri', '2025-08-09 09:42:26', NULL, NULL, NULL, NULL),
(255, 2, 3, 'session-n64l547vj-1754732358369', 'performance', 'paint', 'first-paint', '/', 892.00, 'https://ondernet.net/?s=veri', '2025-08-09 09:42:26', NULL, NULL, NULL, NULL),
(256, 2, 3, 'session-n64l547vj-1754732358369', 'scroll', 'scroll-depth', 'reached', '25%', 25.00, 'https://ondernet.net/?s=veri', '2025-08-09 09:42:29', NULL, NULL, NULL, NULL),
(257, 2, 3, 'session-n64l547vj-1754732358369', 'scroll', 'scroll-depth', 'reached', '50%', 50.00, 'https://ondernet.net/?s=veri', '2025-08-09 09:42:30', NULL, NULL, NULL, NULL),
(258, 2, 3, 'session-n64l547vj-1754732358369', 'timing', 'time-on-page', 'duration', '/', 9.00, 'https://ondernet.net/?s=veri', '2025-08-09 09:42:35', NULL, NULL, NULL, NULL),
(259, 2, 3, 'session-n64l547vj-1754732358369', 'performance', 'paint', 'first-paint', '/page/2/', 1012.00, 'https://ondernet.net/page/2/?s=veri', '2025-08-09 09:42:37', NULL, NULL, NULL, NULL),
(260, 2, 3, 'session-n64l547vj-1754732358369', 'performance', 'page-load', 'load-time', '/page/2/', 1569.00, 'https://ondernet.net/page/2/?s=veri', '2025-08-09 09:42:37', NULL, NULL, NULL, NULL),
(261, 2, 3, 'session-n64l547vj-1754732358369', 'performance', 'paint', 'first-contentful-paint', '/page/2/', 1012.00, 'https://ondernet.net/page/2/?s=veri', '2025-08-09 09:42:37', NULL, NULL, NULL, NULL),
(262, 2, 3, 'session-n64l547vj-1754732358369', 'performance', 'dom-ready', 'time', '/page/2/', 996.00, 'https://ondernet.net/page/2/?s=veri', '2025-08-09 09:42:37', NULL, NULL, NULL, NULL),
(263, 2, 3, 'session-n64l547vj-1754732358369', 'timing', 'time-on-page', 'duration', '/page/2/', 2.00, 'https://ondernet.net/page/2/?s=veri', '2025-08-09 09:42:38', NULL, NULL, NULL, NULL),
(264, 2, 3, 'session-n64l547vj-1754732358369', 'performance', 'page-load', 'load-time', '/hassas-veriler/', 1783.00, 'https://ondernet.net/hassas-veriler/', '2025-08-09 09:42:40', NULL, NULL, NULL, NULL),
(265, 2, 3, 'session-n64l547vj-1754732358369', 'performance', 'paint', 'first-contentful-paint', '/hassas-veriler/', 888.00, 'https://ondernet.net/hassas-veriler/', '2025-08-09 09:42:40', NULL, NULL, NULL, NULL),
(266, 2, 3, 'session-n64l547vj-1754732358369', 'performance', 'paint', 'first-paint', '/hassas-veriler/', 888.00, 'https://ondernet.net/hassas-veriler/', '2025-08-09 09:42:40', NULL, NULL, NULL, NULL),
(267, 2, 3, 'session-n64l547vj-1754732358369', 'performance', 'dom-ready', 'time', '/hassas-veriler/', 927.00, 'https://ondernet.net/hassas-veriler/', '2025-08-09 09:42:43', NULL, NULL, NULL, NULL),
(268, 2, 3, 'session-n64l547vj-1754732358369', 'scroll', 'scroll-depth', 'reached', '25%', 25.00, 'https://ondernet.net/hassas-veriler/', '2025-08-09 09:42:45', NULL, NULL, NULL, NULL),
(269, 2, 3, 'session-n64l547vj-1754732358369', 'timing', 'time-on-page', 'ping', '/hassas-veriler/', 30.00, 'https://ondernet.net/hassas-veriler/', '2025-08-09 09:43:10', NULL, NULL, NULL, NULL),
(270, 2, 3, 'session-n64l547vj-1754732358369', 'timing', 'time-on-page', 'duration', '/hassas-veriler/', 53.00, 'https://ondernet.net/hassas-veriler/', '2025-08-09 09:43:32', NULL, NULL, NULL, NULL),
(271, 2, 3, 'session-n64l547vj-1754732358369', 'performance', 'page-load', 'load-time', '/hassas-veriler/', 2427.00, 'https://ondernet.net/hassas-veriler/', '2025-08-09 09:44:15', NULL, NULL, NULL, NULL),
(272, 2, 3, 'session-n64l547vj-1754732358369', 'performance', 'paint', 'first-contentful-paint', '/hassas-veriler/', 1480.00, 'https://ondernet.net/hassas-veriler/', '2025-08-09 09:44:15', NULL, NULL, NULL, NULL),
(273, 2, 3, 'session-n64l547vj-1754732358369', 'performance', 'paint', 'first-paint', '/hassas-veriler/', 1480.00, 'https://ondernet.net/hassas-veriler/', '2025-08-09 09:44:15', NULL, NULL, NULL, NULL),
(274, 2, 3, 'session-n64l547vj-1754732358369', 'performance', 'dom-ready', 'time', '/hassas-veriler/', 1670.00, 'https://ondernet.net/hassas-veriler/', '2025-08-09 09:44:15', NULL, NULL, NULL, NULL),
(275, 2, 3, 'session-n64l547vj-1754732358369', 'scroll', 'scroll-depth', 'reached', '25%', 25.00, 'https://ondernet.net/hassas-veriler/', '2025-08-09 09:44:26', NULL, NULL, NULL, NULL),
(276, 2, 3, 'session-n64l547vj-1754732358369', 'timing', 'time-on-page', 'duration', '/hassas-veriler/', 14.00, 'https://ondernet.net/hassas-veriler/', '2025-08-09 09:44:28', NULL, NULL, NULL, NULL),
(277, 2, 3, 'session-n64l547vj-1754732358369', 'performance', 'page-load', 'load-time', '/', 1605.00, 'https://ondernet.net/', '2025-08-09 09:44:30', NULL, NULL, NULL, NULL),
(278, 2, 3, 'session-n64l547vj-1754732358369', 'performance', 'paint', 'first-paint', '/', 1040.00, 'https://ondernet.net/', '2025-08-09 09:44:30', NULL, NULL, NULL, NULL),
(279, 2, 3, 'session-n64l547vj-1754732358369', 'performance', 'dom-ready', 'time', '/', 1073.00, 'https://ondernet.net/', '2025-08-09 09:44:30', NULL, NULL, NULL, NULL),
(280, 2, 3, 'session-n64l547vj-1754732358369', 'performance', 'paint', 'first-contentful-paint', '/', 1040.00, 'https://ondernet.net/', '2025-08-09 09:44:30', NULL, NULL, NULL, NULL),
(281, 2, 3, 'session-n64l547vj-1754732358369', 'timing', 'time-on-page', 'duration', '/', 7.00, 'https://ondernet.net/', '2025-08-09 09:44:37', NULL, NULL, NULL, NULL),
(282, 2, 3, 'session-n64l547vj-1754732358369', 'performance', 'dom-ready', 'time', '/google-dorking/', 925.00, 'https://ondernet.net/google-dorking/', '2025-08-09 09:44:38', NULL, NULL, NULL, NULL),
(283, 2, 3, 'session-n64l547vj-1754732358369', 'performance', 'paint', 'first-contentful-paint', '/google-dorking/', 860.00, 'https://ondernet.net/google-dorking/', '2025-08-09 09:44:38', NULL, NULL, NULL, NULL),
(284, 2, 3, 'session-n64l547vj-1754732358369', 'performance', 'page-load', 'load-time', '/google-dorking/', 1539.00, 'https://ondernet.net/google-dorking/', '2025-08-09 09:44:38', NULL, NULL, NULL, NULL),
(285, 2, 3, 'session-n64l547vj-1754732358369', 'performance', 'paint', 'first-paint', '/google-dorking/', 860.00, 'https://ondernet.net/google-dorking/', '2025-08-09 09:44:38', NULL, NULL, NULL, NULL),
(286, 2, 3, 'session-n64l547vj-1754732358369', 'timing', 'time-on-page', 'duration', '/google-dorking/', 3.00, 'https://ondernet.net/google-dorking/', '2025-08-09 09:44:41', NULL, NULL, NULL, NULL),
(287, 2, 3, 'session-n64l547vj-1754732358369', 'performance', 'page-load', 'load-time', '/web-sitenizdeki-guvenlik-aciklarini-google-dorking-ile-test-etme-rehberi/', 1621.00, 'https://ondernet.net/web-sitenizdeki-guvenlik-aciklarini-google-dorking-ile-test-etme-rehberi/', '2025-08-09 09:44:42', NULL, NULL, NULL, NULL),
(288, 2, 3, 'session-n64l547vj-1754732358369', 'performance', 'dom-ready', 'time', '/web-sitenizdeki-guvenlik-aciklarini-google-dorking-ile-test-etme-rehberi/', 898.00, 'https://ondernet.net/web-sitenizdeki-guvenlik-aciklarini-google-dorking-ile-test-etme-rehberi/', '2025-08-09 09:44:43', NULL, NULL, NULL, NULL),
(289, 2, 3, 'session-n64l547vj-1754732358369', 'performance', 'paint', 'first-contentful-paint', '/web-sitenizdeki-guvenlik-aciklarini-google-dorking-ile-test-etme-rehberi/', 852.00, 'https://ondernet.net/web-sitenizdeki-guvenlik-aciklarini-google-dorking-ile-test-etme-rehberi/', '2025-08-09 09:44:43', NULL, NULL, NULL, NULL),
(290, 2, 3, 'session-n64l547vj-1754732358369', 'performance', 'paint', 'first-paint', '/web-sitenizdeki-guvenlik-aciklarini-google-dorking-ile-test-etme-rehberi/', 852.00, 'https://ondernet.net/web-sitenizdeki-guvenlik-aciklarini-google-dorking-ile-test-etme-rehberi/', '2025-08-09 09:44:43', NULL, NULL, NULL, NULL),
(291, 2, 3, 'session-n64l547vj-1754732358369', 'timing', 'time-on-page', 'duration', '/web-sitenizdeki-guvenlik-aciklarini-google-dorking-ile-test-etme-rehberi/', 7.00, 'https://ondernet.net/web-sitenizdeki-guvenlik-aciklarini-google-dorking-ile-test-etme-rehberi/', '2025-08-09 09:44:49', NULL, NULL, NULL, NULL),
(292, 2, 3, 'session-hx7wnf1uq-1754732170159', 'timing', 'time-on-page', 'duration', '/kucuk-isletmeler-buyuk-butce-olmadan-siber-saldirilardan-nasil-korunur/', 2.00, 'https://ondernet.net/kucuk-isletmeler-buyuk-butce-olmadan-siber-saldirilardan-nasil-korunur/', '2025-08-09 09:46:48', NULL, NULL, NULL, NULL),
(293, 2, 3, 'session-hx7wnf1uq-1754732170159', 'performance', 'page-load', 'load-time', '/category/misconfiguration/', 1853.00, 'https://ondernet.net/category/misconfiguration/', '2025-08-09 09:46:50', NULL, NULL, NULL, NULL),
(294, 2, 3, 'session-hx7wnf1uq-1754732170159', 'performance', 'paint', 'first-contentful-paint', '/category/misconfiguration/', 912.00, 'https://ondernet.net/category/misconfiguration/', '2025-08-09 09:46:50', NULL, NULL, NULL, NULL),
(295, 2, 3, 'session-hx7wnf1uq-1754732170159', 'performance', 'dom-ready', 'time', '/category/misconfiguration/', 943.00, 'https://ondernet.net/category/misconfiguration/', '2025-08-09 09:46:50', NULL, NULL, NULL, NULL),
(296, 2, 3, 'session-hx7wnf1uq-1754732170159', 'performance', 'paint', 'first-paint', '/category/misconfiguration/', 912.00, 'https://ondernet.net/category/misconfiguration/', '2025-08-09 09:46:50', NULL, NULL, NULL, NULL),
(297, 2, 3, 'session-n64l547vj-1754732358369', 'performance', 'dom-ready', 'time', '/web-sitenizdeki-guvenlik-aciklarini-google-dorking-ile-test-etme-rehberi/', 1039.00, 'https://ondernet.net/web-sitenizdeki-guvenlik-aciklarini-google-dorking-ile-test-etme-rehberi/', '2025-08-09 09:47:08', NULL, NULL, NULL, NULL),
(298, 2, 3, 'session-n64l547vj-1754732358369', 'performance', 'paint', 'first-contentful-paint', '/web-sitenizdeki-guvenlik-aciklarini-google-dorking-ile-test-etme-rehberi/', 900.00, 'https://ondernet.net/web-sitenizdeki-guvenlik-aciklarini-google-dorking-ile-test-etme-rehberi/', '2025-08-09 09:47:08', NULL, NULL, NULL, NULL),
(299, 2, 3, 'session-n64l547vj-1754732358369', 'performance', 'page-load', 'load-time', '/web-sitenizdeki-guvenlik-aciklarini-google-dorking-ile-test-etme-rehberi/', 1685.00, 'https://ondernet.net/web-sitenizdeki-guvenlik-aciklarini-google-dorking-ile-test-etme-rehberi/', '2025-08-09 09:47:08', NULL, NULL, NULL, NULL),
(300, 2, 3, 'session-n64l547vj-1754732358369', 'performance', 'paint', 'first-paint', '/web-sitenizdeki-guvenlik-aciklarini-google-dorking-ile-test-etme-rehberi/', 900.00, 'https://ondernet.net/web-sitenizdeki-guvenlik-aciklarini-google-dorking-ile-test-etme-rehberi/', '2025-08-09 09:47:08', NULL, NULL, NULL, NULL),
(301, 2, 3, 'session-n64l547vj-1754732358369', 'timing', 'time-on-page', 'duration', '/web-sitenizdeki-guvenlik-aciklarini-google-dorking-ile-test-etme-rehberi/', 4.00, 'https://ondernet.net/web-sitenizdeki-guvenlik-aciklarini-google-dorking-ile-test-etme-rehberi/', '2025-08-09 09:47:11', NULL, NULL, NULL, NULL),
(302, 2, 3, 'session-n64l547vj-1754732358369', 'performance', 'page-load', 'load-time', '/category/misconfiguration/', 1997.00, 'https://ondernet.net/category/misconfiguration/', '2025-08-09 09:47:13', NULL, NULL, NULL, NULL),
(303, 2, 3, 'session-n64l547vj-1754732358369', 'performance', 'dom-ready', 'time', '/category/misconfiguration/', 1410.00, 'https://ondernet.net/category/misconfiguration/', '2025-08-09 09:47:13', NULL, NULL, NULL, NULL),
(304, 2, 3, 'session-n64l547vj-1754732358369', 'performance', 'paint', 'first-paint', '/category/misconfiguration/', 1364.00, 'https://ondernet.net/category/misconfiguration/', '2025-08-09 09:47:13', NULL, NULL, NULL, NULL),
(305, 2, 3, 'session-n64l547vj-1754732358369', 'performance', 'paint', 'first-contentful-paint', '/category/misconfiguration/', 1364.00, 'https://ondernet.net/category/misconfiguration/', '2025-08-09 09:47:13', NULL, NULL, NULL, NULL),
(306, 2, 3, 'session-n64l547vj-1754732358369', 'timing', 'time-on-page', 'duration', '/category/misconfiguration/', 2.00, 'https://ondernet.net/category/misconfiguration/', '2025-08-09 09:47:14', NULL, NULL, NULL, NULL),
(307, 2, 3, 'session-n64l547vj-1754732358369', 'performance', 'paint', 'first-paint', '/web-sitenizdeki-guvenlik-aciklarini-google-dorking-ile-test-etme-rehberi/', 816.00, 'https://ondernet.net/web-sitenizdeki-guvenlik-aciklarini-google-dorking-ile-test-etme-rehberi/', '2025-08-09 09:47:16', NULL, NULL, NULL, NULL),
(308, 2, 3, 'session-n64l547vj-1754732358369', 'performance', 'dom-ready', 'time', '/web-sitenizdeki-guvenlik-aciklarini-google-dorking-ile-test-etme-rehberi/', 742.00, 'https://ondernet.net/web-sitenizdeki-guvenlik-aciklarini-google-dorking-ile-test-etme-rehberi/', '2025-08-09 09:47:16', NULL, NULL, NULL, NULL),
(309, 2, 3, 'session-n64l547vj-1754732358369', 'performance', 'page-load', 'load-time', '/web-sitenizdeki-guvenlik-aciklarini-google-dorking-ile-test-etme-rehberi/', 1348.00, 'https://ondernet.net/web-sitenizdeki-guvenlik-aciklarini-google-dorking-ile-test-etme-rehberi/', '2025-08-09 09:47:16', NULL, NULL, NULL, NULL),
(310, 2, 3, 'session-n64l547vj-1754732358369', 'performance', 'paint', 'first-contentful-paint', '/web-sitenizdeki-guvenlik-aciklarini-google-dorking-ile-test-etme-rehberi/', 816.00, 'https://ondernet.net/web-sitenizdeki-guvenlik-aciklarini-google-dorking-ile-test-etme-rehberi/', '2025-08-09 09:47:16', NULL, NULL, NULL, NULL),
(311, 2, 3, 'session-n64l547vj-1754732358369', 'timing', 'time-on-page', 'duration', '/web-sitenizdeki-guvenlik-aciklarini-google-dorking-ile-test-etme-rehberi/', 2.00, 'https://ondernet.net/web-sitenizdeki-guvenlik-aciklarini-google-dorking-ile-test-etme-rehberi/', '2025-08-09 09:47:17', NULL, NULL, NULL, NULL),
(312, 2, 3, 'session-n64l547vj-1754732358369', 'performance', 'page-load', 'load-time', '/ocsf-open-cybersecurity-schema-framework/', 1940.00, 'https://ondernet.net/ocsf-open-cybersecurity-schema-framework/', '2025-08-09 09:47:19', NULL, NULL, NULL, NULL),
(313, 2, 3, 'session-n64l547vj-1754732358369', 'performance', 'paint', 'first-contentful-paint', '/ocsf-open-cybersecurity-schema-framework/', 1328.00, 'https://ondernet.net/ocsf-open-cybersecurity-schema-framework/', '2025-08-09 09:47:19', NULL, NULL, NULL, NULL),
(314, 2, 3, 'session-n64l547vj-1754732358369', 'performance', 'dom-ready', 'time', '/ocsf-open-cybersecurity-schema-framework/', 1366.00, 'https://ondernet.net/ocsf-open-cybersecurity-schema-framework/', '2025-08-09 09:47:19', NULL, NULL, NULL, NULL),
(315, 2, 3, 'session-n64l547vj-1754732358369', 'performance', 'paint', 'first-paint', '/ocsf-open-cybersecurity-schema-framework/', 1328.00, 'https://ondernet.net/ocsf-open-cybersecurity-schema-framework/', '2025-08-09 09:47:19', NULL, NULL, NULL, NULL),
(316, 2, 3, 'session-n64l547vj-1754732358369', 'timing', 'time-on-page', 'duration', '/ocsf-open-cybersecurity-schema-framework/', 4.00, 'https://ondernet.net/ocsf-open-cybersecurity-schema-framework/', '2025-08-09 09:47:23', NULL, NULL, NULL, NULL),
(317, 2, 37, 'session-s07s9efkm-1754732842741', 'performance', 'paint', 'first-contentful-paint', '/google-dorking/', 600.00, 'https://ondernet.net/google-dorking/', '2025-08-09 09:47:24', NULL, NULL, NULL, NULL),
(318, 2, 37, 'session-s07s9efkm-1754732842741', 'performance', 'dom-ready', 'time', '/google-dorking/', 676.00, 'https://ondernet.net/google-dorking/', '2025-08-09 09:47:24', NULL, NULL, NULL, NULL),
(319, 2, 37, 'session-s07s9efkm-1754732842741', 'performance', 'paint', 'first-paint', '/google-dorking/', 600.00, 'https://ondernet.net/google-dorking/', '2025-08-09 09:47:24', NULL, NULL, NULL, NULL),
(320, 2, 37, 'session-s07s9efkm-1754732842741', 'performance', 'page-load', 'load-time', '/google-dorking/', 788.00, 'https://ondernet.net/google-dorking/', '2025-08-09 09:47:24', NULL, NULL, NULL, NULL),
(321, 2, 3, 'session-n64l547vj-1754732358369', 'performance', 'page-load', 'load-time', '/web-sitenizdeki-guvenlik-aciklarini-google-dorking-ile-test-etme-rehberi/', 1358.00, 'https://ondernet.net/web-sitenizdeki-guvenlik-aciklarini-google-dorking-ile-test-etme-rehberi/', '2025-08-09 09:47:27', NULL, NULL, NULL, NULL),
(322, 2, 3, 'session-n64l547vj-1754732358369', 'performance', 'paint', 'first-paint', '/web-sitenizdeki-guvenlik-aciklarini-google-dorking-ile-test-etme-rehberi/', 852.00, 'https://ondernet.net/web-sitenizdeki-guvenlik-aciklarini-google-dorking-ile-test-etme-rehberi/', '2025-08-09 09:47:27', NULL, NULL, NULL, NULL),
(323, 2, 3, 'session-n64l547vj-1754732358369', 'performance', 'dom-ready', 'time', '/web-sitenizdeki-guvenlik-aciklarini-google-dorking-ile-test-etme-rehberi/', 732.00, 'https://ondernet.net/web-sitenizdeki-guvenlik-aciklarini-google-dorking-ile-test-etme-rehberi/', '2025-08-09 09:47:27', NULL, NULL, NULL, NULL),
(324, 2, 3, 'session-n64l547vj-1754732358369', 'performance', 'paint', 'first-contentful-paint', '/web-sitenizdeki-guvenlik-aciklarini-google-dorking-ile-test-etme-rehberi/', 852.00, 'https://ondernet.net/web-sitenizdeki-guvenlik-aciklarini-google-dorking-ile-test-etme-rehberi/', '2025-08-09 09:47:28', NULL, NULL, NULL, NULL),
(325, 2, 3, 'session-n64l547vj-1754732358369', 'timing', 'time-on-page', 'ping', '/web-sitenizdeki-guvenlik-aciklarini-google-dorking-ile-test-etme-rehberi/', 30.00, 'https://ondernet.net/web-sitenizdeki-guvenlik-aciklarini-google-dorking-ile-test-etme-rehberi/', '2025-08-09 09:47:54', NULL, NULL, NULL, NULL),
(326, 2, 3, 'session-n64l547vj-1754732358369', 'timing', 'time-on-page', 'duration', '/web-sitenizdeki-guvenlik-aciklarini-google-dorking-ile-test-etme-rehberi/', 34.00, 'https://ondernet.net/web-sitenizdeki-guvenlik-aciklarini-google-dorking-ile-test-etme-rehberi/', '2025-08-09 09:47:58', NULL, NULL, NULL, NULL),
(327, 2, 3, 'session-n64l547vj-1754732358369', 'performance', 'page-load', 'load-time', '/veri-korumasi/', 2850.00, 'https://ondernet.net/veri-korumasi/', '2025-08-09 09:50:33', NULL, NULL, NULL, NULL),
(328, 2, 3, 'session-n64l547vj-1754732358369', 'performance', 'dom-ready', 'time', '/veri-korumasi/', 1943.00, 'https://ondernet.net/veri-korumasi/', '2025-08-09 09:50:34', NULL, NULL, NULL, NULL),
(329, 2, 3, 'session-n64l547vj-1754732358369', 'performance', 'paint', 'first-contentful-paint', '/veri-korumasi/', 1472.00, 'https://ondernet.net/veri-korumasi/', '2025-08-09 09:50:34', NULL, NULL, NULL, NULL),
(330, 2, 3, 'session-n64l547vj-1754732358369', 'performance', 'paint', 'first-paint', '/veri-korumasi/', 1472.00, 'https://ondernet.net/veri-korumasi/', '2025-08-09 09:50:34', NULL, NULL, NULL, NULL),
(331, 2, 3, 'session-n64l547vj-1754732358369', 'scroll', 'scroll-depth', 'reached', '25%', 25.00, 'https://ondernet.net/veri-korumasi/', '2025-08-09 09:50:35', NULL, NULL, NULL, NULL),
(332, 2, 3, 'session-n64l547vj-1754732358369', 'scroll', 'scroll-depth', 'reached', '50%', 50.00, 'https://ondernet.net/veri-korumasi/', '2025-08-09 09:50:37', NULL, NULL, NULL, NULL),
(333, 2, 3, 'session-n64l547vj-1754732358369', 'scroll', 'scroll-depth', 'reached', '75%', 75.00, 'https://ondernet.net/veri-korumasi/', '2025-08-09 09:50:39', NULL, NULL, NULL, NULL),
(334, 2, 3, 'session-hx7wnf1uq-1754732170159', 'timing', 'time-on-page', 'duration', '/category/misconfiguration/', 6.00, 'https://ondernet.net/category/misconfiguration/', '2025-08-09 09:50:51', NULL, NULL, NULL, NULL),
(335, 2, 3, 'session-hx7wnf1uq-1754732170159', 'timing', 'time-on-page', 'duration', '/category/misconfiguration/', 6.00, 'https://ondernet.net/category/misconfiguration/', '2025-08-09 09:50:51', NULL, NULL, NULL, NULL),
(336, 2, 3, 'session-n64l547vj-1754732358369', 'timing', 'time-on-page', 'duration', '/veri-korumasi/', 1.00, 'https://ondernet.net/veri-korumasi/', '2025-08-09 09:52:51', NULL, NULL, NULL, NULL),
(337, 2, 3, 'session-n64l547vj-1754732358369', 'performance', 'page-load', 'load-time', '/', 1986.00, 'https://ondernet.net/', '2025-08-09 09:52:53', NULL, NULL, NULL, NULL),
(338, 2, 3, 'session-n64l547vj-1754732358369', 'performance', 'dom-ready', 'time', '/', 1154.00, 'https://ondernet.net/', '2025-08-09 09:52:53', NULL, NULL, NULL, NULL),
(339, 2, 3, 'session-n64l547vj-1754732358369', 'performance', 'paint', 'first-paint', '/', 960.00, 'https://ondernet.net/', '2025-08-09 09:52:53', NULL, NULL, NULL, NULL),
(340, 2, 3, 'session-n64l547vj-1754732358369', 'performance', 'paint', 'first-contentful-paint', '/', 960.00, 'https://ondernet.net/', '2025-08-09 09:52:53', NULL, NULL, NULL, NULL),
(341, 2, 3, 'session-n64l547vj-1754732358369', 'scroll', 'scroll-depth', 'reached', '25%', 25.00, 'https://ondernet.net/', '2025-08-09 09:52:55', NULL, NULL, NULL, NULL),
(342, 2, 3, 'session-n64l547vj-1754732358369', 'timing', 'time-on-page', 'ping', '/', 30.00, 'https://ondernet.net/', '2025-08-09 09:53:22', NULL, NULL, NULL, NULL),
(343, 2, 3, 'session-n64l547vj-1754732358369', 'timing', 'time-on-page', 'ping', '/', 60.00, 'https://ondernet.net/', '2025-08-09 09:53:52', NULL, NULL, NULL, NULL),
(344, 2, 3, 'session-n64l547vj-1754732358369', 'timing', 'time-on-page', 'ping', '/', 90.00, 'https://ondernet.net/', '2025-08-09 09:54:22', NULL, NULL, NULL, NULL),
(345, 2, 38, 'session-x68v38qwo-1754733445439', 'performance', 'page-load', 'load-time', '/veri-korumasi/', 1634.00, 'https://ondernet.net/veri-korumasi/', '2025-08-09 09:57:29', NULL, NULL, NULL, NULL),
(346, 2, 38, 'session-x68v38qwo-1754733445439', 'performance', 'paint', 'first-contentful-paint', '/veri-korumasi/', 540.00, 'https://ondernet.net/veri-korumasi/', '2025-08-09 09:57:29', NULL, NULL, NULL, NULL),
(347, 2, 38, 'session-x68v38qwo-1754733445439', 'performance', 'dom-ready', 'time', '/veri-korumasi/', 954.00, 'https://ondernet.net/veri-korumasi/', '2025-08-09 09:57:29', NULL, NULL, NULL, NULL),
(348, 2, 38, 'session-x68v38qwo-1754733445439', 'performance', 'paint', 'first-paint', '/veri-korumasi/', 540.00, 'https://ondernet.net/veri-korumasi/', '2025-08-09 09:57:29', NULL, NULL, NULL, NULL),
(349, 2, 39, 'session-5nkucv3p5-1754733997418', 'performance', 'dom-ready', 'time', '/', 3968.00, 'https://31.7.36.57/', '2025-08-09 10:06:40', NULL, NULL, NULL, NULL),
(350, 2, 39, 'session-5nkucv3p5-1754733997418', 'performance', 'paint', 'first-paint', '/', 3671.00, 'https://31.7.36.57/', '2025-08-09 10:06:40', NULL, NULL, NULL, NULL),
(351, 2, 39, 'session-5nkucv3p5-1754733997418', 'performance', 'page-load', 'load-time', '/', 8083.00, 'https://31.7.36.57/', '2025-08-09 10:06:40', NULL, NULL, NULL, NULL),
(352, 2, 39, 'session-5nkucv3p5-1754733997418', 'performance', 'paint', 'first-contentful-paint', '/', 3671.00, 'https://31.7.36.57/', '2025-08-09 10:06:40', NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `page_views`
--

CREATE TABLE `page_views` (
  `id` bigint NOT NULL,
  `website_id` int NOT NULL,
  `visitor_id` int NOT NULL,
  `page_url` text CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NOT NULL,
  `page_title` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci DEFAULT NULL,
  `referrer_url` text CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci,
  `browser` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci DEFAULT NULL,
  `os` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci DEFAULT NULL,
  `device_type` enum('desktop','mobile','tablet','other') CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci DEFAULT NULL,
  `country` varchar(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci DEFAULT NULL,
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
-- Tablo döküm verisi `page_views`
--

INSERT INTO `page_views` (`id`, `website_id`, `visitor_id`, `page_url`, `page_title`, `referrer_url`, `ip_address`, `user_agent`, `browser`, `os`, `device_type`, `country`, `city`, `screen_resolution`, `viewport_size`, `language`, `created_at`, `country_code`, `region`, `latitude`, `longitude`, `timezone`, `isp`) VALUES
(1, 1, 2, 'https://quiz-test.online/quiz', 'Quiz Ana Sayfa', '', '31.206.100.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', 'Chrome', 'Windows', 'desktop', NULL, NULL, '1920x1080', '1912x920', 'tr', '2025-08-09 00:32:53', NULL, NULL, NULL, NULL, NULL, NULL),
(2, 1, 2, 'https://quiz-test.online/quiz/setup', 'Quiz Ayarları', 'https://quiz-test.online/quiz', '31.206.100.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', 'Chrome', 'Windows', 'mobile', NULL, NULL, '1920x1080', '749x920', 'tr', '2025-08-09 00:33:09', NULL, NULL, NULL, NULL, NULL, NULL),
(3, 1, 2, 'https://quiz-test.online/quiz/play', 'Quiz - Soru 1', 'https://quiz-test.online/quiz/setup', '31.206.100.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', 'Chrome', 'Windows', 'mobile', NULL, NULL, '1920x1080', '749x920', 'tr', '2025-08-09 00:33:21', NULL, NULL, NULL, NULL, NULL, NULL),
(4, 2, 3, 'https://ondernet.net/', 'Onder Online - bilgi, deneyim, çözüm', '', '31.206.100.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', 'Chrome', 'Windows', 'mobile', NULL, NULL, '1920x1080', '749x920', 'tr', '2025-08-09 00:40:22', NULL, NULL, NULL, NULL, NULL, NULL),
(5, 3, 4, 'https://haaberler.com/', 'Modern Takvim Dashboard', '', '31.206.100.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', 'Chrome', 'Windows', 'desktop', NULL, NULL, '1920x1080', '1912x920', 'tr', '2025-08-09 00:41:50', NULL, NULL, NULL, NULL, NULL, NULL),
(6, 4, 5, 'https://cmd-test.site/', 'Gerçek zamanlı, tarayıcı tabanlı modüler terminal simülatörü.', '', '31.206.100.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', 'Chrome', 'Windows', 'desktop', NULL, NULL, '1920x1080', '1912x920', 'tr', '2025-08-09 00:43:05', NULL, NULL, NULL, NULL, NULL, NULL),
(7, 5, 6, 'https://tekne-sepeti.com/', 'Tekne Sepeti – Bütçenize ve zevkinize uygun turlar', 'https://cloud11.elithosting.com:2087/', '31.206.100.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', 'Chrome', 'Windows', 'desktop', NULL, NULL, '1920x1080', '1912x920', 'tr', '2025-08-09 00:44:34', NULL, NULL, NULL, NULL, NULL, NULL),
(8, 6, 7, 'https://webustasipro.com/', 'Sosyal Medya Eğitim Seti', '', '31.206.100.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', 'Chrome', 'Windows', 'desktop', NULL, NULL, '1920x1080', '1912x920', 'tr', '2025-08-09 00:45:48', NULL, NULL, NULL, NULL, NULL, NULL),
(9, 7, 8, 'https://kocaenerji.com/', 'Koca Enerji', '', '31.206.100.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', 'Chrome', 'Windows', 'desktop', NULL, NULL, '1920x1080', '1912x920', 'tr', '2025-08-09 00:47:43', NULL, NULL, NULL, NULL, NULL, NULL),
(10, 8, 9, 'https://hedefsolar.com/', 'Güneş Enerjisi Çözümleri | Hedef Solar - Sürdürülebilir Gelecek', '', '31.206.100.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', 'Chrome', 'Windows', 'desktop', NULL, NULL, '1920x1080', '1912x920', 'tr', '2025-08-09 00:48:54', NULL, NULL, NULL, NULL, NULL, NULL),
(11, 2, 1, 'https://ondernet.net/', 'Onder Online - bilgi, deneyim, çözüm', '', '46.154.6.143', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Mobile Safari/537.36 OPR/90.0.0.0', 'Chrome', 'Linux', 'mobile', NULL, NULL, '412x892', '455x899', 'tr-TR', '2025-08-09 00:59:57', NULL, NULL, NULL, NULL, NULL, NULL),
(12, 2, 10, 'https://ondernet.net/active-directoryde-folder-redirection-kullanimi/', 'Active Directory\'de Folder Redirection Kullanımı - Onder Online', '', '65.55.210.42', 'Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; bingbot/2.0; +http://www.bing.com/bingbot.htm) Chrome/136.0.0.0 Safari/537.36', 'Chrome', 'Unknown', 'desktop', NULL, NULL, '800x600', '1024x569', 'en-US', '2025-08-09 01:27:10', NULL, NULL, NULL, NULL, NULL, NULL),
(13, 2, 11, 'https://ondernet.net/category/misconfiguration/', 'Misconfiguration arşivleri - Onder Online', 'https://ondernet.net/winutil-windowsun-en-populer-arac-paketi/', '46.154.6.143', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Mobile Safari/537.36', 'Chrome', 'Linux', 'mobile', NULL, NULL, '412x892', '455x837', 'tr', '2025-08-09 01:30:16', NULL, NULL, NULL, NULL, NULL, NULL),
(14, 2, 11, 'https://ondernet.net/', 'Onder Online - bilgi, deneyim, çözüm', 'https://ondernet.net/category/misconfiguration/', '46.154.6.143', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Mobile Safari/537.36', 'Chrome', 'Linux', 'mobile', NULL, NULL, '412x892', '455x837', 'tr', '2025-08-09 01:30:18', NULL, NULL, NULL, NULL, NULL, NULL),
(15, 2, 12, 'https://ondernet.net/exchange-online-powershell-e-nasil-baglanilir/', 'Exchange Online PowerShell’e Nasıl Bağlanılır? - Onder Online', '', '66.249.79.134', 'Mozilla/5.0 (Linux; Android 6.0.1; Nexus 5X Build/MMB29P) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.7151.119 Mobile Safari/537.36 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)', 'Chrome', 'Linux', 'mobile', NULL, NULL, '412x732', '598x1063', 'en-US', '2025-08-09 01:33:36', NULL, NULL, NULL, NULL, NULL, NULL),
(16, 5, 13, 'https://tekne-sepeti.com/', 'Tekne Sepeti – Bütçenize ve zevkinize uygun turlar', '', '202.8.41.146', 'Mozilla/5.0 (compatible; AhrefsBot/7.0; +http://ahrefs.com/robot/)', 'Unknown', 'Unknown', 'desktop', NULL, NULL, '800x600', '1024x12140', 'en-US', '2025-08-09 01:36:03', NULL, NULL, NULL, NULL, NULL, NULL),
(17, 6, 14, 'https://webustasipro.com/', 'Sosyal Medya Eğitim Seti', '', '202.8.43.51', 'Mozilla/5.0 (compatible; AhrefsBot/7.0; +http://ahrefs.com/robot/)', 'Unknown', 'Unknown', 'desktop', NULL, NULL, '800x600', '1024x12140', 'en-US', '2025-08-09 01:57:59', NULL, NULL, NULL, NULL, NULL, NULL),
(18, 8, 15, 'https://www.hedefsolar.com/', 'Güneş Enerjisi Çözümleri | Hedef Solar - Sürdürülebilir Gelecek', '', '3.142.47.192', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/121.0.0.0 Safari/537.36', 'Chrome', 'macOS', 'desktop', NULL, NULL, '1366x768', '1366x768', 'en-US', '2025-08-09 02:01:40', NULL, NULL, NULL, NULL, NULL, NULL),
(19, 8, 16, 'https://www.hedefsolar.com/blog', 'Güneş Enerjisi Çözümleri | Hedef Solar - Sürdürülebilir Gelecek', '', '3.142.47.192', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/121.0.0.0 Safari/537.36', 'Chrome', 'macOS', 'desktop', NULL, NULL, '1366x768', '1366x768', 'en-US', '2025-08-09 02:01:44', NULL, NULL, NULL, NULL, NULL, NULL),
(20, 8, 17, 'https://www.hedefsolar.com/contact', 'Güneş Enerjisi Çözümleri | Hedef Solar - Sürdürülebilir Gelecek', '', '3.142.47.192', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/121.0.0.0 Safari/537.36', 'Chrome', 'macOS', 'desktop', NULL, NULL, '1366x768', '1366x768', 'en-US', '2025-08-09 02:01:47', NULL, NULL, NULL, NULL, NULL, NULL),
(21, 8, 18, 'https://www.hedefsolar.com/about', 'Güneş Enerjisi Çözümleri | Hedef Solar - Sürdürülebilir Gelecek', '', '3.142.47.192', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/121.0.0.0 Safari/537.36', 'Chrome', 'macOS', 'desktop', NULL, NULL, '1366x768', '1366x768', 'en-US', '2025-08-09 02:01:50', NULL, NULL, NULL, NULL, NULL, NULL),
(22, 2, 19, 'https://ondernet.net/tag/microsoft/', 'Microsoft arşivleri - Onder Online', '', '66.249.79.133', 'Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; Googlebot/2.1; +http://www.google.com/bot.html) Chrome/137.0.7151.119 Safari/537.36', 'Chrome', 'Unknown', 'desktop', NULL, NULL, '1024x1024', '1024x1024', 'en-US', '2025-08-09 02:28:20', NULL, NULL, NULL, NULL, NULL, NULL),
(23, 2, 19, 'https://ondernet.net/category/cyber-security/', 'Cyber Security arşivleri - Onder Online', '', '66.249.79.133', 'Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; Googlebot/2.1; +http://www.google.com/bot.html) Chrome/137.0.7151.119 Safari/537.36', 'Chrome', 'Unknown', 'desktop', NULL, NULL, '1024x1024', '1024x1024', 'en-US', '2025-08-09 02:50:28', NULL, NULL, NULL, NULL, NULL, NULL),
(24, 2, 20, 'https://ondernet.net/tag/siem/', 'SIEM arşivleri - Onder Online', '', '66.249.79.132', 'Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; Googlebot/2.1; +http://www.google.com/bot.html) Chrome/137.0.7151.119 Safari/537.36', 'Chrome', 'Unknown', 'desktop', NULL, NULL, '1024x1024', '1024x1024', 'en-US', '2025-08-09 02:58:09', NULL, NULL, NULL, NULL, NULL, NULL),
(25, 7, 21, 'http://kocaenerji.com/', 'Koca Enerji', '', '66.249.79.132', 'Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; Googlebot/2.1; +http://www.google.com/bot.html) Chrome/137.0.7151.119 Safari/537.36', 'Chrome', 'Unknown', 'desktop', NULL, NULL, '1024x1024', '1024x1024', 'en-US', '2025-08-09 03:06:14', NULL, NULL, NULL, NULL, NULL, NULL),
(26, 2, 22, 'https://ondernet.net/tag/soc-analyst/', 'SOC Analyst arşivleri - Onder Online', '', '66.249.79.132', 'Mozilla/5.0 (Linux; Android 6.0.1; Nexus 5X Build/MMB29P) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.7151.119 Mobile Safari/537.36 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)', 'Chrome', 'Linux', 'mobile', NULL, NULL, '412x732', '456x811', 'en-US', '2025-08-09 03:56:35', NULL, NULL, NULL, NULL, NULL, NULL),
(27, 2, 23, 'https://ondernet.net/tag/incident-response/', 'incident response arşivleri - Onder Online', '', '66.249.79.133', 'Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; Googlebot/2.1; +http://www.google.com/bot.html) Chrome/137.0.7151.119 Safari/537.36', 'Chrome', 'Unknown', 'desktop', NULL, NULL, '1024x1024', '1024x1024', 'en-US', '2025-08-09 04:07:04', NULL, NULL, NULL, NULL, NULL, NULL),
(28, 2, 24, 'https://ondernet.net/2025-yilinda-kullanabileceginiz-web-tarayicilari/', '2025 Yılında Kullanabileceğiniz Web Tarayıcıları - Onder Online', '', '40.77.179.127', 'Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; bingbot/2.0; +http://www.bing.com/bingbot.htm) Chrome/136.0.0.0 Safari/537.36', 'Chrome', 'Unknown', 'desktop', NULL, NULL, '800x600', '1024x569', 'en-US', '2025-08-09 04:08:41', NULL, NULL, NULL, NULL, NULL, NULL),
(29, 2, 25, 'https://ondernet.net/category/elek/', 'Elek* arşivleri - Onder Online', '', '66.249.79.134', 'Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; Googlebot/2.1; +http://www.google.com/bot.html) Chrome/137.0.7151.119 Safari/537.36', 'Chrome', 'Unknown', 'desktop', NULL, NULL, '1024x1024', '1024x1024', 'en-US', '2025-08-09 04:42:09', NULL, NULL, NULL, NULL, NULL, NULL),
(30, 8, 26, 'https://www.hedefsolar.com/references/detail/4', 'Güneş Enerjisi Çözümleri | Hedef Solar - Sürdürülebilir Gelecek', '', '66.249.79.132', 'Mozilla/5.0 (Linux; Android 6.0.1; Nexus 5X Build/MMB29P) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.7151.119 Mobile Safari/537.36 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)', 'Chrome', 'Linux', 'tablet', NULL, NULL, '412x732', '981x1742', 'en-US', '2025-08-09 04:53:27', NULL, NULL, NULL, NULL, NULL, NULL),
(31, 1, 27, 'http://www.quiz-test.online/login', 'Giriş Yap', '', '66.249.79.132', 'Mozilla/5.0 (Linux; Android 6.0.1; Nexus 5X Build/MMB29P) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.7151.119 Mobile Safari/537.36 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)', 'Chrome', 'Linux', 'mobile', NULL, NULL, '412x732', '412x732', 'en-US', '2025-08-09 05:22:18', NULL, NULL, NULL, NULL, NULL, NULL),
(32, 2, 28, 'https://ondernet.net/', 'Onder Online - bilgi, deneyim, çözüm', '', '66.249.79.132', 'Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; Googlebot/2.1; +http://www.google.com/bot.html) Chrome/137.0.7151.119 Safari/537.36', 'Chrome', 'Unknown', 'desktop', NULL, NULL, '1024x1024', '1024x1024', 'en-US', '2025-08-09 06:31:41', NULL, NULL, NULL, NULL, NULL, NULL),
(33, 2, 29, 'https://ondernet.net/tag/linux/', 'Linux arşivleri - Onder Online', '', '66.249.79.133', 'Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; Googlebot/2.1; +http://www.google.com/bot.html) Chrome/137.0.7151.119 Safari/537.36', 'Chrome', 'Unknown', 'desktop', NULL, NULL, '1024x1024', '1024x1024', 'en-US', '2025-08-09 06:41:32', NULL, NULL, NULL, NULL, NULL, NULL),
(34, 2, 30, 'https://ondernet.net/category/sistem/', 'Sistem arşivleri - Onder Online', '', '66.249.79.133', 'Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; Googlebot/2.1; +http://www.google.com/bot.html) Chrome/137.0.7151.119 Safari/537.36', 'Chrome', 'Unknown', 'desktop', NULL, NULL, '1024x1024', '1024x1024', 'en-US', '2025-08-09 06:51:41', NULL, NULL, NULL, NULL, NULL, NULL),
(35, 2, 31, 'https://ondernet.net/?page=6', 'Onder Online - bilgi, deneyim, çözüm', '', '66.249.79.132', 'Mozilla/5.0 (Linux; Android 6.0.1; Nexus 5X Build/MMB29P) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.7151.119 Mobile Safari/537.36 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)', 'Chrome', 'Linux', 'mobile', NULL, NULL, '412x732', '456x811', 'en-US', '2025-08-09 06:59:15', NULL, NULL, NULL, NULL, NULL, NULL),
(36, 1, 27, 'https://www.quiz-test.online/login', 'Giriş Yap', '', '66.249.79.133', 'Mozilla/5.0 (Linux; Android 6.0.1; Nexus 5X Build/MMB29P) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.7151.119 Mobile Safari/537.36 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)', 'Chrome', 'Linux', 'mobile', NULL, NULL, '412x732', '412x732', 'en-US', '2025-08-09 07:15:23', NULL, NULL, NULL, NULL, NULL, NULL),
(37, 2, 32, 'https://ondernet.net/', 'Onder Online - bilgi, deneyim, çözüm', '', '188.245.215.47', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/93.0.4577.0 Safari/537.36', 'Chrome', 'macOS', 'desktop', NULL, NULL, '800x600', '1440x900', 'en-US', '2025-08-09 07:19:49', NULL, NULL, NULL, NULL, NULL, NULL),
(38, 2, 33, 'https://ondernet.net/', 'Onder Online - bilgi, deneyim, çözüm', '', '188.245.215.47', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/93.0.4577.0 Safari/537.36', 'Chrome', 'macOS', 'desktop', NULL, NULL, '800x600', '1440x900', 'en-US', '2025-08-09 07:20:01', NULL, NULL, NULL, NULL, NULL, NULL),
(39, 2, 34, 'https://ondernet.net/tag/topoloji/', 'Topoloji arşivleri - Onder Online', '', '66.249.79.133', 'Mozilla/5.0 (Linux; Android 6.0.1; Nexus 5X Build/MMB29P) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.7151.119 Mobile Safari/537.36 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)', 'Chrome', 'Linux', 'mobile', NULL, NULL, '412x732', '456x811', 'en-US', '2025-08-09 07:21:22', NULL, NULL, NULL, NULL, NULL, NULL),
(40, 1, 27, 'https://www.quiz-test.online/login', 'Giriş Yap', '', '66.249.79.134', 'Mozilla/5.0 (Linux; Android 6.0.1; Nexus 5X Build/MMB29P) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.7151.119 Mobile Safari/537.36 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)', 'Chrome', 'Linux', 'mobile', NULL, NULL, '412x732', '412x732', 'en-US', '2025-08-09 07:49:59', NULL, NULL, NULL, NULL, NULL, NULL),
(41, 1, 27, 'https://www.quiz-test.online/login', 'Giriş Yap', '', '66.249.79.133', 'Mozilla/5.0 (Linux; Android 6.0.1; Nexus 5X Build/MMB29P) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.7151.119 Mobile Safari/537.36 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)', 'Chrome', 'Linux', 'mobile', NULL, NULL, '412x732', '412x732', 'en-US', '2025-08-09 08:02:16', NULL, NULL, NULL, NULL, NULL, NULL),
(42, 2, 3, 'https://ondernet.net/', 'Onder Online - bilgi, deneyim, çözüm', '', '31.206.100.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', 'Chrome', 'Windows', 'desktop', NULL, NULL, '1920x1080', '1912x924', 'tr', '2025-08-09 08:31:24', NULL, NULL, NULL, NULL, NULL, NULL),
(43, 2, 3, 'https://ondernet.net/?s=dork', 'dork için arama yaptınız - Onder Online', 'https://ondernet.net/', '31.206.100.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', 'Chrome', 'Windows', 'desktop', NULL, NULL, '1920x1080', '1912x924', 'tr', '2025-08-09 08:31:42', NULL, NULL, NULL, NULL, NULL, NULL),
(44, 2, 3, 'https://ondernet.net/web-sitenizdeki-guvenlik-aciklarini-google-dorking-ile-test-etme-rehberi/', 'Web Sitenizdeki Güvenlik Açıklarını Google Dorking ile Test Etme Rehberi - Onder Online', 'https://ondernet.net/?s=dork', '31.206.100.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', 'Chrome', 'Windows', 'desktop', NULL, NULL, '1920x1080', '1912x924', 'tr', '2025-08-09 08:31:51', NULL, NULL, NULL, NULL, NULL, NULL),
(45, 2, 3, 'https://ondernet.net/web-sitenizdeki-guvenlik-aciklarini-google-dorking-ile-test-etme-rehberi/', 'Web Sitenizdeki Güvenlik Açıklarını Google Dorking ile Test Etme Rehberi - Onder Online', 'https://ondernet.net/wp-admin/post.php?post=14&action=edit&classic-editor', '31.206.100.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', 'Chrome', 'Windows', 'desktop', NULL, NULL, '1920x1080', '1912x924', 'tr', '2025-08-09 08:38:53', NULL, NULL, NULL, NULL, NULL, NULL),
(46, 2, 35, 'https://ondernet.net/ondernet-net/', 'ondernet.net - Onder Online', '', '40.77.177.81', 'Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; bingbot/2.0; +http://www.bing.com/bingbot.htm) Chrome/136.0.0.0 Safari/537.36', 'Chrome', 'Unknown', 'desktop', NULL, NULL, '800x600', '1024x569', 'en-US', '2025-08-09 08:50:13', NULL, NULL, NULL, NULL, NULL, NULL),
(47, 2, 36, 'https://ondernet.net/category/misconfiguration/', 'Misconfiguration arşivleri - Onder Online', '', '66.249.79.134', 'Mozilla/5.0 (Linux; Android 6.0.1; Nexus 5X Build/MMB29P) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.7151.119 Mobile Safari/537.36 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)', 'Chrome', 'Linux', 'mobile', NULL, NULL, '412x732', '456x811', 'en-US', '2025-08-09 09:18:38', NULL, NULL, NULL, NULL, NULL, NULL),
(48, 2, 3, 'https://ondernet.net/hassas-veriler/', 'Hassas Veriler - Onder Online', 'https://ondernet.net/wp-admin/post.php?post=1412&action=edit&classic-editor', '31.206.100.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', 'Chrome', 'Windows', 'desktop', NULL, NULL, '1920x1080', '1912x924', 'tr', '2025-08-09 09:33:12', NULL, NULL, NULL, NULL, NULL, NULL),
(49, 2, 3, 'https://ondernet.net/', 'Onder Online - bilgi, deneyim, çözüm', '', '31.206.100.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', 'Chrome', 'Windows', 'desktop', NULL, NULL, '1920x1080', '1912x924', 'tr', '2025-08-09 09:36:11', NULL, NULL, NULL, NULL, NULL, NULL),
(50, 2, 3, 'https://ondernet.net/category/misconfiguration/', 'Misconfiguration arşivleri - Onder Online', 'https://ondernet.net/', '31.206.100.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', 'Chrome', 'Windows', 'desktop', NULL, NULL, '1920x1080', '1912x924', 'tr', '2025-08-09 09:36:17', NULL, NULL, NULL, NULL, NULL, NULL),
(51, 2, 3, 'https://ondernet.net/?s=siber+sald%C4%B1r%C4%B1', 'siber saldırı için arama yaptınız - Onder Online', 'https://ondernet.net/category/misconfiguration/', '31.206.100.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', 'Chrome', 'Windows', 'desktop', NULL, NULL, '1920x1080', '1912x924', 'tr', '2025-08-09 09:36:41', NULL, NULL, NULL, NULL, NULL, NULL),
(52, 2, 3, 'https://ondernet.net/kucuk-isletmeler-buyuk-butce-olmadan-siber-saldirilardan-nasil-korunur/', 'Küçük İşletmeler Büyük Bütçe Olmadan Siber Saldırılardan Nasıl Korunur? - Onder Online', 'https://ondernet.net/?s=siber+sald%C4%B1r%C4%B1', '31.206.100.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', 'Chrome', 'Windows', 'desktop', NULL, NULL, '1920x1080', '1912x924', 'tr', '2025-08-09 09:36:46', NULL, NULL, NULL, NULL, NULL, NULL),
(53, 2, 31, 'https://ondernet.net/tag/linux/', 'Linux arşivleri - Onder Online', '', '66.249.79.134', 'Mozilla/5.0 (Linux; Android 6.0.1; Nexus 5X Build/MMB29P) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.7151.119 Mobile Safari/537.36 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)', 'Chrome', 'Linux', 'mobile', NULL, NULL, '412x732', '456x811', 'en-US', '2025-08-09 09:38:15', NULL, NULL, NULL, NULL, NULL, NULL),
(54, 2, 3, 'https://ondernet.net/google-dorking/', 'Google Dorking - Onder Online', 'https://ondernet.net/wp-admin/post.php?post=1411&action=edit&classic-editor', '31.206.100.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', 'Chrome', 'Windows', 'desktop', NULL, NULL, '1920x1080', '1912x924', 'tr', '2025-08-09 09:39:19', NULL, NULL, NULL, NULL, NULL, NULL),
(55, 2, 3, 'https://ondernet.net/google-dorking/', 'Google Dorking - Onder Online', 'https://ondernet.net/wp-admin/post.php?post=1411&action=edit&classic-editor', '31.206.100.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', 'Chrome', 'Windows', 'desktop', NULL, NULL, '1920x1080', '1912x924', 'tr', '2025-08-09 09:39:48', NULL, NULL, NULL, NULL, NULL, NULL),
(56, 2, 3, 'https://ondernet.net/google-dorking/', 'Google Dorking - Onder Online', 'https://ondernet.net/wp-admin/post.php?post=1411&action=edit&classic-editor', '31.206.100.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', 'Chrome', 'Windows', 'desktop', NULL, NULL, '1920x1080', '1912x924', 'tr', '2025-08-09 09:42:10', NULL, NULL, NULL, NULL, NULL, NULL),
(57, 2, 3, 'https://ondernet.net/', 'Onder Online - bilgi, deneyim, çözüm', 'https://ondernet.net/google-dorking/', '31.206.100.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', 'Chrome', 'Windows', 'desktop', NULL, NULL, '1920x1080', '1912x924', 'tr', '2025-08-09 09:42:18', NULL, NULL, NULL, NULL, NULL, NULL),
(58, 2, 3, 'https://ondernet.net/?s=veri', 'veri için arama yaptınız - Onder Online', 'https://ondernet.net/', '31.206.100.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', 'Chrome', 'Windows', 'desktop', NULL, NULL, '1920x1080', '1912x924', 'tr', '2025-08-09 09:42:26', NULL, NULL, NULL, NULL, NULL, NULL),
(59, 2, 3, 'https://ondernet.net/page/2/?s=veri', 'veri için arama yaptınız - Sayfa 2 / 21 - Onder Online', 'https://ondernet.net/?s=veri', '31.206.100.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', 'Chrome', 'Windows', 'desktop', NULL, NULL, '1920x1080', '1912x924', 'tr', '2025-08-09 09:42:36', NULL, NULL, NULL, NULL, NULL, NULL),
(60, 2, 3, 'https://ondernet.net/hassas-veriler/', 'Hassas Veriler - Onder Online', 'https://ondernet.net/page/2/?s=veri', '31.206.100.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', 'Chrome', 'Windows', 'desktop', NULL, NULL, '1920x1080', '1912x924', 'tr', '2025-08-09 09:42:39', NULL, NULL, NULL, NULL, NULL, NULL),
(61, 2, 3, 'https://ondernet.net/hassas-veriler/', 'Hassas Veriler - Onder Online', 'https://ondernet.net/wp-admin/post.php?post=1412&action=edit&classic-editor', '31.206.100.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', 'Chrome', 'Windows', 'desktop', NULL, NULL, '1920x1080', '1912x924', 'tr', '2025-08-09 09:44:14', NULL, NULL, NULL, NULL, NULL, NULL),
(62, 2, 3, 'https://ondernet.net/', 'Onder Online - bilgi, deneyim, çözüm', 'https://ondernet.net/hassas-veriler/', '31.206.100.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', 'Chrome', 'Windows', 'desktop', NULL, NULL, '1920x1080', '1912x924', 'tr', '2025-08-09 09:44:29', NULL, NULL, NULL, NULL, NULL, NULL),
(63, 2, 3, 'https://ondernet.net/google-dorking/', 'Google Dorking - Onder Online', 'https://ondernet.net/', '31.206.100.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', 'Chrome', 'Windows', 'desktop', NULL, NULL, '1920x1080', '1912x924', 'tr', '2025-08-09 09:44:38', NULL, NULL, NULL, NULL, NULL, NULL),
(64, 2, 3, 'https://ondernet.net/web-sitenizdeki-guvenlik-aciklarini-google-dorking-ile-test-etme-rehberi/', 'Web Sitenizdeki Güvenlik Açıklarını Google Dorking ile Test Etme Rehberi - Onder Online', 'https://ondernet.net/google-dorking/', '31.206.100.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', 'Chrome', 'Windows', 'desktop', NULL, NULL, '1920x1080', '1912x924', 'tr', '2025-08-09 09:44:42', NULL, NULL, NULL, NULL, NULL, NULL),
(65, 2, 3, 'https://ondernet.net/category/misconfiguration/', 'Misconfiguration arşivleri - Onder Online', 'https://ondernet.net/kucuk-isletmeler-buyuk-butce-olmadan-siber-saldirilardan-nasil-korunur/', '31.206.100.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', 'Chrome', 'Windows', 'desktop', NULL, NULL, '1920x1080', '1912x924', 'tr', '2025-08-09 09:46:50', NULL, NULL, NULL, NULL, NULL, NULL),
(66, 2, 3, 'https://ondernet.net/web-sitenizdeki-guvenlik-aciklarini-google-dorking-ile-test-etme-rehberi/', 'Web Sitenizdeki Güvenlik Açıklarını Google Dorking ile Test Etme Rehberi - Onder Online', 'https://ondernet.net/wp-admin/post.php?post=14&action=edit&classic-editor', '31.206.100.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', 'Chrome', 'Windows', 'desktop', NULL, NULL, '1920x1080', '1912x924', 'tr', '2025-08-09 09:47:07', NULL, NULL, NULL, NULL, NULL, NULL),
(67, 2, 3, 'https://ondernet.net/category/misconfiguration/', 'Misconfiguration arşivleri - Onder Online', 'https://ondernet.net/web-sitenizdeki-guvenlik-aciklarini-google-dorking-ile-test-etme-rehberi/', '31.206.100.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', 'Chrome', 'Windows', 'desktop', NULL, NULL, '1920x1080', '1912x924', 'tr', '2025-08-09 09:47:12', NULL, NULL, NULL, NULL, NULL, NULL),
(68, 2, 3, 'https://ondernet.net/web-sitenizdeki-guvenlik-aciklarini-google-dorking-ile-test-etme-rehberi/', 'Web Sitenizdeki Güvenlik Açıklarını Google Dorking ile Test Etme Rehberi - Onder Online', 'https://ondernet.net/wp-admin/post.php?post=14&action=edit&classic-editor', '31.206.100.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', 'Chrome', 'Windows', 'desktop', NULL, NULL, '1920x1080', '1912x924', 'tr', '2025-08-09 09:47:15', NULL, NULL, NULL, NULL, NULL, NULL),
(69, 2, 3, 'https://ondernet.net/ocsf-open-cybersecurity-schema-framework/', 'OCSF (Open Cybersecurity Schema Framework) - Onder Online', 'https://ondernet.net/web-sitenizdeki-guvenlik-aciklarini-google-dorking-ile-test-etme-rehberi/', '31.206.100.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', 'Chrome', 'Windows', 'desktop', NULL, NULL, '1920x1080', '1912x924', 'tr', '2025-08-09 09:47:19', NULL, NULL, NULL, NULL, NULL, NULL),
(70, 2, 37, 'https://ondernet.net/google-dorking/', 'Google Dorking - Onder Online', '', '40.77.178.154', 'Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; bingbot/2.0; +http://www.bing.com/bingbot.htm) Chrome/136.0.0.0 Safari/537.36', 'Chrome', 'Unknown', 'desktop', NULL, NULL, '800x600', '1024x569', 'en-US', '2025-08-09 09:47:24', NULL, NULL, NULL, NULL, NULL, NULL),
(71, 2, 3, 'https://ondernet.net/web-sitenizdeki-guvenlik-aciklarini-google-dorking-ile-test-etme-rehberi/', 'Web Sitenizdeki Güvenlik Açıklarını Google Dorking ile Test Etme Rehberi - Onder Online', 'https://ondernet.net/wp-admin/post.php?post=14&action=edit&classic-editor', '31.206.100.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', 'Chrome', 'Windows', 'desktop', NULL, NULL, '1920x1080', '1912x924', 'tr', '2025-08-09 09:47:27', NULL, NULL, NULL, NULL, NULL, NULL),
(72, 2, 3, 'https://ondernet.net/veri-korumasi/', 'Veri Koruması - Onder Online', 'https://ondernet.net/wp-admin/post.php?post=1422&action=edit&classic-editor', '31.206.100.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', 'Chrome', 'Windows', 'desktop', NULL, NULL, '1920x1080', '1912x924', 'tr', '2025-08-09 09:50:33', NULL, NULL, NULL, NULL, NULL, NULL),
(73, 2, 3, 'https://ondernet.net/', 'Onder Online - bilgi, deneyim, çözüm', 'https://ondernet.net/veri-korumasi/', '31.206.100.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', 'Chrome', 'Windows', 'desktop', NULL, NULL, '1920x1080', '1912x924', 'tr', '2025-08-09 09:52:52', NULL, NULL, NULL, NULL, NULL, NULL),
(74, 2, 38, 'https://ondernet.net/veri-korumasi/', 'Veri Koruması - Onder Online', '', '40.77.179.200', 'Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; bingbot/2.0; +http://www.bing.com/bingbot.htm) Chrome/136.0.0.0 Safari/537.36', 'Chrome', 'Unknown', 'desktop', NULL, NULL, '800x600', '1024x569', 'en-US', '2025-08-09 09:57:27', NULL, NULL, NULL, NULL, NULL, NULL),
(75, 2, 39, 'https://31.7.36.57/', 'Onder Online - bilgi, deneyim, çözüm', '', '205.169.39.244', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.79 Safari/537.36', 'Chrome', 'Windows', 'desktop', NULL, NULL, '1024x1024', '1024x1024', 'en-US', '2025-08-09 10:06:38', NULL, NULL, NULL, NULL, NULL, NULL);

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
-- Tablo için tablo yapısı `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(191) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Tablo döküm verisi `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `created_at`, `updated_at`) VALUES
(1, 'onder', 'onder7@gmail.com', '$2y$10$xhdZk8e.G9E1TVn29bGCRumpH5iq7/eQsQV44HrqOYBPg4ARJGSNS', '2025-08-08 23:42:51', '2025-08-08 23:42:51');

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
-- Tablo döküm verisi `visitors`
--

INSERT INTO `visitors` (`id`, `visitor_id`, `first_visit`, `last_visit`) VALUES
(1, 'visitor-tmma6qiq1-1754698907566', '2025-08-09 00:26:17', '2025-08-09 01:01:56'),
(2, 'visitor-31gw02k5p-1754698122129', '2025-08-09 00:32:53', '2025-08-09 00:33:27'),
(3, 'visitor-b1zcolt26-1754696975486', '2025-08-09 00:40:22', '2025-08-09 09:54:22'),
(4, 'visitor-2qldhuf7r-1754700109750', '2025-08-09 00:41:50', '2025-08-09 00:41:54'),
(5, 'visitor-n5nqy0kl2-1754700185385', '2025-08-09 00:43:05', '2025-08-09 00:43:25'),
(6, 'visitor-iwvf8v2ik-1754700274482', '2025-08-09 00:44:34', '2025-08-09 00:44:40'),
(7, 'visitor-jqk6boy4u-1754700348158', '2025-08-09 00:45:48', '2025-08-09 00:46:03'),
(8, 'visitor-el33huea2-1754700463277', '2025-08-09 00:47:43', '2025-08-09 00:47:48'),
(9, 'visitor-5cgivuv88-1754700533919', '2025-08-09 00:48:54', '2025-08-09 00:49:13'),
(10, 'visitor-c4c3a645w-1754702827937', '2025-08-09 01:27:10', '2025-08-09 01:27:11'),
(11, 'visitor-5pi5ikflz-1754697611933', '2025-08-09 01:30:16', '2025-08-09 01:30:24'),
(12, 'visitor-h1ftiunob-1754697600082', '2025-08-09 01:33:36', '2025-08-09 01:33:41'),
(13, 'visitor-i9x1pfl24-1754703363072', '2025-08-09 01:36:03', '2025-08-09 01:36:03'),
(14, 'visitor-ncw1cyift-1754704678204', '2025-08-09 01:57:59', '2025-08-09 01:57:59'),
(15, 'visitor-r1f2yo48v-1754704899460', '2025-08-09 02:01:40', '2025-08-09 02:01:40'),
(16, 'visitor-0dr8hr5mf-1754704903811', '2025-08-09 02:01:44', '2025-08-09 02:01:45'),
(17, 'visitor-kxnp1rx0k-1754704906980', '2025-08-09 02:01:47', '2025-08-09 02:01:48'),
(18, 'visitor-txv3chdne-1754704910119', '2025-08-09 02:01:50', '2025-08-09 02:01:51'),
(19, 'visitor-tzf8x6jbv-1754704800050', '2025-08-09 02:28:20', '2025-08-09 02:50:35'),
(20, 'visitor-tzf8x6jbv-1754704800060', '2025-08-09 02:58:09', '2025-08-09 02:58:15'),
(21, 'visitor-2k48h7eup-1754708400136', '2025-08-09 03:06:14', '2025-08-09 03:06:18'),
(22, 'visitor-lec6tn7bd-1754697600020', '2025-08-09 03:56:35', '2025-08-09 03:56:42'),
(23, 'visitor-lec6tn7bd-1754712000020', '2025-08-09 04:07:04', '2025-08-09 04:07:23'),
(24, 'visitor-rlht1v7ct-1754712520076', '2025-08-09 04:08:41', '2025-08-09 04:08:41'),
(25, 'visitor-z0xbw2kjr-1754712000023', '2025-08-09 04:42:09', '2025-08-09 04:42:16'),
(26, 'visitor-lec6tn7bd-1754697600040', '2025-08-09 04:53:27', '2025-08-09 04:53:29'),
(27, 'visitor-n0ja7c6z2-1754697600010', '2025-08-09 05:22:18', '2025-08-09 08:02:20'),
(28, 'visitor-z0xbw2kjr-1754719200020', '2025-08-09 06:31:41', '2025-08-09 06:31:48'),
(29, 'visitor-tzf8x6jbv-1754719200059', '2025-08-09 06:41:32', '2025-08-09 06:41:45'),
(30, 'visitor-tzf8x6jbv-1754719200030', '2025-08-09 06:51:41', '2025-08-09 06:51:47'),
(31, 'visitor-h1ftiunob-1754697600079', '2025-08-09 06:59:15', '2025-08-09 09:38:28'),
(32, 'visitor-6dfw17s65-1754723988553', '2025-08-09 07:19:49', '2025-08-09 07:19:52'),
(33, 'visitor-7g9a7tlp4-1754724000816', '2025-08-09 07:20:01', '2025-08-09 07:20:04'),
(34, 'visitor-2xly6p1sj-1754697600033', '2025-08-09 07:21:22', '2025-08-09 07:21:41'),
(35, 'visitor-bl0xnbkz0-1754729411211', '2025-08-09 08:50:13', '2025-08-09 08:50:13'),
(36, 'visitor-h1ftiunob-1754697600050', '2025-08-09 09:18:38', '2025-08-09 09:18:44'),
(37, 'visitor-leu9cekyz-1754732842741', '2025-08-09 09:47:24', '2025-08-09 09:47:24'),
(38, 'visitor-my8esuvme-1754733445439', '2025-08-09 09:57:27', '2025-08-09 09:57:29'),
(39, 'visitor-ce7lyaxmj-1754733997418', '2025-08-09 10:06:38', '2025-08-09 10:06:40');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `websites`
--

CREATE TABLE `websites` (
  `id` int NOT NULL,
  `domain` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NOT NULL,
  `api_key` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;

--
-- Tablo döküm verisi `websites`
--

INSERT INTO `websites` (`id`, `domain`, `name`, `api_key`, `created_at`, `updated_at`) VALUES
(1, 'quiz-test.online', 'quiz-test.online', '0717ccaee4eb446a0ecf5ae038d369f3e069c7d87d25452992486879e47a4c09', '2025-08-09 00:31:40', '2025-08-09 00:32:44'),
(2, 'ondernet.net', 'ondernet.net', 'b43dee079cac1803189212867c0d3de1c4a523e6835b4c63a8a1b2d04a887217', '2025-08-09 00:33:52', '2025-08-09 00:40:17'),
(3, 'haaberler.com', 'haaberler.com', '375e3c5372940706619cbc27e61fd860ff38cc86704f52939342965ba4cd3e04', '2025-08-09 00:40:59', '2025-08-09 00:41:43'),
(4, 'cmd-test.site', 'cmd-test.site', 'fe49f5e8fbe58e1b5893a53b5d89bd090c6f7e9363b89e48ae62dcef0ea7c266', '2025-08-09 00:42:19', '2025-08-09 00:43:07'),
(5, 'tekne-sepeti.com', 'tekne-sepeti.com', '594ba14ec6fc59356c634a6e4288a99103e87d23bf370f610f000fa81cebd270', '2025-08-09 00:43:47', '2025-08-09 00:43:47'),
(6, 'webustasipro.com', 'webustasipro.com', 'b2a8460bd539d416a064cb8b24d4e63a577a946b171f7e50b4b2a3be5e920457', '2025-08-09 00:44:58', '2025-08-09 00:44:58'),
(7, 'kocaenerji.com', 'kocaenerji.com', '1a126ce53b9c081f404f6fb2e106da0c943a61fd7b8b6efc64c3c4fc694db807', '2025-08-09 00:46:42', '2025-08-09 00:47:27'),
(8, 'hedefsolar.com', 'hedefsolar.com', '212166b883af7d4cae245beee343aec77536fccfcfd45467e5eb8f645e60a611', '2025-08-09 00:48:04', '2025-08-09 00:48:51');

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
  ADD KEY `idx_created_at` (`created_at`);

--
-- Tablo için indeksler `page_views`
--
ALTER TABLE `page_views`
  ADD PRIMARY KEY (`id`),
  ADD KEY `visitor_id` (`visitor_id`),
  ADD KEY `idx_website_created` (`website_id`,`created_at`),
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
  ADD KEY `idx_website_session` (`website_id`,`session_id`),
  ADD KEY `idx_start_time` (`start_time`);

--
-- Tablo için indeksler `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
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
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `domain` (`domain`),
  ADD UNIQUE KEY `api_key` (`api_key`),
  ADD KEY `idx_api_key` (`api_key`);

--
-- Dökümü yapılmış tablolar için AUTO_INCREMENT değeri
--

--
-- Tablo için AUTO_INCREMENT değeri `api_requests`
--
ALTER TABLE `api_requests`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `daily_stats`
--
ALTER TABLE `daily_stats`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `events`
--
ALTER TABLE `events`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=353;

--
-- Tablo için AUTO_INCREMENT değeri `page_views`
--
ALTER TABLE `page_views`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=76;

--
-- Tablo için AUTO_INCREMENT değeri `sessions`
--
ALTER TABLE `sessions`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Tablo için AUTO_INCREMENT değeri `visitors`
--
ALTER TABLE `visitors`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- Tablo için AUTO_INCREMENT değeri `websites`
--
ALTER TABLE `websites`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
