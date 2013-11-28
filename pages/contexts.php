<?php

// Context management
$ctx = new ContextDbAdapter();
$contexts = $ctx->loadAll();
var_dump($contexts);

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

