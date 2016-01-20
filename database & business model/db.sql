-- Adminer 4.2.3 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `todo-list`;
CREATE TABLE `todo-list` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `task` text COLLATE utf8_bin NOT NULL,
  `point` int(11) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;


DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_name` varchar(50) COLLATE utf8_bin NOT NULL,
  `first_name` varchar(100) COLLATE utf8_bin NOT NULL,
  `last_name` varchar(100) COLLATE utf8_bin NOT NULL,
  `todo_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;


-- 2015-12-22 09:10:44