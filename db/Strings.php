<?php

class StringsDbAdapter extends DbAdapter {
	const DB_VERSION = 1;

	public function __construct() {
		parent::__construct(DbAdapter::TABLE_STRINGS, StringsDbAdapter::DB_VERSION);
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

			$this->createTable($statement);
		}
	}

	public function getLangs() {
		try {
			$handle = $this->pdo->prepare("SELECT lang, COUNT(*) as nb FROM " . DbAdapter::getTable(DbAdapter::TABLE_STRINGS) . " GROUP BY lang");
			$handle->execute();
			$langs = array();
			foreach ($handle->fetchAll() as $lang) {
				$langs[$lang['lang']] = $lang;
			}
			return $langs;
		} catch (PDOException $e) {
			L("Unable to retrieve available langs", $e);
		}
		return null;
	}

	public function getFirstLanguage() {
		try {
			$handle = $this->pdo->prepare("SELECT lang, COUNT(*) as n FROM " . DbAdapter::getTable(DbAdapter::TABLE_STRINGS) . " GROUP BY lang ORDER BY n DESC LIMIT 1");
			$handle->execute();
			return $handle->fetch()['lang'];
		} catch (PDOException $e) {
			L("Unable to retrieve best lang", $e);
		}
		return null;
	}

	public function getAll($lang) {
		try {
			$handle = $this->pdo->prepare("SELECT * FROM " . DbAdapter::getTable(DbAdapter::TABLE_STRINGS) . " WHERE lang = ?");
			$handle->bindValue(1, $lang);
			$handle->execute();
			$strings = array();
			foreach ($handle->fetchAll() as $item) {
				$strings[$item['name']] = $item;
			}
			return $strings;
		} catch (PDOException $e) {
			L("Unable to retrieve strings for lang $lang", $e);
		}
		return null;
	}

	public function saveAll($strings) {
		global $_POST;

		//insert into $table (field, value) values (:name, :value) on duplicate key update value=:value2
		$statement = "INSERT INTO " . DbAdapter::getTable(DbAdapter::TABLE_STRINGS) . " (lang, name, text, formatted, date_created, date_updated) VALUES (?, ?, ?, ?, ?, ?)";
		$statement .= " ON DUPLICATE KEY UPDATE name = ?, text = ?, formatted = ?, date_created = ?, date_updated = ?";

		try {
			$handle = $this->pdo->prepare($statement);
			foreach ($strings as $string) {
				$i = 1;
				$handle->bindValue($i++, $_POST['language']);
				$handle->bindValue($i++, $string['name']);
				$handle->bindValue($i++, $string['text']);
				$handle->bindValue($i++, $string['formatted']);
				$handle->bindValue($i++, "now()"); // TODO: doesn't work
				$handle->bindValue($i++, "now()");
				$handle->bindValue($i++, $string['name']);
				$handle->bindValue($i++, $string['text']);
				$handle->bindValue($i++, $string['formatted']);
				$handle->bindValue($i++, "now()");
				$handle->bindValue($i++, "now()");

				$handle->execute();
			}
		} catch (PDOException $e) {
			L("Unable to batch save strings!", $e);
		}
	}
}

