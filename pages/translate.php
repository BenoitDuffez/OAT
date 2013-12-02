<?php
/**
 * User: bicou
 * Date: 28/11/2013
 * Time: 22:18
 */
include "db/Config.php";
include "db/Strings.php";

function generateLeftMenu($defStrings, $strings) {
	echo '
	<div id="list_strings">
		<h2>App strings</h2>
		<ul>';
	foreach ($defStrings as $k => $defString) {
		$class = isset($strings[$k]) && !is_string($strings[$k]) && strlen(trim($strings[$k]['text'])) > 0 ? 'set' : 'unset';
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
		echo "<p>Please import strings first</p>";
	} else {
		$nb = $db->getNbStrings($_GET['lang']);

		$rawDefStrings = $db->getAll($defaultLanguage);
		$rawStrings = $db->getAll($_GET['lang']);

		$strings = array();
		$defStrings = array();
		// Get all empty in target language first
		foreach ($rawDefStrings as $k => $rawDefString) {
			if (!isset($rawStrings[$k]) || strlen(trim($rawStrings[$k]['text'])) == 0) {
				$strings[] = "";
				$defStrings[] = $rawDefStrings[$k];
			}
		}
		// Then add others
		foreach ($rawDefStrings as $k => $rawDefString) {
			if (isset($rawStrings[$k]) && strlen(trim($rawStrings[$k]['text'])) > 0) {
				$strings[] = $rawStrings[$k];
				$defStrings[] = $rawDefStrings[$k];
			}
		}

		// TODO: add specific JS from here

		echo '<div id="translator">';
		generateLeftMenu($defStrings, $strings);
		generateForm();
		generateContext();
		echo '</div>';
	}
}
