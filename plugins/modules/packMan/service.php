<?php
if(!defined('ROOT')) exit('No direct script access allowed');
checkServiceAccess();

$apps=_session("siteList");
if(!isset($_GET['forsite']) || !in_array($_GET['forsite'],array_keys($apps))) {
	printServiceMsg(['msg'=>"Sorry, could not find the app or you do not have permission to operate on the app","error"=>true]);
	return;
}

if(!isset($_REQUEST['src'])) $_REQUEST['src']="installed";
if(!isset($_REQUEST['type'])) $_REQUEST['type']="modules";

switch($_REQUEST["action"]) {
	case "categories":
		$result=[];
		
		switch(strtolower($_REQUEST['src'])) {
			case "installed":
				$result=["General"=>"general"];
				break;
			case "repos":
				$result=["General"=>"general"];
				break;
			case "estore":
				$result=["Featured"=>"featured"];
				break;
		}
		printServiceMsg($result);
		break;
	case "getlist":
		$result=[];
		
		switch(strtolower($_REQUEST['src'])) {
			case "installed":
				$result=getPluginList($_REQUEST['type'],false,true);
				break;
			case "repos":
				break;
			case "estore":
				break;
		}
		
		if(isset($_REQUEST['q']) && strlen($_REQUEST['q'])>0) {
			$q=$_REQUEST['q'];
			foreach($result as $a=>$b) {
				if(strpos($a,$q)===false) {
					unset($result[$a]);
				}
			}
		}
		
		printServiceMsg($result);
		break;
	case "packinfo":
		if(isset($_POST['packid'])) {
			$result=getPluginList($_REQUEST['type']);
			
			if(isset($result[$_POST['packid']])) {
				$package=$result[$_POST['packid']];
				unset($package['packid']);
				
				echo "<div class='table-responsive'>";
				echo arrayToHTML($package,"table","table table-striped table-bordered");
				echo "</div>";
			} else {
				echo "<h3 align=center>Package Information Could Be Found</h3>";
			}
		} else {
			echo "<h3 align=center>No Package Could Be Indentified</h3>";
		}
		break;
}
function getPluginList($type,$more=false,$recache=false) {
	$type=strtoupper($type);
	
	if(!$recache && isset($_SESSION['PACKMAN'][$type])) return $_SESSION['PACKMAN'][$type];
	
	$data=[];
	
	$folders=[
		"local"=>CMS_APPROOT.PLUGINS_FOLDER,
		"global"=>ROOT.PLUGINS_FOLDER,
		"dev"=>ROOT."pluginsDev/",
	];
	
	switch($type) {
		case "MODULES":
			foreach($folders as $srcType=>$pluginFolder) {
				if(is_dir($pluginFolder."modules/")) {
					$fs=scandir($pluginFolder."modules/");
					foreach($fs as $a) {
						if($a=="." || $a=="..") continue;
						if(is_dir($pluginFolder."modules/$a/")) {
							$pinfo=fetchPluginInfo($pluginFolder."modules/$a/",'MODULES',$srcType,$more);
							$data[$pinfo['packid']]=$pinfo;
						}
					}
				}
			}
			break;
		case "VENDORS":
			foreach($folders as $srcType=>$pluginFolder) {
				if(is_dir($pluginFolder."vendors/")) {
					$fs=scandir($pluginFolder."vendors/");
					foreach($fs as $a) {
						if($a=="." || $a=="..") continue;
						if(is_dir($pluginFolder."vendors/$a/")) {
							$pinfo=fetchPluginInfo($pluginFolder."vendors/$a/",'VENDORS',$srcType,$more);
							$data[$pinfo['packid']]=$pinfo;
						}
					}
				}
			}
			break;
		case "WIDGETS":
			foreach($folders as $srcType=>$pluginFolder) {
				if(is_dir($pluginFolder."widgets/")) {
					$fs=scandir($pluginFolder."widgets/");
					foreach($fs as $a) {
						if($a=="." || $a=="..") continue;
						if(is_dir($pluginFolder."modules/$a/") || strpos($a,".php")>1) {
							$pinfo=fetchPluginInfo($pluginFolder."widgets/$a",'WIDGETS',$srcType,$more);
							$data[$pinfo['packid']]=$pinfo;
						}
					}
				}
			}
			break;
		case "PACKAGES":
			foreach($folders as $srcType=>$pluginFolder) {
			}
			break;
	}
	
	$_SESSION['PACKMAN'][$type]=$data;
	
	return $data;
}

function fetchPluginInfo($pluginPath, $type, $srcType,$more) {
	$fname=basename($pluginPath);
	$info=[
		"packid"=>md5($pluginPath),
		"name"=>basename($pluginPath),
		"type"=>strtolower($type),
		"category"=>$srcType,
		"build"=>1,
		"status"=>"OK",
		"created_on"=>date("d M,Y",filectime($pluginPath)),// H:i:s
		"updated_on"=>date("d M,Y",filemtime($pluginPath)),// H:i:s
		"is_global"=>($srcType=="global"),
		"is_local"=>($srcType!="global"),
		"is_configurable"=>false,
		"is_installed"=>false,
		"is_editable"=>false,
		"has_error"=>false,
	];
	
	if($more) {
		$info['path']=$pluginPath;
	}
	
	$configFile=[
		"local1"=>CMS_APPROOT.CFG_FOLDER."features/{$fname}.cfg",
		"local2"=>CMS_APPROOT.CFG_FOLDER."{$fname}.cfg",
		"global"=>ROOT.CFG_FOLDER."features/{$fname}.cfg",
		"core"=>ROOT."config/{$fname}.cfg",
	];
	
	foreach($configFile as $cfg) {
		if(file_exists($cfg)) {
			$info['is_configurable']=true;
			break;
		}
	}
	
	switch($type) {
		case "MODULES":
			$info['is_installed']=is_file($pluginPath."logiks.json");
			break;
		case "VENDORS":
			$info['is_installed']=true;
			break;
		case "WIDGETS":
			$info['is_installed']=false;
			$info['is_file']=is_file($pluginPath);
			break;
	}
	return $info;
}
?>