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
	$menu[''] = array('title' => "Home", 'level' => Role::ANONYMOUS);
	$menu['translate'] = array('title' => "Translate", 'level' => Role::ANONYMOUS);
	$menu['contexts'] = array('title' => "Contexts", 'level' => Role::ADMINISTRATOR);
	$menu['screenshots'] = array('title' => "Screenshots", 'level' => Role::ADMINISTRATOR);
	$menu['import'] = array('title' => "Import", 'level' => Role::ADMINISTRATOR);
	$menu['export'] = array('title' => "Export", 'level' => Role::ADMINISTRATOR);
	$menu['help'] = array('title' => "Help", 'level' => Role::ANONYMOUS);
	echo '
  <div id="menu">
    <div id="user_menu">%USER_MENU%</div>
    <ul>';
	$level = isset($GLOBALS['user']) ? $GLOBALS['user']->role : Role::ANONYMOUS;
	foreach ($menu as $p => $item) {
		if ($level >= $item['level']) {
			$liClass = $p == $page ? ' class="active"' : '';
			$url = $p == "" ? "" : $p . "/";
			echo '<li' . $liClass . '><a href="%PATH%/' . $url . '">' . $item['title'] . '</a></li>';
		}
	}
	echo '
    </ul>
  </div>
';

	// Write page contents
	if ($page != 'account' && !isset($menu[$page])) {
		header('HTTP/1.0 404 Not Found');
		echo "<p>The page '$page' was not found.</p>";
	} else if ($page != 'account' && $level < $menu[$page]['level']) {
		header('HTTP/1.0 403 Forbidden');
		echo "<p>The access to this page is restricted</p>";
	} else {
		switch ($page) {
			case 'contexts':
			case 'screenshots':
			case 'import':
			case 'export':
			case 'translate':
			case 'account':
			case 'help':
				include "pages/$page.php";
				break;

			case 'strings':
			case null:
				include "pages/strings.php";
				break;

			default:
				echo "<div>Unknown page.</div>";
				header('HTTP/1.0 404 Not Found');
				break;
		}
	}
}

