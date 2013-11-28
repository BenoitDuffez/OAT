<?php

$cdb = new ContextDbAdapter();
$contexts = $cdb->loadAll();
$db = new ScreenshotsDbAdapter();

echo "<pre>_POST:" . print_r($_POST, true) . "</pre>";
echo "<pre>_FILES:" . print_r($_FILES, true) . "</pre>";

if (isset($_FILES['screenshot'])) {
	switch ($_FILES['screenshot']['type']) {
		case 'image/png':
		case 'image/jpg':
		case 'image/jpeg':
			$destination = "./screenshots/" . str_replace("..", "_", $_FILES['screenshot']['name']);
			move_uploaded_file($_FILES['screenshot']['tmp_name'], $destination);
			if (!$db->add($_FILES['screenshot']['name'], $_POST['screenshot_context'])) {
				unlink($destination);
			}
			break;

		default:
			echo "<p>This file format (" . $_FILES['screenshot']['type'] . ") is not supported.</p>";
			break;
	}
} else if ($contexts != null && count($contexts) > 0) {
	// Upload form
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
	echo "<div>Before you can upload screenshots, you must create a context.</p>";
}

