-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 14, 2024 at 07:32 PM
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
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `booking_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `table_id` int(11) DEFAULT NULL,
  `table_name` varchar(100) NOT NULL,
  `start_time` datetime NOT NULL,
  `end_time` datetime NOT NULL,
  `status` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`booking_id`, `user_id`, `table_id`, `table_name`, `start_time`, `end_time`, `status`) VALUES
(1, 1, 3, '', '2024-07-13 23:42:00', '2024-07-13 23:42:00', 'Confirmed'),
(2, 1, 3, '', '2024-07-13 01:01:00', '2024-07-13 02:16:00', 'Confirmed'),
(3, 1, 1, '', '2024-07-14 22:57:00', '2024-07-14 23:57:00', 'Confirmed'),
(4, 2, 2, '', '2024-07-15 01:13:00', '2024-07-15 02:14:00', 'Confirmed'),
(5, 2, 1, '', '2024-07-15 01:18:00', '2024-07-15 02:19:00', 'Pending'),
(6, 2, 1, 'Table 4', '2024-07-15 01:25:00', '2024-07-15 02:25:00', 'Confirmed');

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
(44, 3, 9, 8, 1, 1, 8),
(45, 3, 2, 11, 1, 2, 7),
(46, 3, 10, 7, 1, 3, 12),
(47, 3, 1, 12, 1, 4, NULL);

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
  `username` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `players`
--

INSERT INTO `players` (`player_id`, `user_id`, `tournament_id`, `username`) VALUES
(1, '1', 3, 'jake'),
(2, '2', 3, 'sam'),
(3, '2', 2, 'sam'),
(4, '1', 2, 'jake'),
(5, '1', 4, 'jake'),
(6, '2', 4, 'sam'),
(7, '8', 5, 'qwe'),
(8, '8', 3, 'qwe'),
(9, '8', 6, 'qwe'),
(10, '7', 5, 'asd'),
(11, '1', 5, 'jake'),
(12, '2', 5, 'sam'),
(13, '9', 3, 'car'),
(14, '7', 3, 'asd'),
(15, '10', 3, 'pop'),
(16, '11', 3, 'son'),
(17, '12', 3, 'sad'),
(18, '1', 10, 'jake'),
(19, '1', 6, 'jake');

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
(1, 'Table 4', 'Occupied'),
(2, 'Table ni sam', 'Under Maintenance'),
(3, 'Table 2', 'Available'),
(4, 'Table 3', 'Available');

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
  `prize` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tournaments`
--

INSERT INTO `tournaments` (`tournament_id`, `name`, `max_player`, `start_date`, `end_date`, `status`, `created_at`, `prize`) VALUES
(2, 'Tournament 2', 5, '2024-07-04', '2024-07-05', 'upcoming', '2024-07-04 23:02:51', ''),
(3, 'tournament 3', 8, '2024-07-08', '2024-07-09', 'upcoming', '2024-07-08 18:44:03', ''),
(4, 'Tournament 4', 2, '2024-07-09', '2024-07-10', 'upcoming', '2024-07-09 23:53:18', ''),
(5, 'Tournament 5', 4, '2024-07-10', '2024-07-11', 'upcoming', '2024-07-10 00:12:23', ''),
(6, 'tournament 6', 4, '2024-07-10', '2024-07-11', 'upcoming', '2024-07-10 00:13:48', '123'),
(10, '123', 123, '2024-07-13', '2024-07-13', 'upcoming', '2024-07-13 22:33:39', '123');

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
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`transaction_id`, `booking_id`, `amount`, `payment_method`, `status`, `timestamp`) VALUES
(1, 1, 0.00, 'Pending', 'Pending', '2024-07-13 15:42:47'),
(2, 2, 37.50, 'cash', 'Pending', '2024-07-13 16:01:06'),
(3, 3, 30.00, 'cash', 'Pending', '2024-07-14 14:57:28'),
(4, 4, 30.50, 'cash', 'Pending', '2024-07-14 17:13:39'),
(5, 5, 30.50, 'cash', 'Pending', '2024-07-14 17:18:57'),
(6, 6, 30.00, 'cash', 'Pending', '2024-07-14 17:25:13');

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
(12, 'sad', 'sad', 'sad', 'sad', 'user');

--
-- Indexes for dumped tables
--

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
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `booking_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `bracket`
--
ALTER TABLE `bracket`
  MODIFY `bracket_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

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
  MODIFY `player_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

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
  MODIFY `tournament_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `transaction_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

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
