<?php

preg_match("#^([^\.]+)\.html#", $_GET['args'], $matches);
$action = isset($matches[1]) ? $matches[1] : '';
switch ($action) {
case 'register':
	$showForm = true;

	if (isset($_POST['submit'])) {
		if (!empty($_POST['login']) && !empty($_POST['password'])) {
			$db = new UsersDbAdapter();
			$targetUser = $db->getUser($_POST['login']);
			if ($targetUser != null) {
				echo "<p>Login already in use. Please choose a different one.</p>";
			} else if (!$db->registerUser($_POST['login'], $_POST['password'], $_POST['email'])) {
				echo "<p>Unable to create account</p>";
			} else {
				$GLOBALS['user'] = $db->getUser($_POST['login']);
				$_SESSION['userId'] = $GLOBALS['user']->id;
				header("Location: " . getInstallPath() . "/");
			}
		} else {
			echo "<p>Please fill the form appropriately</p>";
		}
	}

	if ($showForm) {
		$login = isset($_POST['login']) ? $_POST['login'] : '';
		$email = isset($_POST['email']) ? $_POST['email'] : '';
		echo <<<HTML
<form method="post">
<h3>Register account</h3>
<p>Please fill in this form in order to create your account.</p>
<input type="text" name="login" value="$login" placeholder="Login" /><br />
<input type="password" name="password" placeholder="Password" /><br />
<input type="password" placeholder="Password confirmation" /><span id="password_check" /><br />
<input type="email" name="email" value="$email" placeholder="Email address" /> Optional. If you ever forget your password.<br />
<input type="submit" name="submit" value="Register" />
</form>
HTML;
	}
	break;

case 'forgot':
	echo "<p>You should not forget your password.</p>";
	break;

case 'login':
	$pw = isset($_POST['password']) ? $_POST['password'] : '';
	$db = new UsersDbAdapter();
	$realHash = $db->getUserPassword(isset($_POST['login']) ? $_POST['login'] : '');
	if (password_verify($pw, $realHash)) {
		$_SESSION['userId'] = $db->getUser($_POST['login'])->id;
		header('Location: ' . getInstallPath() . '/');
	} else {
		echo "<p>Invalid user or password.</p>";
	}
	break;

case 'logout':
	unset($_SESSION['userId']);
	header('Location: ' . getInstallPath() . '/');
	break;

default:
	echo "<p>Welcome, " . $GLOBALS['user']->getName() . "</p>";
	break;
}

