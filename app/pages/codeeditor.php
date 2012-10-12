<?php
if(!defined('ROOT')) exit('No direct script access allowed');
user_admin_check(true);

if(!isset($_REQUEST["forsite"])) {
	dispErrMessage("HostSite Is Required But Not Found","CMS Error",400);
	exit();
}
checkUserSiteAccess($_REQUEST['forsite'],true);
loadModule("codeEdit");
?>
