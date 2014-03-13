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
		$max = $langs[$defaultLang]['nb'];
		foreach ($langs as $lang) {
			echo '<div class="home_language">';
			echo '<h3>' . $languages[$lang['lang']] . '</h3>';
			if ($lang['lang'] == $cfg->getDefaultLanguage()) {
				echo '<span title="Development language" class="dev">[dev]</span>';
			}
			echo '<hr />';
			echo '<a href="%PATH%/translate/' . $lang['lang'] . '/">Translate</a>';
			echo '<p>' . $lang['nb'] . ' strings translated</p>';
			$percent = 100 * $lang['nb'] / $max;
			echo '<div class="progress">';
			echo '<div class="translated" style="width: ' . $percent . '%;"></div>';
			if ($percent < 100) {
				echo '<div style="width: ' . (100 - $percent) . '%;"></div>';
			}
			echo '</div>';
			echo sprintf("<div>%.1f %%</div>", $percent);
			echo '</div>';
		}
	}
}

echo '
</ul>
';

