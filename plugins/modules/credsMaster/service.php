<?php
if(!defined('ROOT')) exit('No direct script access allowed');

checkServiceAccess();

if(!isset($_REQUEST["action"])) {
	printServiceErrorMsg("NotAcceptable","Action Not Defined.");
}
if(!isset($_REQUEST["src"])) {
	printServiceErrorMsg("NotAcceptable","Source Not Defined.");
}
if(!isset($_POST["q"])) {
	printServiceErrorMsg("NotAcceptable","Item Not Defined.");
}

switch($_REQUEST["action"]) {
	case "delete":
		$src=$_POST['src'];
		$q=explode(",", $_POST['q']);

		switch ($src) {
			case 'users':
				$sql=_db(true)->_deleteQ(_dbTable("users",true))->_whereIn("id",$q);
				break;
			
			case 'access':
				$sql=_db(true)->_deleteQ(_dbTable("access",true))->_whereIn("id",$q);
				break;

			case 'privilege':
				$sql=_db(true)->_deleteQ(_dbTable("privileges",true))->_whereIn("id",$q);
				break;

			default:
				$sql=false;
				break;
		}
		if($sql) {
			$ans=$sql->_run();
			if(!$ans) {
				echo "Sorry, Target could not be deleted!";
			} else {
				echo  "done";
			}
		} else {
			echo "Sorry, Source not supported yet.";
		}
	break;
	case "block":
		$src=$_POST['src'];
		$status=$_POST['status'];
		$q=explode(",", $_POST['q']);

		switch ($src) {
			case 'users':
				$sql=_db(true)->_updateQ(_dbTable("users",true),["blocked"=>$status])->_whereIn("id",$q);
				break;
			
			case 'access':
				$sql=_db(true)->_updateQ(_dbTable("access",true),["blocked"=>$status])->_whereIn("id",$q);
				break;

			case 'privilege':
				$sql=_db(true)->_updateQ(_dbTable("privileges",true),["blocked"=>$status])->_whereIn("id",$q);
				break;

			default:
				$sql=false;
				break;
		}
		if($sql) {
			$ans=$sql->_run();
			if(!$ans) {
				echo "Sorry, Target could not be deleted!";
			} else {
				echo  "done";
			}
		} else {
			echo "Sorry, Source not supported yet.";
		}
	break;
}
?>