-- phpMyAdmin SQL Dump
-- version 3.1.3.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: May 06, 2009 at 05:52 PM
-- Server version: 5.1.33
-- PHP Version: 5.2.9

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `bomba_bill`
--

-- --------------------------------------------------------

--
-- Table structure for table `assembly_head`
--

CREATE TABLE IF NOT EXISTS `assembly_head` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_list` varchar(512) NOT NULL,
  `item_id` varchar(128) NOT NULL,
  `Quantity` varchar(128) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Table structure for table `bom_head`
--

CREATE TABLE IF NOT EXISTS `bom_head` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Main BOM_ID',
  `item_id` varchar(512) NOT NULL COMMENT 'Stores References to bom_item.id',
  `quantity` varchar(512) NOT NULL COMMENT 'Stores Quantity per bom_item.id',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COMMENT='Only exists to keep track of what parts are in a given BOM' AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Table structure for table `bom_item`
--

CREATE TABLE IF NOT EXISTS `bom_item` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Unique ID',
  `item_name` varchar(64) NOT NULL COMMENT 'Product Human Name',
  `parent_id` int(11) NOT NULL COMMENT 'For assigning part to assemblies',
  `type` varchar(12) NOT NULL COMMENT 'Purchased, Manufactured or Assembly',
  `service` varchar(3) NOT NULL COMMENT 'Is it a service and not a physical part? Yes or No',
  `description` varchar(128) NOT NULL COMMENT 'What is it?',
  `costing` decimal(10,3) NOT NULL COMMENT 'How much did it cost?',
  `manufacturer` varchar(64) NOT NULL COMMENT 'Who made it?',
  `vendor` varchar(64) NOT NULL COMMENT 'Where do you buy it from?',
  `weight` decimal(10,3) NOT NULL COMMENT 'How much does it weigh?',
  `notes` varchar(256) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `item_name` (`item_name`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COMMENT='Stores all Items, including assemblies' AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Table structure for table `projects`
--

CREATE TABLE IF NOT EXISTS `projects` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Unique ID for Projects',
  `Codename` varchar(128) NOT NULL COMMENT 'Unique Codename For Humans',
  `Description` varchar(256) NOT NULL COMMENT 'Description for Humans',
  `bom_head` int(11) NOT NULL COMMENT 'Reference to project BOM ID',
  `entry_date` date NOT NULL COMMENT 'Date of Entry',
  PRIMARY KEY (`id`),
  UNIQUE KEY `codename` (`Codename`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COMMENT='Exists to give BOM''s codenames that are easily identifiable' AUTO_INCREMENT=3 ;