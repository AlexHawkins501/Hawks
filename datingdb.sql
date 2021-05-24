-- phpMyAdmin SQL Dump
-- version 4.9.5
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3307
-- Generation Time: May 24, 2021 at 12:04 AM
-- Server version: 5.7.24
-- PHP Version: 7.4.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `datingdb`
--

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(50) NOT NULL,
  `msg_from_user_id` int(50) NOT NULL,
  `msg` varchar(250) COLLATE utf8mb4_bin NOT NULL,
  `msg_to_user_id` int(50) NOT NULL,
  `msg_date` datetime NOT NULL,
  `is_msg_read` tinyint(1) NOT NULL DEFAULT '0',
  `msg_read_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- --------------------------------------------------------

--
-- Table structure for table `profile`
--

CREATE TABLE `profile` (
  `id` int(10) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(250) NOT NULL,
  `firstName` varchar(50) NOT NULL,
  `lastName` varchar(50) NOT NULL,
  `city` varchar(50) NOT NULL,
  `bio` varchar(250) DEFAULT NULL,
  `birthDate` date NOT NULL,
  `gender` varchar(50) NOT NULL,
  `imgUrl` text NOT NULL,
  `receive_notification` tinyint(1) NOT NULL DEFAULT '0',
  `user_role` text NOT NULL,
  `created_date` datetime DEFAULT NULL,
  `modified_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `profile`
--

INSERT INTO `profile` (`id`, `email`, `password`, `firstName`, `lastName`, `city`, `bio`, `birthDate`, `gender`, `imgUrl`, `receive_notification`, `user_role`, `created_date`, `modified_date`) VALUES
(7, 'admin@gmail.com', 'test', 'Admin', 'Admin', 'Admin', '', '1997-05-10', 'male', './images/user_images/admin@gmail.com_XxiOYFnagE4.jpg', 0, 'admin', '2020-10-04 23:16:35', NULL),
(10, 'aka@gmail.com', '123', 'Александр', 'Пушкин', 'Санкт-Петербург', '', '2000-12-10', 'male', './images/user_images/aka@gmail.com_Foto-Pushkina-16.jpg', 0, 'regular', NULL, NULL),
(11, 'nadya@gmail.com', '123', 'Анна', 'Каренина', 'Москва', NULL, '1999-11-10', 'female', './images/user_images/nadya@gmail.com_big_96209_00002362.jpg', 0, 'regular', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_favourite_list`
--

CREATE TABLE `user_favourite_list` (
  `id` int(50) NOT NULL,
  `user_id` int(50) NOT NULL,
  `user_id_favourited` int(50) NOT NULL,
  `dateCreated` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `MSG_FROM_USER` (`msg_from_user_id`),
  ADD KEY `MSG_TO_USER` (`msg_to_user_id`);

--
-- Indexes for table `profile`
--
ALTER TABLE `profile`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `user_favourite_list`
--
ALTER TABLE `user_favourite_list`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id_favourite` (`user_id`),
  ADD KEY `user_id_to_favourite` (`user_id_favourited`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `profile`
--
ALTER TABLE `profile`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `user_favourite_list`
--
ALTER TABLE `user_favourite_list`
  MODIFY `id` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `MSG_FROM_USER` FOREIGN KEY (`msg_from_user_id`) REFERENCES `profile` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `MSG_TO_USER` FOREIGN KEY (`msg_to_user_id`) REFERENCES `profile` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `user_favourite_list`
--
ALTER TABLE `user_favourite_list`
  ADD CONSTRAINT `user_id_favourite` FOREIGN KEY (`user_id`) REFERENCES `profile` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `user_id_to_favourite` FOREIGN KEY (`user_id_favourited`) REFERENCES `profile` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
