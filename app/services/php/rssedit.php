<?php
if(!defined('ROOT')) exit('No direct script access allowed');
checkServiceSession();
checkUserSiteAccess($_REQUEST['forsite'],true);
user_admin_check(true);

if(isset($_REQUEST["action"])) {
	loadModule("dbcon");
	getDBControls();
	
	$tbl=_dbTable('rss');
	
	if($_REQUEST["action"]=="rsslist") {
		$sql="SELECT * FROM $tbl WHERE (site='*' OR site='{$_REQUEST['forsite']}')";
		$result=_dbQuery($sql);
		if($result) {
			$dbData=_dbData($result);
			_dbFree($result);
			//printArray($dbData);
			foreach($dbData as $data) {
				printRSSRow($data);
			}
		} else {
			echo "<tr><th align=center colspan=25><h3>No RSS Feeds Found</h3></th></tr>";
		}
		exit();
	} elseif($_REQUEST["action"]=="delete" && isset($_POST['id'])) {
		$sql="DELETE FROM $tbl WHERE (site='*' OR site='{$_REQUEST['forsite']}') AND id={$_POST['id']}";
		$result=_dbQuery($sql);
		if(!$result) {
			echo "Failed To Delete RSS Feed";
		}
		exit();
	} elseif($_REQUEST["action"]=="view" && isset($_POST['id'])) {
		$sql="SELECT * FROM $tbl WHERE (site='*' OR site='{$_REQUEST['forsite']}') AND id={$_POST['id']}";
		$result=_dbQuery($sql);
		if($result) {
			$data=_dbData($result);
			_dbFree($result);
			$data=$data[0];
			unset($data['doc']);
			unset($data['doe']);
			unset($data['userid']);
			
			$attr=$data['attributes'];
			unset($data['attributes']);
			
			if(strlen($attr)>0) {
				@$attr=json_decode($attr,true);
				if(is_array($attr)) {
					foreach($attr as $a=>$b) {
						$data["attributes_{$a}"]=$b;
					}
				}
			}
			
			echo json_encode($data);
		}
		exit();
	} elseif($_REQUEST["action"]=="save" && isset($_POST['id'])) {
		$sql="";
		if(isset($_POST['rssid'])) {
			$_POST['rssid']=str_replace(" ","",$_POST['rssid']);
		}
		if($_POST['id']==0) {
			unset($_POST['id']);
			
			$sql="SELECT * FROM $tbl WHERE (site='*' OR site='{$_REQUEST['forsite']}') AND rssid='{$_POST['rssid']}'";
			$result=_dbQuery($sql);
			if($result) {
				$data=_dbData($result);
				_dbFree($result);
				if(count($data)>0) {
					echo "Multiple RSS Feed Found With RSSID : <b>{$_POST['rssid']}</b><br/>Please change the RSSID.";
					exit();
				}
			}
			
			$_POST['userid']=$_SESSION['SESS_USER_ID'];
			$_POST['doc']=date("Y-m-d");
			$_POST['doe']=date("Y-m-d");
			
			$attr=array();
			$q1="";
			$q2="";
			foreach($_POST as $a=>$b) {
				if($b=='null') $b="";
				if(strpos("###".$a,"attributes_")==3) {
					$attr[substr($a,11)]=$b;
				} else {
					$q1.="$a,";
					$q2.="'$b',";
				}
			}
			if(count($attr)>0) {
				$q1.="attributes,";
				$q2.="'".json_encode($attr)."',";
			}
			if(strlen($q1)>2) {
				$q1=substr($q1,0,strlen($q1)-1);
				$q2=substr($q2,0,strlen($q2)-1);
			}
			$sql="INSERT INTO $tbl ";
			$sql.="($q1) VALUES ($q2)";
			
			$result=_dbQuery($sql);
			if(!$result) {
				echo "Failed To Create RSS Feed";
			}
		} else {
			$id=$_POST['id'];
			unset($_POST['id']);
			
			$_POST['userid']=$_SESSION['SESS_USER_ID'];
			$_POST['doe']=date("Y-m-d");
			
			$attr=array();
			$q1="";
			foreach($_POST as $a=>$b) {
				if($b=='null') $b="";
				if(strpos("###".$a,"attributes_")==3) {
					$attr[substr($a,11)]=$b;
				} else {
					$q1.="$a='$b',";
				}
			}
			if(count($attr)>0) {
				$q1.="attributes='".json_encode($attr)."',";
			}
			if(strlen($q1)>2) {
				$q1=substr($q1,0,strlen($q1)-1);
			}
			$sql="UPDATE $tbl SET $q1 ";
			$sql.=" WHERE (site='*' OR site='{$_REQUEST['forsite']}') AND id={$id}";
			
			$result=_dbQuery($sql);
			if(!$result) {
				echo "Failed To Update RSS Feed";
			}
		}
		exit();
	} elseif($_REQUEST["action"]=="blocked" && isset($_REQUEST["v"]) && isset($_POST['id'])) {
		$date=date("Y-m-d");
		$sql="UPDATE $tbl SET blocked='{$_REQUEST["v"]}',doe='$date' WHERE (site='*' OR site='{$_REQUEST['forsite']}') AND id={$_POST['id']}";
		$result=_dbQuery($sql);
		if(!$result) {
			echo "Failed To Update RSS Feed";
		}
		exit();
	}
}

function printRSSRow($data) {
	$s="<tr rel='{$data['id']}' rssid='{$data['rssid']}'>";
	$s.="<td class='rssid'>{$data['rssid']}</td>";
	$s.="<td class='title'>{$data['title']}</td>";
	$s.="<td class='category'>{$data['category']}</td>";
	$s.="<td class='language'>{$data['language']}</td>";
	$s.="<td class='author'>{$data['author']}</td>";
	$s.="<td class='avlbl_till'>{$data['avlbl_till']}</td>";
	
	if($data['blocked']=="true") {
		$s.="<td class='blocked' align=center><input name=blocked type=checkbox checked=true /></td>";
	} else {
		$s.="<td class='blocked' align=center><input name=blocked type=checkbox /></td>";
	}
	
	$s.="<td class='action'>";
	$s.="<div class='editicon minibtn right'></div>";
	$s.="<div class='linkicon minibtn right'></div>";
	$s.="</td>";
	$s.="<td><div class='deleteicon minibtn right'></div></td>";
	$s.="</tr>";
	echo $s;
}
?>
