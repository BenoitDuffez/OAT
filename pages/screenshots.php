<?php

$cdb = new ContextDbAdapter();
$contexts = $cdb->loadAll();
$db = new ScreenshotsDbAdapter();

// Debug
echo "<pre>_POST:" . print_r($_POST, true) . "</pre>";
echo "<pre>_FILES:" . print_r($_FILES, true) . "</pre>";

// Handle screenshot upload
if (isset($_FILES['screenshot'])) {
	switch ($_FILES['screenshot']['type']) {
		case 'image/png':
		case 'image/jpg':
		case 'image/jpeg':
			$destination = "./screenshots/" . str_replace("..", "_", $_FILES['screenshot']['name']);
			move_uploaded_file($_FILES['screenshot']['tmp_name'], $destination);
			chmod($destination, "0444");
			if (!$db->add($_FILES['screenshot']['name'], $_POST['screenshot_context'])) {
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

// Display upload form
if ($contexts != null && count($contexts) > 0) {
	echo '
<form method="post" action="' . $_SERVER['REQUEST_URI'] . '" enctype="multipart/form-data">
Screenshot: <input type="file" name="screenshot" />
Related context: <select name="screenshot_context">';
	foreach ($contexts as $context) {
		echo '<option value="' . $context->id . '">' . $context->name . '</option>';
	}
	echo <<<HTML
</select>
<input type="submit" value="Add screenshot"/>
</form>
HTML;
} else {
//	echo "<div>Before you can upload screenshots, you must create a context.</p>";
}


// Context management
echo "<div>";
$contexts = $cdb->loadAll();
if (isset($_POST['context_name'])) {
	$ctx->add($_POST['context_name']);
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
		echo "<div><p>Screenshots related to <b>" . $screen['context'] . "</b></p>";
	}
	$prevContext = $screen['context_id'];
	echo '<p><img class="screenshot" src="./screenshots/' . $screen['name'] . '" /></p>';
}
