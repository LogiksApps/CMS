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
		} else {
		    $forSite=DEFAULT_SITE;
		    $_REQUEST["forsite"]=DEFAULT_SITE;
		}
		$_REQUEST['forSite']=$forSite;
		$_GET['forSite']=$forSite;
		_pageVar("forSite",$forSite);

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
                $siteList[]=['title'=>$t,'url'=>$lnk];
            }
        }
        
        _pageVar("siteList",$siteList);
        _pageVar("SESS_USER_NAME",$_SESSION['SESS_USER_NAME']);
	}
	function checkServiceAccess() {
		$ls=new LogiksSecurity();
		session_check();
		$ls->checkUserSiteAccess($_REQUEST['forsite'],true);
		user_admin_check(true);
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
