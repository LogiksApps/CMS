<?php
if(!defined('ROOT')) exit('No direct script access allowed');
checkServiceSession();
checkUserSiteAccess($_REQUEST['forsite'],true);
user_admin_check(true);

if(isset($_REQUEST["action"]) && isset($_REQUEST["forpage"]) && strlen($_REQUEST["forpage"])>0) {
	checkUserSiteAccess($_REQUEST['forsite'],true);
		
	loadModule("dbcon");
	$folders=loadFolderConfig();
	
	if(isset($_REQUEST['encodeURL']) && $_REQUEST['encodeURL']=="true")
		$metaFiles=array(base64_encode($_REQUEST["forpage"]));
	else
		$metaFiles=array($_REQUEST["forpage"]);
	
	if($_REQUEST["action"]=="fetchmeta") {
		foreach($metaFiles as $mf) {
			$metaFile=$_SESSION["APP_FOLDER"]["APPROOT"].$_SESSION["APP_FOLDER"]["APPS_CONFIG_FOLDER"]."meta/{$mf}.json";
			
			if(!is_dir(dirname($metaFile))) {
				if(mkdir(dirname($metaFile),0777,true)) {
					chmod(dirname($metaFile),0777);
				}
			}
			if(file_exists($metaFile) && is_readable($metaFile)) {
				$data=file_get_contents($metaFile);
				echo $data;
			}
		}
		exit();
	}
	elseif($_REQUEST["action"]=="savemeta" && isset($_POST['title'])) {
		foreach($metaFiles as $mf) {
			$metaFile=$_SESSION["APP_FOLDER"]["APPROOT"].$_SESSION["APP_FOLDER"]["APPS_CONFIG_FOLDER"]."meta/{$mf}.json";
			
			foreach($_POST as $a=>$b) {
				$_POST[$a]=str_replace("\"","'",$b);
			}
			$json=json_encode($_POST);
			file_put_contents($metaFile,$json);
		}
		exit();
	}
}
?>
