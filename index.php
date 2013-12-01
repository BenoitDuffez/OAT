<?php

function dump_html($html) {
	global $_SERVER;
	$path = str_replace("/index.php", "", $_SERVER['SCRIPT_NAME']);
	return str_replace(array('%PATH%'), array($path), $html);
}

ob_start("dump_html");

?><!DOCTYPE HTML>
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
	<meta http-equiv="content-type" content="text/html;charset=utf-8"/>
	<link rel="stylesheet" type="text/css" href="%PATH%/pages/styles.css"/>
	<link href='http://fonts.googleapis.com/css?family=Ubuntu+Mono|Roboto|Roboto+Condensed' rel='stylesheet' type='text/css'>
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
	<script type="text/javascript">
		function setCurrentString(name) {
			$.getJSON("ajax.php?action=getString&name=" + name + "&lang=<?php echo $_GET['lang'] ?>", null, function (data) {
				$('#sourcetext').val(data.source.text);
				$('#translatedtext').val(data.destination.text).focus();
			});
			var scr = $('#screenshots');
			scr.empty();
			$.getJSON("ajax.php?action=getScreenshots&name=" + name, null, function (data) {
				prevCid = -1;
				$.each(data, function (i, screen) {
					if (prevCid != screen.context_id) {
						if (prevCid > 0) {
							scr.append('</div>');
						}
						scr.append('<div class="context"><h3>'+screen.context_name);
					}
					prevCid = screen.context_id;
					scr.append('<div class="screenshot"><img class="screenshot" src="screenshots/'+screen.name+'" /></div>');
				});
				scr.append('</div>');
			});
		}
	</script>
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
<div id="menu">
	<a href=".">Home</a>
	 | <a href="?page=translate">Translate</a>
	 | <a href="?page=contexts">Contexts</a>
	 | <a href="?page=screenshots">Screenshots</a>
	 | <a href="?page=import">Import</a>
	 | <a href="?page=help">Help</a>
</div>
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

ob_end_flush();
