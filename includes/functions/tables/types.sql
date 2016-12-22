		CREATE TABLE IF NOT EXISTS `$this->table_name_brewing_types` (
			id mediumint(3) unsigned NOT NULL AUTO_INCREMENT,
			type tinytext,
			UNIQUE KEY id (id)
		) $charset_collate;

		INSERT INTO `$this->table_name_brewing_types` (`type`) VALUES 
			('Beer'),
			('Kombucha'),
			('Rum'),
			('Vodka'),
			('Whiskey'),
			('Schnapps');