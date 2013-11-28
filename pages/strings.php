<?php

$str = new StringsDbAdapter();

echo "<div>Available languages: ";
$langs = $str->getLangs();
foreach ($langs as $lang) {
	echo "<a href=\"?page=" . $_GET['page'] . "&lang=" . $lang['lang'] . "\">" . $languages[$lang['lang']] . "</a> ";
}
echo "</div>";


echo "<div>Strings for " . $languages[$_GET['lang']] . ": <br />";
if (isset($_GET['lang'])) {
	var_dump($str->getAll($_GET['lang']));
}
echo "</div>";

