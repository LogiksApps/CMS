<?php
if(!defined('ROOT')) exit('No direct script access allowed');
checkServiceSession();
checkUserSiteAccess($_REQUEST['forsite'],true);
user_admin_check(true);

if(isset($_REQUEST["action"]) && isset($_REQUEST["mode"])) {
	loadModule("dbcon");$dbCon=getDBControls();
	$tbl="";
	if($_REQUEST["mode"]=="forms") $tbl=_dbtable("forms");
	elseif($_REQUEST["mode"]=="reports") $tbl=_dbtable("reports");
	elseif($_REQUEST["mode"]=="search") $tbl=_dbtable("search");
	else {
		printErr("WrongFormat");
		exit();
	}
	
	$f=checkModule("datacontrols");
	$f=dirname($f)."/config.php";
	include $f;
	
	if(isset($_POST['id'])) {
		$_POST['id']=str_replace("--","",$_POST['id']);
		$_POST['id']=str_replace(" ","",$_POST['id']);
		$_POST['id']=str_replace("or","",$_POST['id']);
		$_POST['id']=str_replace("OR","",$_POST['id']);
		$_POST['id']=mysql_real_escape_string($_POST['id'],_db()->getLink());
	} else {
		printErr("WrongFormat");
		exit();
	}
	
	if($_REQUEST['action']=="fetch") {
		$sql="SELECT {$properties[$_REQUEST['mode']]['cols']} FROM $tbl WHERE id={$_POST['id']} AND (site='{$_REQUEST['forsite']}' OR site='*')";
		$r=_dbQuery($sql);
		if($r) {
			$a=_dbData($r);			
			_db()->freeResult($r);
			if(isset($a[0])) {
				$a=$a[0];
				echo json_encode($a);
			}			
			exit();
		}
		exit();
	} elseif($_REQUEST['action']=="save") {
		$date=date("Y-m-d");
		$userid=$_SESSION["SESS_USER_ID"];
		$site=$_REQUEST['forsite'];
		$id=$_POST['id'];
		unset($_POST['id']);
		
		$cols=$properties[$_REQUEST['mode']]['cols'];
		$cols=explode(",",$cols);
		$cols=array_flip($cols);
		$out=array();
		foreach($cols as $a=>$b) {
			if(isset($_POST[$a])) array_push($out,"{$a}='{$_POST[$a]}'");			
		}
		$cols=implode(", ",$out);		
		$sql="UPDATE $tbl SET $cols WHERE id={$id}";
		$r=_dbQuery($sql);
		if(!$r) {
			echo "Error In Updating DataControl Properties. Try Again";
		}
		exit();
	}
}
?>
