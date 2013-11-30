<?php

header('Content-Type: application/json');

include "config.db.php";
include "lang.php";
include "util.php";

// Actual code that may need dumb includes
include "db/DbAdapter.php";
include "db/Config.php";

// Init DB connection
try {
	$pdo = new PDO("mysql:host=$_db_host;dbname=$_db_name", $_db_user, $_db_pass);
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
	L("Unable to connect to DB server: $e");
	die();
}

$cfg = new Config();
switch ($_GET['action']) {
	case 'getString':
	{
		include "db/Strings.php";
		$db = new StringsDbAdapter();
		$data = array();
		$data['source'] = $db->getString($cfg->getDefaultLanguage(), $_GET['name']);
		$data['destination'] = $db->getString($_GET['lang'], $_GET['name']);
		echo json_encode($data);
		break;
	}

	case 'getScreenshote':
	{
		include "db/Screenshots.php";
		include "db/Context.php";
		$db = new ScreenshotsDbAdapter();
		$ctx = new ContextDbAdapter();
		break;
	}
}
