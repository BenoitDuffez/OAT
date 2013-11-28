<?php

function L($msg, $exception = null) {
	echo "<div class=\"error\">Message: $msg";
	echo "<br/>Stack trace:<ul>";
	foreach (debug_backtrace() as $k => $v) {
		echo "<li>$k: ". $v['file'].":".$v['line']."<br />";
	}
	echo "<br/>Exception: " . $exception->getMessage() . "</div>";
}

function utf8_fopen_read($fileName, $encoding) {
	if ($encoding == 'utf-8') {
		return fopen($fileName, "r");
	}
	$fc = iconv($encoding, 'utf-8', file_get_contents($fileName));
	$handle = fopen("php://memory", "rw");
	fwrite($handle, $fc);
	fseek($handle, 0);
	return $handle;
}

