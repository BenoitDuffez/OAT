<?php

$str = new StringsDbAdapter();

echo "<div>Available languages: ";
$langs = $str->getLangs();
$first = true;
foreach ($langs as $lang) {
	if ($first) {
		$first = false;
	} else {
		echo " | ";
	}
	echo "<a href=\"?lang=" . $lang['lang'] . "\">" . $languages[$lang['lang']] . "</a> (" . $lang['nb'] . " strings)";
}
echo "</div>";


$desiredLang = isset($_GET['lang']) ? $_GET['lang'] : $str->getFirstLanguage();

echo "<div>Strings for " . $languages[$desiredLang] . ": <br />";
if (isset($desiredLang)) {
	var_dump($str->getAll($desiredLang));
}
echo "</div>";

