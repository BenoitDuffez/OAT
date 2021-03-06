<?php
/**
 * User: bicou
 * Date: 30/11/2013
 * Time: 17:54
 */
require_once "db/DbAdapter.php";

class LinksDbAdapter extends DbAdapter {
	const DB_VERSION = 1;

	public function __construct() {
		parent::__construct(DbAdapter::TABLE_LINKS, LinksDbAdapter::DB_VERSION);
	}

	protected function onUpgrade($oldVersion, $newVersion) {
		if ($oldVersion < 1) {
			$sql = <<<SQL
CREATE TABLE IF NOT EXISTS table (
  id int(11) NOT NULL AUTO_INCREMENT,
  tbl1 varchar(50) NOT NULL,
  id1 varchar(100) NOT NULL,
  tbl2 varchar(50) NOT NULL,
  id2 varchar(100) NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY link (table1, id1, table2, id2)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
SQL;
			$this->createTable($sql);
		}
	}

	public function addLink($table1, $id1, $table2, $id2) {
		try {
			$sql = "INSERT INTO " . DbAdapter::getTable(DbAdapter::TABLE_LINKS) . " (tbl1, tbl2, id1, id2) VALUES (?, ?, ?, ?)";
			$handle = $this->pdo->prepare($sql);
			$i = 1;
			$handle->bindValue($i++, $table1);
			$handle->bindValue($i++, $table2);
			$handle->bindValue($i++, $id1);
			$handle->bindValue($i++, $id2);
			$handle->execute();
		} catch (PDOException $e) {
			L("Unable to add link: tables=$table1,$table2; ids=$id1,$id2", $e);
		}
	}
}