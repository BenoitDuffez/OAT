<?php

/**
 * User: bicou
 * Date: 27/11/2013
 * Time: 22:28
 */

include "init.php";
include "db/Users.php";
include "functions.php";

// Build page content
ob_start("dump_html");
readfile("static/header.html");
if ($pdo == null) {
	echo "<p>Service is currently down for maintenance.</p>";
	L("Unable to connect to database server (\$pdo==null).");
} else {
	// Try to identify the user
	if(isset($_SESSION['user'])) {
		$user_menu = '<a href="%PATH%/account/logout.html">Log out</a>';
		$user = new User(unserialize($_SESSION['user']));

		// Refresh from DB
		$db = new UsersDbAdapter();
		$user = $db->getUser($user->name);
	} else {
		$user = User::$ANONYMOUS;
		// display the login form
		$user_menu = <<<HTML
<a href="javascript:$('#login_form').toggle(300);">Log in</a>
<div id="login_form" style="display: none;">
  <form method="post" action="%PATH%/account/login.html">
    <input type="text" name="login" placeholder="Username" /><br />
    <input type="password" name="password" placeholder="Password" /><br />
    <input type="submit" value="Log in" />
    <a href="%PATH%/account/register.html">register</a> /
    <a href="%PATH%/account/forgot.html">forgot</a>
  </form>
</div>
HTML;
	}

	if (isset($user) && is_object($user)) {
		$user_menu = "[ " . $user->getName() . " ] " . $user_menu;
	}

	writeContents();
}
readfile("static/footer.html");
ob_end_flush();


