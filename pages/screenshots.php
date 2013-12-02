<?php

include "db/Context.php";
include "db/Screenshots.php";

$cdb = new ContextDbAdapter();
$contexts = $cdb->loadAll();
$db = new ScreenshotsDbAdapter();

/* TODO: Validate end of upload:
POST=array(2) { ["screenshot_context"]=> string(1) "1" ["files"]=> array(1) { [0]=> string(16) "IMG_1247 (2).JPG" } }

TODO: we will need a cronjob to remove unassigned screenshots


*/

// Display upload form
if ($contexts != null && count($contexts) > 0) {
	echo <<<HTML

  <div id="screenshot_uploader">
    <h3>Upload a new screenshot</h3>
    <div id="screenshot_files" class="files"></div>
    <span class="btn btn-success fileinput-button">
      <i class="glyphicon glyphicon-plus"></i>
      <span>Select image...</span>
      <input id="fileupload" type="file" name="files[]">
    </span>
    <div id="screenshot_upload">
      <p>Related context:<br />
      <select id="screenshot_context" name="screenshot_context">
HTML;
	foreach ($contexts as $context) {
		echo '<option value="' . $context->id . '">' . $context->name . '</option>';
	}
	echo <<<HTML
      </select>
    </div>
    <input id="screenshot_upload_button" type="button" value="Add screenshot" disabled onclick="javascript:addScreenshots();"/>
    <div id="progress"><div class="progress-bar-success"></div></div>

    <script src="%PATH%/static/screenshots.js"></script>
  </div>
HTML;
} else {
	echo "<div>Before you can upload screenshots, you must create a context.</p>";
}


// Context management
echo "<div>";
$contexts = $cdb->loadAll();
if (isset($_POST['context_name'])) {
	$cdb->add($_POST['context_name']);
	echo "<p>Context added.</p>";
} else {
	echo '
<form method="post" action="' . $_SERVER['REQUEST_URI'] . '">
Context name: <input type="text" name="context_name" />
<input type="submit" value="Add context"/>
</form>';
}
echo "</div>";

echo '<div id="screenshots_container"></div>';