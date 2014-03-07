<?php

preg_match("#^([^\.]+)\.html#", $_GET['args'], $matches);
$action = isset($matches[1]) ? $matches[1] : '';
switch ($action) {
case 'register':
	$showForm = true;

	if (isset($_POST['submit'])) {
		if (!empty($_POST['login']) && !empty($_POST['password']) && !empty($_POST['email'])) {
			$db = new UsersDbAdapter();
			$targetUser = $db->getUser($_POST['login']);
			print_r($targetUser);
			if ($targetUser != null) {
				echo "<p>Login already in use. Please choose a different one.</p>";
			} else if (!$db->registerUser($_POST['login'], $_POST['password'], $_POST['email'])) {
				echo "<p>Unable to create account</p>";
			} else {
				$GLOBALS['user'] = $db->getUser($_POST['login']);
				$_SESSION['user'] = serialize($GLOBALS['user']); 
				header("Location: " . getInstallPath() . "/");
			}
		}
	}

	if ($showForm) {
		$form = <<<HTML
<form method="post">
<h3>Register account</h3>
<p>Please fill in this form in order to create your account.</p>
<input type="text" name="login" placeholder="Login" /><br />
<input type="password" name="password" placeholder="Password" /><br />
<input type="password" placeholder="Password confirmation" /><span id="password_check" /><br />
<input type="email" name="email" placeholder="Email address" /> Optional. If you ever forget your password.<br />
<input type="submit" name="submit" value="Register" />
</form>
HTML;
		$login = isset($_POST['login']) ? $_POST['login'] : '';
		$email = isset($_POST['email']) ? $_POST['email'] : '';
		echo str_replace(array('$login', '$email'), array($login, $email), $form);
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
		$_SESSION['user'] = serialize($db->getUser($_POST['login']));
		header('Location: ' . getInstallPath() . '/');
	} else {
		echo "<p>Invalid user or password.</p>";
	}
	break;

case 'logout':
	unset($_SESSION['user']);
	header('Location: ' . getInstallPath() . '/');
	break;

default:
	echo "<p>Welcome, " . $GLOBALS['user']->getName() . "</p>";
	break;
}

