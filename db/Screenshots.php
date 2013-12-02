<?php
/**
 * User: bicou
 * Date: 28/11/2013
 * Time: 23:20
 */
require_once "db/DbAdapter.php";

class ScreenshotsDbAdapter extends DbAdapter {
	const DB_VERSION = 1;
	private $lastException;

	public function __construct() {
		parent::__construct(DbAdapter::TABLE_SCREENSHOTS, ScreenshotsDbAdapter::DB_VERSION);
	}

	public function add($name, $context) {
		try {
			$handle = $this->pdo->prepare("INSERT INTO " . $this->getTable(DbAdapter::TABLE_SCREENSHOTS) . " (name, context_id) VALUES (?, ?)");
			$handle->bindValue(1, $name);
			$handle->bindValue(2, $context);
			$handle->execute();
			return true;
		} catch (PDOException $e) {
			L("Unable to add screenshot", $e);
			$this->lastException = $e;
		}
		return false;
	}

	public function getLastException() {
		return $this->lastException;
	}

	public function getAll() {
		try {
			$sql = "SELECT s.*, c.name as context";
			$sql .= " FROM " . $this->getTable(DbAdapter::TABLE_SCREENSHOTS) . " s, " . $this->getTable(DbAdapter::TABLE_CONTEXTS) . " c";
			$sql .= " WHERE c.id = s.context_id";
			$sql .= " ORDER BY s.context_id ASC";
			$handle = $this->pdo->prepare($sql);
			$handle->execute();
			return $handle->fetchAll(PDO::FETCH_ASSOC);
		} catch (PDOException $e) {
			L("Unable retrieve all screenshots", $e);
		}
		return null;
	}

	protected function onUpgrade($oldVersion, $newVersion) {
		if ($oldVersion < 1) {
			$statement = <<<SQL
CREATE TABLE IF NOT EXISTS table (
  id int(11) NOT NULL AUTO_INCREMENT,
  name varchar(255) NOT NULL,
  context_id int(11) NOT NULL,
  PRIMARY KEY (id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
SQL;
			$this->createTable($statement);
		}
	}
}
