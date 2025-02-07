-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 17, 2025 at 08:26 AM
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
-- Database: `shop_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `items`
--

CREATE TABLE `items` (
  `id` int(11) NOT NULL,
  `item_name` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `quantity` int(11) NOT NULL,
  `date_modified` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `items`
--

INSERT INTO `items` (`id`, `item_name`, `price`, `quantity`, `date_modified`) VALUES
(5, 'Hotdog v2', 101.00, 2, '2025-01-09 14:23:00'),
(6, 'Hotdog', 100.00, 12, '2025-01-09 07:31:57'),
(7, 'Nice Dog', 300.00, 2, '2025-01-10 06:13:40'),
(8, 'Nice Dog v2', 301.00, 3, '2025-01-10 06:15:22'),
(9, 'Hotdog v3', 302.00, 2, '2025-01-10 06:16:59'),
(10, 'Nice Dog v3', 300.00, 2, '2025-01-10 06:18:17');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(20) DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `failed_attempts` int(11) DEFAULT 0,
  `last_failed_attempt` timestamp NULL DEFAULT NULL,
  `account_disabled` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `password`, `role`, `created_at`, `failed_attempts`, `last_failed_attempt`, `account_disabled`) VALUES
(2, 'admin', '$2y$10$RSjuo4SBY8muWUXfKFfuaufvVGYxFvDIPS29o/4uZnappmxLQb/RC', 'admin', '2025-01-09 05:31:21', 0, '2025-01-17 00:07:45', 0),
(3, 'user', '$2y$10$Ilmb8tyRlQSss.jIncav/O79ZTwXXNCgKhFVn1kk.n59CU2UmH98q', 'user', '2025-01-09 05:33:50', 1, '2025-01-17 00:15:44', 0),
(4, 'SuperAdmin', '$2y$10$lLkODn40mtNX5C47nvGQveTyTUxaqHrr/p7lnGOjXGkOvpw502Asy', 'SuperAdmin', '2025-01-09 07:25:32', 0, '2025-01-17 00:10:24', 0),
(5, 'jancarlofp@gmail.com', '$2y$10$.uR0.zCIYR5jtld18jfOOu3ZhlaI04BRQY44TLWW5Ge77Mv0ZfGbS', 'user', '2025-01-09 09:05:52', 0, NULL, 0),
(6, 'bagang', '$2y$10$7u5lQWCF9r88z0diY76LbOjT1BsrO5A6fE3y9s5NMNm7Qe4FuHB.K', 'user', '2025-01-17 06:50:59', 0, NULL, 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `items`
--
ALTER TABLE `items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `items`
--
ALTER TABLE `items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
