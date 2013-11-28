<?php

class StringsDbAdapter extends DbAdapter {
	const DB_VERSION = 1;

	public function __construct(PDO $pdo) {
		parent::__construct($pdo, DbAdapter::TABLE_STRINGS, StringsDbAdapter::DB_VERSION);
	}

	protected function onUpgrade($oldVersion, $newVersion) {
		if ($oldVersion < 1) {
			$statement = <<<SQL
CREATE TABLE IF NOT EXISTS `oat_strings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lang` varchar(8) NOT NULL,
  `name` varchar(255) NOT NULL,
  `text` text NOT NULL,
  `formatted` tinyint(1) NOT NULL,
  `date_created` date NOT NULL,
  `date_updated` date NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `lang` (`lang`,`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
SQL;

			try {
				$handle = $this->pdo->exec($statement);
			} catch (PDOException $e) {
				L("Unable to create strings table", $e);
			}
		}
	}
}

