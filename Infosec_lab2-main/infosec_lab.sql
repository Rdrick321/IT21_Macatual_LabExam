-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 08, 2026 at 06:07 PM
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
-- Database: `infosec_lab`
--

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` int(11) NOT NULL,
  `student_id` varchar(50) DEFAULT NULL,
  `fullname` varchar(50) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `course` varchar(50) DEFAULT NULL,
  `course_description` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `student_id`, `fullname`, `email`, `course`, `course_description`) VALUES
(5, '6767', 'Xymon Milla', 'x.milla.6767@umindanao.edu.ph', 'BSIT', 'IT'),
(11, '5656', 'Roderick Macatual', 'r.macatual.5656@umindanao.edu.ph', 'BSIT', 'IT'),
(12, '1717', 'Kayla Macatual', 'k.macatual.1717@umindanao.edu.ph', 'BSIT', 'IT');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `role` varchar(20) NOT NULL DEFAULT 'admin'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `created_at`, `role`) VALUES
(1, 'admin', '$2y$10$.SCUBXfDKQd1wog7DYMD9ewfr6KXosmZPl69YFJd3BWEKBhEn3/za', '2026-05-07 10:33:50', 'admin'),
(2, 'superadmin', '$2y$10$WvQ9JQ.CUNMCdc755SJLaOsXtMTHn8q65zhMN1RmDm6PuG4cbGE/q', '2026-05-07 10:43:42', 'superadmin'),
(3, 'Kei', '$2y$10$oB4ISfi3NuiXEI4h0mq.V.HTQQyCOBGqUS9O1upk4.zKi5vJ4nkDO', '2026-05-07 10:49:35', 'admin'),
(4, 'rods', '$2y$10$JVgEE.GJ9Vfkbh.HQru7je46BHQl9KZovCTIs.HTZ8SZjTUE4be1C', '2026-05-08 14:04:33', 'admin'),
(5, 'mike', '$2y$10$kyRmWjV/G6arfV4AJLjyoe6QxKZc.5OHcXlGDUNmVJV8UTmWa9pM2', '2026-05-08 14:08:41', 'admin');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`);

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
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
