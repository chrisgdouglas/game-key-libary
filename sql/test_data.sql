-- phpMyAdmin SQL Dump
-- version 4.6.4
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Dec 22, 2016 at 04:54 PM
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

--
-- Dumping data for table `games`
--

INSERT INTO `games` (`id`, `game_name`, `game_owner`, `purchase_date`, `store`, `game_key`, `redeemed`, `cost`, `purchase_currency`, `played`, `distribution_platform`, `store_id`, `notes`, `image`, `popular_tags`) VALUES
('10c13225-6ee1-4677-a101-09c4d9ac9365', 'The Darkness II', '27fe67b6-48b6-4365-bad0-ebce67ecc7d7', '2016-07-19', 'Humble Bundle Bundles', '5Y72S-YPYMU-GSSSY', 'Yes', 0.33, 'USD', 0, 'Steam', '67370', 'Humble 2K Bundle 2', 'The Darkness II Header', 'FPS, Action, Gore, Shooter'),
('137a0d4f-2c0f-4a8a-8067-62a6dcff8c37', 'Red Goblin: Cursed Forest', '27fe67b6-48b6-4365-bad0-ebce67ecc7d7', '2016-12-09', 'Green Man Gaming', '7RC7W-3NQUS-RSL76', 'No', 0.29, 'USD', 0, 'Steam', '364480', 'Mystery Bundle - 10 Pack', 'Red Goblin: Cursed Forest Header', 'Indie, Casual, Retro, Platformer'),
('19803ce2-94f8-4ebb-945b-ace5815f606f', 'Sonic CD', '27fe67b6-48b6-4365-bad0-ebce67ecc7d7', '2016-06-21', 'Humble Bundle Bundles', '0E20V-INCC6-93VPL', 'Yes', 0.56, 'USD', 0, 'Steam', '200940', 'Humble Sonic 25th Anniversary Bundle', 'Sonic CD Header', 'Platformer, Classic, Great Soundtrack, Retro'),
('65671a38-2e39-4c7a-bf9e-e346f1e7c30a', 'Divinity: Original Sin 2', '27fe67b6-48b6-4365-bad0-ebce67ecc7d7', '2015-08-27', 'Kickstarter', '3VCH8524OR303NS150', 'Yes', 25, 'USD', 0, 'Steam', '435150', 'Kickstarter Reward: Original Sin Pack\r\nGet a copy of Divinity: Original Sin 2 and a copy of Divinity: Original Sin - Enhanced Edition as digital downloads for PC, available from Steam with Alpha and Beta access included, or DRM-free from GOG.com. Also comes with a Digital Game Manual, a \'Backer\' Forum Badge and behind-the-scenes footage.', 'Divinity: Original Sin 2 Header', 'Early Access, RPG, Strategy, Adventure'),
('7cba9231-91d1-4092-b95b-31345480c445', 'CMYW', '27fe67b6-48b6-4365-bad0-ebce67ecc7d7', '2016-12-09', 'Green Man Gaming', 'OR4K7-R3AL2-UN4R6', 'No', 0.29, 'USD', 0, 'Steam', '396590', 'Mystery Bundle - 10 Pack', 'CMYW Header', 'Action, Indie, Space, Arcade');


