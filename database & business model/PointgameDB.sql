-- Adminer 4.2.3 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `listitem`;
CREATE TABLE `listitem` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `task` text COLLATE utf8_bin NOT NULL,
  `score` int(11) NOT NULL,
  `todo_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `listitem_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
  CONSTRAINT `listitem_ibfk_1` FOREIGN KEY (`id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;


DROP TABLE IF EXISTS `todolist`;
CREATE TABLE `todolist` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `day` timestamp NULL DEFAULT NULL,
  `week` timestamp NULL DEFAULT NULL,
  `month` timestamp NULL DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

INSERT INTO `todolist` (`id`, `day`, `week`, `month`, `user_id`, `item_id`) VALUES
(6,	'2016-01-07 10:20:27',	NULL,	NULL,	1,	1);

DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) COLLATE utf8_bin NOT NULL,
  `password` varchar(50) COLLATE utf8_bin NOT NULL,
  `firstname` varchar(100) COLLATE utf8_bin NOT NULL,
  `lastname` varchar(100) COLLATE utf8_bin NOT NULL,
  `todo_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

INSERT INTO `user` (`id`, `username`, `password`, `firstname`, `lastname`, `todo_id`) VALUES
(1,	'Anders',	'test',	'Anders',	'Söderström',	1),
(3,	'Kalle',	'test',	'Karl',	'Karlsson',	2),
(4,	'Micke',	'test',	'Mikael',	'Bjerkerot',	3),
(5,	'Claudia',	'test',	'Claudia',	'Oljemark',	4),
(6,	'Patrik',	'test',	'Patrik',	'Nordahl',	5);

-- 2016-01-07 10:28:30