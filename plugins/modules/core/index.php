<?php
if(!defined('ROOT')) exit('No direct script access allowed');

if(!function_exists("setupCMSEnviroment")) {
	function setupCMSEnviroment() {
		if(!isset($_SESSION['SESS_PRIVILEGE_NAME'])) $_SESSION['SESS_PRIVILEGE_NAME']="";
		if(!isset($_SESSION['SESS_ACCESS_SITES'])) $_SESSION['SESS_ACCESS_SITES']=[];
		if(!isset($_SESSION['SESS_USER_NAME'])) $_SESSION['SESS_USER_NAME']="";
		
		//echo CMSSITE;
		if(isset($_REQUEST["forsite"])) {
		    $forSite=$_REQUEST["forsite"];

		    if($_REQUEST["forsite"]==SITENAME) {
		    	$lx=_link("","&site=".SITENAME."&forsite=".DEFAULT_SITE);
		    	header("Location:{$lx}");
		    }
		} else {
		    $forSite=DEFAULT_SITE;
		    $_REQUEST["forsite"]=DEFAULT_SITE;
		}
		$_REQUEST['forSite']=$forSite;
		$_GET['forSite']=$forSite;

		$arr=[];
		$siteList=[];
        if($_SESSION['SESS_PRIVILEGE_NAME']=="root") {
            $arr=scandir(ROOT.APPS_FOLDER);
            //$arr
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

        if(!array_key_exists($forSite, $siteList)) {
        	trigger_logikserror("Site <b>'".SITENAME."'</b> Does Not Have Access rights for you.<a href='"._link("")."'>Go Back</a>",E_ERROR);
        }
        
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

        _session("siteList",$siteList);
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
		return file_put_contents($src, $content);
	}
	setupCMSEnviroment();
}
?>
