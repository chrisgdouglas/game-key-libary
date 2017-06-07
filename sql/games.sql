-- phpMyAdmin SQL Dump
-- version 4.6.4
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Nov 11, 2016 at 12:23 PM
-- Server version: 5.5.53-0+deb8u1
-- PHP Version: 5.6.27-0+deb8u1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `games`
--

-- --------------------------------------------------------

--
-- Table structure for table `distplatform_lkup`
--

CREATE TABLE `distplatform_lkup` (
  `platform` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `distplatform_lkup`
--

INSERT INTO `distplatform_lkup` (`platform`) VALUES
('Desura'),
('DRM Free'),
('Good Old Games'),
('Origin'),
('Steam'),
('UPlay');

-- --------------------------------------------------------

--
-- Table structure for table `exchange_rate`
--

CREATE TABLE `exchange_rate` (
  `currency` varchar(25) NOT NULL,
  `rate_percentage` float NOT NULL,
  `currency_symbol` varchar(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `exchange_rate`
--

INSERT INTO `exchange_rate` (`currency`, `rate_percentage`, `currency_symbol`) VALUES
('CAD', 1, '$'),
('Euro', 1.45, '€'),
('GBP', 1.63, '£'),
('USD', 1.25, '$');

-- --------------------------------------------------------

--
-- Table structure for table `external_urls`
--

CREATE TABLE `external_urls` (
  `external_site` varchar(50) NOT NULL,
  `external_url` varchar(1024) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `external_urls`
--

INSERT INTO `external_urls` (`external_site`, `external_url`) VALUES
('Metacritic', 'http://www.metacritic.com/search/game/?/results'),
('Steam', 'http://store.steampowered.com/app/'),
('SteamDB', 'https://steamdb.info/app/');

-- --------------------------------------------------------

--
-- Table structure for table `games`
--

CREATE TABLE `games` (
  `id` varchar(37) NOT NULL,
  `game_name` varchar(512) NOT NULL,
  `game_owner` varchar(37) NOT NULL,
  `purchase_date` date NOT NULL,
  `store` varchar(255) NOT NULL,
  `game_key` varchar(255) DEFAULT NULL,
  `redeemed` varchar(50) NOT NULL,
  `cost` float NOT NULL,
  `purchase_currency` varchar(25) DEFAULT NULL,
  `played` tinyint(1) NOT NULL,
  `distribution_platform` varchar(255) NOT NULL,
  `store_id` varchar(50) DEFAULT NULL,
  `notes` longtext,
  `image` varchar(255) DEFAULT NULL,
  `popular_tags` varchar(1024) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `images`
--

CREATE TABLE `images` (
  `description` varchar(255) NOT NULL,
  `file_path` varchar(728) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `images`
--

INSERT INTO `images` (`description`, `file_path`) VALUES
('No Image', ''),

--
-- Table structure for table `redemption_lkup`
--

CREATE TABLE `redemption_lkup` (
  `value` varchar(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `redemption_lkup`
--

INSERT INTO `redemption_lkup` (`value`) VALUES
('Gifted'),
('No'),
('Yes');

-- --------------------------------------------------------

--
-- Table structure for table `store_lkup`
--

CREATE TABLE `store_lkup` (
  `store_name` varchar(255) NOT NULL,
  `store_url` varchar(1024) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `store_lkup`
--

INSERT INTO `store_lkup` (`store_name`, `store_url`) VALUES
('Bundle Stars', 'https://www.bundlestars.com/'),
('Desura', 'N/A'),
('Gamer\'s Gate', 'http://www.gamersgate.com/'),
('Good Old Games', 'https://www.gog.com'),
('Green Man Gaming', 'https://www.greenmangaming.com/'),
('Groupees', 'https://groupees.com'),
('Humble Bundle Bundles', 'https://www.humblebundle.com'),
('Humble Bundle Store', 'https://www.humblebundle.com/store'),
('Indie Gala Bundles', 'https://www.indiegala.com'),
('Indie Gala Store', 'https://www.indiegala.com/store'),
('Indie Royale', 'N/A'),
('Kickstarter', 'https://www.kickstarter.com'),
('Origin', 'https://www.origin.com/can/en-us/store'),
('Steam', 'https://store.steampowered.com'),
('UPlay', 'https://store.ubi.com/');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` varchar(37) NOT NULL,
  `email` varchar(512) NOT NULL,
  `password` varchar(512) NOT NULL,
  `display_name` varchar(255) NOT NULL,
  `user_role` int(11) NOT NULL DEFAULT '0' COMMENT '0 - disabled, 1 - active, 2 - admin',
  `game_key_privacy` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `distplatform_lkup`
--
ALTER TABLE `distplatform_lkup`
  ADD PRIMARY KEY (`platform`);

--
-- Indexes for table `exchange_rate`
--
ALTER TABLE `exchange_rate`
  ADD PRIMARY KEY (`currency`),
  ADD KEY `currency` (`currency`),
  ADD KEY `currency_symbol` (`currency_symbol`);

--
-- Indexes for table `external_urls`
--
ALTER TABLE `external_urls`
  ADD PRIMARY KEY (`external_site`);

--
-- Indexes for table `games`
--
ALTER TABLE `games`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`),
  ADD KEY `store` (`store`),
  ADD KEY `redeemed` (`redeemed`),
  ADD KEY `distribution_platform` (`distribution_platform`),
  ADD KEY `image` (`image`),
  ADD KEY `game_owner` (`game_owner`),
  ADD KEY `currency` (`purchase_currency`),
  ADD KEY `popular_tags` (`popular_tags`(767));

--
-- Indexes for table `images`
--
ALTER TABLE `images`
  ADD PRIMARY KEY (`description`),
  ADD UNIQUE KEY `file_path` (`file_path`),
  ADD KEY `description` (`description`);

--
-- Indexes for table `redemption_lkup`
--
ALTER TABLE `redemption_lkup`
  ADD PRIMARY KEY (`value`);

--
-- Indexes for table `store_lkup`
--
ALTER TABLE `store_lkup`
  ADD PRIMARY KEY (`store_name`),
  ADD KEY `store_name` (`store_name`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`),
  ADD KEY `display_name` (`display_name`);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `games`
--
ALTER TABLE `games`
  ADD CONSTRAINT `games_ibfk_4` FOREIGN KEY (`image`) REFERENCES `images` (`description`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `games_ibfk_5` FOREIGN KEY (`distribution_platform`) REFERENCES `distplatform_lkup` (`platform`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `games_ibfk_6` FOREIGN KEY (`store`) REFERENCES `store_lkup` (`store_name`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `games_ibfk_7` FOREIGN KEY (`redeemed`) REFERENCES `redemption_lkup` (`value`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `games_ibfk_8` FOREIGN KEY (`game_owner`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `games_ibfk_9` FOREIGN KEY (`purchase_currency`) REFERENCES `exchange_rate` (`currency`) ON DELETE NO ACTION ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
