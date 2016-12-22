		CREATE TABLE IF NOT EXISTS `$this->table_name_brew_lock` (
			serial mediumint(5) unsigned NOT NULL,
			brew_locked boolean NOT NULL DEFAULT false,
			user_id mediumint(5),
			date datetime DEFAULT '0000-00-00 00:00:00',
			UNIQUE KEY serial (serial)
		) $charset_collate;