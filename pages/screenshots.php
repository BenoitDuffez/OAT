<?php

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


