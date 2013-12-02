<?php

/**
 * User: bicou
 * Date: 27/11/2013
 * Time: 22:28
 */

function dump_html($html) {
	global $_SERVER, $html_head;

	$path = str_replace("/index.php", "", $_SERVER['SCRIPT_NAME']);
	$head = isset($html_head) ? $html_head : "";

	$src = array('%HEAD%', '%PATH%');
	$dst = array($head, $path);

	return str_replace($src, $dst, $html);
}

function addHtmlHeader($header) {
	global $html_head;
	$html_head .= $header;
}

ob_start("dump_html");
error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
ini_set('display_errors', '1');

readfile("static/header.html");

// Main includes
include "config.db.php";
include "lang.php";
include "util.php";

// Init DB connection
try {
	$pdo = @new PDO("mysql:host=$_db_host;dbname=$_db_name", $_db_user, $_db_pass);
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
	L("Unable to connect to DB server:", $e);
	$pdo = null;
}

// Build page content
if ($pdo == null) {
	echo "<p>Can't do anything without the DB server</p>";
} else {
	// Desired page
	$page = isset($_GET['page']) ? $_GET['page'] : null;

	// Write menu
	$menu = array();
	$menu[''] = "Home";
	$menu['translate'] = "Translate";
	$menu['contexts'] = "Contexts";
	$menu['screenshots'] = "Screenshots";
	$menu['import'] = "Import";
	$menu['export'] = "Export";
	$menu['help'] = "Help";
	echo '
  <div id="menu">
    <ul>';
	foreach ($menu as $p => $title) {
		$liClass = $p == $page ? ' class="active"' : '';
		$url = $p == "" ? "" : $p . "/";
		echo '<li' . $liClass . '><a href="%PATH%/' . $url . '">' . $title . '</a></li>';
	}
	echo '
    </ul>
  </div>
';

	// Write page contents
	switch ($page) {
		case 'contexts':
		case 'screenshots':
		case 'import':
		case 'export':
		case 'translate':
			include "pages/$page.php";
			break;

		case 'strings':
		case null:
			include "pages/strings.php";
			break;

		case 'help':
			echo file_get_contents("static/help.html");
			break;

		default:
			echo "<div>Unknown page.</div>";
			break;
	}
}

readfile("static/footer.html");
ob_end_flush();
