<style>
div.error {
	background: #FAA;
	border: 1px dashed red;
	padding: 10px;
	margin: 10px;
}
div.warning {
	border: 1px dashed #CCC;
}
</style>
<?php

error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
ini_set('display_errors', '1');

/**
 * User: bicou
 * Date: 27/11/2013
 * Time: 22:28
 */

// Dumb includes
include "config.db.php";
include "lang.php";
include "util.php";

// Actual code that may need dumb includes
include "DbAdapter.php";
include "Config.php";
include "Context.php";

try {
	$pdo = new PDO("mysql:host=$_db_host;dbname=$_db_name", $_db_user, $_db_pass);
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
	L("Unable to connect to DB server: $e");
	die();
}

$cfg = new Config($pdo);
$ctx = new ContextDbAdapter($pdo);
$contexts = $ctx->loadAll();
var_dump($contexts);

echo "<pre>_POST:" . print_r($_POST, true) . "</pre>";
echo "<pre>_FILES:" . print_r($_FILES, true) . "</pre>";

// Context management
if (isset($_POST['context_name'])) {
	$ctx->add($_POST['context_name']);
}
echo <<<HTML
<form method="post" action="/OAT/">
Context name: <input type="text" name="context_name" />
<input type="submit" value="Add context"/>
</form>
HTML;

// Screenshot management
echo <<<HTML
<form method="post" action="/OAT/">
Screenshot: <input type="file" name="screenshot" />
Related context: <select name="screenshot_context">
HTML;
foreach ($contexts as $context) {
	echo '<option value="' . $context->id . '">' . $context->name . '</option>';
}
echo <<<HTML
</select>
<input type="submit" value="Add screenshot"/>
</form>
HTML;

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
<input type="submit" value="Import XML"/>
</form>
HTML;

