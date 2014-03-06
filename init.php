<?php

// Main includes
include "config.db.php";
include "lang.php";
include "util.php";

// Init DB connection
try {
	$pdo = @new PDO("mysql:host=$_db_host;dbname=$_db_name", $_db_user, $_db_pass);
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
	L("Unable to connect to DB server:", $e);
	$pdo = null;
}

error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
ini_set('display_errors', '1');

// Init session
session_start();

