SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

CREATE TABLE IF NOT EXISTS `acos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) DEFAULT NULL,
  `model` varchar(255) NOT NULL DEFAULT '',
  `foreign_key` int(11) DEFAULT NULL,
  `alias` varchar(255) NOT NULL DEFAULT '',
  `lft` int(11) DEFAULT NULL,
  `rght` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `parent_id` (`parent_id`),
  KEY `foreign_key` (`foreign_key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `aros` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) DEFAULT NULL,
  `model` varchar(255) DEFAULT '',
  `foreign_key` int(11) DEFAULT NULL,
  `alias` varchar(255) DEFAULT '',
  `lft` int(11) DEFAULT NULL,
  `rght` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `parent_id` (`parent_id`),
  KEY `foreign_key` (`foreign_key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `aros_acos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `aro_id` int(11) DEFAULT NULL,
  `aco_id` int(11) DEFAULT NULL,
  `_create` char(2) NOT NULL DEFAULT '0',
  `_read` char(2) NOT NULL DEFAULT '0',
  `_update` char(2) NOT NULL DEFAULT '0',
  `_delete` char(2) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `companies` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email_1` varchar(255) DEFAULT NULL,
  `email_2` varchar(255) DEFAULT NULL,
  `email_3` int(11) DEFAULT NULL,
  `phone_1` varchar(255) DEFAULT NULL,
  `phone_2` varchar(255) DEFAULT NULL,
  `phone_3` varchar(255) DEFAULT NULL,
  `mobile_1` varchar(255) DEFAULT NULL,
  `mobile_2` varchar(255) DEFAULT NULL,
  `mobile_3` varchar(255) DEFAULT NULL,
  `fax_1` int(11) DEFAULT NULL,
  `fax_2` int(11) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `postcode` varchar(10) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `country` varchar(255) DEFAULT NULL,
  `logo_file_id` int(11) DEFAULT NULL,
  `about` text,
  `latitude` varchar(20) DEFAULT NULL,
  `lontitude` varchar(20) DEFAULT NULL,
  `icq` varchar(255) DEFAULT NULL,
  `skype` varchar(255) DEFAULT NULL,
  `msn` varchar(255) DEFAULT NULL,
  `jabber` varchar(150) DEFAULT NULL,
  `twitter` varchar(150) DEFAULT NULL,
  `fbid` varchar(150) DEFAULT NULL,
  `created_user_id` int(11) DEFAULT NULL,
  `created` int(11) NOT NULL,
  `modified_user__id` int(11) DEFAULT NULL,
  `modified` int(11) DEFAULT NULL,
  `facebook_link` varchar(255) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `vat_1` varchar(50) DEFAULT NULL,
  `vat_2` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `company_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `owner` int(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `files` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `path` varchar(255) DEFAULT NULL,
  `description` text,
  `type` varchar(255) DEFAULT NULL,
  `properties` text,
  `created_user_id` int(11) NOT NULL,
  `created_time` int(11) DEFAULT NULL,
  `modified_user_id` int(11) DEFAULT NULL,
  `modified_time` int(11) DEFAULT NULL,
  `file_name` varchar(255) DEFAULT NULL,
  `size` int(11) DEFAULT NULL,
  `extension` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `extension` (`extension`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `files_relations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `file_id` int(11) NOT NULL,
  `table` varchar(100) DEFAULT NULL,
  `table_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `file_id` (`file_id`),
  KEY `table` (`table`,`table_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `i18n` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `locale` varchar(6) NOT NULL,
  `model` varchar(50) NOT NULL,
  `foreign_key` int(11) NOT NULL,
  `field` varchar(50) NOT NULL,
  `content` text,
  PRIMARY KEY (`id`),
  KEY `locale` (`locale`,`model`,`foreign_key`,`field`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `medias` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `file_id` int(11) DEFAULT NULL,
  `play` smallint(6) DEFAULT NULL,
  `ready` smallint(6) DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL,
  `public` smallint(1) DEFAULT NULL,
  `created_user_id` int(11) DEFAULT NULL,
  `created_time` int(11) DEFAULT NULL,
  `modified_user_id` int(11) DEFAULT NULL,
  `modified_time` int(11) DEFAULT NULL,
  `public_from_time` int(11) DEFAULT NULL,
  `public_to_time` int(11) DEFAULT NULL,
  `priority` smallint(4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `file_id` (`file_id`),
  KEY `company_id` (`company_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `media_formats` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `media_id` int(11) DEFAULT NULL,
  `format` varchar(5) DEFAULT NULL,
  `width` int(11) DEFAULT NULL,
  `height` int(11) DEFAULT NULL,
  `screenbox_id` int(11) DEFAULT NULL,
  `file_name` varchar(200) DEFAULT NULL,
  `file_id` int(11) DEFAULT NULL,
  `ready` smallint(6) DEFAULT NULL,
  `downloaded` smallint(6) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `screenboxes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `key` varchar(20) DEFAULT NULL,
  `width` int(11) DEFAULT NULL,
  `height` int(11) DEFAULT NULL,
  `format` varchar(5) DEFAULT NULL,
  `type` varchar(30) DEFAULT NULL,
  `outputs` int(11) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `city` varchar(150) DEFAULT NULL,
  `street` varchar(150) DEFAULT NULL,
  `latitude` varchar(50) DEFAULT NULL,
  `lontitude` varchar(50) DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL,
  `shared` smallint(6) DEFAULT NULL,
  `public` smallint(6) DEFAULT NULL,
  `created_user_id` int(11) DEFAULT NULL,
  `created_time` int(11) DEFAULT NULL,
  `modified_user_id` int(11) DEFAULT NULL,
  `modified_time` int(11) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `config` text,
  PRIMARY KEY (`id`),
  KEY `company_id` (`company_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `screenbox_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `screenbox_id` int(11) NOT NULL,
  `apache` smallint(6) DEFAULT '0',
  `mem_free` int(11) DEFAULT '0',
  `mem_total` int(11) DEFAULT '0',
  `mem_cached` int(11) DEFAULT '0',
  `swap_total` int(11) DEFAULT '0',
  `wap_free` int(11) DEFAULT '0',
  `cpu` double DEFAULT '0',
  `disk_total` double DEFAULT '0',
  `disk_used` double DEFAULT '0',
  `disk_avail` double DEFAULT '0',
  `disk_percent` smallint(6) DEFAULT '0',
  `uptime` double DEFAULT '0',
  `log_time` int(11) DEFAULT NULL,
  `flashplayer` smallint(6) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `screenbox_id` (`screenbox_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `screenbox_playtimes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `media_id` int(11) DEFAULT NULL,
  `time_from` int(11) DEFAULT NULL,
  `time_to` int(11) DEFAULT NULL,
  `day` smallint(6) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `media_id` (`media_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `screenbox_playtime_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `media_id` int(11) DEFAULT NULL,
  `screenbox_id` int(11) DEFAULT NULL,
  `playtime` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `media_id` (`media_id`),
  KEY `screenbox_id` (`screenbox_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `password` varchar(50) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(150) DEFAULT NULL,
  `user_group_id` int(11) DEFAULT NULL,
  `active` int(11) DEFAULT NULL,
  `alias` varchar(100) DEFAULT NULL,
  `timezone` varchar(150) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `user_groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text,
  `active` tinyint(1) DEFAULT '1',
  `created_user_id` int(11) DEFAULT NULL,
  `created_time` int(11) DEFAULT NULL,
  `modefied_user_id` int(11) DEFAULT NULL,
  `modified_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `user_groups_relations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `created_user_id` int(11) DEFAULT NULL,
  `created_time` int(11) DEFAULT NULL,
  `modified_user_id` int(11) DEFAULT NULL,
  `modified_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
