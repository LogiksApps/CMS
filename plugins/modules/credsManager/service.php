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
	case "pwd":
		loadHelpers("pwdhash");
		
		$data=_db(true)->_selectQ(_dbTable("users",true),"*",['md5(id)'=>$_POST['q']])->_GET();

		if(count($data)<=0) {
			printServiceMsg("User not found");
			return;
		} else {
			$data=$data[0];
		}
		$oldPWD=$data['pwd'];
		$oldPWDSalt=$data['pwd_salt'];
		
		$newPWD=$_POST['pwNewPass'];
		
		if($_SESSION['SESS_PRIVILEGE_ID']!=1) {
			$currPWD=$_POST['pwCurrPass'];
			
			$a=matchPWD($oldPWD,$currPWD,$oldPWDSalt);
			
			if(!$a) {
				printServiceMsg("Current Password didn't match Existing Password");
				return;
			}
		}
		//if(checkUserID($userID,$site)) {}
		
		//$hashSalt=strtr(base64_encode(mcrypt_create_iv(16, MCRYPT_DEV_URANDOM)), '+', '.');
		$hashSalt = LogiksEncryption::generateSalt();
		$pwdAns=getPWDHash($newPWD,$hashSalt);
		if(is_array($pwdAns)) $pwdAns=$pwdAns['hash'];

		$dataUser=array(
				"pwd"=>$pwdAns,
				"pwd_salt"=>$hashSalt,
				"edited_by"=>$_SESSION['SESS_USER_ID'],
				"edited_on"=>date("Y-m-d H:i:s"),
			);
		
		$sql=_db(true)->_updateQ(_dbTable("users",true),$dataUser,array("userid"=>$data['userid']));
		$res=_dbQuery($sql,true);
		if($res) {
			printServiceMsg("success");
		} else {
			printServiceMsg("Error updating User Password");
		}
	break;
}
?>
