<?php

include "db/Translations.php";
include "db/Config.php";

setHtmlTitle('Open Android Translator - Dashboard');

$tr = new Translations();
$cfg = new Config();

$defaultLang = $cfg->getDefaultLanguage();
$devLang = $defaultLang == null ? "none" : $languages[$defaultLang]; 


$langs = $tr->getLangs();
if ($defaultLang == null) {
	echo '<p>You have an empty database. You need to <a href="%PATH%/import/">import an XML file</a> before you can translate anything.
		Be sure to import the XML file in the language you are developing in!</p>';
} else {
	echo '<div>Development language: ' . $devLang . '</div>';

	if (count($langs) == 0) {
		echo '<p>You need to pick a now language to translate your strings to.
		Please go to the <a href="%PATH%/translate/">translate tab</a> to pick one</p>';
	} else {
		echo '<div>Available languages: ';
		$max = $langs[$defaultLang]['nb'];
		echo '<ul>';
		foreach ($langs as $lang) {
			echo '<li><a href="%PATH%/translate/' . $lang['lang'] . '/">';
			echo $languages[$lang['lang']];
			echo '</a>';
			if ($lang['lang'] == $cfg->getDefaultLanguage()) {
				echo ' *';
			}
			echo ' (' . $lang['nb'] . ' strings - ' . sprintf("%.1f %%", 100.0 * $lang['nb'] / $max) . ')</li>';
		}
		echo '</ul></div>';
	}
}

echo '
</ul>
';

