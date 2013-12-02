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

    <script>
    var uploadedFiles = [];

    function addScreenshots() {
		var ctx = $('#screenshot_context').val();
		$.ajax({
			type: "POST",
			url: "%PATH%/ajax.php?action=addScreenshots",
			data: JSON.stringify({ context_id: ctx, screenshots: uploadedFiles }),
			contentType: "application/json; charset=utf-8",
			dataType: "json",
			success: function(data) {
				$('#screenshot_files').empty();
				$('#screenshot_upload_button').attr('disabled', 'disabled');
				if (data.status=='KO') {
					alert("Couldn't save screenshot: "+data.reason);
				}
				refreshScreenshots();
			},
			failure: function(errMsg) { alert(errMsg); }
		});
    }

    function refreshScreenshots() {
    	$.getJSON("%PATH%/ajax.php?action=getScreenshots", null, function(result) {
    	console.log(result);
    	});
    }

    $(document).ready(function(){refreshScreenshots();});

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
				if (file.error!=undefined) {
					$('#screenshot_files').append('Error: unable to upload file');
				}else{
					$('#screenshot_files').append('<img class="screenshot" src="'+file.thumbnailUrl+'" />');
					window.uploadedFiles.push(file);
					$('#screenshot_upload_button').removeAttr('disabled');
					$('#progress .progress-bar-success').css('width', '0%');
                }
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
