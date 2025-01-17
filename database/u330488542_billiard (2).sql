-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Nov 15, 2024 at 12:58 PM
-- Server version: 10.11.10-MariaDB
-- PHP Version: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `u330488542_billiard`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_notifications`
--

CREATE TABLE `admin_notifications` (
  `notification_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `is_read` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admin_notifications`
--

INSERT INTO `admin_notifications` (`notification_id`, `user_id`, `message`, `created_at`, `is_read`) VALUES
(16, NULL, 'New walk-in booking confirmed for Table Table 3 from 2024-11-05T20:31 to 2024-11-05T21:31.', '2024-11-05 12:30:20', 1),
(17, NULL, 'New walk-in booking confirmed for Table Table 1 from 2024-11-05T20:32 to 2024-11-05T21:32.', '2024-11-05 12:30:52', 1),
(18, NULL, 'New walk-in booking confirmed for Table Table 1 by asdad from 2024-11-05T14:16 to 2024-11-05T15:16.', '2024-11-05 13:14:40', 1),
(19, NULL, 'New walk-in booking confirmed for Table Table 2 by jp from 2024-11-06T12:00 to 2024-11-06T13:00.', '2024-11-06 02:26:12', 1),
(20, NULL, 'User \'kyle\' has joined the tournament \'Tournament elimination\' with a pending payment verification.', '2024-11-07 17:58:53', 1),
(21, NULL, 'New walk-in booking confirmed for Table Table 1 by kenth john from 2024-11-08T09:09 to 2024-11-08T10:09.', '2024-11-08 01:10:03', 1),
(22, NULL, 'User \'sam\' has joined the tournament \'pakusganay tournament\' with a pending payment verification.', '2024-11-08 01:16:43', 1),
(23, NULL, 'New walk-in booking confirmed for Table Table 1 by asd from 2024-11-10T21:57 to 2024-11-10T22:57.', '2024-11-10 13:56:05', 1),
(24, 17, 'Booking #165 payment by jake via Cash confirmed on Table 1 from 2024-11-11 10:54 PM to 2024-11-11 11:54 PM.', '2024-11-11 14:52:55', 1),
(25, NULL, 'New walk-in booking confirmed for Table Table 2 by kuyaw from 2024-11-12T01:30 to 2024-11-12T02:30.', '2024-11-11 16:29:38', 1),
(26, NULL, 'Booking ID 157 has been archived by admin.', '2024-11-11 16:32:40', 1),
(27, NULL, 'Booking ID 162 has been archived by admin.', '2024-11-12 04:01:00', 1);

-- --------------------------------------------------------

--
-- Table structure for table `announcements`
--

CREATE TABLE `announcements` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `body` text NOT NULL,
  `tournament_id` int(11) NOT NULL,
  `round` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `expires_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `announcements`
--

INSERT INTO `announcements` (`id`, `title`, `body`, `tournament_id`, `round`, `created_at`, `expires_at`) VALUES
(197, 'New Tournament: asdad', 'The tournament asdad is starting on November 12, 2024\nStart time at 5:23 am\nand will end on November 12, 2024\nEnd Time at 10:23 am\nVenue: adad\nMaximum players allowed: 2\nCategory: Class A\nStatus: Upcoming.', 44, 0, '2024-11-11 16:21:38', '2024-11-12 10:23:00');

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `booking_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `table_id` int(11) DEFAULT NULL,
  `customer_name` varchar(255) DEFAULT NULL,
  `contact_number` varchar(15) DEFAULT NULL,
  `table_name` varchar(100) NOT NULL,
  `start_time` datetime DEFAULT NULL,
  `end_time` datetime DEFAULT NULL,
  `status` varchar(100) DEFAULT 'Pending',
  `payment_status` varchar(255) NOT NULL,
  `num_matches` int(11) DEFAULT NULL,
  `amount` varchar(255) NOT NULL,
  `archive` int(11) DEFAULT 0,
  `num_players` int(11) DEFAULT NULL,
  `payment_link` varchar(255) DEFAULT NULL,
  `paypal_payment_id` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`booking_id`, `user_id`, `table_id`, `customer_name`, `contact_number`, `table_name`, `start_time`, `end_time`, `status`, `payment_status`, `num_matches`, `amount`, `archive`, `num_players`, `payment_link`, `paypal_payment_id`) VALUES
(155, 22, 1, NULL, '', 'Table 1', '2024-11-06 12:00:00', '2024-11-06 13:00:00', 'Checked Out', '', NULL, '', 0, 4, 'https://pm.link/tjamessportybar/test/icBaicR', NULL),
(156, NULL, 2, 'jp', '', 'Table 2', '2024-11-06 12:00:00', '2024-11-06 13:00:00', 'Checked Out', '', NULL, '', 0, 4, NULL, NULL),
(157, 0, 1, NULL, '', 'Table 1', '2024-11-07 10:44:00', '2024-11-07 11:44:00', 'Confirmed', '', NULL, '', 1, 3, NULL, NULL),
(158, 24, 4, NULL, '', 'Table 4', '2024-11-08 03:00:00', '2024-11-08 04:00:00', 'Checked Out', '', NULL, '', 0, 2, NULL, 'PAYID-M4WPWPA1JA72809N0249183D'),
(159, 32, 3, NULL, '', 'Table 3', '2024-11-08 03:00:00', '2024-11-08 04:00:00', 'Checked Out', '', NULL, '', 0, 2, 'https://pm.link/tjamessportybar/test/zCCbYVD', NULL),
(160, 25, 1, NULL, '', 'Table 1', '2024-11-08 13:00:00', '2024-11-08 15:00:00', 'Checked Out', '', NULL, '', 0, 2, 'https://pm.link/tjamessportybar/test/8fazJ37', NULL),
(161, NULL, 1, 'kenth john', '09773433504', 'Table 1', '2024-11-08 09:09:00', '2024-11-08 10:09:00', 'Checked Out', '', NULL, '', 0, 2, NULL, NULL),
(162, NULL, 1, 'asd', '09773433504', 'Table 1', '2024-11-10 21:57:00', '2024-11-10 22:57:00', 'Checked Out', '', NULL, '', 1, 2, NULL, NULL),
(164, 17, 2, NULL, NULL, 'Table 2', '2024-11-11 22:52:00', '2024-11-11 23:52:00', 'Checked Out', '', NULL, '', 0, 2, NULL, NULL),
(165, 17, 1, NULL, NULL, 'Table 1', '2024-11-11 22:54:00', '2024-11-11 23:54:00', 'Checked Out', '', NULL, '', 0, 1, NULL, NULL),
(166, NULL, 2, 'kuyaw', '09939243993', 'Table 2', '2024-11-12 01:30:00', '2024-11-12 02:30:00', 'Checked Out', '', NULL, '', 0, 2, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `bracket`
--

CREATE TABLE `bracket` (
  `bracket_id` int(11) NOT NULL,
  `tournament_id` int(11) NOT NULL,
  `player1_id` int(11) DEFAULT NULL,
  `player2_id` int(11) DEFAULT NULL,
  `round` int(11) NOT NULL,
  `match_number` int(11) NOT NULL,
  `winner_id` int(11) DEFAULT NULL,
  `player1_score` int(11) DEFAULT NULL,
  `player2_score` int(11) DEFAULT NULL,
  `scheduled_time` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `bracket`
--

INSERT INTO `bracket` (`bracket_id`, `tournament_id`, `player1_id`, `player2_id`, `round`, `match_number`, `winner_id`, `player1_score`, `player2_score`, `scheduled_time`) VALUES
(210, 41, 32, 30, 1, 1, 32, NULL, NULL, '2024-11-11 13:00:00'),
(211, 41, 31, 25, 1, 2, 31, NULL, NULL, '2024-11-11 17:00:00'),
(212, 41, 45, 33, 1, 3, 33, NULL, NULL, '2024-11-11 21:00:00'),
(213, 41, 17, 22, 1, 4, NULL, NULL, NULL, '2024-11-12 01:00:00'),
(214, 41, 32, 31, 2, 1, NULL, NULL, NULL, '2024-11-12 05:00:00'),
(215, 41, 33, NULL, 2, 2, NULL, NULL, NULL, '2024-11-12 09:00:00'),
(216, 41, NULL, NULL, 3, 1, NULL, NULL, NULL, '2024-11-12 13:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `chat_messages`
--

CREATE TABLE `chat_messages` (
  `id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL DEFAULT 3,
  `message` text NOT NULL,
  `timestamp` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `chat_messages`
--

INSERT INTO `chat_messages` (`id`, `sender_id`, `receiver_id`, `message`, `timestamp`) VALUES
(3, 17, 3, 'asdad', '2024-10-31 16:47:31'),
(4, 17, 3, 'adad', '2024-10-31 16:49:21'),
(5, 3, 17, 'asdada', '2024-10-31 17:32:32'),
(6, 25, 3, 'asdad', '2024-10-31 17:32:57'),
(7, 25, 3, 'adad', '2024-10-31 17:41:01'),
(8, 17, 3, 'asdad', '2024-11-01 16:46:49'),
(9, 3, 17, 'adadad', '2024-11-05 04:08:32'),
(10, 17, 3, 'ghh', '2024-11-05 12:36:42'),
(11, 3, 25, 'asdasd', '2024-11-15 02:50:23');

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `experience` enum('bad','average','good') NOT NULL,
  `feedback` text NOT NULL,
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `feedback`
--

INSERT INTO `feedback` (`id`, `name`, `email`, `experience`, `feedback`, `submitted_at`) VALUES
(1, 'jake', 'jake@gmail.com', 'bad', 'so baddd', '2024-07-17 13:47:06'),
(2, 'sam', 'sam@gmail.com', 'good', 'improve moreadad adadas adas daddasd asdasdas adasd adasda adasd aadas adasda das das', '2024-07-17 14:06:59'),
(3, 'cris', 'cris@gmail.com', 'good', 'so goooddd adad adad adasd asdasasdasdasdas', '2024-07-17 14:07:14'),
(4, 'ator', 'ATOR@gmail.com', 'bad', 'bad experience during billiard tournament nuong october 23, 2024 8pm', '2024-10-23 13:25:50');

-- --------------------------------------------------------

--
-- Table structure for table `inventory`
--

CREATE TABLE `inventory` (
  `item_id` int(11) NOT NULL,
  `item_name` varchar(255) NOT NULL,
  `quantity` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `image` varchar(255) NOT NULL,
  `date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `inventory`
--

INSERT INTO `inventory` (`item_id`, `item_name`, `quantity`, `description`, `image`, `date`) VALUES
(29, 'ball', '15', 'item in table 1', 'penguin.jpg', '2024-11-10'),
(30, 'tako', '4', 'item in table 1', '1.jpg', '2024-11-10'),
(31, 'ball', '15', 'item in table 2', 'penguin.jpg', '2024-11-10'),
(32, 'tako', '4', 'item in table 2', '1.jpg', '2024-11-10'),
(33, 'ball', '15', 'item in table 3', 'penguin.jpg', '2024-11-11'),
(34, 'tako', '4', 'item in table 3', '1.jpg', '2024-11-10'),
(35, 'ball', '15', 'item in table 4', 'penguin.jpg', '2024-11-10'),
(36, 'tako', '4', 'item in table 4', '1.jpg', '2024-11-10'),
(37, 'Tisa', '10', 'item ', 'master.jpg', '2024-11-10'),
(38, 'special cue ', '5', 'Brand - Mezzcue', 'mezzcue.png', '2024-11-10'),
(39, 'special cue ', '1', 'brand - Peapson', 'peapson.jpg', '2024-11-10');

-- --------------------------------------------------------

--
-- Table structure for table `inventory_transactions`
--

CREATE TABLE `inventory_transactions` (
  `transaction_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `transaction_type` enum('inflow','outflow') NOT NULL,
  `quantity` int(11) NOT NULL,
  `date_time` datetime DEFAULT current_timestamp(),
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `inventory_transactions`
--

INSERT INTO `inventory_transactions` (`transaction_id`, `item_id`, `transaction_type`, `quantity`, `date_time`, `description`) VALUES
(15, 29, 'outflow', -15, '2024-11-06 03:06:21', 'use in table 1'),
(16, 29, 'outflow', -15, '2024-11-06 03:06:55', 'use in table 1'),
(17, 31, 'outflow', -15, '2024-11-06 03:07:36', 'use in table 2'),
(18, 30, 'inflow', 13, '2024-11-06 03:08:37', 'returned from table 2 '),
(19, 30, 'outflow', -15, '2024-11-06 03:09:12', 'use in table 2'),
(24, 29, 'outflow', 15, '2024-11-06 13:54:48', 'used in table 1\r\n'),
(25, 29, 'inflow', 15, '2024-11-06 13:56:17', 'returned from table 1\r\n'),
(28, 32, 'outflow', 2, '2024-11-07 17:21:53', 'used in table 4'),
(29, 32, 'inflow', 2, '2024-11-07 17:22:37', 'returned from table 4'),
(30, 30, 'outflow', 2, '2024-11-08 01:42:21', 'used in table 1'),
(31, 30, 'outflow', 2, '2024-11-08 01:45:57', 'used in table 1'),
(32, 30, 'inflow', 4, '2024-11-11 09:26:57', 'Returned from table 1\r\n');

-- --------------------------------------------------------

--
-- Table structure for table `matches`
--

CREATE TABLE `matches` (
  `match_id` int(11) NOT NULL,
  `tournament_id` int(11) NOT NULL,
  `table_id` int(11) DEFAULT NULL,
  `player1_id` int(11) NOT NULL,
  `player2_id` int(11) NOT NULL,
  `player1_score` int(11) DEFAULT 0,
  `player2_score` int(11) DEFAULT 0,
  `start_time` datetime DEFAULT NULL,
  `end_time` datetime DEFAULT NULL,
  `status` varchar(100) DEFAULT NULL,
  `created_at` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `notification_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `is_read` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`notification_id`, `user_id`, `message`, `created_at`, `is_read`) VALUES
(22, NULL, 'Upcoming Tournament: 123 at asdad starting on November 5, 2024 at 9:24 pm.', '2024-11-05 12:22:59', 0),
(23, NULL, 'Your booking (ID: 153) status has been updated to Cancelled.', '2024-11-05 13:06:38', 0),
(24, NULL, 'Your booking (ID: 153) status has been updated to Confirmed.', '2024-11-05 13:06:42', 0),
(25, 22, 'Your booking (ID: 155) status has been updated to Confirmed.', '2024-11-06 02:22:36', 0),
(26, NULL, 'Upcoming Tournament: ator cup at tjames katanggawan starting on November 7, 2024 at 3:00 pm.', '2024-11-06 02:48:44', 0),
(27, NULL, 'Your booking (ID: 156) status has been updated to Checked Out.', '2024-11-07 01:37:01', 0),
(28, NULL, 'Your booking (ID: 156) status has been updated to Confirmed.', '2024-11-07 01:37:06', 0),
(29, NULL, 'Your booking (ID: 156) status has been updated to Checked Out.', '2024-11-07 04:08:32', 0),
(30, 22, 'Your booking (ID: 155) status has been updated to Checked Out.', '2024-11-07 04:08:49', 0),
(31, 17, 'Your booking (ID: 157) status has been updated to Confirmed.', '2024-11-07 04:47:11', 0),
(32, 17, 'Your booking (ID: 157) status has been updated to Checked Out.', '2024-11-07 04:47:35', 0),
(33, 17, 'Your booking (ID: 157) status has been updated to Confirmed.', '2024-11-07 04:49:15', 0),
(34, 17, 'Your booking (ID: 157) status has been updated to Checked Out.', '2024-11-07 04:49:54', 0),
(35, 22, 'Your booking (ID: 155) status has been updated to Confirmed.', '2024-11-07 06:25:57', 0),
(36, NULL, 'Your booking (ID: 156) status has been updated to Confirmed.', '2024-11-07 06:26:03', 0),
(37, 22, 'Your booking (ID: 155) status has been updated to Checked Out.', '2024-11-07 06:26:09', 0),
(38, NULL, 'Your booking (ID: 156) status has been updated to Checked Out.', '2024-11-07 06:26:18', 0),
(39, NULL, 'Upcoming Tournament: Tournament elimination at blabagno compound katanggawan General Santos City - Tjames Sporty Bar  starting on November 9, 2024 at 8:30 am.', '2024-11-07 16:17:19', 1),
(40, 25, 'Your booking (ID: 160) status has been updated to Confirmed.', '2024-11-08 01:08:15', 0),
(41, 25, 'Your booking (ID: 160) status has been updated to Checked Out.', '2024-11-08 01:08:36', 0),
(42, NULL, 'Your booking (ID: 161) status has been updated to Checked Out.', '2024-11-08 01:10:26', 0),
(43, NULL, 'Upcoming Tournament: pakusganay tournament at blabagno compound starting on November 11, 2024 at 1:00 pm.', '2024-11-08 01:14:43', 0),
(44, NULL, 'Upcoming Tournament: asdad at asd starting on November 10, 2024 at 12:36 pm.', '2024-11-10 04:34:53', 0),
(45, 22, 'Your booking (ID: 155) status has been updated to Confirmed.', '2024-11-10 12:02:38', 0),
(46, NULL, 'Your booking (ID: 156) status has been updated to Confirmed.', '2024-11-10 12:02:42', 0),
(47, 17, 'Your booking (ID: 157) status has been updated to Confirmed.', '2024-11-10 12:02:46', 0),
(48, 32, 'Your booking (ID: 159) status has been updated to Confirmed.', '2024-11-10 12:02:51', 0),
(49, NULL, 'Your booking (ID: 161) status has been updated to Confirmed.', '2024-11-10 12:02:55', 0),
(50, 25, 'Your booking (ID: 160) status has been updated to Confirmed.', '2024-11-10 12:03:03', 0),
(51, 22, 'Your booking (ID: 155) status has been updated to Checked Out.', '2024-11-10 12:03:27', 0),
(52, NULL, 'Your booking (ID: 156) status has been updated to Checked Out.', '2024-11-10 12:03:31', 0),
(53, 17, 'Your booking (ID: 157) status has been updated to Checked Out.', '2024-11-10 12:03:35', 0),
(54, 24, 'Your booking (ID: 158) status has been updated to Checked Out.', '2024-11-10 12:03:39', 0),
(55, 32, 'Your booking (ID: 159) status has been updated to Checked Out.', '2024-11-10 12:03:43', 0),
(56, NULL, 'Your booking (ID: 161) status has been updated to Checked Out.', '2024-11-10 12:03:48', 0),
(57, 25, 'Your booking (ID: 160) status has been updated to Checked Out.', '2024-11-10 12:03:52', 0),
(58, NULL, 'Upcoming Tournament: asd at asd starting on November 10, 2024 at 11:11 pm.', '2024-11-10 15:10:05', 0),
(59, NULL, 'Upcoming Tournament: asdad at adad starting on November 12, 2024 at 5:23 am.', '2024-11-11 16:21:38', 0),
(60, NULL, 'Your booking (ID: 162) status has been updated to Checked Out.', '2024-11-11 16:31:55', 0),
(63, 24, 'Your booking (ID: 158) status has been updated to Checked Out.', '2024-11-11 16:32:13', 0),
(65, 17, 'Your booking (ID: 164) status has been updated to Checked Out.', '2024-11-11 16:33:13', 0),
(66, 17, 'Your booking (ID: 165) status has been updated to Checked Out.', '2024-11-11 16:33:17', 0),
(67, NULL, 'Your booking (ID: 166) status has been updated to Checked Out.', '2024-11-12 12:55:18', 0);

-- --------------------------------------------------------

--
-- Table structure for table `otp_codes`
--

CREATE TABLE `otp_codes` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `user_id` int(11) NOT NULL,
  `otp_code` varchar(6) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `expires_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `otp_codes`
--

INSERT INTO `otp_codes` (`id`, `email`, `user_id`, `otp_code`, `created_at`, `expires_at`) VALUES
(1, '', 17, '710451', '2024-10-11 12:26:03', '2024-10-11 12:31:03'),
(2, '', 17, '300938', '2024-10-11 12:31:03', '2024-10-11 12:36:03'),
(3, '', 17, '360690', '2024-10-11 12:31:39', '2024-10-11 12:36:39'),
(4, '', 17, '392916', '2024-10-11 12:34:04', '2024-10-11 12:39:04'),
(5, '', 17, '263017', '2024-10-11 12:34:22', '2024-10-11 12:39:22'),
(6, '', 17, '784240', '2024-10-11 12:36:18', '2024-10-11 12:41:18'),
(7, '', 17, '193139', '2024-10-11 12:36:45', '2024-10-11 12:41:45'),
(10, '', 17, '621403', '2024-10-11 12:53:55', '2024-10-11 12:58:55'),
(11, 'edric5611@gmail.com', 17, '306838', '2024-10-11 12:56:20', '2024-10-11 13:01:20'),
(12, 'edric5611@gmail.com', 17, '684238', '2024-10-11 12:56:49', '2024-10-11 13:01:49'),
(13, 'edric5611@gmail.com', 17, '470530', '2024-10-11 12:57:30', '2024-10-11 13:02:30'),
(14, 'edric5611@gmail.com', 17, '720609', '2024-10-11 12:58:15', '2024-10-11 13:03:15'),
(15, 'edric5611@gmail.com', 17, '734642', '2024-10-11 12:59:32', '2024-10-11 13:04:32'),
(16, 'edric5611@gmail.com', 17, '386402', '2024-10-11 13:00:40', '2024-10-11 13:05:40'),
(17, 'edric5611@gmail.com', 17, '201962', '2024-10-11 13:03:21', '2024-10-11 13:08:21'),
(18, 'edric5611@gmail.com', 17, '412742', '2024-10-11 13:04:38', '2024-10-11 13:09:38'),
(19, 'edric5611@gmail.com', 17, '138698', '2024-10-11 13:11:35', '2024-10-11 13:16:35'),
(20, 'edric5611@gmail.com', 17, '933919', '2024-10-11 13:13:42', '2024-10-11 13:18:42'),
(21, 'edric5611@gmail.com', 17, '307815', '2024-10-11 13:16:01', '2024-10-11 13:21:01'),
(22, 'edric5611@gmail.com', 17, '871871', '2024-10-11 13:17:34', '2024-10-11 13:22:34'),
(23, 'edric5611@gmail.com', 17, '849711', '2024-10-11 13:18:50', '2024-10-11 13:23:50'),
(24, 'edric5611@gmail.com', 17, '915682', '2024-10-11 13:21:59', '2024-10-11 13:26:59'),
(25, 'edric5611@gmail.com', 17, '843768', '2024-10-11 13:22:42', '2024-10-11 13:27:42'),
(26, 'edric5611@gmail.com', 17, '304437', '2024-10-11 13:22:45', '2024-10-11 13:27:45'),
(27, 'edric5611@gmail.com', 17, '938476', '2024-10-11 13:22:50', '2024-10-11 13:27:50'),
(28, 'edric5611@gmail.com', 17, '355006', '2024-10-11 13:22:54', '2024-10-11 13:27:54'),
(29, 'edric5611@gmail.com', 17, '273705', '2024-10-11 13:24:43', '2024-10-11 13:29:43'),
(30, 'edric5611@gmail.com', 17, '303653', '2024-10-11 13:28:35', '2024-10-11 13:33:35'),
(31, 'edric5611@gmail.com', 17, '349783', '2024-10-11 13:47:30', '2024-10-11 13:52:30'),
(32, 'edric5611@gmail.com', 17, '915073', '2024-10-11 13:51:20', '2024-10-11 13:56:20'),
(33, '', 17, '295289', '2024-10-11 13:52:13', '2024-10-11 13:57:13'),
(34, '', 17, '520695', '2024-10-11 13:52:44', '2024-10-11 13:57:44'),
(35, 'edric5611@gmail.com', 17, '196771', '2024-10-11 13:54:01', '2024-10-11 13:59:01'),
(36, 'edric5611@gmail.com', 17, '933034', '2024-10-11 13:57:11', '2024-10-11 14:02:11'),
(37, 'edric5611@gmail.com', 17, '655148', '2024-10-11 13:59:54', '2024-10-11 14:04:54'),
(38, 'edric5611@gmail.com', 17, '829590', '2024-10-11 14:00:22', '2024-10-11 14:05:22'),
(39, 'edric5611@gmail.com', 17, '793038', '2024-10-11 14:01:10', '2024-10-11 14:06:10'),
(40, 'edric5611@gmail.com', 17, '854915', '2024-10-11 14:03:27', '2024-10-11 14:08:27'),
(41, 'edric5611@gmail.com', 17, '368893', '2024-10-11 14:05:56', '2024-10-11 14:10:56'),
(42, 'edric5611@gmail.com', 17, '841804', '2024-10-11 14:07:33', '2024-10-11 14:12:33'),
(47, 'johncutie16@gmail.com', 28, '810872', '2024-10-18 15:14:59', '2024-10-18 15:19:59');

-- --------------------------------------------------------

--
-- Table structure for table `players`
--

CREATE TABLE `players` (
  `player_id` int(11) NOT NULL,
  `user_id` varchar(100) NOT NULL,
  `tournament_id` int(11) DEFAULT NULL,
  `username` varchar(255) DEFAULT NULL,
  `proof_of_payment` varchar(255) DEFAULT NULL,
  `status` varchar(255) NOT NULL,
  `payment_status` enum('pending','paid') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `players`
--

INSERT INTO `players` (`player_id`, `user_id`, `tournament_id`, `username`, `proof_of_payment`, `status`, `payment_status`) VALUES
(55, '17', 30, 'jake', 'payments/adobe.png', 'confirmed', 'pending'),
(56, '25', 30, 'sam', 'payments/Awrd.jpg', 'confirmed', 'pending'),
(57, '45', 29, 'tamba', 'payments/images (1).png', 'confirmed', 'pending'),
(58, '22', 29, 'ator', 'payments/images (1).png', 'confirmed', 'pending'),
(59, '33', 29, 'philip', 'payments/images (1).png', 'confirmed', 'pending'),
(60, '25', 29, 'sam', 'payments/images (1).png', 'confirmed', 'pending'),
(61, '32', 29, 'kyle', 'payments/images (1).png', 'confirmed', 'pending'),
(62, '24', 32, 'jp', 'payments/images (1).png', 'confirmed', 'pending'),
(63, '22', 32, 'ator', 'payments/images (1).png', 'confirmed', 'pending'),
(64, '25', 32, 'sam', 'payments/images (1).png', 'confirmed', 'pending'),
(65, '32', 32, 'kyle', 'payments/images (1).png', 'confirmed', 'pending'),
(66, '33', 32, 'philip', 'payments/images (1).png', 'confirmed', 'pending'),
(67, '45', 32, 'tamba', 'payments/images (1).png', 'confirmed', 'pending'),
(73, '17', 29, 'jake', 'uploads/proof_17_1730440156.jpg', 'confirmed', 'pending'),
(74, '25', 33, 'sam', '6H435316CD913124B', 'confirmed', 'pending'),
(75, '17', 36, 'jake', 'payments/Untitled.jpg', 'confirmed', 'pending'),
(76, '17', 36, 'jake', 'payments/Untitled.jpg', 'confirmed', 'pending'),
(77, '17', 37, 'jake', 'payments/1201_Overview_of_Nervous_System.jpg', 'confirmed', 'pending'),
(78, '25', 37, 'sam', 'payments/0b4c1a931d128c5a8a80794e833a956b2ea3ecac.png', 'confirmed', 'pending'),
(79, '17', 38, 'jake', 'uploads/proof_17_1730809474.jpg', 'pending', 'pending'),
(80, '17', 39, 'jake', 'uploads/proof_17_1730861528.jpg', 'confirmed', 'pending'),
(81, '24', 39, 'jp', 'uploads/proof_24_1730861538.jpg', 'confirmed', 'pending'),
(82, '16', 39, 'sulek', 'uploads/proof_16_1730861556.jpg', 'confirmed', 'pending'),
(83, '32', 39, 'kyle', 'uploads/proof_32_1730861568.jpg', 'confirmed', 'pending'),
(84, '23', 39, 'jayson', 'uploads/proof_23_1730861602.jpg', 'confirmed', 'pending'),
(85, '33', 39, 'philip', 'uploads/proof_33_1730861622.jpg', 'confirmed', 'pending'),
(86, '25', 39, 'sam', 'uploads/proof_25_1730861761.jpg', 'confirmed', 'pending'),
(87, '30', 39, 'wanwan', 'uploads/proof_30_1730861778.jpg', 'confirmed', 'pending'),
(88, '32', 40, 'kyle', 'payments/gcash receipt.png', 'confirmed', 'pending'),
(89, '25', 41, 'sam', 'payments/456334632_1690820518344077_7209698902401365916_n.jpg', 'confirmed', 'pending'),
(90, '17', 41, 'jake', 'uploads/proof_17_1731028674.jpg', 'confirmed', 'pending'),
(91, '22', 41, 'ator', 'uploads/proof_22_1731028692.jpg', 'confirmed', 'pending'),
(92, '32', 41, 'kyle', 'uploads/proof_32_1731028726.jpg', 'confirmed', 'pending'),
(93, '33', 41, 'philip', 'uploads/proof_33_1731028739.jpg', 'confirmed', 'pending'),
(94, '31', 41, 'tanggol', 'uploads/proof_31_1731028760.jpg', 'confirmed', 'pending'),
(95, '45', 41, 'tamba', 'uploads/proof_45_1731028778.jpg', 'confirmed', 'pending'),
(96, '30', 41, 'wanwan', 'uploads/proof_30_1731028806.jpg', 'confirmed', 'pending'),
(139, '17', 44, 'jake', 'uploads/receipt_jake_44_1731342424.png', 'confirmed', 'pending');

-- --------------------------------------------------------

--
-- Table structure for table `reports`
--

CREATE TABLE `reports` (
  `id` int(11) NOT NULL,
  `type` varchar(50) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `datetime` datetime DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `charges` decimal(10,2) DEFAULT NULL,
  `caused_by` varchar(255) NOT NULL,
  `contact_number` varchar(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `reports`
--

INSERT INTO `reports` (`id`, `type`, `description`, `datetime`, `photo`, `name`, `charges`, `caused_by`, `contact_number`) VALUES
(29, 'item_damage', 'ator broke the cue stick', '2024-11-08 13:00:00', 'img/broken cue stick 1.jpg', 'cashier', 500.00, '', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `rating_service` int(11) NOT NULL CHECK (`rating_service` between 1 and 5),
  `rating_facilities` int(11) NOT NULL CHECK (`rating_facilities` between 1 and 5),
  `rating_tournaments` int(11) NOT NULL CHECK (`rating_tournaments` between 1 and 5),
  `comments` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`id`, `user_id`, `rating_service`, `rating_facilities`, `rating_tournaments`, `comments`, `created_at`) VALUES
(1, 17, 1, 1, 2, 'asdad', '2024-10-31 16:17:24'),
(2, 17, 2, 2, 4, 'ada', '2024-10-31 16:47:57'),
(3, 17, 4, 2, 3, 'adad', '2024-10-31 16:49:25');

-- --------------------------------------------------------

--
-- Table structure for table `tables`
--

CREATE TABLE `tables` (
  `table_id` int(11) NOT NULL,
  `table_number` varchar(100) NOT NULL,
  `status` enum('Available','Occupied','Under Maintenance') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tables`
--

INSERT INTO `tables` (`table_id`, `table_number`, `status`) VALUES
(1, 'Table 1', 'Available'),
(2, 'Table 2', 'Available'),
(3, 'Table 3', 'Available'),
(4, 'Table 4', 'Available');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_user`
--

CREATE TABLE `tbl_user` (
  `tbl_user_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_user`
--

INSERT INTO `tbl_user` (`tbl_user_id`, `name`, `username`, `password`, `role`) VALUES
(1, 'Lorem Ipsum', 'admin', 'admin', 'admin'),
(3, 'John Doe', 'user', 'user', 'user'),
(4, 'jake', 'jake', 'jake', 'user'),
(5, 'ada', 'asda', 'asd', 'cashier');

-- --------------------------------------------------------

--
-- Table structure for table `tournaments`
--

CREATE TABLE `tournaments` (
  `tournament_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `max_player` int(11) NOT NULL,
  `start_date` datetime NOT NULL,
  `end_date` datetime DEFAULT NULL,
  `status` varchar(100) NOT NULL,
  `created_at` varchar(100) NOT NULL,
  `prize` text NOT NULL,
  `fee` decimal(10,2) NOT NULL DEFAULT 0.00,
  `qualification` varchar(255) NOT NULL,
  `venue` varchar(255) NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `winner_id` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tournaments`
--

INSERT INTO `tournaments` (`tournament_id`, `name`, `max_player`, `start_date`, `end_date`, `status`, `created_at`, `prize`, `fee`, `qualification`, `venue`, `start_time`, `end_time`, `winner_id`) VALUES
(41, 'pakusganay tournament', 8, '2024-11-11 00:00:00', '2024-11-12 00:00:00', 'completed', '2024-11-08 01:14:43', '5000', 625.00, 'A', 'blabagno compound', '13:00:00', '17:00:00', NULL),
(44, 'asdad', 2, '2024-11-12 00:00:00', '2024-11-12 00:00:00', 'upcoming', '2024-11-11 16:21:38', '123', 123.00, 'A', 'adad', '05:23:00', '10:23:00', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tournament_schedule`
--

CREATE TABLE `tournament_schedule` (
  `schedule_id` int(11) NOT NULL,
  `tournament_id` int(11) NOT NULL,
  `match_number` int(11) NOT NULL,
  `player1_id` int(11) DEFAULT NULL,
  `player2_id` int(11) DEFAULT NULL,
  `scheduled_time` datetime DEFAULT NULL,
  `status` varchar(50) DEFAULT 'pending',
  `round` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tournament_scores`
--

CREATE TABLE `tournament_scores` (
  `id` int(11) NOT NULL,
  `tournament_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `scores` int(11) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tournament_scores`
--

INSERT INTO `tournament_scores` (`id`, `tournament_id`, `user_id`, `scores`, `created_at`, `updated_at`) VALUES
(87, 41, 32, 2, '2024-11-10 16:14:06', '2024-11-10 16:14:06'),
(88, 41, 30, 1, '2024-11-10 16:14:06', '2024-11-10 16:14:06'),
(89, 41, 31, 2, '2024-11-10 16:14:41', '2024-11-10 16:14:41'),
(90, 41, 25, 1, '2024-11-10 16:14:41', '2024-11-10 16:14:41'),
(91, 41, 45, 3, '2024-11-10 16:22:30', '2024-11-10 16:22:30'),
(92, 41, 33, 5, '2024-11-10 16:22:30', '2024-11-10 16:22:30');

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `transaction_id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `status` varchar(50) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `proof_of_payment` varchar(255) DEFAULT NULL,
  `paymaya_payment_id` varchar(255) DEFAULT NULL,
  `paymaya_status` varchar(50) DEFAULT 'Pending',
  `paypal_payment_id` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`transaction_id`, `booking_id`, `amount`, `payment_method`, `status`, `timestamp`, `proof_of_payment`, `paymaya_payment_id`, `paymaya_status`, `paypal_payment_id`) VALUES
(120, 155, 100.00, 'gcash', 'Pending', '2024-11-06 02:19:58', NULL, NULL, 'Pending', NULL),
(121, 156, 100.00, 'cash', 'Confirmed', '2024-11-06 02:26:12', NULL, NULL, 'Pending', NULL),
(122, 157, 100.00, 'cash', 'Pending', '2024-11-07 01:42:32', NULL, NULL, 'Pending', NULL),
(123, 158, 100.00, 'paypal', 'Completed', '2024-11-07 17:39:08', NULL, NULL, 'Pending', 'PAYID-M4WPWPA1JA72809N0249183D'),
(124, 159, 100.00, 'gcash', 'Pending', '2024-11-07 17:44:55', NULL, NULL, 'Pending', NULL),
(125, 160, 200.00, 'gcash', 'Pending', '2024-11-08 01:05:59', NULL, NULL, 'Pending', NULL),
(126, 161, 100.00, 'cash', 'Confirmed', '2024-11-08 01:10:03', NULL, NULL, 'Pending', NULL),
(127, 162, 100.00, 'cash', 'Confirmed', '2024-11-10 13:56:05', NULL, NULL, 'Pending', NULL),
(128, 165, 100.00, 'cash', 'Pending', '2024-11-11 14:52:55', NULL, NULL, 'Pending', NULL),
(129, 166, 100.00, 'cash', 'Confirmed', '2024-11-11 16:29:38', NULL, NULL, 'Pending', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` text NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(100) NOT NULL,
  `activation_code` varchar(64) DEFAULT NULL,
  `role` varchar(100) NOT NULL,
  `archive` int(11) NOT NULL DEFAULT 0,
  `profile_pic` varchar(255) DEFAULT NULL,
  `is_activated` tinyint(1) DEFAULT 0,
  `contact_number` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `name`, `email`, `username`, `password`, `activation_code`, `role`, `archive`, `profile_pic`, `is_activated`, `contact_number`) VALUES
(3, 'admin', 'admin@admin.com', 'admin', 'admin', NULL, 'admin', 0, NULL, 1, ''),
(16, 'sulekz', 'sulek@gmail.com', 'sulek', 'sulek', NULL, 'user', 0, NULL, 1, ''),
(17, 'jake', 'edric5611@gmail.com', 'jake', '$2y$10$VRXnA1xNlsxUZtynkjvYPe5G6zJf6L0tI5APVYocwfAEJACSCdEB6', NULL, 'user', 0, 'Flag_of_Sudan.svg.png', 1, '9773433504'),
(18, 'her', 'her', 'her', '$2y$10$7VwUPypJCXjNoMUrimRtTOA5CcEvgcFWtFs18kWs.iUByVWii8zoi', NULL, 'user', 0, NULL, 1, ''),
(19, 'cashier', 'cashier', 'cashier', 'cashier', NULL, 'cashier', 0, NULL, 1, '09773433504'),
(22, 'ator', 'ATOR@gmail.com', 'ator', '$2y$10$GROUj7epBbzTDL40iA4CKORLu8AQKLdTuPVnRZHWDgvYCmE.eYp.q', NULL, 'user', 0, NULL, 1, ''),
(23, 'jayson', 'jayson@gmail.com', 'jayson', '$2y$10$9HJqUveLRqXnzgdVdUkOOenu9IwDOIT1hiuyuoOh00bDJJCjYCP3e', NULL, 'user', 0, NULL, 1, ''),
(24, 'jp', 'jp@gmail.com', 'jp', '$2y$10$KPMk561FYUGvkn31ZndINOV/XLNzANqVEUXooj70ifG1fqOs4tUpi', NULL, 'user', 0, 'wallpaperflare.com_wallpaper.jpg', 1, '9454055131'),
(25, 'sam', 'sam@gmail.com', 'sam', '$2y$10$/SNAXL5TdAhBDTYFWTMKQeEDh9u/3DD2UsXI83t5D84GGIykZkUhi', NULL, 'user', 0, NULL, 1, '09773433504'),
(28, 'Patrick John Orcia', 'johncutie16@gmail.com', 'Zedrickpatrick', '$2y$10$o/p04pxbRW2aO.ja24TcF.LyYvcidsZVRJSrzt7F8N4eUPOECKlNO', NULL, 'user', 0, NULL, 1, ''),
(29, 'jayson', 'jaysonescaro2@gmail.com', 'jaysonescaro2@gmail.com', '$2y$10$qBiybM3ZVh2yS2Ru9vrHeOX1nEV/qprQPwTWlPpznH8PlCZzF7t0i', NULL, 'user', 0, NULL, 1, ''),
(30, 'patotoya', 'victorwembanyama122@gmail.com', 'wanwan', '$2y$10$RHZ57ntlX/GVpHgyX6Ep/OxofT.Ticw4C7DjOj/iQsiClHk5D5FHK', NULL, 'user', 0, NULL, 1, ''),
(31, 'Tanggol', 'tanggol@gmail.com', 'tanggol', '$2y$10$2pJM2u85CEwn6kEHaIKhH..x4MYkjsnUl/KMRQCjHbQpMEhwc/Lo.', NULL, 'user', 0, NULL, 1, ''),
(32, 'kyle', 'kyle@gmail.com', 'kyle', '$2y$10$1fVsF1Z6ln0twJ1rbMKZG.Qht3zG2CZRt909zdPE6yNVcJ3HszPYe', NULL, 'user', 0, NULL, 1, ''),
(33, 'philip', 'philiproque303@gmail.com', 'philip', '$2y$10$5odD26wRmgvVLAbfX.ED4e8xsjIeAw/Ais2yxk77gCq3mmR4CefXe', NULL, 'user', 0, NULL, 1, ''),
(34, 'kenth ator', 'kent@gmail.com', 'kenot', '$2y$10$KWb.YpWxNtFxnmkTNUr2GefWzut7r19qMITxpqxF0iKVEYpwmSTB6', NULL, 'user', 0, NULL, 1, ''),
(35, 'rosie', 'rosalindarosalinda12341234@gmail.com', 'rosa', '$2y$10$1dkphMMHpo59CxMNZgIlA.NTjyf/.LSxKg4ID9imPGYzHi9Kj/fVS', NULL, 'user', 0, NULL, 1, ''),
(36, 'ator01', 'ator01@gmail.com', 'ator01', '$2y$10$13jQJDbNBMrYRjKPWSlbXOd.0OQ4DNhV5aed5.jEaX8AKf2jvjXO6', NULL, 'user', 0, NULL, 1, ''),
(41, 'France Limpiado', 'limpiado098@gmail.com', 'limpiado', '$2y$10$aDqxzeeGMGgpSm9WQACPqeY9ivz11iLxuFJpaOkmCmih45xrvMnIC', 'c106b8a6321b858c84c20e1fa2d3548504f966d23554aaf130358d432ff3a3a2', 'user', 0, NULL, 1, ''),
(42, 'jake jake', 'limpiado5611@gmail.com', 'jake', '$2y$10$COx4.L0pRJtUQ0OQqTwRie/57CkCQWXYKF.hk92hHd1L4n6hjvlX2', NULL, 'user', 0, NULL, 0, ''),
(43, 'boy boy', 'francelimpiado0@gmail.com', 'boy', '$2y$10$DcxeZapsD4qm5GlKmF8vbu.W/H6nuInFX.gAWGXKKID7r0xk/SmV6', NULL, 'user', 0, NULL, 0, ''),
(45, 'kyle tamba', 'ktamba034@gmail.com', 'tamba', '$2y$10$Yr4pCXP6YUr5FtTWVYsEE.6TBOczY7CFxywHfkbNVAs8skwt2wRIC', NULL, 'user', 0, NULL, 1, ''),
(46, 'Dame Kyle cute Baque', 'kyleabequibelbaquez@gmail.com', 'kyle', '$2y$10$hJenEt2lQBdo1DwGa86v0.IIldPcC3rtKANNKO7VomYxDyqSUrbK6', '9797b8fd3ce3f4795ffc0e3fdb4a0c0d5f610b97380a7fd6a0088a096b870b63', 'user', 0, NULL, 0, ''),
(47, 'Dame Kyle baque', 'kyleabequibelbaque@gmail.com', 'kyle', '$2y$10$ffViqMCMEo8C6G3lUECab.0A8T/YO/TlSVizBob75Q.8e6/vE.N4G', NULL, 'user', 0, NULL, 1, ''),
(48, 'kenth john ator', 'Kenthjohnl.ator17@gmail.com', 'djkenth', '$2y$10$4Gn/n3x2/ZEe5hEaZhMclub7oyXtU2wZOXxYLjIWkSTTqrQjykYpu', 'd0575aaf9ea3c4f8754884a52622a8374612b61459e19f98068ca74263d2d46d', 'user', 0, NULL, 0, '09773433504'),
(54, 'te te', 'francelimpiado@gmail.com', 'te', '$2y$10$z0mE6UqW6XTOl60q0v4kbOUCQHc0hy6/oXMEYeubZBUJMsHo.d5t6', NULL, 'user', 0, NULL, 1, '123');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_notifications`
--
ALTER TABLE `admin_notifications`
  ADD PRIMARY KEY (`notification_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `announcements`
--
ALTER TABLE `announcements`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`booking_id`);

--
-- Indexes for table `bracket`
--
ALTER TABLE `bracket`
  ADD PRIMARY KEY (`bracket_id`),
  ADD KEY `tournament_id` (`tournament_id`),
  ADD KEY `player1_id` (`player1_id`),
  ADD KEY `player2_id` (`player2_id`);

--
-- Indexes for table `chat_messages`
--
ALTER TABLE `chat_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sender_id` (`sender_id`),
  ADD KEY `receiver_id` (`receiver_id`);

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `inventory`
--
ALTER TABLE `inventory`
  ADD PRIMARY KEY (`item_id`);

--
-- Indexes for table `inventory_transactions`
--
ALTER TABLE `inventory_transactions`
  ADD PRIMARY KEY (`transaction_id`),
  ADD KEY `item_id` (`item_id`);

--
-- Indexes for table `matches`
--
ALTER TABLE `matches`
  ADD PRIMARY KEY (`match_id`),
  ADD KEY `tournament_id` (`tournament_id`),
  ADD KEY `table_id` (`table_id`),
  ADD KEY `player1_id` (`player1_id`),
  ADD KEY `player2_id` (`player2_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`notification_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `otp_codes`
--
ALTER TABLE `otp_codes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `players`
--
ALTER TABLE `players`
  ADD PRIMARY KEY (`player_id`),
  ADD KEY `tournament_id` (`tournament_id`);

--
-- Indexes for table `reports`
--
ALTER TABLE `reports`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `tables`
--
ALTER TABLE `tables`
  ADD PRIMARY KEY (`table_id`);

--
-- Indexes for table `tbl_user`
--
ALTER TABLE `tbl_user`
  ADD PRIMARY KEY (`tbl_user_id`);

--
-- Indexes for table `tournaments`
--
ALTER TABLE `tournaments`
  ADD PRIMARY KEY (`tournament_id`);

--
-- Indexes for table `tournament_schedule`
--
ALTER TABLE `tournament_schedule`
  ADD PRIMARY KEY (`schedule_id`),
  ADD KEY `tournament_id` (`tournament_id`),
  ADD KEY `player1_id` (`player1_id`),
  ADD KEY `player2_id` (`player2_id`);

--
-- Indexes for table `tournament_scores`
--
ALTER TABLE `tournament_scores`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_tournament_user` (`tournament_id`,`user_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`transaction_id`),
  ADD KEY `booking_id` (`booking_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_notifications`
--
ALTER TABLE `admin_notifications`
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `announcements`
--
ALTER TABLE `announcements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=198;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `booking_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=167;

--
-- AUTO_INCREMENT for table `bracket`
--
ALTER TABLE `bracket`
  MODIFY `bracket_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=217;

--
-- AUTO_INCREMENT for table `chat_messages`
--
ALTER TABLE `chat_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `inventory`
--
ALTER TABLE `inventory`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `inventory_transactions`
--
ALTER TABLE `inventory_transactions`
  MODIFY `transaction_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `matches`
--
ALTER TABLE `matches`
  MODIFY `match_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=68;

--
-- AUTO_INCREMENT for table `otp_codes`
--
ALTER TABLE `otp_codes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT for table `players`
--
ALTER TABLE `players`
  MODIFY `player_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=140;

--
-- AUTO_INCREMENT for table `reports`
--
ALTER TABLE `reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tables`
--
ALTER TABLE `tables`
  MODIFY `table_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tbl_user`
--
ALTER TABLE `tbl_user`
  MODIFY `tbl_user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tournaments`
--
ALTER TABLE `tournaments`
  MODIFY `tournament_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT for table `tournament_schedule`
--
ALTER TABLE `tournament_schedule`
  MODIFY `schedule_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `tournament_scores`
--
ALTER TABLE `tournament_scores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=93;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `transaction_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=130;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admin_notifications`
--
ALTER TABLE `admin_notifications`
  ADD CONSTRAINT `admin_notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `chat_messages`
--
ALTER TABLE `chat_messages`
  ADD CONSTRAINT `chat_messages_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `chat_messages_ibfk_2` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `inventory_transactions`
--
ALTER TABLE `inventory_transactions`
  ADD CONSTRAINT `inventory_transactions_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `inventory` (`item_id`) ON DELETE CASCADE;

--
-- Constraints for table `matches`
--
ALTER TABLE `matches`
  ADD CONSTRAINT `matches_ibfk_1` FOREIGN KEY (`tournament_id`) REFERENCES `tournaments` (`tournament_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `matches_ibfk_2` FOREIGN KEY (`table_id`) REFERENCES `tables` (`table_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `matches_ibfk_3` FOREIGN KEY (`player1_id`) REFERENCES `players` (`player_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `matches_ibfk_4` FOREIGN KEY (`player2_id`) REFERENCES `players` (`player_id`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `otp_codes`
--
ALTER TABLE `otp_codes`
  ADD CONSTRAINT `otp_codes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `tournament_schedule`
--
ALTER TABLE `tournament_schedule`
  ADD CONSTRAINT `tournament_schedule_ibfk_1` FOREIGN KEY (`tournament_id`) REFERENCES `tournaments` (`tournament_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tournament_schedule_ibfk_2` FOREIGN KEY (`player1_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `tournament_schedule_ibfk_3` FOREIGN KEY (`player2_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `tournament_scores`
--
ALTER TABLE `tournament_scores`
  ADD CONSTRAINT `tournament_scores_ibfk_1` FOREIGN KEY (`tournament_id`) REFERENCES `tournaments` (`tournament_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tournament_scores_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
