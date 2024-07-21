-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 21, 2024 at 04:22 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `billiard`
--

-- --------------------------------------------------------

--
-- Table structure for table `announcements`
--

CREATE TABLE `announcements` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `body` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `announcements`
--

INSERT INTO `announcements` (`id`, `title`, `body`, `created_at`) VALUES
(2, 'Tournament', 'Tournament 1 will start now', '2024-07-19 14:48:28'),
(3, 'Tournament', 'Tournament 1 will start now', '2024-07-19 14:48:45'),
(4, 'Tournament 2 ready', 'Will start in one hour\r\n', '2024-07-19 14:49:25');

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `booking_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `table_id` int(11) DEFAULT NULL,
  `table_name` varchar(100) NOT NULL,
  `start_time` datetime DEFAULT NULL,
  `end_time` datetime DEFAULT NULL,
  `status` varchar(100) NOT NULL,
  `num_matches` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`booking_id`, `user_id`, `table_id`, `table_name`, `start_time`, `end_time`, `status`, `num_matches`) VALUES
(1, 1, 3, 'Table 1', '2024-07-13 23:42:00', '2024-07-13 23:42:00', 'Confirmed', 0),
(2, 1, 3, 'Table 3', '2024-07-13 01:01:00', '2024-07-13 02:16:00', 'Confirmed', 0),
(3, 1, 1, 'Table 2', '2024-07-14 22:57:00', '2024-07-14 23:57:00', 'Confirmed', 0),
(4, 2, 2, 'Table 2', '2024-07-15 01:13:00', '2024-07-15 02:14:00', 'Confirmed', 0),
(5, 2, 1, 'Table 1', '2024-07-15 01:18:00', '2024-07-15 02:19:00', 'Pending', 0),
(6, 2, 1, 'Table 4', '2024-07-15 01:25:00', '2024-07-15 02:25:00', 'Confirmed', 0),
(7, 1, 2, 'Table 4', '2024-07-17 23:07:00', '2024-07-18 00:08:00', 'Pending', 0),
(8, 1, 1, 'Table 4', '2024-07-17 23:08:00', '2024-07-18 00:09:00', 'Confirmed', 0),
(9, 1, 2, 'Table 1', '2024-07-18 00:54:00', '2024-07-18 01:55:00', 'Confirmed', 0),
(10, 1, 2, 'Table 2', NULL, NULL, 'Pending', 1),
(11, 1, 2, 'Table 2', '2024-07-20 08:46:00', '2024-07-20 09:47:00', 'Confirmed', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `bracket`
--

CREATE TABLE `bracket` (
  `bracket_id` int(11) NOT NULL,
  `tournament_id` int(11) NOT NULL,
  `player1_id` int(11) NOT NULL,
  `player2_id` int(11) NOT NULL,
  `round` int(11) NOT NULL,
  `match_number` int(11) NOT NULL,
  `winner_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `bracket`
--

INSERT INTO `bracket` (`bracket_id`, `tournament_id`, `player1_id`, `player2_id`, `round`, `match_number`, `winner_id`) VALUES
(1, 4, 1, 2, 0, 0, NULL),
(2, 5, 1, 2, 0, 0, NULL),
(3, 5, 7, 8, 0, 0, NULL),
(44, 3, 9, 8, 1, 1, 9),
(45, 3, 2, 11, 1, 2, 7),
(46, 3, 10, 7, 1, 3, 11),
(47, 3, 1, 12, 1, 4, NULL);

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
(3, 'cris', 'cris@gmail.com', 'good', 'so goooddd adad adad adasd asdasasdasdasdas', '2024-07-17 14:07:14');

-- --------------------------------------------------------

--
-- Table structure for table `inventory`
--

CREATE TABLE `inventory` (
  `item_id` int(11) NOT NULL,
  `item_name` varchar(255) NOT NULL,
  `quantity` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `image` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `inventory`
--

INSERT INTO `inventory` (`item_id`, `item_name`, `quantity`, `description`, `image`) VALUES
(15, 'tako', '123', 'tako', 'tako.jpg'),
(16, 'tako', '4555', 'tako', 'tako.jpg'),
(18, 'tako', '123', 'tako', 'tako.jpg');

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
-- Table structure for table `players`
--

CREATE TABLE `players` (
  `player_id` int(11) NOT NULL,
  `user_id` varchar(100) NOT NULL,
  `tournament_id` int(11) DEFAULT NULL,
  `username` varchar(255) DEFAULT NULL,
  `proof_of_payment` varchar(255) DEFAULT NULL,
  `status` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `players`
--

INSERT INTO `players` (`player_id`, `user_id`, `tournament_id`, `username`, `proof_of_payment`, `status`) VALUES
(1, '1', 3, 'jake', NULL, ''),
(2, '2', 3, 'sam', NULL, ''),
(3, '2', 2, 'sam', NULL, ''),
(4, '1', 2, 'jake', NULL, ''),
(5, '1', 4, 'jake', NULL, ''),
(6, '2', 4, 'sam', NULL, ''),
(7, '8', 5, 'qwe', NULL, ''),
(8, '8', 3, 'qwe', NULL, ''),
(9, '8', 6, 'qwe', NULL, ''),
(10, '7', 5, 'asd', NULL, ''),
(11, '1', 5, 'jake', NULL, ''),
(12, '2', 5, 'sam', NULL, ''),
(13, '9', 3, 'car', NULL, 'confirmed'),
(14, '7', 3, 'asd', NULL, ''),
(15, '10', 3, 'pop', NULL, ''),
(16, '11', 3, 'son', NULL, ''),
(17, '12', 3, 'sad', NULL, ''),
(18, '1', 10, 'jake', NULL, ''),
(19, '1', 6, 'jake', NULL, ''),
(20, '1', 11, 'jake', NULL, ''),
(22, '1', 12, 'jake', NULL, ''),
(23, '13', 2, 'lol', NULL, 'cancelled'),
(24, '13', 6, 'lol', NULL, 'cancelled'),
(25, '13', 12, 'lol', NULL, 'cancelled'),
(26, '13', 13, 'lol', 'payments/receipt_3 (4).png', 'confirmed'),
(27, '1', 14, 'jake', 'payments/receipt_3 (4).png', 'pending');

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
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `status` varchar(100) NOT NULL,
  `created_at` varchar(100) NOT NULL,
  `prize` text NOT NULL,
  `fee` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tournaments`
--

INSERT INTO `tournaments` (`tournament_id`, `name`, `max_player`, `start_date`, `end_date`, `status`, `created_at`, `prize`, `fee`) VALUES
(2, 'Tournament 2', 6, '2024-07-04', '2024-07-05', 'upcoming', '2024-07-04 23:02:51', '10000', 100.00),
(3, 'Tournament 3', 8, '2024-07-08', '2024-07-09', 'upcoming', '2024-07-08 18:44:03', '10000', 100.00),
(4, 'Tournament 4', 2, '2024-07-09', '2024-07-10', 'upcoming', '2024-07-09 23:53:18', '10000', 100.00),
(5, 'Tournament 5', 4, '2024-07-10', '2024-07-11', 'upcoming', '2024-07-10 00:12:23', '10000', 100.00),
(6, 'tournament 6', 4, '2024-07-10', '2024-07-11', 'upcoming', '2024-07-10 00:13:48', '10000', 100.00),
(10, 'Tournament 7', 6, '2024-07-13', '2024-07-13', 'upcoming', '2024-07-13 22:33:39', '10000', 100.00),
(11, 'Tournament 8', 8, '2024-07-17', '2024-07-17', 'upcoming', '2024-07-17 23:57:18', '100', 100.00),
(12, 'Tournament 9', 8, '2024-07-18', '2024-07-18', 'upcoming', '2024-07-18 00:51:52', '10000', 100.00),
(13, 'Tournament 10', 4, '2024-07-18', '2024-07-18', 'upcoming', '2024-07-18 00:53:40', '1000', 100.00),
(14, 'Tournament 11', 4, '2024-07-31', '2024-08-01', 'upcoming', '2024-07-20 09:03:12', '10000', 100.00);

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
  `proof_of_payment` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`transaction_id`, `booking_id`, `amount`, `payment_method`, `status`, `timestamp`, `proof_of_payment`) VALUES
(1, 1, 0.00, 'Pending', 'Pending', '2024-07-13 15:42:47', NULL),
(2, 2, 37.50, 'cash', 'Pending', '2024-07-13 16:01:06', NULL),
(3, 3, 30.00, 'cash', 'Pending', '2024-07-14 14:57:28', NULL),
(4, 4, 30.50, 'cash', 'Pending', '2024-07-14 17:13:39', NULL),
(5, 5, 30.50, 'cash', 'Pending', '2024-07-14 17:18:57', NULL),
(6, 6, 30.00, 'cash', 'Pending', '2024-07-14 17:25:13', NULL),
(7, 8, 30.50, 'gcash', 'Pending', '2024-07-17 15:09:08', 'payments/proof.jpg'),
(8, 9, 30.50, 'gcash', 'Pending', '2024-07-17 16:54:58', 'payments/receipt_3 (4).png'),
(9, 10, 50.00, 'cash', 'Pending', '2024-07-19 13:44:10', ''),
(10, 11, 81.33, 'gcash', 'Pending', '2024-07-20 00:46:31', 'payments/receipt_9 (1).png');

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
  `role` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `name`, `email`, `username`, `password`, `role`) VALUES
(1, 'jakezyrus', 'jakezyrus@gmail.com', 'jake', 'jake', 'user'),
(2, 'sam', 'sam', 'sam', 'sam', 'user'),
(3, 'admin', 'admin@admin.com', 'admin', 'admin', 'admin'),
(4, 'cashier', 'cashier@cashier.com', 'cashier', 'cashier', 'cashier'),
(5, 'was', 'was', 'was', 'was', 'cashier'),
(6, 'jake zyrus', 'jake@gmail.com', 'zyrus', 'zyrus', 'user'),
(7, 'asd', 'asd', 'asd', 'asd', 'user'),
(8, 'qwe', 'qwe', 'qwe', 'qwe', 'user'),
(9, 'car', 'car', 'car', 'car', 'user'),
(10, 'pop', 'pop', 'pop', 'pop', 'user'),
(11, 'son', 'son', 'son', 'son', 'user'),
(12, 'sad', 'sad', 'sad', 'sad', 'user'),
(13, 'lol', 'lol', 'lol', 'lol', 'user');

--
-- Indexes for dumped tables
--

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
-- Indexes for table `matches`
--
ALTER TABLE `matches`
  ADD PRIMARY KEY (`match_id`),
  ADD KEY `tournament_id` (`tournament_id`),
  ADD KEY `table_id` (`table_id`),
  ADD KEY `player1_id` (`player1_id`),
  ADD KEY `player2_id` (`player2_id`);

--
-- Indexes for table `players`
--
ALTER TABLE `players`
  ADD PRIMARY KEY (`player_id`),
  ADD KEY `tournament_id` (`tournament_id`);

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
-- AUTO_INCREMENT for table `announcements`
--
ALTER TABLE `announcements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `booking_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `bracket`
--
ALTER TABLE `bracket`
  MODIFY `bracket_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `inventory`
--
ALTER TABLE `inventory`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `matches`
--
ALTER TABLE `matches`
  MODIFY `match_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `players`
--
ALTER TABLE `players`
  MODIFY `player_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

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
  MODIFY `tournament_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `transaction_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bracket`
--
ALTER TABLE `bracket`
  ADD CONSTRAINT `bracket_ibfk_1` FOREIGN KEY (`tournament_id`) REFERENCES `tournaments` (`tournament_id`),
  ADD CONSTRAINT `bracket_ibfk_2` FOREIGN KEY (`player1_id`) REFERENCES `players` (`player_id`),
  ADD CONSTRAINT `bracket_ibfk_3` FOREIGN KEY (`player2_id`) REFERENCES `players` (`player_id`);

--
-- Constraints for table `matches`
--
ALTER TABLE `matches`
  ADD CONSTRAINT `matches_ibfk_1` FOREIGN KEY (`tournament_id`) REFERENCES `tournaments` (`tournament_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `matches_ibfk_2` FOREIGN KEY (`table_id`) REFERENCES `tables` (`table_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `matches_ibfk_3` FOREIGN KEY (`player1_id`) REFERENCES `players` (`player_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `matches_ibfk_4` FOREIGN KEY (`player2_id`) REFERENCES `players` (`player_id`) ON DELETE CASCADE;

--
-- Constraints for table `players`
--
ALTER TABLE `players`
  ADD CONSTRAINT `players_ibfk_1` FOREIGN KEY (`tournament_id`) REFERENCES `tournaments` (`tournament_id`);

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`booking_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
