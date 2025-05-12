-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Anamakine: 127.0.0.1:3306
-- Üretim Zamanı: 10 May 2025, 19:45:00
-- Sunucu sürümü: 9.1.0
-- PHP Sürümü: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Veritabanı: `db`
--

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `productlist`
--

DROP TABLE IF EXISTS `productlist`;
CREATE TABLE `productlist` (
  `id` int NOT NULL AUTO_INCREMENT,
  `market_id` int NOT NULL,
  `title` varchar(255) NOT NULL,
  `price` float NOT NULL,
  `discount_price` float NOT NULL,
  `stock` int NOT NULL,
  `expiration_date` date NOT NULL,
  `image` varchar(255) NOT NULL,
  `city` varchar(100) NOT NULL,
  `district` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

INSERT INTO `productlist` (`market_id`, `title`, `price`, `discount_price`, `stock`, `expiration_date`, `image`, `city`, `district`) VALUES
(1, 'Magnum Classic', 25, 20, 10, '2025-07-01', 'magnum.jpg', 'ankara', 'Çankaya'),
(2, 'Magnolia Cake', 50, 45, 5, '2024-06-10', 'magnolia.jpg', 'Ankara', 'Çankaya'),
(1, 'Nutmag Spice', 30, 25, 8, '2025-08-01', 'nutmag.jpg', 'Ankara', 'Çankaya'),
(1, 'Magic Milk', 15, 12, 8, '2024-12-05', 'milk.png', 'Ankara', 'Çankaya'),
(1, 'Mango Juice', 18, 14, 20, '2025-09-01', 'mango.png', 'Ankara', 'Çankaya'),
(2, 'MagSafe Charger', 120, 99, 5, '2025-08-15', 'magsafe.png', 'Ankara', 'Yenimahalle'),
(2, 'Yogurt', 12, 10, 18, '2025-11-01', 'yogurt.png', 'Ankara', 'Çankaya'),
(1, 'Cheddar Cheese', 22, 19, 16, '2025-10-01', 'cheddar.png', 'Ankara', 'Keçiören'),
(2, 'Cucumber', 5, 4, 30, '2025-09-25', 'cucumber.png', 'Istanbul', 'Çankaya'),
(1, 'Magical Berries', 60, 45, 7, '2025-12-31', 'berries.png', 'Ankara', 'Çankaya'),
(1, 'UwU', 60, 45, 7, '2025-12-31', 'uwu.png', 'Istanbul', 'astana');

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
