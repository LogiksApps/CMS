<?php
if(!defined('ROOT')) exit('No direct script access allowed');
user_admin_check(true);

if(isset($_REQUEST["scope"])) {
	include "editor.php";
	if(strlen($_REQUEST["scope"])>0) {
		loadScope($_REQUEST["scope"]);
	} else {
		echo "<style>body {overflow:hidden;}</style>";
		dispErrMessage("Config Request Not Found.","404:Not Found",404);
	}
} else {
	include "manager.php";
}
?>
