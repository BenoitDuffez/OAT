<!DOCTYPE HTML>
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
	<meta http-equiv="content-type" content="text/html;charset=utf-8" />
	<style>
div.error {
	background: #FAA;
	border: 1px dashed red;
	padding: 10px;
	margin: 10px;
}

div.warning {
	border: 1px dashed #CCC;
}

img.screenshot {
	max-width: 250px;
	height: auto;
}
	</style>
</head>
<body>
<?php

error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
ini_set('display_errors', '1');

/**
 * User: bicou
 * Date: 27/11/2013
 * Time: 22:28
 */

// Dumb includes
include "config.db.php";
include "lang.php";
include "util.php";

// Actual code that may need dumb includes
include "db/DbAdapter.php";
include "db/Config.php";
include "db/Context.php";
include "db/Strings.php";
include "db/Screenshots.php";

// Init DB connection
try {
	$pdo = new PDO("mysql:host=$_db_host;dbname=$_db_name", $_db_user, $_db_pass);
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
	L("Unable to connect to DB server: $e");
	die();
}

// Load config
$cfg = new Config($pdo);

// Write page contents
echo <<<HTML
<div>
	<a href=".">Home</a>
	 | <a href="?page=translate">Translate</a>
	 | <a href="?page=contexts">Contexts</a>
	 | <a href="?page=screenshots">Screenshots</a>
	 | <a href="?page=import">Import</a>
	 | <a href="?page=help">Help</a>
</div>
<hr />
HTML;

$page = isset($_GET['page']) ? $_GET['page'] : null;
switch ($page) {
	case 'contexts':
	case 'screenshots':
	case 'import':
	case 'translate':
		include "pages/" . $page . ".php";
		break;

	case 'strings':
	case null:
		include "pages/strings.php";
		break;

	case 'help':
		echo file_get_contents("pages/help.html");
		break;

	default:
		echo "<div>Unknown page.</div>";
		break;
}


