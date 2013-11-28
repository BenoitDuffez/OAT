<?php
/**
 * User: bicou
 * Date: 28/11/2013
 * Time: 22:18
 */

if (!isset($_GET['lang'])) {
	echo "<p>Pick the target language</p>";
	echo "<div>";
	foreach ($languages as $lang => $language) {
		echo "<a href=\"?page=" . $_GET['page'] . "&lang=$lang\">$language</a> ";
	}
	echo "</div>";
} else {
	$config = new Config();
	$db = new StringsDbAdapter();

	$defaultLanguage = $config->getDefaultLanguage();
	if ($defaultLanguage == null) {
		$defaultLanguage = $db->getFirstLanguage();
	}

	if ($defaultLanguage == null) {
		echo "<p>Please import strings first</p>";
	} else {
		$defStrings = $db->getAll($defaultLanguage);
		$strings = $db->getAll($_GET['lang']);
		echo '
<form method="POST" action="' . $_SERVER['REQUEST_URI'] . '">
<table style="border: 1px #CCC solid">
	<tr>
		<td>String</td>
		<td>
';
		echo $languages[$defaultLanguage];
		echo <<<HTML
		</td>
		<td>
HTML;
		echo $languages[$_GET['lang']];
		echo <<<HTML
		</td>
		<td>Formatted?</td>
	</tr>
HTML;

		foreach ($defStrings as $k => $defString) {
			echo "<tr>";
			echo "<td>" . $defString['name'] . "</td>";
			echo "<td><textarea cols=\"50\" rows=\"5\">" . $defString['text'] . "</textarea></td>";
			$trString = isset($strings[$defString['name']]) ? $strings[$defString['name']]['text'] : "";
			echo "<td><textarea cols=\"50\" rows=\"5\">" . $trString . "</textarea></td>";
			$checked = $defString['formatted'] ? "checked" : "";
			echo "<td><input type=\"checkbox\" name=\"formatted_" . $k . "\" " . $checked . "/></td>";
			echo "</tr>";
		}
		echo "</table></form>";
	}
}