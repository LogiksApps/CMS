<?php
if (!defined('ROOT')) exit('No direct script access allowed');
checkServiceSession();
/*
if($_REQUEST["forsite"]) $forSite=$_REQUEST["forsite"];
else $forSite="";
*/
if(isset($_REQUEST["action"])) {
	if($_REQUEST["action"]=="applist" || $_REQUEST["action"]=="sitelist") {
		$fs=$_SESSION["SESS_ACCESS_SITES"];				
		foreach($fs as $a=>$b) {
			unset($fs[$a]);
			if(file_exists(ROOT.APPS_FOLDER.$b."/apps.cfg") && file_exists(ROOT.APPS_FOLDER.$b."/cms.php")) {
				$fs[$b]=$b;
			}
		}
		printFormattedArray($fs);
	} elseif($_REQUEST["action"]=="privilegelist") {
		checkUserSiteAccess($_REQUEST['forsite'],true);
		
		$s="";
		if(isset($_REQUEST["forsite"])) $s=$_REQUEST["forsite"];
		$sql="SELECT id,name FROM "._dbtable("privileges",true)." where blocked='false' and site='*'";
		if(strlen($s)>0) $sql.=" or site='$s'";
		$r=_dbQuery($sql,true);
		if($r) {
			$a=_db(true)->fetchAllData($r);
			$o=array();
			foreach($a as $x=>$c) {
				$o[$c["name"]]=$c["id"];
			}
			printFormattedArray($o);
		}
	} elseif($_REQUEST["action"]=="accesslist") {
		checkUserSiteAccess($_REQUEST['forsite'],true);
		
		$s="";
		if(isset($_REQUEST["forsite"])) $s=$_REQUEST["forsite"];
		$sql="SELECT id,master,sites FROM "._dbtable("access",true)." where blocked='false'";
		if($_SESSION['SESS_PRIVILEGE_ID']<3) {
			$sql.=" and (sites='*' or sites LIKE '%$s%')";
		} else {
			$sql.=" and sites LIKE '%$s%'";
		}
		$r=_dbQuery($sql,true);
		if($r) {
			$a=_db(true)->fetchAllData($r);
			$o=array();
			foreach($a as $x=>$c) {
				$o[$c["master"]]=$c["id"];
			}
			printFormattedArray($o);
		}
	}
}
exit();

?>
