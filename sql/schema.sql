--
-- MySQL 5.6.28
-- Thu, 03 Mar 2016 19:32:33 +0000
--
CREATE TABLE `lgks_links` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `guid` varchar(75) DEFAULT 'globals',
  `menuid` varchar(25) DEFAULT NULL,
  `title` varchar(150) DEFAULT NULL,
  `mode` varchar(150) DEFAULT '*',
  `category` varchar(255) DEFAULT NULL,
  `menugroup` varchar(150) DEFAULT NULL,
  `class` varchar(150) DEFAULT NULL,
  `target` varchar(55) DEFAULT NULL,
  `link` varchar(255) DEFAULT '#',
  `iconpath` varchar(255) DEFAULT NULL,
  `tips` varchar(255) DEFAULT NULL,
  `site` varchar(150) DEFAULT '*',
  `device` varchar(20) DEFAULT '*',
  `privilege` varchar(1000) DEFAULT '*',
  `weight` int(11) DEFAULT '10',
  `onmenu` enum('true','false') DEFAULT 'true',
  `blocked` enum('true','false') DEFAULT 'false',
  `rules` text,
  `creator` varchar(155) NOT NULL,
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `edited_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
