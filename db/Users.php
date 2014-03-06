<?php

class Role {
	const level_ANONYMOUS = 0;
	const level_REGISTERED = 1;
	const level_ADMINISTRATOR = 2;

	public static $ANONYMOUS, $REGISTERED, $ADMINISTRATOR;
	private $level;

	function __construct($level) {
		if ($level < 0) {
			Role::$ANONYMOUS = new Role(Role::level_ANONYMOUS);
			Role::$REGISTERED = new Role(Role::level_REGISTERED);
			Role::$ADMINISTRATOR = new Role(Role::level_ADMINISTRATOR);
		} else {
			$this->level = $level;
		}
	}
}

new Role(-1);

class User {
	private $role;
}

require_once "db/DbAdapter.php";

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


var_dump(Role::$ANONYMOUS);
var_dump(Role::$REGISTERED);
var_dump(Role::$ADMINISTRATOR);

