		CREATE TABLE IF NOT EXISTS `$this->table_name_brew_owners` (
			serial mediumint(5) unsigned NOT NULL,
			owner int NOT NULL,
			UNIQUE KEY serial (serial)
		) $charset_collate;