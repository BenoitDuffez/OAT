<?php

include "db/Context.php";
include "db/Config.php";
include "db/Strings.php";
include "db/Links.php";

// Load helpers
$ctx = new ContextDbAdapter();
$links = new LinksDbAdapter();
$cfg = new Config();
$sdb = new StringsDbAdapter();

setHtmlTitle('Contexts management - OAT');

// List contexts
$contexts = $ctx->loadAll();
if (isset($_POST['context_name'])) {
	$ctx->add($_POST['context_name']);
	echo "<p>Context added.</p>";

	// Allow that context to be in the list right away
	$contexts = $ctx->loadAll();
}

// Show add new context form
echo <<<HTML
<form id="context" method="post" action="%PATH%/contexts/">
  <div>
    <h3>Create a new context</h3>
    <p>Context name:</p>
    <input type="text" name="context_name" />
    <input type="submit" value="Add context"/>
  </div>
</form>
HTML;

// Handle new string(s) / context linkage
if (isset($_POST['context_id']) && isset($_POST['strings']) && is_array($_POST['strings'])) {
	$contextId = 0 + $_POST['context_id'];
	foreach ($_POST['strings'] as $stringName => $selected) {
		if ($selected) {
			$links->addLink(DbAdapter::TABLE_CONTEXTS, $_POST['context_id'], DbAdapter::TABLE_STRINGS, $stringName);
		}
	}
	echo "<p>String" . (count($_POST['strings']) == 1 ? "" : "s") . " linked to context.</p>";
} // Display the form
else {
	echo <<<HTML
  <form id="link" method="post" action="%PATH%/contexts/">
    <div>
      <h3>Link a set of strings with a context</h3>
      <p>Please select the target context:</p>
      <select name="context_id">
HTML;
	foreach ($contexts as $context) {
		echo '<option value="' . $context->id . '">' . $context->name . '</option>';
	}
	echo <<<HTML
      </select>
      <p>Select the strings to link with that context:</p>
      <div id="strings_list">
HTML;
	foreach ($sdb->getAll($cfg->getDefaultLanguage()) as $context) {
		$checked = isset($_GET['string']) && $context['name'] == $_GET['string'] ? 'checked' : '';
		echo '<div style="overflow: hidden;"><input type="checkbox" name="strings[' . $context['name'] . ']" value="true" id="cb_' . $context['name'] . '"' . $checked . '>';
		echo '<label for="cb_' . $context['name'] . '">' . $context['name'] . '</label></div>';
	}
	echo <<<HTML
      </div>
      <input type="submit" value="Link String(s) with context"/>
    </div>
  </form>
HTML;
}

