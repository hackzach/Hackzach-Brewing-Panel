		CREATE TABLE IF NOT EXISTS `$this->table_name_brew_collaborators` (
			id int NOT NULL AUTO_INCREMENT,
			serial mediumint(5) unsigned NOT NULL,
			collaborator mediumint(5) NOT NULL,
			UNIQUE KEY id(id)
		) $charset_collate;