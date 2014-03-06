<?php

require_once "db/DbAdapter.php";

class StringsDbAdapter extends DbAdapter {
	const DB_VERSION = 1;

	public function __construct() {
		parent::__construct(DbAdapter::TABLE_STRINGS, StringsDbAdapter::DB_VERSION);
	}

	protected function onUpgrade($oldVersion, $newVersion) {
		try {
			if ($oldVersion < 1) {
				$statement = <<<SQL
CREATE TABLE IF NOT EXISTS table (
  id int(11) NOT NULL AUTO_INCREMENT,
  name varchar(255) NOT NULL,
  formatted tinyint(1) NOT NULL,
  filename varchar(255) NOT NULL,
  stringtype varchar(50) NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY name (name)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
SQL;
				$this->createTable($statement);
			}
		} catch (PDOException $e) {
			L("Unable to upgrade strings database from $oldVersion to $newVersion", $e);
		}
	}

	public function getAll($trLang, $countUses = false) {
		try {
			$sql = "SELECT s.*, t.id is not null as is_translated";
			if ($countUses) {
				$sql .= ", COUNT(l.id) as nb_contexts";
			}
			$sql .= " FROM " . DbAdapter::getTable(DbAdapter::TABLE_STRINGS) . " s";
			$sql .= " LEFT JOIN " . DbAdapter::getTable(DbAdapter::TABLE_TRANSLATIONS) . " t";
			$sql .= " ON t.lang = ? AND t.name = s.name ";
			if ($countUses) {
				$sql .= " LEFT JOIN " . DbAdapter::getTable(DbAdapter::TABLE_LINKS) . " l";
				$sql .= " ON l.tbl1 = ? AND l.tbl2 = ? AND l.id2 = s.name";
			}
			if ($countUses) {
				$sql .= " GROUP BY s.id";
			}
			$sql .= " ORDER BY is_translated ASC, s.id ASC";

			$handle = $this->pdo->prepare($sql);
			$handle->bindValue(1, $trLang);
			if ($countUses) {
				$handle->bindValue(2, DbAdapter::TABLE_CONTEXTS);
				$handle->bindValue(3, DbAdapter::TABLE_STRINGS);
			}

			$handle->execute();
			$strings = array();
			return $handle->fetchAll(PDO::FETCH_ASSOC);
		} catch (PDOException $e) {
			L("Unable to retrieve strings ($sql - $trLang)", $e);
		}
		return null;
	}

	public function saveAll($strings, $filename) {
		global $_POST;

		$statement = "INSERT INTO " . DbAdapter::getTable(DbAdapter::TABLE_STRINGS);
		$statement .= " (name, formatted, filename, stringtype) VALUES (?, ?, ?, ?)";
		$statement .= " ON DUPLICATE KEY UPDATE name = ?, formatted = ?, filename = ?, stringtype = ?";

		try {
			$handle = $this->pdo->prepare($statement);
			foreach ($strings as $string) {
				$i = 1;
				$handle->bindValue($i++, $string['name']);
				$handle->bindValue($i++, $string['formatted']);
				$handle->bindValue($i++, $filename);
				$handle->bindValue($i++, $string['type']);

				$handle->bindValue($i++, $string['name']);
				$handle->bindValue($i++, $string['formatted']);
				$handle->bindValue($i++, $filename);
				$handle->bindValue($i++, $string['type']);

				$handle->execute();
			}
		} catch (PDOException $e) {
			L("Unable to batch save strings!", $e);
		}
	}

	public function getFileNames() {
		try {
			$sql = "SELECT filename FROM " . DbAdapter::getTable(DbAdapter::TABLE_STRINGS) . " GROUP BY filename ORDER BY filename ASC";
			$handle = $this->pdo->prepare($sql);
			$handle->execute();
			return $handle->fetchAll(PDO::FETCH_ASSOC);
		} catch (PDOException $e) {
			L("Unable to get file names!", $e);
		}
		return null;
	}

	/**
	 * Retrieve the list of string names, from a specific filename
	 * @param $filename string Desired file name
	 * @return array The list of string names that should be contained in that file
	 */
	public function getStringNames($filename) {
		try {
			$sql = "SELECT name FROM " . DbAdapter::getTable(DbAdapter::TABLE_STRINGS) . " WHERE filename = ? ORDER BY id ASC";
			$handle = $this->pdo->prepare($sql);
			$handle->bindValue(1, $filename);
			$handle->execute();
			return $handle->fetchAll(PDO::FETCH_ASSOC);
		} catch (PDOException $e) {
			L("Unable to get string names for $filename!", $e);
		}
		return null;
	}
}

