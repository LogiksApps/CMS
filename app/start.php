<?php
if(!defined('ROOT')) exit('No direct script access allowed');
if(!defined('APPROOT')) exit('No direct script access allowed');

$a=session_check(true);
if($_SESSION['SESS_PRIVILEGE_ID']>getConfig("MAX_PRIVILEGE_ID")) {
	$relink=SiteLocation . "login.php?site=".SITENAME;
	redirectTo($relink,"SESSION Expired. Going To Login Page");
	sessionExpired();
	exit();
}

if(!isset($_REQUEST["forsite"])) {
	if(isset($_SESSION["LGKS_CMS_SITE"])) {
		$_REQUEST["forsite"]=$_SESSION["LGKS_CMS_SITE"];
	} else {
		if(in_array(DEFAULT_SITE,$_SESSION['SESS_ACCESS_SITES'])) {
			$_REQUEST["forsite"]=DEFAULT_SITE;
		} else {
			foreach($_SESSION['SESS_ACCESS_SITES'] as $a) {
				if($a!="admincp" && $a!=SITENAME) {
					$_REQUEST["forsite"]=$a;
					break;
				}
			}
		}
	}
	$site=$_REQUEST["forsite"];
	if(is_dir(ROOT.APPS_FOLDER.$site)
		&& file_exists(ROOT.APPS_FOLDER.$site."/apps.cfg")
		&& file_exists(ROOT.APPS_FOLDER.$site."/cms.php") &&
		$_REQUEST["forsite"]!=$_REQUEST["site"]) {
		header("Location:"._url("&forsite=$site"));
	} else {
		logoutSession("Failed To Determine AppSite !");
		exit("X");
	}
}
if(is_array($_SESSION['SESS_ACCESS_SITES']) && !(in_array($_REQUEST["forsite"],$_SESSION['SESS_ACCESS_SITES'])
	&& in_array(SITENAME,$_SESSION['SESS_ACCESS_SITES']))) {
	logoutSession("No AppSite Found !");
	exit("Y");
}
$_SESSION["LGKS_CMS_SITE"]=$_REQUEST["forsite"];

if(defined("APPS_CSS_TYPE")) $css->TypeOfDispatch(APPS_CSS_TYPE);
if(defined("APPS_JS_TYPE")) $js->TypeOfDispatch(APPS_JS_TYPE);

if(!isset($_REQUEST["page"]) || strlen($_REQUEST["page"])==0) {
	$_REQUEST["page"]=getConfig("LANDING_PAGE");
}

_js(array("jquery","jquery.ui"));
printSubSkin();
_css(array("style","ajax","colors"));

$css->loadCSS("ie6","*","ie6");
$css->loadCSS("print","*","","print");
$js->display();
$css->display();
echo "\n";

$page=$_REQUEST["page"];

$arrPages=array();
array_push($arrPages,APPROOT.APPS_PAGES_FOLDER."$page");
if(ALLOW_DEFAULT_SYSTEM_PAGES=="true") {
	array_push($arrPages,ROOT.PAGES_FOLDER."$page");
}

$loaded=false;
if(isLayoutConfig($page)) {
	$loaded=true;
	echo "<style>html,body {width:100%;height:100%;padding:0px;margin:0px;}".getUserPageStyle(false)."</style>\n";
	echo "</head>\n<body style='width:100%;height:100%;padding:0px;margin:0px;' ".getBodyContext().">\n";
	generatePageLayout($page);
	echo "</body>";
} else {
	$found=false;
	foreach($arrPages as $a) {
		if(!$loaded) {
			$arr1=getSupportedPages($a);
			foreach($arr1 as $f) {
				if(file_exists($f)) {
					$loaded=true;
					echo "<style>html,body {width:100%;height:100%;padding:0px;margin:0px;}".getUserPageStyle(false)."</style>\n";
					echo "</head>\n<body style='width:100%;height:100%;padding:0px;margin:0px;' ".getBodyContext().">\n";
					include $f;
					echo "</body>";
					break;
				}
			}
		}
	}
}
if(!$loaded) {
	dispErrMessage("<i>$page</i> Page Not Found.","Page Not Found",404,'media/images/notfound/file.png');
}
//$js->display();
//$css->display();
_css("print","*","","print");
_js(array("ajax","commons","dialogs"));
printSubSkin();
exit();
?>
