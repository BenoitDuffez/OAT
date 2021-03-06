<?php

include "db/Strings.php";
include "db/Translations.php";

$str = new StringsDbAdapter();
$tr = new Translations();
$files = $str->getFileNames();
$langs = $tr->getLangs();

setHtmlTitle('Strings export to XML files - OAT');

echo '
<div>
  <p>Export
  <select id="filename">
';
foreach ($files as $file) {
	echo '<option value="' . $file['filename'] . '">' . $file['filename'] . '</option>';
}
echo '
  </select>
  in
  <select id="lang">';
foreach ($langs as $lang => $foo) {
	echo '<option value="' . $lang . '">' . $languages[$lang] . '</option>';
}
echo <<<HTML
  </select>
  <input type="button" onclick="javascript:exportStringsToFile();" value="Download" />
  <iframe id="downloader" style="display: none;"></iframe>
</div>
HTML;

addHtmlHeader('<script src="%PATH%/static/export.js"></script>');
