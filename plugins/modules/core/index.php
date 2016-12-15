<?php
if(!defined('ROOT')) exit('No direct script access allowed');

if(!function_exists("setupCMSEnviroment")) {
	function setupCMSEnviroment() {
		if(defined("PAGE") && (PAGE=="login" || PAGE=="register" || PAGE=="forgotpwd")) {
			return true;
		}
		if(!isset($_SESSION['SESS_PRIVILEGE_NAME'])) $_SESSION['SESS_PRIVILEGE_NAME']="";
		if(!isset($_SESSION['SESS_ACCESS_SITES'])) $_SESSION['SESS_ACCESS_SITES']=[];
		if(!isset($_SESSION['SESS_USER_NAME'])) $_SESSION['SESS_USER_NAME']="";
		//printArray($_SESSION);exit();
		//echo CMSSITE;
		
		if(isset($_REQUEST["forsite"])) {
		    $forSite=$_REQUEST["forsite"];

// 		    if($_REQUEST["forsite"]==SITENAME) {
// 		    	$lx=_link("","&site=".SITENAME."&forsite=".DEFAULT_SITE);
// 		    	header("Location:{$lx}");
// 		    }
		} elseif(defined("SERVICE_ROOT")) {
				$_REQUEST["forsite"]=SITENAME;
				$forSite=SITENAME;
		} else {
		    $forSite=DEFAULT_SITE;
		    $_REQUEST["forsite"]=DEFAULT_SITE;
		}
		$_REQUEST['forSite']=$forSite;
		$_GET['forSite']=$forSite;
		
		if(defined("SERVICE_ROOT")) {
			if(isset($_REQUEST['scmd']) && $_REQUEST['scmd']=="auth") {
				return true;
			}
		}
		
		$arr=[];
		$siteList=[];
		if($_SESSION['SESS_PRIVILEGE_NAME']=="root") {
				$arr=scandir(ROOT.APPS_FOLDER);
		} else {
				$arr=$_SESSION['SESS_ACCESS_SITES'];
		}
		foreach($arr as $b) {
				if(file_exists(ROOT.APPS_FOLDER.$b."/apps.cfg")) {
						$t=ucwords($b);
						$lnk=SiteLocation."?site=cms&forsite=$b";
						$siteList[$b]=['title'=>$t,'url'=>$lnk];
				}
		}

		if(count($siteList)>0 && !array_key_exists($forSite, $siteList)) {
			echo "<h5 class='errormsg'>Site <b>'".$forSite."'</b> Does Not Have Access rights for you.<br>Redirecting ...</h5>";
			if(count($siteList)>0) {
				header("Location:".$siteList[array_keys($siteList)[0]]['url']);
			} else {
				header("Location:".SiteLocation);
			}
			//trigger_logikserror("Site <b>'".$forSite."'</b> Does Not Have Access rights for you.<a href='"._link("")."'>Go Back</a>",E_ERROR);
			exit();
		}
		$_SESSION['siteList']=$siteList;
		
		$f=ROOT.CFG_FOLDER."/jsonConfig/db.json";
		if(file_exists($f)) {
			$jsonDB=json_decode(file_get_contents($f),true);

			if(isset($jsonDB[$_REQUEST['forsite']])) {
				foreach ($jsonDB[$_REQUEST['forsite']] as $dbKey => $dbParams) {
					Database::connect($dbKey,$dbParams);
				}
			}
		}
        
    define("CMS_APPROOT",ROOT.APPS_FOLDER.$forSite."/");
		
		if($_SESSION['SESS_PRIVILEGE_NAME']!="root") {
			unset($siteList["cms"]);
		}

    _session("siteList",$siteList);
		
		$moduleDir=getLoaderFolders('pluginPaths',"modules");
		$moduleDir[]="apps/{$forSite}/plugins/modules/";
		
		$_ENV['MODULE_DIRS']=$moduleDir;
		$_ENV['LOADERS_PLUGINPATHS']['modules']=$moduleDir;
	}
	function checkServiceAccess() {
		$ls=new LogiksSecurity();
		session_check();
		$ls->checkUserSiteAccess($_REQUEST['forsite'],true);
		user_admin_check(true);
		//Check Role Controls
	}
	function getAppFile($file) {
		if(substr($file, 0, 1)!="/") $file="/{$file}";
		return ROOT.APPS_FOLDER.$_REQUEST['forsite'].$file;
	}
	function saveAppFile($src,$content) {
		if(!is_dir(dirname($src))) {
			mkdir(dirname($src),0777,true);
		}
		$a=file_put_contents($src, $content);
		if($a===false) return false;
		return true;
	}

	setupCMSEnviroment();
}
?>
