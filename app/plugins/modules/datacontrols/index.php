<?php
if(!defined('ROOT')) exit('No direct script access allowed');
session_check(true);
user_admin_check();

$webPath=getWebPath(__FILE__);
$rootPath=getRootPath(__FILE__);

include "config.php";

if(isset($_REQUEST["list"])) {
	$a=checkModule($_REQUEST["list"]);
	if(strlen($a)<=0) {
		dispErrMessage("Module <i><u>{$_REQUEST["mode"]}</u></i> Is Not Installed.","Module Missing",412);
		exit();
	}

	if(array_key_exists($_REQUEST["list"],$cntrls)) {
		include "list.php";
	} else {
		dispErrMessage("Sorry, Required DataControl Module Not Supported Yet","DataControl Not Implemented",501);
	}	
} elseif(isset($_REQUEST["editor"])) {
	if(array_key_exists($_REQUEST["editor"],$editors)) {
		include $editors[$_REQUEST["editor"]]["editor"];
	} else {
		dispErrMessage("Sorry, Required DataControl Editor Not Supported Yet","DataControl Not Implemented",501);
	}	
} else {
	dispErrMessage("DataControls Module :: Control Command Missing","Control Command Missing",406);
}
?>
