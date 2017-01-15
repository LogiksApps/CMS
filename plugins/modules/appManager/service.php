<?php
if(!defined('ROOT')) exit('No direct script access allowed');
checkServiceAccess();

$apps=_session("siteList");

switch($_REQUEST["action"]) {
	case "listApps":
		$appsFinal=[];
		foreach($apps as $k=>$app) {
			$cfg=ROOT.APPS_FOLDER.$k."/apps.cfg";
			if(file_exists($cfg)) {
				$cfgArr=LogiksConfig::parseConfigFile($cfg);
				$appsFinal[]=[
					"appkey"=>$k,
					"title"=>$cfgArr['DEFINE-APPS_NAME']['value'],
					"vers"=>$cfgArr['DEFINE-APPS_VERS']['value'],
					"devmode"=>$cfgArr['DEFINE-DEV_MODE_ENABLED']['value'],
					"status"=>$cfgArr['DEFINE-APPS_STATUS']['value'],
					"published"=>$cfgArr['DEFINE-PUBLISH_MODE']['value'],
					"router"=>$cfgArr['DEFINE-APPS_ROUTER']['value'],
					"access"=>$cfgArr['DEFINE-ACCESS']['value'],
					"url"=>SiteLocation."?site={$k}",
					"urlcms"=>$app['url'],
					"readonly"=>(!is_writable($cfg)),
					"database"=>0,
					"msgs"=>0,
					"cache"=>0,
					"domain"=>0,
					"services"=>0,
					"allow_clone"=>($k!="cms")
					//"cfg"=>$cfgArr
				];
			}
		}
		printServiceMsg($appsFinal);
		break;
	case "appEditor":
		if(isset($_POST['app']) && in_array($_POST['app'],array_keys($apps))) {
			$app=$_POST['app'];unset($_POST['app']);
			include_once __DIR__."/editor.php";
		} else {
			echo "<h2 class='errorBox'>Sorry, could not find the app, or you do not have permission to operate on the app</h2>";
		}
		break;
	case "saveApp":
		if(isset($_POST['app']) && in_array($_POST['app'],array_keys($apps))) {
			$app=$_POST['app'];unset($_POST['app']);
			
			$cfg=ROOT.APPS_FOLDER."{$app}/apps.cfg";
			if(!file_exists($cfg) || !is_writable($cfg)) {
				printServiceMsg(['msg'=>"App configuration not found or is readonly.","error"=>true]);
				return;
			}
			
			$out=[];
			foreach($_POST as $a=>$b) {
				$c=explode("-",$a);
				$x=$c[0];unset($c[0]);
				$y=implode("-",$c);
				$out[$x][]="{$y}={$b}";
			}
			$text="";
			foreach($out as $a=>$b) {
				$text.="[$a]\n".implode("\n",$b)."\n";
			}
			//printArray($out);
			$a=file_put_contents($cfg,$text);
			if($a>1) {
				printServiceMsg(['msg'=>"App configuration updated successfully."]);
			} else {
				printServiceMsg(['msg'=>"Error updating app configuration.","error"=>true]);
			}
		} else {
			echo "<h2 class='errorBox'>Sorry, could not find the app, or you do not have permission to operate on the app</h2>";
		}
		break;
		case "cloneApp":
			if(isset($_POST['app']) && in_array($_POST['app'],array_keys($apps))) {
				$app=$_POST['app'];unset($_POST['app']);
				
				//Copy files -usermedia
				
				//For Database
				//Create DB if possible
				//Copy tables if possible
				//Attach db to app
				
				//optionally attach to app
				//cache
				//msg
				
				echo $app;
			}
		break;
}
?>