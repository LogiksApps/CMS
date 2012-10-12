<?php
if(!defined('ROOT')) exit('No direct script access allowed');
checkServiceSession();
checkUserSiteAccess($_REQUEST['forsite'],true);
user_admin_check(true);

if(isset($_REQUEST["action"])) {
	loadModule("dbcon");
	$dbCon=getDBControls();
	$folders=loadFolderConfig();
	
	$uiconf=parseConfigFile(ROOT.APPS_FOLDER.$_REQUEST['forsite']."/config/uiconf.cfg");
	$dbConfig=parseConfigFile(ROOT.APPS_FOLDER.$_REQUEST['forsite']."/config/db.cfg");
	
	loadModuleLib("menuedit","MenuTree");
	
	if($_REQUEST["action"]=="sitemaptree") {
		if($dbCon==null) {
			exit("<h3>No Menu System.</h3>");
		}
		
		$menuid=$uiconf['DEFAULT_NAVIGATION']['value'];
		$subMenus=ROOT.APPS_FOLDER.$_REQUEST['forsite']."/config/menugenerator.json";
		
		$useCategory=true;
		
		$dbTables=getDBTableList($dbCon,$dbConfig);
		$modules=getModuleList($folders);
		
		echo "<li><h2 title='".ucwords($menuid)."'>MenuBar</h2><ul>";
		loadModule("navigator",array(
					"menuid"=>$menuid,
					"site"=>$_REQUEST['forsite'],
					"dbLink"=>$dbCon,
					"dbtable"=>_dbtable("links"),
					"tableList"=>$dbTables,
					"moduleList"=>$modules,
					"menuAutoGroupFile"=>$subMenus,
					"showID"=>true,
					"allGenerators"=>true,
					"useCategory"=>$useCategory,
					"noScript"=>true
				));
		echo "</ul></li>";
		$sql="SELECT menuid FROM "._dbTable("links")." WHERE site='{$_REQUEST['forsite']}' GROUP BY menuid";
		$result=$dbCon->executeQuery($sql);
		$data=_dbData($result);
		$dbCon->freeResult($result);
		foreach($data as $a) {
			if(strlen($a['menuid'])>0 && $a['menuid']!=$menuid) {
				$menuid=$a['menuid'];
				echo "<li><h2>".ucwords($menuid)."</h2><ul>";
				loadModule("navigator",array(
							"menuid"=>$menuid,
							"site"=>$_REQUEST['forsite'],
							"dbLink"=>$dbCon,
							"dbtable"=>_dbtable("links"),
							"tableList"=>$dbTables,
							"moduleList"=>$modules,
							"showID"=>true,
							"useCategory"=>$useCategory,
							"noScript"=>true,
						));
				echo "</ul></li>";
			}
		}
		if($dbCon!=null) $dbCon->close();
		exit();
	} elseif($_REQUEST["action"]=="properties") {
		$props=getLinkProperties();
		$mpath=checkModule("sitemap");
		if(strlen($mpath)>0) {
			$mpath=dirname($mpath)."/props/{$props['src']}.php";
		}
		if(file_exists($mpath)) {
			echo "<table id=propertiesEditor class='datatable' width=70% border=0 cellpadding=2 cellspacing=0>";
			include $mpath;
			echo "</table>";
		}
		exit();
	}
	elseif($_REQUEST["action"]=="deletepage") {
		$props=getLinkProperties();
		//printArray($props);
		exit();
	}
	elseif($_REQUEST["action"]=="editor") {
		$props=getLinkProperties();
		$url="";
		if($props['src']=="links") {
			$page=findPage($props['page'],$folders);
			if(strlen($page)>0) {
				//$url="../index.php?site=".SITENAME."&forsite={$_REQUEST['forsite']}&page=";
			} else {
				$url="../index.php?site=".SITENAME."&forsite={$_REQUEST['forsite']}&page=listpages";
			}
		} elseif($props['src']=="forms") {
			$url="../index.php?site=".SITENAME."&forsite={$_REQUEST['forsite']}&page=modules&mod=datacontrols&mode=forms&editor=forms&id={$props['rel']}";
		} elseif($props['src']=="reports") {
			$url="../index.php?site=".SITENAME."&forsite={$_REQUEST['forsite']}&page=modules&mod=datacontrols&mode=reports&editor=datatable&id={$props['rel']}";
		} elseif($props['src']=="views") {
			$url="../index.php?site=".SITENAME."&forsite={$_REQUEST['forsite']}&page=modules&mod=datacontrols&mode=views&editor=template&id={$props['rel']}";
		} elseif($props['src']=="search") {
			$url="../index.php?site=".SITENAME."&forsite={$_REQUEST['forsite']}&page=modules&mod=datacontrols&mode=search&editor=search&id={$props['rel']}";
		}
		if(strlen($url)>0) {
			header("Location:$url");
		} else {
			printErr("NotAcceptable","Sorry, The Page Source Is Not Yet Supported For Source Mode.");
		}
		exit();
	}
	elseif($_REQUEST["action"]=="preview") {
		$url="../index.php?site={$_REQUEST['forsite']}&popup=true&".$_REQUEST["href"];
		header("Location:$url");
		exit();
	}
	elseif($_REQUEST["action"]=="manage") {
		$props=getLinkProperties();
		$url="";
		if($props['src']=="links") {
			$url="../index.php?site=".SITENAME."&forsite={$_REQUEST['forsite']}&page=listpages";
		} elseif($props['src']=="forms") {
			$url="../index.php?site=".SITENAME."&forsite={$_REQUEST['forsite']}&page=modules&mod=datacontrols&list=forms";
		} elseif($props['src']=="reports") {
			$url="../index.php?site=".SITENAME."&forsite={$_REQUEST['forsite']}&page=modules&mod=datacontrols&list=reports";
		} elseif($props['src']=="views") {
			$url="../index.php?site=".SITENAME."&forsite={$_REQUEST['forsite']}&page=modules&mod=datacontrols&list=views";
		} elseif($props['src']=="search") {
			$url="../index.php?site=".SITENAME."&forsite={$_REQUEST['forsite']}&page=modules&mod=datacontrols&list=search";
		}
		if(strlen($url)>0) {
			header("Location:$url");
		} else {
			printErr("NotAcceptable","Sorry, The Page Source Is Not Yet Supported For Source Mode.");
		}
		exit();
	}
}

function findMedia($media) {
	$media=MenuTree::findMedia($media);
	return $media;
}
function findPage($page,$baseDirs) {
	return "";
}
function getLinkProperties() {
	$href=explode("&",$_REQUEST["href"]);
	foreach($href as $n=>$a) {
		unset($href[$n]);
		$a=explode("=",$a);
		if(count($a)==1) {
			$href[$a[0]]=$a[0];
		} elseif(count($a)==2) {
			$href[$a[0]]=$a[1];
		} elseif(count($a)>2) {
			$sx=$a[0];
			unset($a[0]);
			$href[$sx]=implode("=",$a);
		}
	}
	$props=$href;
	$props["href"]=$_REQUEST["href"];
	$props["src"]=$_REQUEST["src"];
	$props["rel"]=$_REQUEST["rel"];
	return $props;
}
function getDBTableList($dbCon,$config) {
	$arr=array();
	
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
	if(is_dir($modules)) {
		$modules=scandir($modules);
		unset($modules[0]);unset($modules[1]);
		return $modules;
	} else {
		return array();
	}
}
?>
