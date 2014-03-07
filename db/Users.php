<?php

require_once "db/DbAdapter.php";

class Role {
	const ANONYMOUS = 0;
	const REGISTERED = 1;
	const ADMINISTRATOR = 2;
}

class User {
	public $id;
	public $role;
	public $name;
	public $passwordHash;

	public static $ANONYMOUS;

	public static function init() {
		self::$ANONYMOUS = new User(Role::ANONYMOUS);
	}

	public function __construct($data) {
		if (is_int($data) && $data == Role::ANONYMOUS) {
			$this->role = $data;
		} else if (is_array($data)) {
			$this->id   = $data['id'];
			$this->name = $data['name'];
			$this->role = $data['role'];
			$this->passwordHash = $data['password'];
		} else {
			$this->id   = $data->id;
			$this->role = $data->role;
			$this->name = $data->name;
			$this->passwordHash = $data->passwordHash;
		}
	}

	public function getName() {
		if ($this->id <= 0) {
			return "anonymous";
		} else {
			return $this->name;
		}
	}
} User::init();

class UsersDbAdapter extends DbAdapter {
        const DB_VERSION = 1;

        public function __construct() {
                parent::__construct(DbAdapter::TABLE_USERS, UsersDbAdapter::DB_VERSION);
        }

        protected function onUpgrade($oldVersion, $newVersion) {
                try {
                        if ($oldVersion < 1) {
                                $statement = <<<SQL
CREATE TABLE IF NOT EXISTS table (
  id int(11) NOT NULL AUTO_INCREMENT,
  name varchar(255) NOT NULL,
  role tinyint(1) NOT NULL,
  email varchar(255) NOT NULL,
  password varchar(255) NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY name (name)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
SQL;
                                $this->createTable($statement);
                        }
                } catch (PDOException $e) {
                        L("Unable to upgrade strings database from $oldVersion to $newVersion", $e);
                }
        }

	function registerUser($login, $password, $email) {
		try {
			$statement = "INSERT INTO " . $this->getTable(DbAdapter::TABLE_USERS) . " (name, role, email, password) ";
			$statement .= " VALUES (?, ?, ?, ?)";

			$hash = UsersDbAdapter::getPasswordHash($password);

			$handle = $this->pdo->prepare($statement);
			$handle->bindValue(1, $login);
			$handle->bindValue(2, Role::ANONYMOUS);
			$handle->bindValue(3, $email);
			$handle->bindValue(4, $hash);

			$handle->execute();
			return true;
		} catch (PDOException $e) {
			L("Unable to register user '$login', '$email'", $e);
		}
		return false;
	}

	public static function getPasswordHash($password) {
		$options = [ 'cost' => 12 ];
		return password_hash($password, PASSWORD_BCRYPT, $options);
	}

	function getUserPassword($login) {
		$user = $this->getUser($login);
		if ($user != null && !empty($user->passwordHash)) {
			return $user->passwordHash;
		}
		return null;
	}

	function getUser($id) {
		try {
			$statement = "SELECT * FROM " . $this->getTable(DbAdapter::TABLE_USERS);
			if (is_int($id)) {
				$statement .= " WHERE id = ?";
			} else {
				$statement .= " WHERE name = ?";
			}

			$handle = $this->pdo->prepare($statement);
			$handle->bindValue(1, $id);

			$handle->execute();
			if ($handle->rowCount() == 1) {
				return new User($handle->fetch());
			}
		} catch (PDOException $e) {
			L("Unable to register user '$login', '$email'", $e);
		}
		return null;
	}
}

