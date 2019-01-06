<?php
if(!defined('ROOT')) exit('No direct script access allowed');

checkServiceAccess();

if(!isset($_REQUEST["action"])) {
	printServiceErrorMsg("NotAcceptable","Action Not Defined.");
}

switch ($_REQUEST['action']) {
	case 'update':
		if(isset($_POST['pwd']) && strlen($_POST['pwd'])>1) {
			loadHelpers("pwdhash");

			$ans=updatePassword($_POST['pwd']);
			if($ans===true) {
				printServiceMsg("success");
			} else {
				printServiceErrorMsg(412,$ans['error']);
			}
			
		} else {
			printServiceErrorMsg(400,"Password Not Found");
		}
	break;
}

?>