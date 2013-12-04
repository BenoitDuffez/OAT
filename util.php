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
	fprintf($log, "%s %s: %s\n", date(DATE_ATOM), $exception == null ? "warning" : "error", $msg);
	if ($exception != null) { // TODO: if ($severity > WARNING)
		fprintf($log, "<br/>Stack trace:<ul>");
		foreach (debug_backtrace() as $k => $v) {
			fprintf($log, "<li>$k: " . $v['file'] . ":" . $v['line'] . "<br />");
		}
		fprintf($log, "<br/>Exception: " . $exception->getMessage());
		fprintf($log, "</div>");
	}
	fclose($log);
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

