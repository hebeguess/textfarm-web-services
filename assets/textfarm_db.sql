-- phpMyAdmin SQL Dump
-- version 3.5.2.2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Sep 27, 2012 at 09:16 PM
-- Server version: 5.5.27
-- PHP Version: 5.4.6

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `textfarm`
--

-- --------------------------------------------------------

--
-- Table structure for table `authkey`
--

CREATE TABLE IF NOT EXISTS `authkey` (
  `authID` int(11) NOT NULL AUTO_INCREMENT,
  `fileID` int(11) DEFAULT NULL,
  `authKeys` varchar(40) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `authState` tinyint(1) NOT NULL,
  PRIMARY KEY (`authID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=34 ;

-- --------------------------------------------------------

--
-- Table structure for table `file`
--

CREATE TABLE IF NOT EXISTS `file` (
  `fileID` int(11) NOT NULL AUTO_INCREMENT,
  `userID` varchar(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `fileName` varchar(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `filePath` mediumblob,
  `fileExt` varchar(12) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `filePermission` tinyint(1) NOT NULL,
  PRIMARY KEY (`fileID`),
  UNIQUE KEY `fileID` (`fileID`),
  KEY `userID` (`userID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=31 ;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `userID` varchar(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `userPass` varchar(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `regDate` date NOT NULL DEFAULT '1000-01-01',
  `isActive` tinyint(1) NOT NULL,
  `privatePassphrase` varchar(40) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `publicPassphrase` varchar(40) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`userID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `file`
--
ALTER TABLE `file`
  ADD CONSTRAINT `file_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `user` (`userID`) ON DELETE SET NULL ON UPDATE NO ACTION;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
