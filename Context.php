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

class ContextDbAdapter {
	private $pdo;

	public function __construct(PDO $pdo, $db_version) {
		$this->pdo = $pdo;
	}

	public function install() {
		try {
			$statement = "CREATE TABLE " . DbAdapter::getTable(DbAdapter::TABLE_CONTEXTS) . "( id INT(11) NOT NULL AUTO_INCREMENT" . ", name VARCHAR(50) NOT NULL" . ", PRIMARY KEY (id)" . ") ENGINE=MyISAM DEFAULT CHARSET=UTF8;";
			$this->pdo->exec($statement);
		} catch (PDOException $e) {
			L("Unable to create config table: " . $e->getMessage());
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
