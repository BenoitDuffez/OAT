<?php

// TODO: severity
function L($msg, $exception = null) {
	if (!file_exists("./log")) {
		mkdir("./log");
	}
	if (!file_exists("./log")) {
		echo "Fatal error: can't create log folder while trying to log: $msg";
		return;
	}
	$log = fopen("./log/application.log", "a+");
	if (!$log) {
		echo "Fatal error: can't create/append log file while trying to log: $msg";
		return;
	}
	logLine($log, "%s: %s", $exception == null ? "warning" : "error", $msg);
	if ($exception != null) { // TODO: if ($severity > WARNING)
		logLine($log, "Stack trace:");
		foreach (debug_backtrace() as $k => $v) {
			logLine($log, "$k: " . $v['file'] . ":" . $v['line']);
		}
		logLine($log, "Exception: " . $exception->getMessage());
	}
	fclose($log);
}

function logLine($file, $line) {
	$args = func_get_args();
	array_shift($args); // $file
	$formattedLine = count($args) > 1 ? call_user_func_array('sprintf', $args) : $line;
	fprintf($file, "%s [%s] %s\n", date(DATE_ATOM), $GLOBALS['_oat_env'], $formattedLine);
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

