<?php

require_once "db/DbAdapter.php";

class Translations extends DbAdapter {
	const DB_VERSION = 2;

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
  user_id int(11) NOT NULL,
  last_mod_date datetime NOT NULL,
  PRIMARY KEY (id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
SQL;
				$this->createTable($statement);
			}

			if ($oldVersion < 2) {
				$statement = "ALTER TABLE " . $this->getTable(DbAdapter::TABLE_TRANSLATIONS);
				$statement .= " ADD user_id int(11) NOT NULL, ";
				$statement .= " ADD last_mod_date datetime NOT NULL, ";
				$statement .= " DROP INDEX lang";
				$this->pdo->exec($statement);
			}
		} catch (PDOException $e) {
			L("Unable to upgrade strings database from $oldVersion to $newVersion", $e);
			return false;
		}
		return true;
	}

	public function saveAll($lang, $strings) {
		$statement = "INSERT INTO " . DbAdapter::getTable(DbAdapter::TABLE_TRANSLATIONS);
		$statement .= " (name, lang, text, user_id, last_mod_date) VALUES (?, ?, ?, ?, now())";

		try {
			$handle = $this->pdo->prepare($statement);
			foreach ($strings as $string) {
				$i = 1;
				$handle->bindValue($i++, $string['name']);
				$handle->bindValue($i++, $lang);
				$handle->bindValue($i++, $string['text']);
				$handle->bindValue($i++, intval($GLOBALS['user']->id));

				$handle->execute();
			}
		} catch (PDOException $e) {
			L("Unable to batch save strings!", $e);
		}
	}

	public function addTranslation($name, $lang, $text) {
		$statement = "INSERT INTO " . DbAdapter::getTable(DbAdapter::TABLE_TRANSLATIONS);
		$statement .= " (name, lang, text, user_id, last_mod_date) VALUES (?, ?, ?, ?, now())";

		try {
			$handle = $this->pdo->prepare($statement);
			$i = 1;
			$handle->bindValue($i++, $name);
			$handle->bindValue($i++, $lang);
			$handle->bindValue($i++, $text);
			$handle->bindValue($i++, intval($GLOBALS['user']->id));

			return $handle->execute();
		} catch (PDOException $e) {
			L("Unable to save string!", $e);
		}

		return $e;
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
			$sql = "SELECT * FROM " . DbAdapter::getTable(DbAdapter::TABLE_TRANSLATIONS);
			$sql .= " WHERE lang = ? AND name = ?";
			$sql .= " ORDER BY last_mod_date DESC";
			$sql .= " LIMIT 1";

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

	/**
	 * Retrieve all the best translations for a language.
	 * Used to generate the XML file for export
	 */
	public function getStrings($lang, $filename) {
		try {
			$sql = "SELECT s.*, t.name, t.lang, r.text, r.user_id, r.last_mod_date";
			$sql .= " FROM " . DbAdapter::getTable(DbAdapter::TABLE_STRINGS) . " s, ";
			$sql .= DbAdapter::getTable(DbAdapter::TABLE_TRANSLATIONS) . " t";
			$sql .= " LEFT JOIN (";
			$sql .= "   SELECT t1.name, t1.text, t1.user_id, t1.last_mod_date, t1.lang";
			$sql .= "   FROM " . DbAdapter::getTable(DbAdapter::TABLE_TRANSLATIONS) . " t1";
			$sql .= "   LEFT JOIN " . DbAdapter::getTable(DbAdapter::TABLE_TRANSLATIONS) . " t2";
			$sql .= "   ON t1.name = t2.name AND t1.lang = t2.lang AND t1.last_mod_date < t2.last_mod_date";
			$sql .= "   WHERE t2.name IS NULL AND t1.lang = ?";
			$sql .= " ) r";
			$sql .= " ON r.name = t.name";
			$sql .= " WHERE s.filename = ? AND s.name = t.name AND t.lang = r.lang";
			$sql .= " GROUP BY t.name";
			$sql .= " ORDER BY t.name ASC";

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
			$sql = "SELECT COUNT(*) as nb FROM " . DbAdapter::getTable(DbAdapter::TABLE_TRANSLATIONS);
			$sql .= " WHERE lang = ?";
			$sql .= " GROUP BY name";

			$handle = $this->pdo->prepare($sql);
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
