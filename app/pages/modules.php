<?php
if(!defined('ROOT')) exit('No direct script access allowed');
user_admin_check(true);
checkUserSiteAccess($_REQUEST['forsite'],true);

$module=$_REQUEST["mod"];
$_SESSION["CMS_FORSITE"]=$_REQUEST["forsite"];
$forSite=$_SESSION["CMS_FORSITE"];

$p=checkModule($module);
if(strlen($p)>0) {
	loadModule($module);
} else {
	echo "<style>body {overflow:hidden;}</style>";
	dispErrMessage("'$module' Could Not Be Found For Site <b>$forSite</b>, It May Not Have Been Installed.",
		"404:Module Not Found",404,"media/images/notfound/apps.png");
}
?>
