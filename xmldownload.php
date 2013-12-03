<?php
/**
 * User: bicou
 * Date: 03/12/2013
 * Time: 00:33
 */

echo <<<XML
<?xml version="1.0" encoding="utf-8"?>
<resources>
XML;

header('Content-type: application/xml');

include "init.php";
include "db/Strings.php";
include "db/Config.php";

$db = new StringsDbAdapter();
$cfg = new Config();

// TODO: so many queries, such slowness, wow
// Get the list of string names
$names = $db->getStringNames($cfg->getDefaultLanguage(), $_GET['filename']);
foreach ($names as $name) {
	$string = $db->getString($_GET['lang'], $name['name']);
	$formatted = $string['formatted'] ? '' : ' formatted="false"';
	echo "\n    ";
	echo '<string name="'.$string['name'].'"'.$formatted.'>'.$string['text'].'</string>';

}

?>
</resources>
