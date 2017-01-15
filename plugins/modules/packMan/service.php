<?php
if(!defined('ROOT')) exit('No direct script access allowed');
checkServiceAccess();

$apps=_session("siteList");
if(!isset($_GET['forsite']) || !in_array($_GET['forsite'],array_keys($apps))) {
	printServiceMsg(['msg'=>"Sorry, could not find the app or you do not have permission to operate on the app","error"=>true]);
	return;
}

switch($_REQUEST["action"]) {
	case "getlist":
		if(!isset($_REQUEST['type'])) $_REQUEST['type']="installed";
		$result=[];
		
		switch($_REQUEST['type']) {
			case "installed":
				$result=getPluginList();
				
				if(isset($_REQUEST['q']) && strlen($_REQUEST['q'])>0) {
					$q=$_REQUEST['q'];
					foreach($result['MODULES'] as $a=>$b) {
						if(strpos($a,$q)===false) {
							unset($result['MODULES'][$a]);
						}
					}
					foreach($result['VENDORS'] as $a=>$b) {
						if(strpos($a,$q)===false) {
							unset($result['VENDORS'][$a]);
						}
					}
					foreach($result['WIDGETS'] as $a=>$b) {
						if(strpos($a,$q)===false) {
							unset($result['WIDGETS'][$a]);
						}
					}
				}
				break;
			case "repos":
				break;
		}
		
		printServiceMsg($result);
		break;
	
	
}
function getPluginList() {
	$data=['MODULES'=>[],'VENDORS'=>[],'WIDGETS'=>[]];
	
	$folders=[
		"local"=>CMS_APPROOT.PLUGINS_FOLDER,
		"global"=>ROOT.PLUGINS_FOLDER,
		"dev"=>ROOT."pluginsDev/",
	];
	
	foreach($folders as $srcType=>$pluginFolder) {
		if(is_dir($pluginFolder."modules/")) {
			$fs=scandir($pluginFolder."modules/");
			foreach($fs as $a) {
				if($a=="." || $a=="..") continue;
				if(is_dir($pluginFolder."modules/$a/")) {
					$data['MODULES'][$a]=fetchPluginInfo($pluginFolder."modules/$a/",'MODULES',$srcType);
				}
			}
		}

		if(is_dir($pluginFolder."vendors/")) {
			$fs=scandir($pluginFolder."vendors/");
			foreach($fs as $a) {
				if($a=="." || $a=="..") continue;
				if(is_dir($pluginFolder."vendors/$a/")) {
					$data['VENDORS'][$a]=fetchPluginInfo($pluginFolder."vendors/$a/",'VENDORS',$srcType);
				}
			}
		}

		if(is_dir($pluginFolder."widgets/")) {
			$fs=scandir($pluginFolder."widgets/");
			foreach($fs as $a) {
				if($a=="." || $a=="..") continue;
				if(is_dir($pluginFolder."modules/$a/") || strpos($a,".php")>1) {
					$data['WIDGETS'][$a]=fetchPluginInfo($pluginFolder."widgets/$a",'WIDGETS',$srcType);
				}
			}
		}
	}
	
	return $data;
}

function fetchPluginInfo($pluginPath, $type, $srcType) {
	$info=[
		"pkey"=>basename($pluginPath),
		"name"=>basename($pluginPath),
		"type"=>strtolower($type),
		"src"=>$srcType,
		"is_global"=>($srcType=="global")
	];
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