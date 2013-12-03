<?php
/**
 * User: bicou
 * Date: 28/11/2013
 * Time: 22:18
 */

include "db/Config.php";
include "db/Strings.php";

function generateLeftMenu($defStrings) {
	echo '
	<div id="list_strings">
		<h2>App strings</h2>
		<ul>';
	foreach ($defStrings as $k => $defString) {
		$class = (0 + $defString['is_translated']) > 0 ? 'set' : 'unset';
		echo '<li id="' . $defString['name'] . '" class="' . $class . '">';
		echo '<a class="button" href="javascript:setCurrentString(\'' . $defString['name'] . '\', \'' . $_GET['lang'] . '\');">' . $defString['name'] . '</a></li>';
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
		<textarea id="sourcetext" class="readonly"></textarea>
		<textarea id="translatedtext" class="readwrite" autofocus placeholder="Enter the text translated to ' . $languages[$_GET['lang']] . '"></textarea>
		<p class="tip">
			Tips: use Alt+Right to copy from source language —
			use Ctrl+Enter to save string and go to next —
			use Alt+Up or Down to navigate next/prev string
		</p>
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

		addHtmlHeader('<script language="javascript" src="%PATH%/static/translate.js"></script>');
		echo '<div id="translator">';
		generateLeftMenu($defStrings);
		generateForm();
		generateContext();
		echo '</div>';
	}
}

