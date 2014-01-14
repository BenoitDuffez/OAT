<?php

abstract class DbAdapter {
	protected $pdo;
	private $tableName;

	const TABLE_CONFIG = "config";
	const TABLE_CONTEXTS = "contexts";
	const TABLE_STRINGS = "strings";
	const TABLE_TRANSLATIONS = "translations";
	const TABLE_SCREENSHOTS = "screenshots";
	const TABLE_ACCOUNTS = "accounts";
	const TABLE_LOGIN_ATTEMPTS = "login_attempts";
	const TABLE_LINKS = "links";

	abstract protected function onUpgrade($oldVersion, $newVersion);

	public static function getTable($name) {
		global $_tprefix;
		return $_tprefix . $name;
	}

	public function __construct($table, $version) {
		global $pdo;

		$this->pdo = $pdo;
		$this->tableName = $this->getTable($table);

		$currentVersion = 0 + $this->getDbVersion($table);
		if ($currentVersion < $version) {
			if ($this->onUpgrade($currentVersion, $version)) {
				$this->setDbVersion($table, $version);
			}
		}
	}

	protected function createTable($statement, $tableName = "") {
		if (strlen(trim($tableName)) == 0) {
			$tableName = $this->tableName;
		}

		$sql = str_replace("table", $tableName, $statement);
		try {
			$this->pdo->exec($sql);
		} catch (PDOException $e) {
			L("Unable to create strings table", $e);
		}
	}

	public function getDbVersion($table) {
		try {
			$handle = $this->pdo->prepare("SELECT value FROM " . DbAdapter::getTable(DbAdapter::TABLE_CONFIG) . " WHERE name = ?");
			$handle->bindValue(1, $table . "_db_version");
			$handle->execute();
			$result = $handle->fetch();
			return $result === false ? false : $result['value'];
		} catch (PDOException $e) {
			L("Unable to retrieve DB version for $table", $e);
		}
		return null;
	}

	public function setDbVersion($table, $version) {
		try {
			$handle = $this->pdo->prepare("DELETE FROM " . DbAdapter::getTable(DbAdapter::TABLE_CONFIG) . " WHERE name = ?");
			$handle->bindValue(1, $table . "_db_version");
			$handle->execute();
			$handle = $this->pdo->prepare("INSERT INTO " . DbAdapter::getTable(DbAdapter::TABLE_CONFIG) . " (name, value) VALUES (?, ?)");
			$handle->bindValue(1, $table . "_db_version");
			$handle->bindValue(2, $version);
			$handle->execute();
		} catch (PDOException $e) {
			L("Unable to set DB version to $version for $table", $e);
		}
		return null;
	}
}

