<?php

function getInstallPath() {
	return str_replace("/index.php", "", $_SERVER['SCRIPT_NAME']);
}

function dump_html($html) {
	global $_SERVER, $html_head, $user_menu;

	$path = getInstallPath();
	$head = isset($html_head) ? $html_head : "";

	$src = array('%HEAD%', '%USER_MENU%', '%PATH%');
	$dst = array($head, $user_menu, $path);

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
    <div id="user_menu">%USER_MENU%</div>
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
		case 'account':
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

