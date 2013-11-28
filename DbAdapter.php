<?php

abstract class DbAdapter {
	protected $pdo;

	const TABLE_CONFIG = "config";
	const TABLE_CONTEXTS = "contexts";
	const TABLE_STRINGS = "strings";

	abstract protected function onUpgrade($oldVersion, $newVersion);

	public static function getTable($name) {
		global $_tprefix;
		return $_tprefix . $name;
	}

	public function __construct(PDO $pdo, $table, $version) {
		$this->pdo = $pdo;

		$currentVersion = 0 + $this->getDbVersion($table);
		if ($currentVersion < $version) {
			$this->onUpgrade($currentVersion, $version);
			$this->setDbVersion($table, $version);
		}
	}

        public function getDbVersion($table) {
                try {
                        $handle = $this->pdo->prepare("SELECT value FROM " . DbAdapter::getTable(DbAdapter::TABLE_CONFIG). " WHERE name = ?");
                        $handle->bindValue(1, $table."_db_version");
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
                        $handle->bindValue(1, $table."_db_version");
                        $handle->execute();
			$handle = $this->pdo->prepare("INSERT INTO " . DbAdapter::getTable(DbAdapter::TABLE_CONFIG) . " (name, value) VALUES (?, ?)");
			$handle->bindValue(1, $table."_db_version");
			$handle->bindValue(2, $version);
			$handle->execute();
                } catch (PDOException $e) {
                        L("Unable to set DB version to $version for $table", $e);
                }
                return null;
        }
}

