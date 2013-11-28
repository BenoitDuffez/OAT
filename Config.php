<?php
/**
 * User: bicou
 * Date: 27/11/2013
 * Time: 22:34
 */

class Config extends DbAdapter {
	const DB_VERSION = 1;

	public function __construct(PDO $pdo) {
		parent::__construct($pdo, DbAdapter::TABLE_CONFIG, Config::DB_VERSION);
	}

	public function get($key) {
		try {
			$handle = $this->pdo->prepare("SELECT value FROM " . DbAdapter::getTable(DbAdapter::TABLE_CONFIG) . " WHERE name = ?");
			$handle->bindValue(1, $key);
			$handle->execute();
			$result = $handle->fetch();
			return $result === false ? false : $result['value'];
		} catch (PDOException $e) {
			L("Unable to retrieve config item '$key'");
		}
		return null;
	}

	public function set($key, $value) {
		try {
			$oldValue = $this->get($key);
			if ($oldValue === $value) {
				return;
			}
			else {
				if ($oldValue === false) {
					$sql = "INSERT INTO " . DbAdapter::getTable(DbAdapter::TABLE_CONFIG) . " (value, name) VALUES (?, ?)";
				} else {
					$sql = "UPDATE " . DbAdapter::getTable(DbAdapter::TABLE_CONFIG) . " SET value = ? WHERE name = ?";
				}
				$handle = $this->pdo->prepare($sql);
				$handle->bindValue(1, $value);
				$handle->bindValue(2, $key);
				$handle->execute();
			}
		} catch (PDOException $e) {
			L("Unable to set '$key'='$value': " . $e->getMessage());
		}
	}

	protected function onUpgrade($oldVersion, $newVersion) {
		if ($oldVersion < 1) {
			$statement = "CREATE TABLE " . DbAdapter::getTable(DbAdapter::TABLE_CONFIG)
				. "( id INT(11) NOT NULL AUTO_INCREMENT"
				. ", name VARCHAR(50) NOT NULL"
				. ", value VARCHAR(250) NOT NULL"
				. ", PRIMARY KEY (id)"
				. ") ENGINE=MyISAM DEFAULT CHARSET=UTF8;";
			$this->createTable($statement);
		}
	}
}
