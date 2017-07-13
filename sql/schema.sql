--
-- MySQL 5.6.30
-- Sun, 15 Jan 2017 16:45:23 +0000
--

CREATE TABLE `lgks_cache_editor` (
   `id` int(10) unsigned not null auto_increment,
   `guid` varchar(150) not null,
   `site` varchar(150) not null,
   `client_ip` varchar(25) not null,
   `filepath` varchar(255),
   `content` longblob,
   `disksize` int(11) not null default '0',
   `src_hash` varchar(155) not null,
   `content_hash` varchar(155) not null,
   `created_by` varchar(155) not null,
   `created_on` timestamp not null default CURRENT_TIMESTAMP,
   `edited_by` varchar(155) not null,
   `edited_on` timestamp not null default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
   PRIMARY KEY (`id`),
   UNIQUE KEY (`content_hash`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;