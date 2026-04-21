-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: mariadb
-- Generation Time: Apr 21, 2026 at 09:03 AM
-- Server version: 12.0.2-MariaDB-ubu2404
-- PHP Version: 8.2.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `Social_app`
--

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `id` int(11) UNSIGNED NOT NULL COMMENT 'post-> comment',
  `thread_id` int(11) UNSIGNED NOT NULL COMMENT 'user->comment',
  `user_id` int(11) UNSIGNED NOT NULL,
  `content` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`id`, `thread_id`, `user_id`, `content`, `created_at`) VALUES
(1, 11, 11, 'kl', '2026-04-07 09:39:31'),
(2, 11, 11, 'klm', '2026-04-07 09:39:35'),
(3, 10, 11, 'kk', '2026-04-07 09:39:41'),
(4, 1, 11, 'test_comment', '2026-04-07 09:40:00'),
(5, 11, 11, 'feerer', '2026-04-07 09:43:56'),
(6, 11, 11, 'dsd', '2026-04-07 09:45:07'),
(7, 11, 11, 'dfdf', '2026-04-07 09:45:13'),
(8, 11, 11, 'fd', '2026-04-07 09:48:31'),
(9, 11, 7, 'hej', '2026-04-08 08:33:51'),
(10, 15, 11, 'jh', '2026-04-21 08:02:00');

-- --------------------------------------------------------

--
-- Table structure for table `friends`
--

CREATE TABLE `friends` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `friend_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_uca1400_ai_ci;

--
-- Dumping data for table `friends`
--

INSERT INTO `friends` (`id`, `user_id`, `friend_id`, `created_at`) VALUES
(1, 8, 7, '2026-03-09 12:35:36'),
(2, 7, 16, '2026-03-09 12:37:41'),
(3, 7, 5, '2026-03-09 12:41:41'),
(4, 7, 13, '2026-03-09 12:41:42'),
(5, 7, 17, '2026-03-09 12:41:43'),
(6, 7, 8, '2026-03-09 12:41:44'),
(7, 7, 12, '2026-03-09 12:41:54'),
(8, 18, 11, '2026-03-09 13:22:07'),
(9, 11, 16, '2026-03-10 06:50:29'),
(10, 11, 8, '2026-03-10 07:34:04'),
(11, 11, 7, '2026-03-10 09:15:44'),
(12, 8, 11, '2026-03-10 09:20:02'),
(13, 8, 16, '2026-03-11 12:09:08'),
(14, 11, 13, '2026-04-07 09:23:52'),
(15, 11, 12, '2026-04-08 08:49:40');

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `from_id` int(11) NOT NULL,
  `to_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_uca1400_ai_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `from_id`, `to_id`, `message`, `created_at`) VALUES
(1, 8, 7, 'hej', '2026-03-09 12:35:58'),
(2, 8, 7, 'hej igen', '2026-03-09 12:36:14'),
(3, 7, 8, 'hejhej', '2026-03-09 12:37:04'),
(4, 7, 16, 'hej', '2026-03-09 12:41:31'),
(6, 11, 18, 'hej jigga', '2026-03-09 13:22:58'),
(7, 11, 8, 'hej ferit', '2026-03-10 07:34:10'),
(8, 8, 11, 'hej max', '2026-03-10 07:34:57'),
(9, 11, 8, 'Hur mår Ferit idag?', '2026-03-10 09:16:20'),
(10, 8, 11, 'Bara bra!', '2026-03-10 09:16:50'),
(11, 8, 7, 'Hej', '2026-03-10 09:25:24'),
(12, 8, 16, 'Hejsan carl, vad trevligt att hitta dig här', '2026-03-11 12:09:26'),
(13, 11, 8, 'hj', '2026-04-07 09:23:25'),
(14, 11, 18, 'f', '2026-04-07 09:23:32'),
(15, 11, 18, 'f', '2026-04-07 09:23:32'),
(16, 11, 18, 'f', '2026-04-07 09:23:32'),
(17, 11, 18, 'f', '2026-04-07 09:23:32'),
(18, 11, 18, 'f', '2026-04-07 09:23:32'),
(19, 11, 18, '@d', '2026-04-07 09:23:37'),
(20, 11, 18, 'a', '2026-04-07 09:23:38'),
(21, 11, 13, 'dds', '2026-04-07 09:23:59'),
(22, 11, 13, 'ha', '2026-04-21 08:05:23');

-- --------------------------------------------------------

--
-- Table structure for table `post`
--

CREATE TABLE `post` (
  `pid` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `post_txt` text NOT NULL,
  `date` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_uca1400_ai_ci;

--
-- Dumping data for table `post`
--

INSERT INTO `post` (`pid`, `uid`, `post_txt`, `date`) VALUES
(1, 11, 'hej hej', '2026-03-16 12:22:06'),
(2, 11, 'hallllllllllåååååååååå', '2026-03-16 12:22:24');

-- --------------------------------------------------------

--
-- Table structure for table `threads`
--

CREATE TABLE `threads` (
  `id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL COMMENT 'foreign key to users.id',
  `content` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `threads`
--

INSERT INTO `threads` (`id`, `user_id`, `content`, `created_at`) VALUES
(1, 11, 'hej hej hej jeh', '2026-03-18 12:19:08'),
(2, 11, 'hej hej', '2026-03-18 12:19:17'),
(3, 11, 'hej', '2026-03-23 13:09:56'),
(4, 11, 'hej detta är inlägg idag', '2026-03-23 13:21:02'),
(5, 11, 'jjj', '2026-03-23 13:21:15'),
(6, 11, 'pizza', '2026-04-07 09:27:43'),
(7, 11, 'hhh', '2026-04-07 09:29:27'),
(8, 11, 'nm', '2026-04-07 09:30:27'),
(9, 11, 'hj', '2026-04-07 09:32:51'),
(10, 11, 'hj', '2026-04-07 09:33:19'),
(11, 11, 'k', '2026-04-07 09:33:32'),
(12, 11, 'hj', '2026-04-08 08:48:43'),
(13, 11, 'hejsan alla detta var en trevlig dag', '2026-04-08 08:49:27'),
(14, 11, 'hej', '2026-04-21 08:01:42'),
(15, 11, 'hej', '2026-04-21 08:01:47');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(100) UNSIGNED NOT NULL COMMENT 'Primär Nyckel',
  `username` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `last_login` datetime DEFAULT NULL,
  `display_name` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `biography` text DEFAULT NULL,
  `pfps` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_swedish_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`, `created_at`, `last_login`, `display_name`, `biography`, `pfps`) VALUES
(5, 'byhe', '$2b$12$krFsB2wuBo76rl/fDxj3lOxNPBrh5Aun/lNuQNnJk8O6eJA7liv9a', 'henrik@gmail.com', '2026-01-13 11:42:59', NULL, 'Henrik Bygren', NULL, NULL),
(7, 'alexbalex', '$2y$12$8GdjW4Dwp5ZKi5aF4VxDq.lVZP//THeDITpY2NeTYnime1f7Baosq', 'alexfellert@gmail.com', '2026-01-28 12:31:14', NULL, 'Alexander Fellert', NULL, '/uploads/pfps/pfp_7_1775637258.png'),
(8, 'Feba', '$2y$12$mAcegiPziHbIOTh3fgLZYu/yvhJfZNly5Q2WDse2Ir8I3cVzcaRxW', 'feba@gmail.com', '2026-02-02 13:16:48', NULL, 'Ferit Bat', 'hejhej', '/uploads/pfps/pfp_8_1772627669.png'),
(11, 'Lyma', '$2y$12$rUXPvr24LVD15GyJvFWP8e36mSQ8WfLv/csxoZEXVK3nR4spvoLa2', 'maxlysell@gmail.com', '2026-02-09 13:36:22', NULL, 'Max Lysell', 'hejhejhej....', '/uploads/pfps/pfp_11_1773062624.jpeg'),
(12, 'mily', '$2y$12$DUkN/uLXUjIsU66v48cAbewclL3alMHVHRUkPVf12ILvOQd2RGea.', 'mily@gmail.com', '2026-02-18 14:06:47', NULL, 'Mika lysell', NULL, NULL),
(13, 'sebnam', '$2y$12$Ym2Zpt5htgZbf/AJiMGzUuZL4/Xqcal8oxSVXwTQvHAC4fXaF7I3S', 'sebnam@gmail.com', '2026-02-23 13:24:26', NULL, 'Seb Namn', NULL, NULL),
(14, 'olpe', '$2y$12$UTlhuE6Bg2q5u7Liv/Jq6OqmPr0XRWIp.al5sLLuoQ0n7t00PAmDG', 'ollepetterson@gmail.com', '2026-02-24 09:28:04', NULL, 'Olle petterson', NULL, NULL),
(16, 'callaballa', '$2y$12$SR7d2z6FBkM4vMYQFRJD3ee2mGhcvNwYIi1aYRLxcxrZ20pVvWQ7e', 'carl@hemsle.se', '2026-02-26 13:00:35', NULL, 'Carl Hemsle', NULL, NULL),
(17, 'zibi', '$2y$12$uv1ZKb4VijMwmT3KgYUEluojD41N0CBbukK7DHlSHAi/bVpYgME/O', 'zibi@gmail.com', '2026-03-09 12:22:17', NULL, 'zinka binka', NULL, NULL),
(18, 'supermegaman', '$2y$12$.BIiP3BRtS8AfiqNQ6a6..RT.YGV5ytzs7x9LHW2q4iz3Bi9dO8U.', 'a@a', '2026-03-09 13:21:36', NULL, 'algot grlnvall', 'jag är jag', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `thread_id` (`thread_id`);

--
-- Indexes for table `friends`
--
ALTER TABLE `friends`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_pair` (`user_id`,`friend_id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `post`
--
ALTER TABLE `post`
  ADD PRIMARY KEY (`pid`),
  ADD KEY `uid` (`uid`);

--
-- Indexes for table `threads`
--
ALTER TABLE `threads`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'post-> comment', AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `friends`
--
ALTER TABLE `friends`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `post`
--
ALTER TABLE `post`
  MODIFY `pid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `threads`
--
ALTER TABLE `threads`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(100) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Primär Nyckel', AUTO_INCREMENT=19;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
