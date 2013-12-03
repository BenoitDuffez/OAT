<?php
/**
 * User: bicou
 * Date: 03/12/2013
 * Time: 00:33
 */

header('Content-type: application/xml');
header('Content-Disposition: attachment; filename="' . $_GET['filename'] . '"');

include "init.php";
include "db/Strings.php";
include "db/Translations.php";
include "db/Config.php";

$db = new StringsDbAdapter();
$tr = new Translations();
$cfg = new Config();

echo '
<?xml version="1.0" encoding="utf-8"?>
<resources>
';

// Get the list of strings
$strings = $tr->getStrings($_GET['lang'], $_GET['filename']);
foreach ($strings as $string) {
	$formatted = $string['formatted'] ? '' : ' formatted="false"';
	echo "\n    ";
	echo '<' . $string['stringtype'] . ' name="' . $string['name'] . '"' . $formatted . '>' . $string['text'] . '</' . $string['stringtype'] . '>';

}

?>

</resources>
