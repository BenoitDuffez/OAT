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

function setHtmlTitle($title) {
	addHtmlHeader('<title>' . $title . '</title>');
}

function writeContents() {
	global $languages;

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
			setHtmlTitle('Help - OAT');
			echo file_get_contents("static/help.html");
			break;

		default:
			echo "<div>Unknown page.</div>";
			break;
	}
}

include "init.php";

// Build page content
ob_start("dump_html");
readfile("static/header.html");
if ($pdo == null) {
	echo "<p>Service is currently down for maintenance.</p>";
	L("Unable to connect to database server (\$pdo==null).");
} else {
	// Try to identify the user
	if(!empty($_SESSION['logged_in']) && !empty($_SESSION['username'])) {
		// let the user access the main page
	} else if(!empty($_POST['username']) && !empty($_POST['password'])) {
		// let the user login
	} else {
		// display the login form
	}

	writeContents();
}
readfile("static/footer.html");
ob_end_flush();


