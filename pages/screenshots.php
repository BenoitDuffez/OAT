<?php

include "db/Context.php";
include "db/Screenshots.php";

$cdb = new ContextDbAdapter();
$contexts = $cdb->loadAll();

// TODO: we will need a cronjob to remove unassigned screenshots

// Display screenshot upload form
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

echo '<div id="screenshots_container"></div>';