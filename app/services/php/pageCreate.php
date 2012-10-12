<?php
if(!defined('ROOT')) exit('No direct script access allowed');
checkServiceSession();
checkUserSiteAccess($_REQUEST['forsite'],true);
user_admin_check(true);

if(isset($_REQUEST["action"])) {
	if($_REQUEST["action"]=="showDialog") {
		if(!isset($_REQUEST['ptype'])) $_REQUEST['ptype']="all";
		loadModuleLib("pageCreate","dialog");
	}
	elseif($_REQUEST["action"]=="create") {
		
	}
}
?>
