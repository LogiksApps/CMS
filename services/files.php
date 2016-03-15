<?php
if(!defined('ROOT')) exit('No direct script access allowed');

checkServiceAccess();

if(!isset($_REQUEST["action"])) {
	printServiceErrorMsg("NotAcceptable","Action Not Defined.");
}

$_ENV['HIDDEN']=[
		"tmp",
	];

switch ($_REQUEST['action']) {
	case 'listFiles':
		loadHelpers("files");

		$APPPATH=ROOT.APPS_FOLDER.$_GET['forsite']."/";
		printServiceMsg(scanFolderTree($APPPATH));
		break;
	case 'listAllFiles':
		# code...
		break;
}
function scanFolderTree($folder) {
	$files=array();
	$folders=array();
	if(is_dir($folder)) {
		$out=scandir($folder);
		$out=array_splice($out, 2);
		foreach($out as $key => $value) {
			$bf=$folder.$value;
			$bf=str_replace(ROOT.APPS_FOLDER."{$_GET['forsite']}/", "", $bf);
			if(in_array($bf, $_ENV['HIDDEN'])) continue;
			if(is_dir($folder.$value)) {
				$folders[$value]=scanFolderTree($folder.$value."/");
			} else {
				$files[]=$value;
			}
		}
	}
	return array_merge_recursive($folders,$files);
}
?>