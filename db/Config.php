<?php
/**
 * User: bicou
 * Date: 27/11/2013
 * Time: 22:34
 */
require_once "db/DbAdapter.php";

class Config extends DbAdapter {
	const DB_VERSION = 1;
	const DEFAULT_LANGUAGE = "default_language";

	private $cache;

	public function __construct() {
		parent::__construct(DbAdapter::TABLE_CONFIG, Config::DB_VERSION);
		$cache = array();
	}

	public function get($key) {
		if (!isset($cache[$key])) {
			try {
				$handle = $this->pdo->prepare("SELECT value FROM " . DbAdapter::getTable(DbAdapter::TABLE_CONFIG) . " WHERE name = ?");
				$handle->bindValue(1, $key);
				$handle->execute();
				$result = $handle->fetch();
				$cache[$key] = $result === false ? false : $result['value'];
			} catch (PDOException $e) {
				L("Unable to retrieve config item '$key'");
				$cache[$key] = null;
			}
		}

		return $cache[$key];
	}

	public function set($key, $value) {
		try {
			$oldValue = $this->get($key);
			if ($oldValue === $value) {
				return;
			} else {
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

	public function getDefaultLanguage() {
		$defaultLanguage = $this->get(Config::DEFAULT_LANGUAGE);
		if ($defaultLanguage == null) {
			$strings = new StringsDbAdapter();
			$defaultLanguage = $strings->getFirstLanguage();
			$this->set(Config::DEFAULT_LANGUAGE, $defaultLanguage);
		}
		return $defaultLanguage;
	}

	protected function onUpgrade($oldVersion, $newVersion) {
		if ($oldVersion < 1) {
			$statement = "CREATE TABLE " . DbAdapter::getTable(DbAdapter::TABLE_CONFIG) . "( id INT(11) NOT NULL AUTO_INCREMENT" . ", name VARCHAR(50) NOT NULL" . ", value VARCHAR(250) NOT NULL" . ", PRIMARY KEY (id)" . ") ENGINE=MyISAM DEFAULT CHARSET=UTF8;";
			$this->createTable($statement);
		}
	}
}
