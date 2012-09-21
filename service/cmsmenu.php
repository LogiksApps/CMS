<?php
if(!defined('ROOT')) exit('No direct script access allowed');
checkServiceSession();

if(isset($_REQUEST["action"]) && isset($_REQUEST['forsite'])) {
	if(!checkUserSiteAccess($_REQUEST['forsite'],false)) {
		echo "<li class='nosubmenu'><a href='#' onclick=\"$"."tabs.tabs('select',0);\"><img src='".loadMedia("icons/sidebar/home.png")."' />Dashboard</a></li>";
		exit();
	}
	
	$dbFile=ROOT.APPS_FOLDER.$_REQUEST['forsite']."/config/db.cfg";
	$config=parseConfigFile($dbFile);
	$dbCon=null;
	if(file_exists($dbFile)) {
		$dbCon=new Database($config["DB_DRIVER"]['value']);
		$dbCon->connect($config["DB_USER"]['value'],$config["DB_PASSWORD"]['value'],$config["DB_HOST"]['value'],$config["DB_DATABASE"]['value']);
	}
	//$folders=loadFolderConfig();
	
	if($_REQUEST["action"]=="loadmenu") {
		$SHOW_EMPTY_HOLDERS=getSiteSettings("SHOW_EMPTY_HOLDERS","false","SIDEBAR");
		
		$dbTables=getDBTableList($dbCon,$config);
		
		echo "<li class='nosubmenu'><a href='#' onclick=\"$"."tabs.tabs('select',0);\"><img src='".loadMedia("icons/sidebar/home.png")."' />Dashboard</a></li>";
		loadModule("navigator",
				array(
					"site"=>"cms",
					"dbtable"=>_dbtable("admin_links",true),
					"dbLink"=>getSysDBLink(),
					"menuid"=>"cms_menu",//getConfig("DEFAULT_NAVIGATION"),
					"tableList"=>$dbTables,
					//"moduleList"=>$modules,
					"showEmptyHolders"=>"$SHOW_EMPTY_HOLDERS")
			);
	}
	
	if($dbCon!=null) $dbCon->close();
}
//printErr("WrongFormat");
exit();
function getDBTableList($dbCon,$config) {
	$arr=array();
	if($dbCon==null) return $arr;
	$arr1=$dbCon->getTableList();
	$arr2=_db(true)->getTableList();
	
	$ss=$config["DB_APPS"]['value']."_";
	$ssln=strlen($ss);
	foreach($arr1 as $n=>$a) {
		if(substr($a,0,$ssln)==$ss) {
			$arr1[$n]=substr($a,$ssln);
		}
	}
	$ss=$GLOBALS['DBCONFIG']["DB_SYSTEM"]."_";
	$ssln=strlen($ss);
	foreach($arr2 as $n=>$a) {
		if(substr($a,0,$ssln)==$ss) {
			$arr2[$n]=substr($a,$ssln);
		}
	}
	$arr=array_merge($arr1,$arr2);
	return $arr;
}
function getModuleList($folders) {
	$modules=ROOT.APPS_FOLDER.$_REQUEST['forsite']."/{$folders['APPS_PLUGINS_FOLDER']}/modules/";
	$modules=scandir($modules);
	unset($modules[0]);unset($modules[1]);
	return $modules;
}
?>
