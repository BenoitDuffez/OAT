<?php

include "db/Config.php";
include "db/Strings.php";
include_once "db/Translations.php";

setHtmlTitle('Strings import from XML files - OAT');

$cfg = new Config();

// XML import
if (isset($_FILES['xmlimport'])) {
	if ($_FILES['xmlimport']['type'] == 'text/xml') {
		$file = fopen($_FILES['xmlimport']['tmp_name'], "r");
		$encoding = null;
		while ($line = fgets($file)) {
			if (preg_match("/^<\\?xml/", $line)) {
				if (preg_match("/.*encoding=\"([^\"]+)\"/", $line, $matches)) {
					$encoding = strtolower($matches[1]);
					break;
				}
			}
		}
		if ($encoding == null) {
			// Assume UTF8
			$encoding = "utf-8";
			echo "<div>Warning: coudln't detect encoding: assuming UTF-8</div>";
		}
		fclose($file);

		echo "<p>Parsing " . $_FILES['xmlimport']['name'] . " using encoding: $encoding</p>";
		$file = utf8_fopen_read($_FILES['xmlimport']['tmp_name'], $encoding);
		$xml = "";
		while ($line = fgets($file)) {
			$xml .= $line;
		}
		fclose($file);

		$lang = $_POST['language'];
		$xmlStrings = simplexml_load_string($xml); // TODO: hide the default error message and display a nice one instead
		if (!$xmlStrings) {
			echo "<p>Unable to parse XML file. Please double check and validate it.</p>";
		} else {
			$strings = array();
			foreach ($xmlStrings as $string) {
				$formatted = true;
				$name = null;
				$text = null;

				echo "<ul>";
				foreach ($string->attributes() as $attribute => $value) {
					if ($attribute == 'formatted') {
						$formatted = $value == 'true';
					} else if ($attribute == 'name') {
						$name = $value;
					} else {
						L("Unhandled attribute for string: $attribute; value=$value");
					}

					echo "<li>$attribute = $value</li>";
				}
				$type = $string->getName();
				$text = preg_replace(array("#<" . $type . "[^>]*>#", "#(</" . $type . ">)#"), '', (string)$string->asXml());
				echo "<li><textarea rows=5 cols=50>$text</textarea></li>";
				echo "</ul><hr />";

				$strings[] = array('formatted' => $formatted, 'name' => $name, 'text' => $text, 'type' => $type);
			}

			$tr = new Translations();
			$db = new StringsDbAdapter();

			// Update string definitions only if the file is in the development language
			if ($_POST['language'] == $cfg->getDefaultLanguage()) {
				$db->saveAll($strings, $_FILES['xmlimport']['name']);
			}

			// Save the strings in the DB
			$tr->saveAll($_POST['language'], $strings);
		}
	}
}

// Display import form
echo <<<HTML
<form method="post" action="%PATH%/import/" enctype="multipart/form-data">
  <p>
    XML file to import:
    <input type="file" name="xmlimport" />
    <select name="language">
HTML;
foreach ($languages as $code => $name) {
	echo '<option value="' . $code . '"' . ($code == 'en' ? ' selected' : '') . '>' . $name . '</option>';
}
echo <<<HTML
    </select>
    <input type="submit" value="Import XML" />
  </p>
</form>
HTML;


