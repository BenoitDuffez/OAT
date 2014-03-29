<?php

header('Content-Type: application/json');

include "init.php";
include "db/DbAdapter.php";
include "db/Config.php";

$cfg = new Config();
switch ($_GET['action']) {
	case 'getString':
	{
		include_once "db/Translations.php";
		$db = new Translations();
		$data = array();
		$data['source'] = $db->getString($cfg->getDefaultLanguage(), $_GET['name']);
		$data['destination'] = $db->getString($_GET['lang'], $_GET['name']);
		echo json_encode($data);
		break;
	}

	case 'addString':
	{
		include_once "db/Translations.php";
		$db = new Translations();
		$req = json_decode($HTTP_RAW_POST_DATA);
		$res = $db->addTranslation($req->name, $req->lang, $req->text);
		$json = array();
		if ($res === true) {
			$json['result'] = 'OK';
		} else if ($res === false) {
			$json['result'] = 'KO';
			$json['reason'] = 'Unknown PDO error';
		} else {
			$json['result'] = 'KO';
			$json['reason'] = $res->getMessage();
		}
		echo json_encode($json);
		break;
	}

	case 'getScreenshots':
	{
		include "db/Context.php";
		$ctx = new ContextDbAdapter();
		if (isset($_GET['name']) && strlen(trim($_GET['name'])) > 0) {
			echo json_encode($ctx->getScreenshots($_GET['name']));
		} else {
			echo json_encode($ctx->getAllScreenshots());
		}
		break;
	}

	case 'addScreenshots':
	{
		include "db/Screenshots.php";
		$db = new ScreenshotsDbAdapter();

		$req = json_decode($HTTP_RAW_POST_DATA);
		$result = array();

		// Handle screenshot upload
		if (isset($req->context_id) && isset($req->screenshots) && is_array($req->screenshots) && count($req->screenshots)) {
			foreach ($req->screenshots as $file) {
				// $file->name is just the base name of the file
				// it is located in ./upload/files/
				if (!$db->add($file->name, $req->context_id)) {
					unlink("./upload/files/" . $file->name);
					$result['status'] = 'KO';
					$result['reason'] = 'Unable to add file to DB: ' . $db->getLastException()->getMessage();
				} else {
					$result['status'] = 'OK';
				}
			}
		} else {
			$result['status'] = 'KO';
			$result['reason'] = 'Unable to understand browser query: needed context_id and screenshots in POST body, received: ' . print_r($req, true);
		}
		echo json_encode($result);
		break;
	}
}
