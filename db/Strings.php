<?php

require_once "db/DbAdapter.php";

class StringsDbAdapter extends DbAdapter {
	const DB_VERSION = 2;

	public function __construct() {
		parent::__construct(DbAdapter::TABLE_STRINGS, StringsDbAdapter::DB_VERSION);
	}

	protected function onUpgrade($oldVersion, $newVersion) {
		try {
			if ($oldVersion < 1) {
				$statement = <<<SQL
CREATE TABLE IF NOT EXISTS table (
  id int(11) NOT NULL AUTO_INCREMENT,
  lang varchar(8) NOT NULL,
  name varchar(255) NOT NULL,
  text text NOT NULL,
  formatted tinyint(1) NOT NULL,
  date_created date NOT NULL,
  date_updated date NOT NULL,
  filename varchar(255) NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY lang (lang,name)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
SQL;

				$this->createTable($statement);
			}

			if ($oldVersion < 2) {
				$statement = "ALTER TABLE " . DbAdapter::getTable(DbAdapter::TABLE_STRINGS) . " ADD stringtype varchar(50)";
				$this->pdo->exec($statement);
			}
		} catch (PDOException $e) {
			L("Unable to upgrade strings database from $oldVersion to $newVersion", $e);
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

	public function getNbStrings($lang) {
		try {
			$handle = $this->pdo->prepare("SELECT COUNT(*) as nb FROM " . DbAdapter::getTable(DbAdapter::TABLE_STRINGS) . " WHERE lang = ?");
			$handle->bindValue(1, $lang);
			$handle->execute();
			$result = $handle->fetch();
			return $result['nb'];
		} catch (PDOException $e) {
			L("Unable to retrieve available langs", $e);
		}
		return 0;
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

	public function getAll($lang) {
		try {
			$handle = $this->pdo->prepare("SELECT * FROM " . DbAdapter::getTable(DbAdapter::TABLE_STRINGS) . " WHERE lang = ? ORDER BY id ASC");
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

	public function getString($lang, $name) {
		try {
			$sql = "SELECT * FROM " . DbAdapter::getTable(DbAdapter::TABLE_STRINGS) . " WHERE lang = ? AND name = ?";
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

	public function saveAll($strings, $filename) {
		global $_POST;

		$statement = "INSERT INTO " . DbAdapter::getTable(DbAdapter::TABLE_STRINGS);
		$statement .= " (lang, name, text, formatted, date_created, date_updated, filename, stringtype) VALUES (?, ?, ?, ?, NOW(), NOW(), ?, ?)";
		$statement .= " ON DUPLICATE KEY UPDATE name = ?, text = ?, formatted = ?, date_updated = NOW(), filename = ?, stringtype = ?";

		try {
			$handle = $this->pdo->prepare($statement);
			foreach ($strings as $string) {
				$i = 1;
				$handle->bindValue($i++, $_POST['language']);
				$handle->bindValue($i++, $string['name']);
				$handle->bindValue($i++, $string['text']);
				$handle->bindValue($i++, $string['formatted']);
				$handle->bindValue($i++, $filename);
				$handle->bindValue($i++, $string['type']);

				$handle->bindValue($i++, $string['name']);
				$handle->bindValue($i++, $string['text']);
				$handle->bindValue($i++, $string['formatted']);
				$handle->bindValue($i++, $filename);
				$handle->bindValue($i++, $string['type']);

				$handle->execute();
			}
		} catch (PDOException $e) {
			L("Unable to batch save strings!", $e);
		}
	}

	public function getFileNames($defaultLanguage) {
		try {
			$sql = "SELECT filename FROM " . DbAdapter::getTable(DbAdapter::TABLE_STRINGS) . " WHERE lang = ? GROUP BY filename ORDER BY filename ASC";
			$handle = $this->pdo->prepare($sql);
			$handle->bindValue(1, $defaultLanguage);
			$handle->execute();
			return $handle->fetchAll(PDO::FETCH_ASSOC);
		} catch (PDOException $e) {
			L("Unable to get file names!", $e);
		}
		return null;
	}

	/**
	 * Retrieve the list of string names contained in a specific file
	 * @param $lang string Default language
	 * @param $filename string Desired file name
	 * @return array The list of string names that should be contained in that file
	 */
	public function getStringNames($lang, $filename) {
		try {
			$sql = "SELECT name FROM " . DbAdapter::getTable(DbAdapter::TABLE_STRINGS) . " WHERE lang = ? AND filename = ? ORDER BY id ASC";
			$handle = $this->pdo->prepare($sql);
			$handle->bindValue(1, $lang);
			$handle->bindValue(2, $filename);
			$handle->execute();
			return $handle->fetchAll(PDO::FETCH_ASSOC);
		} catch (PDOException $e) {
			L("Unable to get string names for $filename (in $lang)!", $e);
		}
		return null;
	}
}

