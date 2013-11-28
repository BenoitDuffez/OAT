<?php

echo "<pre>_POST:" . print_r($_POST, true) . "</pre>";
echo "<pre>_FILES:" . print_r($_FILES, true) . "</pre>";

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

		echo "<p>Parsing ".$_FILES['xmlimport']['name']." using encoding: $encoding</p>";
		$file = utf8_fopen_read($_FILES['xmlimport']['tmp_name'], $encoding);
		$xml = "";
		while ($line = fgets($file)) {
			$xml .= $line;
		}
		fclose($file);

		$strings = simplexml_load_string($xml);
		foreach ($strings as $string) {
			echo "<ul>";
			foreach ($string->attributes() as $attribute => $value) {
				echo "<li>$attribute = $value</li>";
			}
			$rawData = preg_replace(array("#<string[^>]*>#", "#(</string>)#"), '', (string) $string->asXml());
			echo "<li><textarea rows=5 cols=50>$rawData</textarea></li>";
			echo "</ul><hr />";
		}
	}
}
echo <<<HTML
<form method="post" action="/OAT/" enctype="multipart/form-data">
XML file to import: <input type="file" name="xmlimport" />
<select name="language">
HTML;
foreach ($languages as $code => $name) {
	echo '<option value="' . $code . '"' . ($code == 'en' ? ' selected' : '') . '>' . $name . '</option>';
}
echo <<<HTML
</select>
</forum>
HTML;


