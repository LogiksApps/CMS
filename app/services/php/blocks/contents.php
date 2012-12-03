<?php
if(!defined('ROOT')) exit('No direct script access allowed');
checkServiceSession();
checkUserSiteAccess($_REQUEST['forsite'],true);
user_admin_check(true);

if(isset($_REQUEST["action"])) {
	loadModule("dbcon");
	$dbCon=getDBControls();
	$lf=$_SESSION["APP_FOLDER"]["APPROOT"].$_SESSION["APP_FOLDER"]["APPS_PAGES_FOLDER"]."comps/";
	
	$tbl=_dbtable("contents");
	
	if($_REQUEST["action"]=="list") {
		$sql="SELECT * FROM $tbl WHERE (SITE='*' OR SITE='{$_REQUEST['forsite']}') ORDER BY id";
		$res=$dbCon->executeQuery($sql);
		$out=array();
		$noCat=array();
		if($res) {
			while($data=$dbCon->fetchData($res)) {
				if(strlen($data['category'])<=0) {
					$data['category']="/";
				}
				if(!isset($out[$data['category']])) {
					$out[$data['category']]=array();
				}
				array_push($out[$data['category']],$data);
			}
			$dbCon->freeResult($res);
		}
		if(isset($out['/'])) {
			$noCat=$out['/'];
			unset($out['/']);
		}
		$st="<tr rel='#id#' title='#title#' class='#editable#'><td class='#icon#'></td><td class='title'>#reflink#</td></tr>";
		
		foreach($out as $a=>$b) {
			echo "<tr><th class='clr_darkmaroon' align=left colspan=10>".ucwords($a)."</th></tr>";
			foreach($b as $x=>$y) {
				$ss=$st;
				$ss=str_replace("#id#",$y['id'],$ss);
				$ss=str_replace("#title#",$y['title'],$ss);
				$ss=str_replace("#category#",$y['category'],$ss);
				$ss=str_replace("#reflink#",$y['reflink'],$ss);
				if($y['blocked']=="false")
					$ss=str_replace("#icon#","okicon",$ss);
				else
					$ss=str_replace("#icon#","notokicon",$ss);
				if($y['site']=="*" && $_SESSION['SESS_PRIVILEGE_ID']>3)
					$ss=str_replace("#editable#","",$ss);
				else
					$ss=str_replace("#editable#","editable",$ss);
				echo $ss;
			}
		}
		echo "<tr><th class='clr_darkblue' align=left colspan=10>No Category</th></tr>";
		foreach($noCat as $y) {
			$ss=$st;
			$ss=str_replace("#id#",$y['id'],$ss);
			$ss=str_replace("#title#",$y['title'],$ss);
			$ss=str_replace("[#category#]","",$ss);
			$ss=str_replace("#reflink#",$y['reflink'],$ss);
			if($y['blocked']=="false")
				$ss=str_replace("#icon#","okicon",$ss);
			else
				$ss=str_replace("#icon#","notokicon",$ss);
			if($y['site']=="*" && $_SESSION['SESS_PRIVILEGE_ID']>3)
				$ss=str_replace("#editable#","",$ss);
			else
				$ss=str_replace("#editable#","editable",$ss);
			echo $ss;
		}
	} elseif($_REQUEST["action"]=="fetch" && isset($_REQUEST["id"])) {
		$sql="SELECT id,reflink,title,category,blocked,text,site,doc,doe FROM $tbl WHERE (SITE='*' OR SITE='{$_REQUEST['forsite']}') AND id={$_REQUEST["id"]}";
		$res=$dbCon->executeQuery($sql);
		$data=array();
		if($res) {
			while($d=$dbCon->fetchData($res)) {
				$data=$d;
				break;
			}
			$dbCon->freeResult($res);
		}
		echo json_encode($data);
	} elseif($_REQUEST["action"]=="save" && isset($_REQUEST["id"]) && isset($_POST["data"])) {
		$data=$_POST["data"];
		$data=cleanText($data);
		$data=mysql_real_escape_string($data);
		//$data="";
		$sql="UPDATE $tbl SET ";
		$sql.="title='{$_POST["title"]}', category='{$_POST["category"]}', blocked='{$_POST["blocked"]}', text='{$data}', ";
		$sql.="doe='".date('Y/m/d')."'";
		$sql.=" WHERE (SITE='*' OR SITE='{$_REQUEST['forsite']}') AND id={$_REQUEST["id"]}";
		$res=$dbCon->executeQuery($sql);
		
		if(!$res) {
			echo $dbCon->getError();
			echo "Error Updating Article#{$_REQUEST["id"]}. Try Again.";
		}
	} elseif($_REQUEST["action"]=="create" && isset($_REQUEST["title"])) {
		if(strlen($_REQUEST["title"])>0) {
			$site="*";
			$userid=$_SESSION['SESS_USER_ID'];
			$date=date('Y/m/d');
			$sql="INSERT INTO $tbl (reflink,title,category,text,site,userid,doc,doe) VALUES ";
			$sql.="('".str_replace(" ","_",strtolower($_REQUEST["title"]))."','{$_REQUEST["title"]}','','','$site','$userid','$date','$date')";
			$res=$dbCon->executeQuery($sql);
			if($res) {
				$d=array("id"=>$dbCon->insert_id());
				echo json_encode($d);
			} else {
				$d=array("id"=>"0","msg"=>"There was error creating article with name <b>{$_REQUEST["title"]}</b>. <br/>Please try again.");
				echo json_encode($d);
			}
		} else {
			$d=array("id"=>"0","msg"=>"There was error creating article with name <b>{$_REQUEST["title"]}</b>. <br/>Please try again.");
			echo json_encode($d);
		}
	} elseif($_REQUEST["action"]=="delete" && isset($_REQUEST["id"])) {
		$sql="DELETE FROM $tbl WHERE ID={$_REQUEST["id"]}";
		$res=$dbCon->executeQuery($sql);
		if(!$res) {
			echo "Error Deleting Content.";
		}
	} 
}
?>
