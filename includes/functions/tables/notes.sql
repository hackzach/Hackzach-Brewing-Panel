		CREATE TABLE IF NOT EXISTS `$this->table_name_brewing_notes` (
			id mediumint(7) unsigned NOT NULL AUTO_INCREMENT,
			user_id mediumint(5) unsigned NOT NULL,
			modify_id mediumint(5) unsigned DEFAULT NULL,
			modify_date datetime DEFAULT '0000-00-00 00:00:00',
			serial mediumint(5) unsigned NOT NULL,
			stage tinytext NOT NULL,
			date datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
			notes text,
			UNIQUE KEY id (id)
		) $charset_collate;