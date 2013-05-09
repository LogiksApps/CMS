<?php
if(!defined('ROOT')) exit('No direct script access allowed');
checkServiceSession();
checkUserSiteAccess($_REQUEST['forsite'],true);
user_admin_check(true);

if(isset($_REQUEST["action"])) {
	loadModule("dbcon");getDBControls();
	$tbl=_dbtable("lists");
	$whrX="";
	//Selector Controls
	if($_SESSION['SESS_PRIVILEGE_ID']>=3) {
		$whrX="(site='*' OR site='{$_REQUEST['forsite']}')";
	}
	if($_REQUEST["action"]=="selectorlist") {
		if(strlen($whrX)>0) $whrX="WHERE ".$whrX;
		$sql="SELECT groupid,count(*) as cnt FROM $tbl {$whrX} group by groupid";
		$r=_dbQuery($sql);
		if($r) {
			$arr=_dbData($r);
			foreach($arr as $a) {
				echo "<option cnt={$a['cnt']} value='{$a['groupid']}'>{$a['groupid']} ({$a["cnt"]})</option>";
			}
		} else {
			echo "<option value='#'>No Selector Found</option>";
		}
	} elseif($_REQUEST["action"]=="selectordata") {
		if(!isset($_REQUEST['gid'])) {
			printErr("WrongFormat","Command Missing Argument");
			exit();
		}
		if(strlen($whrX)>0) $whrX=" AND ".$whrX;
		$sql="SELECT id,groupid,title,value,class,site,privilege,blocked FROM $tbl WHERE groupid='{$_REQUEST['gid']}' {$whrX} ORDER BY title,id";
		
		$r=_dbQuery($sql);
		if($r) {
			$s="";
			while($a=_db()->fetchData($r)) {
				if($a['blocked']=="true") {
					$clz="blocked";
				} else {
					$clz="";
				}
				
				$s.="<tr class='{$clz}' rel='{$a['id']}' gid='{$a['groupid']}' claz='{$a['class']}' site='{$a['site']}' privilege='{$a['privilege']}' >";
				$s.="<th align=left class=title width=50%>{$a['title']}</th><td class=value>{$a['value']}</td>";
				$s.="<td width=25px align=center><input type=checkbox rel='{$a['id']}' title='{$a['title']}' v='{$a['value']}' /></td>";
				$s.="</tr>";
			}
			if(strlen($s)>0) {
				echo $s;
			} else {
				echo "<tr><td colspan=10><h3 align=center>No Selectors Found For This Group</h3></td></tr>";
			}
		} else {
			echo "<tr><td colspan=10><h3 align=center>No Selectors Found For This Group</h3></td></tr>";
		}
	} elseif($_REQUEST["action"]=="block" && isset($_REQUEST["for"])) {
		if(!isset($_REQUEST['for'])) {
			printErr("WrongFormat","Command Missing Argument");
			exit();
		}
		$for=explode(",",$_REQUEST["for"]);
		$forS="";
		foreach($for as $f) {
			if(strlen($f>0)) {
				if(strlen($forS)==0) $forS.="id=$f ";
				else $forS.="OR id=$f ";
			}
		}
		$sql="UPDATE $tbl SET blocked='true' WHERE $forS";
		_dbQuery($sql);
	} elseif($_REQUEST["action"]=="unblock") {
		if(!isset($_REQUEST['for'])) {
			printErr("WrongFormat","Command Missing Argument");
			exit();
		}
		$for=explode(",",$_REQUEST["for"]);
		$forS="";
		foreach($for as $f) {
			if(strlen($f>0)) {
				if(strlen($forS)==0) $forS.="id=$f ";
				else $forS.="OR id=$f ";
			}
		}
		$sql="UPDATE $tbl SET blocked='false' WHERE $forS";
		_dbQuery($sql);
	} elseif($_REQUEST["action"]=="delete") {
		if(!isset($_REQUEST['for'])) {
			printErr("WrongFormat","Command Missing Argument");
			exit();
		}
		$for=explode(",",$_REQUEST["for"]);
		$forS="";
		foreach($for as $f) {
			if(strlen($f>0)) {
				if(strlen($forS)==0) $forS.="id=$f ";
				else $forS.="OR id=$f ";
			}
		}
		$sql="DELETE FROM $tbl WHERE $forS";
		_dbQuery($sql);
	} elseif($_REQUEST["action"]=="additem") {
		if(!isset($_POST['gid']) || !isset($_POST['a1']) || !isset($_POST['b1'])) {
			printErr("WrongFormat","Command Missing Argument");
			exit();
		}
		$userid=$_SESSION["SESS_USER_ID"];
		$date=date("Y-m-d");
		
		//if(!isset($_POST['claz'])) 
		$_POST['claz']="";
		//if(!isset($_POST['priv'])) 
		$_POST['priv']="*";
		
		$sql="insert into $tbl (id, groupid, title, value, class, site, privilege, blocked, userid, doc, doe) values ";
		$sql.="(0, '{$_POST['gid']}', '{$_POST['a1']}', '{$_POST['b1']}', '{$_POST['claz']}', '{$_REQUEST['forsite']}', '{$_POST['priv']}', 'false', '{$userid}', '{$date}', '{$date}')";
		
		_dbQuery($sql);
		if(_db()->insert_id()>0) {
			echo "success";
		} else {
			echo "failed";
		}
	} elseif($_REQUEST["action"]=="edititem") {
		if(!isset($_POST['gid']) || !isset($_POST['id']) || !isset($_POST['b1'])) {
			printErr("WrongFormat","Command Missing Argument");
			exit();
		}
		$userid=$_SESSION["SESS_USER_ID"];
		$date=date("Y-m-d");
		
		$sql="UPDATE $tbl SET value = '{$_POST['b1']}', userid = '$userid',doe = '$date' WHERE id={$_POST['id']} AND groupid='{$_POST['gid']}'";
		_dbQuery($sql);
		if(_db()->affected_rows()>0) {
			echo "success";
		} else {
			echo "failed";
		}
	} elseif($_REQUEST["action"]=="newlist") {
		if(!isset($_REQUEST['gid'])) {
			printErr("WrongFormat","Command Missing Argument");
			exit();
		}
		$userid=$_SESSION["SESS_USER_ID"];
		$date=date("Y-m-d");
		
		$sql="insert into $tbl (id, groupid, title, value, class, site, privilege, blocked, userid, doc, doe) values ";
		$sql.="(0, '{$_REQUEST['gid']}', '--', '', '', '{$_REQUEST['forsite']}', '*', 'false', '{$userid}', '{$date}', '{$date}')";
		_dbQuery($sql);
		echo $_REQUEST['gid'];
	} elseif($_REQUEST["action"]=="deletelist") {
		if(!isset($_REQUEST['for'])) {
			printErr("WrongFormat","Command Missing Argument");
			exit();
		}
		$sql="DELETE FROM $tbl WHERE groupid='{$_REQUEST['for']}'";
		_dbQuery($sql);
	}
}
?>
