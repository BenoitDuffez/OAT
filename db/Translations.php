<?php

require_once "db/DbAdapter.php";

class Translations extends DbAdapter {
	const DB_VERSION = 1;

	public function __construct() {
		parent::__construct(DbAdapter::TABLE_TRANSLATIONS, Translations::DB_VERSION);
	}

	protected function onUpgrade($oldVersion, $newVersion) {
		try {
			if ($oldVersion < 1) {
				$statement = <<<SQL
CREATE TABLE IF NOT EXISTS table (
  id int(11) NOT NULL AUTO_INCREMENT,
  name varchar(100) NOT NULL,
  lang varchar(25) NOT NULL,
  text text NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY lang (lang, name)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
SQL;
				$this->createTable($statement);
				return true;
			}
		} catch (PDOException $e) {
			L("Unable to upgrade strings database from $oldVersion to $newVersion", $e);
		}
		return false;
	}

	public function saveAll($lang, $strings) {
		$statement = "INSERT INTO " . DbAdapter::getTable(DbAdapter::TABLE_TRANSLATIONS);
		$statement .= " (name, lang, text) VALUES (?, ?, ?)";
		$statement .= " ON DUPLICATE KEY UPDATE name = ?, lang = ?, text = ?";

		try {
			$handle = $this->pdo->prepare($statement);
			foreach ($strings as $string) {
				$i = 1;
				$handle->bindValue($i++, $string['name']);
				$handle->bindValue($i++, $lang);
				$handle->bindValue($i++, $string['text']);

				$handle->bindValue($i++, $string['name']);
				$handle->bindValue($i++, $lang);
				$handle->bindValue($i++, $string['text']);

				$handle->execute();
			}
		} catch (PDOException $e) {
			L("Unable to batch save strings!", $e);
		}
	}

	public function getFirstLanguage() {
		try {
			$handle = $this->pdo->prepare("SELECT lang, COUNT(*) as n FROM " . DbAdapter::getTable(DbAdapter::TABLE_STRINGS) . " GROUP BY lang ORDER BY n DESC LIMIT 1");
			$handle->execute();
			$result = $handle->fetch();
			return $result['lang'];
		} catch (PDOException $e) {
			L("Unable to retrieve best lang", $e);
		}
		return null;
	}

	public function getString($lang, $name) {
		try {
			$sql = "SELECT * FROM " . DbAdapter::getTable(DbAdapter::TABLE_TRANSLATIONS) . " WHERE lang = ? AND name = ?";
			$handle = $this->pdo->prepare($sql);
			$handle->bindValue(1, $lang);
			$handle->bindValue(2, $name);
			$handle->execute();
			return $handle->fetch(PDO::FETCH_ASSOC);
		} catch (PDOException $e) {
			L("Unable to retrieve string $name for lang $lang", $e);
		}
		return null;
	}

	public function getStrings($lang, $filename) {
		try {
			$sql = "SELECT s.*, t.text FROM " . DbAdapter::getTable(DbAdapter::TABLE_STRINGS) . " s, ";
			$sql .= DbAdapter::getTable(DbAdapter::TABLE_TRANSLATIONS) . " t";
			$sql .= " WHERE t.lang = ? AND s.filename = ? AND t.name = s.name";
			$handle = $this->pdo->prepare($sql);
			$handle->bindValue(1, $lang);
			$handle->bindValue(2, $filename);
			$handle->execute();
			return $handle->fetchAll(PDO::FETCH_ASSOC);
		} catch (PDOException $e) {
			L("Unable to retrieve strings from $filename for lang $lang", $e);
		}
		return null;
	}


	public function getNbStrings($lang) {
		try {
			$handle = $this->pdo->prepare("SELECT COUNT(*) as nb FROM " . DbAdapter::getTable(DbAdapter::TABLE_TRANSLATIONS) . " WHERE lang = ?");
			$handle->bindValue(1, $lang);
			$handle->execute();
			$result = $handle->fetch();
			return $result['nb'];
		} catch (PDOException $e) {
			L("Unable to retrieve available langs", $e);
		}
		return 0;
	}

	public function getLangs() {
		try {
			$handle = $this->pdo->prepare("SELECT lang, COUNT(*) as nb FROM " . DbAdapter::getTable(DbAdapter::TABLE_TRANSLATIONS) . " GROUP BY lang");
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
}
