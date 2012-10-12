<?php
if(!defined('ROOT')) exit('No direct script access allowed');
checkServiceSession();
checkUserSiteAccess($_REQUEST['forsite'],true);
user_admin_check(true);

if(isset($_REQUEST["action"]) && isset($_REQUEST["forpage"]) && strlen($_REQUEST["forpage"])>0) {
	checkUserSiteAccess($_REQUEST['forsite'],true);
		
	loadModule("dbcon");
	$folders=loadFolderConfig();
	
	$metaFiles=getMetaFiles($_REQUEST["forpage"]);
	
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
function getMetaFiles($metaReq="") {
	if(strlen($metaReq)>0) {
		if(strpos("#".$metaReq,"=")>1) {
			$pgArr=explode("&",$metaReq);
			foreach($pgArr as $n=>$a) {
				$a=explode("=",$a);
				if(!isset($a[1])) $a[1]="";
				unset($pgArr[$n]);
				$pgArr[$a[0]]=$a[1];
			}
			$metaReq=$pgArr;
			if(!isset($metaReq["page"])) {
				$metaReq["page"]=md5($_REQUEST["forpage"]);
			}
		} else {
			$metaReq=array("page"=>$metaReq);
		}
		unset($metaReq['site']);unset($metaReq['forsite']);
		
		$metaFile1=$metaReq["page"];
		
		$href=array();
		foreach($metaReq as $a=>$b) {array_push($href,"$a=$b");}
		$metaFile2=md5(implode("&",$href));
		$metaFile2=(implode("&",$href));
		
		return array($metaFile2,$metaFile1);
	}
	return "";
}
?>
