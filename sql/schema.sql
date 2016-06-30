--
-- MySQL 5.6.28
-- Thu, 03 Mar 2016 19:32:33 +0000
--
CREATE TABLE `lgks_links` (
   `id` int(10) unsigned not null auto_increment,
   `menuid` varchar(25),
   `title` varchar(150),
   `mode` varchar(150) default '*',
   `category` varchar(255),
   `menugroup` varchar(150),
   `class` varchar(150),
   `target` varchar(55),
   `link` varchar(255) default '#',
   `iconpath` varchar(255),
   `tips` varchar(255),
   `site` varchar(150) default '*',
   `device` varchar(20) default '*',
   `privilege` varchar(1000) default '*',
   `weight` int(11) default '10',
   `onmenu` enum('true','false') default 'true',
   `blocked` enum('true','false') default 'false',
   `rules` text,
   `creator` varchar(155) not null,
   `dtoc` timestamp not null default CURRENT_TIMESTAMP,
   `dtoe` timestamp not null default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
   PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
