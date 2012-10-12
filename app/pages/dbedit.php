<?php
if(!defined('ROOT')) exit('No direct script access allowed');
user_admin_check(true);

if(isset($_REQUEST["forsite"])) {
	checkUserSiteAccess($_REQUEST['forsite'],true);

	loadModule("dbedit");
	loadDbConsole($_REQUEST["forsite"]);
} else {
	dispErrMessage("No Site Mentioned...","CMS Error",400);
}
?>
