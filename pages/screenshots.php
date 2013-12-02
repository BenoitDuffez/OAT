<?php

include "db/Context.php";
include "db/Screenshots.php";

$cdb = new ContextDbAdapter();
$contexts = $cdb->loadAll();
$db = new ScreenshotsDbAdapter();

// Handle screenshot upload
if (isset($_FILES['screenshot'])) {
	switch ($_FILES['screenshot']['type']) {
		case 'image/png':
		case 'image/jpg':
		case 'image/jpeg':
			do {
				$name = "img_" . substr(base64_encode(time() . rand()), 0, 16);
				$destination = "./screenshots/" . $name; 
			} while (file_exists($destination));
			move_uploaded_file($_FILES['screenshot']['tmp_name'], $destination);
			chmod($destination, "0444");
			if (!$db->add($name, $_POST['screenshot_context'])) {
				unlink($destination);
			} else {
				echo "<p>Screenshot added</p>";
			}
			break;

		default:
			echo "<p>This file format (" . $_FILES['screenshot']['type'] . ") is not supported.</p>";
			break;
	}
}

/* TODO: Validate end of upload:
POST=array(2) { ["screenshot_context"]=> string(1) "1" ["files"]=> array(1) { [0]=> string(16) "IMG_1247 (2).JPG" } }

we will need a cronjob to remove unassigned screenshots


*/

// Display upload form
if ($contexts != null && count($contexts) > 0) {
	echo <<<HTML

  <div id="screenshot_uploader">
    <form method="post" action="%PATH%/screenshots/">
      <h3>Upload a new screenshot</h3>
      <div id="screenshot_files" class="files"></div>
      <span class="btn btn-success fileinput-button">
        <i class="glyphicon glyphicon-plus"></i>
        <span>Select image...</span>
        <!-- The file input field used as target for the file upload widget -->
        <input id="fileupload" type="file" name="files[]">
      </span>
      <div id="screenshot_upload">
        <p>Related context:<br />
        <select name="screenshot_context">
HTML;
	foreach ($contexts as $context) {
		echo '<option value="' . $context->id . '">' . $context->name . '</option>';
	}
	echo <<<HTML
        </select>
      </div>
      <input id="screenshot_upload_button" type="submit" value="Add screenshot" disabled/>
      <div id="progress"><div class="progress-bar-success"></div></div>
    </form>

    <script>
/*jslint unparam: true */
/*global window, $ */
$(function () {
    'use strict';
    $('#fileupload').fileupload({
        url: '%PATH%/upload/',
        dataType: 'json',
        done: function (e, data) {
            $.each(data.result.files, function (index, file) {
/*
file contains:
deleteType: "DELETE"
deleteUrl: "http://oat.bicou.net/upload/?file=IMG_27112013_175042%20%282%29.png"
name: "IMG_27112013_175042 (2).png"
size: 23931
thumbnailUrl: "http://oat.bicou.net/upload/files/thumbnail/IMG_27112013_175042%20%282%29.png"
type: "image/png"
url: "http://oat.bicou.net/upload/files/IMG_27112013_175042%20%282%29.png"
*/
                $('#screenshot_files').append('<img class="screenshot" src="'+file.thumbnailUrl+'" />');
                $('#screenshot_upload').append('<input type="hidden" name="files[]" value="'+file.name+'" />');
                $('#screenshot_upload_button').removeAttr('disabled');
                $('#progress .progress-bar-success').css('width', '0%');
            });
        },
        progressall: function (e, data) {
            var progress = parseInt(data.loaded / data.total * 100, 10);
            $('#progress .progress-bar-success').css(
                'width',
                progress + '%'
            );
        }
    }).prop('disabled', !$.support.fileInput)
      .parent().addClass($.support.fileInput ? undefined : 'disabled');
});
    </script>
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

// Display screenshots
$screens = $db->getAll();
$prevContext = -1;
foreach ($screens as $screen) {
	if ($prevContext != $screen['context_id']) {
		if ($prevContext > 0) {
			echo '</div>';
		}
		echo '<div class="context"><p>Screenshots related to <b>' . $screen['context'] . '</b></p>';
	}
	$prevContext = $screen['context_id'];
	echo '<p><img class="screenshot" src="%PATH%/screenshots/' . $screen['name'] . '" /></p>';
}
echo '</div>';
