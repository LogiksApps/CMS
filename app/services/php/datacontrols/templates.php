<?php
if(!defined('ROOT')) exit('No direct script access allowed');
checkServiceSession();
checkUserSiteAccess($_REQUEST['forsite'],true);
user_admin_check(true);

if(isset($_REQUEST["action"]) && isset($_REQUEST["mode"])) {
	loadModule("dbcon");$dbCon=getDBControls();
	
	if($_REQUEST["mode"]=="views") $tbl=_dbtable("views");
	else {
		printErr("WrongFormat");
		exit();
	}
	
	if($_REQUEST['action']=="fetch" && isset($_REQUEST['tmpl'])) {
		$sql="SELECT * FROM $tbl WHERE id={$_REQUEST['tmpl']}";
		$result=_dbQuery($sql);
		if($result) {
			$data=_dbData($result);
			_dbFree($result);
			$out=array();
			
			$out['tmpl']=$_REQUEST['tmpl'];
			$out['template']=$data[0]['template'];
			$out['sql']=$data[0]['queries'];
			$out['form']=$data[0]['frmdata'];
			
			$out['sql']=explode("\n",$out['sql']);
			
			if($out['template']==null) $out['template']="";
			if($out['sql']==null) $out['sql']="";
			if($out['form']==null) $out['form']="";
			
			echo json_encode($out);
		} else {
			$arr=array("error"=>"Error Finding Template Configuration.");
			echo json_encode($arr);
		}
		exit();
	} elseif($_REQUEST['action']=="save" && isset($_REQUEST['tmpl'])) {
		$template=$_POST['template'];
		$sql=$_POST['sql'];
		$form=$_POST['form'];
		$date=date("Y-m-d");
		
		$template=cleanText($template);
		$sql=cleanText($sql);
		
		$template=str_replace("&#39;","'",$template);
		$template=str_replace("&quot;",'"',$template);
		
		$template=str_replace("'","\'",$template);
		$sql=str_replace("'","\'",$sql);
		$sql=str_replace("\n\n","\n",$sql);
		
		$form=str_replace("'","\"",$form);
		$form=str_replace("<!--?","<?",$form);
		$form=str_replace("?-->","?>",$form);
		
		$sql="UPDATE $tbl SET frmdata='$form',queries='$sql',template='$template',doe='$date' WHERE id={$_REQUEST['tmpl']}";
		if(strlen($sql)>0) {
			$r=_dbQuery($sql);
			if(!$r) {
				echo "Error Saving The View. Try Again.";
			} else {
				$cacheID="views_{$_REQUEST['tmpl']}";
				flushSiteCacheFile($cacheID);
				echo "View Data Saved Successfully.";
			}
		}
		exit();
	} else {
		printErr("WrongFormat");
	}
}
?>
