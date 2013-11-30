<?php
/**
 * User: bicou
 * Date: 27/11/2013
 * Time: 23:33
 */

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

	public function getScreenshots($stringName) {
		$contexts = array();
		try {
			$sql = "SELECT c.name, s.* FROM " . DbAdapter::getTable(DbAdapter::TABLE_CONTEXTS) . " c, ";
			$sql .= DbAdapter::getTable(DbAdapter::TABLE_SCREENSHOTS) . " s ";
			$sql .= " WHERE s.context_id=c.id AND ";
			$handle = $this->pdo->prepare($sql);
			$handle->execute();
			while ($result = $handle->fetch()) {
				$contexts[] = new Context($result);
			}
		} catch (PDOException $e) {
			L("Unable to retrieve all contexts: " . $e->getMessage());
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
