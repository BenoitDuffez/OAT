<?php

include "db/Strings.php";
include "db/Config.php";

$cfg = new Config();
$str = new StringsDbAdapter();
$files = $str->getFileNames($cfg->getDefaultLanguage());
$langs = $str->getLangs();

echo '<div><p>Export
<select id="filename">';
foreach ($files as $file) {
	echo '<option value="'.$file['filename'].'">'.$file['filename'].'</option>';
}
echo '</select>
in
<select id="lang">';
foreach ($langs as $lang => $foo) {
	echo '<option value="'.$lang.'">'.$languages[$lang].'</option>';
}
echo <<<HTML
</select>
<input type="button" onclick="javascript:exportStringsToFile();" value="Download" />
<iframe id="downloader" style="display: none;"/>
HTML;

addHtmlHeader('<script language="javascript" src="%PATH%/static/export.js"></script>');
