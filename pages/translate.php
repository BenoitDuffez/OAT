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

		$i = 0;
		foreach ($defStrings as $k => $defString) {
			echo "<tr>";
			echo "<td>" . $defString['name'] . "</td>";
			echo "<td><textarea cols=\"50\" rows=\"5\">" . $defString['text'] . "</textarea></td>";
			if (is_string($strings[$i])) {
				$trString = $strings[$i];
				$checked = "checked";
			} else {
				$trString = $strings[$i]['text'];
				$checked = $defString['formatted'] ? "checked" : "";
			}
			echo "<td><textarea cols=\"50\" rows=\"5\">" . $trString . "</textarea></td>";
			echo "<td><input type=\"checkbox\" name=\"formatted_" . $defString['name'] . "\" " . $checked . "/></td>";
			echo "</tr>";

			$i++;
		}
		echo "</table></form>";
	}
}
