<?php
/**
 * User: bicou
 * Date: 28/11/2013
 * Time: 22:18
 */

function generateLeftMenu($defStrings, $strings) {
	echo '
	<div id="list_strings">
		<h2>App strings</h2>
		<ul>';
	foreach ($defStrings as $k => $defString) {
		$class = isset($strings[$k]) && !is_string($strings[$k]) && strlen(trim($strings[$k]['text'])) > 0 ? 'set' : 'unset';
		echo '<li class="' . $class . '">';
		echo '<a href="#">' . $defString['name'] . '</a></li>';
	}

	echo '
		</ul>
	</div>';
}

function generateForm() {
	global $languages;
	echo '
	<div id="topForm">
		<h2>Translation into ' . $languages[$_GET['lang']] . '</h2>
		<textarea class="readonly"></textarea>
		<textarea class="readwrite"></textarea>
	</div>';
}

function generateContext() {
	echo '
	<div id="context">
		<h2>String context</h2>
        	<div class="scroll-x">
        	   Here’s some content that can scroll vertically
        	</div>
		<div>Here should be listed the contexts and screenshots related to that string</div>
	</div>';
}

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

		echo '<div id="translator">';
		generateLeftMenu($defStrings, $strings);
		generateForm();
		generateContext();
		echo '</div>';

		/*
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
		*/
	}
}
