<?php

class DbAdapter {
	private $pdo;

	const TABLE_CONFIG = "config";
	const TABLE_CONTEXTS = "contexts";

	public static function getTable($name) {
		global $_tprefix;
		return $_tprefix . $name;
	}

	public function __construct(PDO $pdo, $version) {
		$this->pdo = $pdo;
		
	}
}

