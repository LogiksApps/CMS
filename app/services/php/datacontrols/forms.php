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
	//elseif($_REQUEST["mode"]=="search") $tbl=_dbtable("search");
	else {
		printErr("WrongFormat");
		exit();
	}
	
	$cntrls=array();
	$cntrls["forms"]=array("cols"=>"id,title,header,footer,frmdata as data");
	$cntrls["reports"]=array("cols"=>"id,title,header,footer");
	$cntrls["search"]=array("cols"=>"id,title,header,footer,search_form as data");
	
	if(isset($_REQUEST['id'])) {
		$_REQUEST['id']=str_replace("--","",$_REQUEST['id']);
		$_REQUEST['id']=str_replace(" ","",$_REQUEST['id']);
		$_REQUEST['id']=str_replace("or","",$_REQUEST['id']);
		$_REQUEST['id']=str_replace("OR","",$_REQUEST['id']);
		$_REQUEST['id']=mysql_real_escape_string($_REQUEST['id'],$dbCon->getLink());
	} else {
		printErr("WrongFormat");
		exit();
	}
	
	if($_REQUEST['action']=="fetch") {
		$sql="SELECT {$cntrls[$_REQUEST['mode']]['cols']} FROM $tbl WHERE id={$_REQUEST['id']} AND (site='{$_REQUEST['forsite']}' OR site='*')";
		$r=$dbCon->executeQuery($sql);
		if($r) {
			$a=$dbCon->fetchAllData($r);			
			$dbCon->freeResult($r);
			if(isset($a[0])) {
				$a=$a[0];
				echo json_encode($a);
			}			
			exit();
		}
		exit();
	} elseif($_REQUEST['action']=="save") {
		$s=$_POST['data'];
		$s=cleanText($s);
		//$s=preg_replace("/(<tr[^>]+>)/","<tr>",$s);
		$s=str_replace("'","\"",$s);
		$s=str_replace("<!--?","<?",$s);
		$s=str_replace("?-->","?>",$s);
		
		if(isset($_POST['header']) && $_POST['header']=="(Click here to add text)") $_POST['header']="";
		if(isset($_POST['footer']) && $_POST['footer']=="(Click here to add text)") $_POST['footer']="";
		
		if($_REQUEST['mode']=="forms") 
			$sql="UPDATE $tbl SET submit_table='{$_POST['stable']}',header='{$_POST['header']}',footer='{$_POST['footer']}',frmdata='$s' WHERE id={$_REQUEST['id']} AND (site='{$_REQUEST['forsite']}' OR site='*')";
		elseif($_REQUEST['mode']=="search") 
			$sql="UPDATE $tbl SET datatable_table='{$_POST['stable']}',header='{$_POST['header']}',footer='{$_POST['footer']}',search_form='$s' WHERE id={$_REQUEST['id']} AND (site='{$_REQUEST['forsite']}' OR site='*')";
		else
			$sql="";
		if(strlen($sql)>0) {
			$r=$dbCon->executeQuery($sql);
			if(!$r) {
				echo "Error Saving The Design. Try Again.";
			} else {
				$cacheID="form__{$_REQUEST['id']}";
				flushSiteCacheFile($cacheID);
			}
			exit();
		}
		exit();
	}
}
?>
