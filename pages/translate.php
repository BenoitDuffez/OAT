<?php
/**
 * User: bicou
 * Date: 28/11/2013
 * Time: 22:18
 */

include "db/Config.php";
include "db/Strings.php";

setHtmlTitle('Translator UI - OAT');

function generateLeftMenu($defStrings) {
	echo '
	<div id="list_strings">
		<h2>App strings</h2>
		<ul>';
	foreach ($defStrings as $k => $defString) {
		$class = (0 + $defString['is_translated']) > 0 ? 'set' : 'unset';
		echo '<li class="' . $class . '">';
		echo '<a href="javascript:setCurrentString(\'' . $defString['name'] . '\', \''.$_GET['lang'].'\');">' . $defString['name'] . '</a></li>';
	}

	echo '
		</ul>
	</div>';
}

function generateForm() {
	global $languages;
	echo '
	<div id="topForm" style="visibility: hidden;">
		<h2>Translation into ' . $languages[$_GET['lang']] . '</h2>
		<p class="tip">Tip: use Alt+Right to copy from source language<br />Tip: use Ctrl+Enter to save string and go to next</p>
		<textarea id="sourcetext" class="readonly"></textarea>
		<textarea id="translatedtext" class="readwrite" autofocus placeholder="Enter the text translated to ' . $languages[$_GET['lang']] . '"></textarea>
	</div>';
}

function generateContext() {
	echo '
	<div id="context" style="visibility: hidden;">
		<h2>String context</h2>
       	<div id="screenshots"></div>
	</div>';
}

if (!isset($_GET['lang'])) {
	echo "<p>Pick the target language</p>";
	echo "<div>";
	foreach ($languages as $lang => $language) {
		echo '<a href="%PATH%/translate/' . $lang . '/">' . $language . '</a> ';
	}
	echo "</div>";
} else {
	$config = new Config();
	$db = new StringsDbAdapter();

	$defaultLanguage = $config->getDefaultLanguage();
	if ($defaultLanguage == null) {
		echo "<p>Please import development strings first</p>";
	} else {
		$defStrings = $db->getAll($_GET['lang']);

		addHtmlHeader('<script src="%PATH%/static/translate.js"></script>');
		echo '<div id="translator">';
		generateLeftMenu($defStrings);
		generateForm();
		generateContext();
		echo '</div>';
	}
}

