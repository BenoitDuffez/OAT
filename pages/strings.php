<?php

include "db/Translations.php";
include "db/Config.php";

setHtmlTitle('Open Android Translator - Dashboard');

$tr = new Translations();
$cfg = new Config();

echo '<div>Development language: ' . $languages[$cfg->getDefaultLanguage()] . '</div>
<p>Available languages:</p>
<ul>
';

$langs = $tr->getLangs();
if (count($langs) == 0) {
	echo "none";
} else {
	$max = $langs[$cfg->getDefaultLanguage()]['nb'];
	foreach ($langs as $lang) {
		echo '<li><a href="%PATH%/translate/' . $lang['lang'] . '/">';
		echo $languages[$lang['lang']];
		echo "</a>";
		if ($lang['lang'] == $cfg->getDefaultLanguage()) {
			echo " *";
		}
		echo " (" . $lang['nb'] . " strings - " . sprintf("%.1f %%", 100.0 * $lang['nb'] / $max) . ")</li>";
	}
}

echo '
</ul>
';


