<?php

// Context management
$ctx = new ContextDbAdapter();
$contexts = $ctx->loadAll();
var_dump($contexts);

if (isset($_POST['context_name'])) {
	$ctx->add($_POST['context_name']);
}
echo <<<HTML
<form method="post" action="/OAT/">
Context name: <input type="text" name="context_name" />
<input type="submit" value="Add context"/>
</form>
HTML;


