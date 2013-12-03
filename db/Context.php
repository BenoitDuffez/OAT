<?php
/**
 * User: bicou
 * Date: 27/11/2013
 * Time: 23:33
 */
require_once "db/DbAdapter.php";

class Context {
	public $id;
	public $name;

	public function __construct($result) {
		$this->id = $result['id'];
		$this->name = $result['name'];
	}
}

class ContextDbAdapter extends DbAdapter {
	const DB_VERSION = 1;

	public function __construct() {
		parent::__construct(DbAdapter::TABLE_CONTEXTS, ContextDbAdapter::DB_VERSION);
	}

	protected function onUpgrade($oldVersion, $newVersion) {
		if ($oldVersion < 1) {
			$statement = "CREATE TABLE " . DbAdapter::getTable(DbAdapter::TABLE_CONTEXTS);
			$statement .= " ( id INT(11) NOT NULL AUTO_INCREMENT" . ", name VARCHAR(50) NOT NULL" . ", PRIMARY KEY (id)" . ")";
			$statement .= " ENGINE=MyISAM DEFAULT CHARSET=UTF8;";
			$this->createTable($statement);
		}
	}

	public function load($id) {
		try {
			$handle = $this->pdo->prepare("SELECT * FROM " . DbAdapter::getTable(DbAdapter::TABLE_CONTEXTS) . " WHERE id = ?");
			$handle->bindValue(1, $id);
			$handle->execute();
			$result = $handle->fetch();
			return $result === false ? false : new Context($result);
		} catch (PDOException $e) {
			L("Unable to retrieve context id #'$id'");
		}
		return null;
	}

	public function loadAll() {
		$contexts = array();
		try {
			$handle = $this->pdo->prepare("SELECT * FROM " . DbAdapter::getTable(DbAdapter::TABLE_CONTEXTS) . " WHERE 1");
			$handle->execute();
			while ($result = $handle->fetch()) {
				$contexts[] = new Context($result);
			}
		} catch (PDOException $e) {
			L("Unable to retrieve all contexts: " . $e->getMessage());
		}
		return $contexts;
	}

	public function getAllScreenshots() {
		try {
			$sql = "SELECT s.*, c.name as context_name FROM " . DbAdapter::getTable(DbAdapter::TABLE_SCREENSHOTS) . " s, " . DbAdapter::getTable(DbAdapter::TABLE_CONTEXTS) . " c";
			$sql .= " WHERE c.id = s.context_id ORDER BY context_name ASC";
			$handle = $this->pdo->prepare($sql);
			$handle->execute();
			return $handle->fetchAll(PDO::FETCH_ASSOC);
		} catch (PDOException $e) {
			L("Unable to retrieve all screenshots", $e);
		}
	}

	public function getScreenshots($stringName) {
		$contexts = array();
		try {
			$sql = "SELECT s.*, c.name as context_name FROM " . DbAdapter::getTable(DbAdapter::TABLE_LINKS) . " l ";
			$sql .= " LEFT JOIN " . DbAdapter::getTable(DbAdapter::TABLE_CONTEXTS) . " c ON c.id = l.id1 ";
			$sql .= " INNER JOIN " . DbAdapter::getTable(DbAdapter::TABLE_SCREENSHOTS) . " s ON s.context_id = c.id";
			$sql .= " WHERE l.tbl1 = ? AND l.tbl2 = ? AND l.id2 = ?"; // strings table + string name
			$sql .= " ORDER BY context_name ASC";
			$handle = $this->pdo->prepare($sql);
			$i = 1;
			$handle->bindValue($i++, DbAdapter::TABLE_CONTEXTS);
			$handle->bindValue($i++, DbAdapter::TABLE_STRINGS);
			$handle->bindValue($i++, $stringName);
			$handle->execute();
			return $handle->fetchAll(PDO::FETCH_ASSOC);
		} catch (PDOException $e) {
			L("Unable to retrieve all contexts: ", $e);
		}
		return $contexts;
	}

	public function add($context_name) {
		if (trim($context_name) == "") {
			return;
		}

		try {
			$sql = "INSERT INTO " . DbAdapter::getTable(DbAdapter::TABLE_CONTEXTS) . " (name) VALUES (?)";
			$handle = $this->pdo->prepare($sql);
			$handle->bindValue(1, $context_name);
			$handle->execute();
		} catch (PDOException $e) {
			L("Unable to create context: $context_name: " . $e->getMessage());
		}
	}
}
