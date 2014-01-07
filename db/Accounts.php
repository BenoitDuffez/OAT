<?php
/**
 * User: bicou
 * Date: 07/01/2014 
 * Time: 10:33
 */
require_once "db/DbAdapter.php";

class Account {
	public $id;
	public $name;
	public $mail;
	public $hash;
	public $creationDate;
	public $lastLoginDate;
	public $lastLoginIP;
	public $lastLoginHost;

	public function buildHash($password) {
		$cost = 10;
		$salt = strtr(base64_encode(mcrypt_create_iv(16, MCRYPT_DEV_URANDOM)), '+', '.');
		$salt = sprintf("$2a$%02d$", $cost) . $salt;
		$this->hash = crypt($password, $salt);
	}

	public function validatePassword($password) {
		return crypt($password, $this->hash) == $this->hash;
	}
}

class AccountsDbAdapter extends DbAdapter {
	const DB_VERSION = 1;

	public function __construct() {
		parent::__construct(DbAdapter::TABLE_ACCOUNTS, AccountsDbAdapter::DB_VERSION);
	}

	public function add($account) {
		try {
			$sql = "INSERT INTO " . $this->getTable(DbAdapter::TABLE_ACCOUNTS);
			$sql .= " (user_name, user_hash, user_mail, creation_date, last_login_date, last_login_ip, last_login_host)";
			$sql .= " VALUES (?, ?, ?, ?, ?, ?, ?)";
			$handle = $this->pdo->prepare($sql);

			$i = 1;
			$handle->bindValue($i++, $account->name);
			$handle->bindValue($i++, $account->hash);
			$handle->bindValue($i++, $account->mail);
			$handle->bindValue($i++, $account->creationDate);
			$handle->bindValue($i++, $account->hash);
			$handle->bindValue($i++, $account->hash);
			$handle->bindValue($i++, $account->hash);

			$handle->execute();
			return true;
		} catch (PDOException $e) {
			L("Unable to add account", $e);
			$this->lastException = $e;
		}
		return false;
	}

	protected function onUpgrade($oldVersion, $newVersion) {
		if ($oldVersion < 1) {
			$statement = <<<SQL
CREATE TABLE IF NOT EXISTS table (
  id int(11) NOT NULL AUTO_INCREMENT,
  user_name varchar(255) NOT NULL,
  user_hash varchar(123) NOT NULL,
  user_mail varchar(255) NOT NULL,
  creation_date datetime NOT NULL,
  last_login_date datetime NOT NULL,
  last_login_ip varchar(16) NOT NULL,
  last_login_host varchar(255) NOT NULL,
  PRIMARY KEY (id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
SQL;
			$this->createTable($statement);
		}
	}
}
