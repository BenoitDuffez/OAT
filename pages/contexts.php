<?php

// Context management
$ctx = new ContextDbAdapter();
$contexts = $ctx->loadAll();

if (isset($_POST['context_name'])) {
	$ctx->add($_POST['context_name']);
	echo "<p>Context added.</p>";
} else {
	echo '
<form id="context" method="post" action="' . $_SERVER['REQUEST_URI'] . '">
Context name: <input type="text" name="context_name" />
<input type="submit" value="Add context"/>
</form>';
}

include "db/Links.php";
$links = new LinksDbAdapter();
$sdb = new StringsDbAdapter();

if (isset($_POST['context_id']) && isset($_POST['strings']) && is_array($_POST['strings'])) {
	$contextId = 0 + $_POST['context_id'];
	foreach ($_POST['strings'] as $stringName => $selected) {
		if ($selected) {
			$links->addLink(DbAdapter::TABLE_CONTEXTS, $_POST['context_id'], DbAdapter::TABLE_STRINGS, $stringName);
		}
	}
	echo "<p>String" . (count($_POST['strings']) == 1 ? "" : "s") . " linked to context.</p>";
} else {
	echo '
<form id="link" method="post" action="' . $_SERVER['REQUEST_URI'] . '">
<p>Context: <select name="context_id">';
	foreach ($contexts as $context) {
		echo '<option value="' . $context->id . '">' . $context->name . '</option>';
	}
	echo '
</select></p>
<p>Strings:</p>
<ul>';
	foreach ($sdb->getAll($cfg->getDefaultLanguage()) as $context) {
		$checked = isset($_GET['string']) && $context['name'] == $_GET['string'] ? 'checked' : '';
		echo '<li><input type="checkbox" name="strings[' . $context['name'] . ']" value="true" id="cb_' . $context['name'] . '"'.$checked.'>';
		echo '<label for="cb_' . $context['name'] . '">' . $context['name'] . '</label></li>';
	}
}
echo '
</ul>
<input type="submit" value="Link String(s) with context"/>
</form>';

