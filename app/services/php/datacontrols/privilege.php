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
		$sql="SELECT id,privilege,privilege_model FROM $tbl WHERE id={$_POST['id']}";
		$result=$dbCon->executeQuery($sql);
		$data=array();
		if($result) {
			$data=$dbCon->fetchAllData($result);
			$dbCon->freeResult($result);
		}
		if(isset($data[0])) $data=$data[0];
		if(count($data)!=1) {
			$arr=array("err"=>"Error Fetching Privileges");
		}
		$privilege=$data["privilege"];
		$model=$data["privilege_model"];
		if(strlen($model)>0) {
			echo $model;
		}
		exit();
	} elseif($_REQUEST['action']=="save") {
		$id=$_POST["id"];
		unset($_POST["id"]);		
		$model=json_encode($_POST);
		$privileges=implode(",",array_keys($_POST));
		if(strlen($privileges)>0) $privileges.=",";
		$sql="UPDATE $tbl SET privilege='$privileges',privilege_model='$model' where id=$id";
		$result=$dbCon->executeQuery($sql);
		if(!$result) {
			echo "Error Updating Privileges. Please try again.";
		} else {
			flushPermissions($_REQUEST['forsite']);
		}
		exit();
	}
}
?>
