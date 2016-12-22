CREATE TABLE IF NOT EXISTS `$this->table_name_brewing_panel` (
				serial mediumint(5) NOT NULL AUTO_INCREMENT,
				bottle mediumint(3) unsigned zerofill NOT NULL DEFAULT '000',
				date datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
				modify_id mediumint(5) NOT NULL,
				type tinytext NOT NULL,
				name tinytext NOT NULL,
				stage tinytext NOT NULL,
				private BOOLEAN NOT NULL DEFAULT TRUE,
				ferment_date datetime DEFAULT '0000-00-00 00:00:00',
				distill_date datetime DEFAULT '0000-00-00 00:00:00',
				condition_date datetime DEFAULT '0000-00-00 00:00:00',
				aging_date datetime DEFAULT '0000-00-00 00:00:00',
				bottle_date datetime DEFAULT '0000-00-00 00:00:00',
				og decimal(4,3),
				yeast text,
				fermentables longblob,
				expect_fg decimal(4,3),
				actual_fg decimal(4,3),
				other longblob,
				UNIQUE KEY serial (serial)
		) $charset_collate;