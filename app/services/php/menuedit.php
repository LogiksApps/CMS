<?php
if(!defined('ROOT')) exit('No direct script access allowed');
checkServiceSession();
checkUserSiteAccess($_REQUEST['forsite'],true);
user_admin_check(true);

if(isset($_REQUEST["action"])) {
	loadModule("dbcon");
	$dbcon=getDBControls();
	loadFolderConfig();
	
	loadModuleLib("menuedit","MenuTree");
	
	$sql="";
	if($_REQUEST["action"]=="menulist" && isset($_REQUEST["menuid"])) {
		if($dbcon==null) {
			exit("<h3>No Menu System.</h3>");
		}
		
		$menuid=$_REQUEST["menuid"];
		
		$orderBy="category asc,weight,id";//category desc,weight,id desc//weight,id,category desc//menugroup asc,category desc,
		if($_SESSION['SESS_PRIVILEGE_ID']<=3) {
			$sql="select * from "._dbTable('links')." where (site='{$_REQUEST['forsite']}' OR site='*') and menuid='{$_REQUEST['menuid']}' order by $orderBy";
		} else {
			$sql="select * from "._dbTable('links')." where site='{$_REQUEST['forsite']}' and menuid='{$_REQUEST['menuid']}' order by $orderBy";
		}
		
		if(strlen($sql)>0) {
			$result=$dbcon->executeQuery($sql);
			echo MenuTree::printMenuTree($result);
			$dbcon->freeResult($result);
		} else {
		}
		if($dbcon!=null) $dbcon->close();
		exit();
	}
	elseif($_REQUEST["action"]=="menudelete" && isset($_REQUEST["menuid"])) {
		if($_SESSION['SESS_PRIVILEGE_ID']<=3) {
			$sql="delete from "._dbTable('links')." where (site='{$_REQUEST['forsite']}' OR site='*') and menuid='{$_REQUEST['menuid']}'";
		} else {
			$sql="delete * from "._dbTable('links')." where site='{$_REQUEST['forsite']}' and menuid='{$_REQUEST['menuid']}'";
		}
		if(strlen($sql)>0) {
			$result=$dbcon->executeQuery($sql);
			if(!$result) {
				echo "Error Deleting MenuGroup {$_REQUEST["menuid"]}";
			}
		} else {
		}
		flushPermissionCache();
		if($dbcon!=null) $dbcon->close();
		exit();
	} 
	elseif($_REQUEST["action"]=="itemview" && isset($_REQUEST["menuid"]) && isset($_REQUEST["itemid"])) {
		if($_SESSION['SESS_PRIVILEGE_ID']<=3) {
			$sql="select * from "._dbTable('links')." where (site='{$_REQUEST['forsite']}' OR site='*') and menuid='{$_REQUEST['menuid']}' AND id={$_REQUEST['itemid']}";
		} else {
			$sql="select * from "._dbTable('links')." where site='{$_REQUEST['forsite']}' and menuid='{$_REQUEST['menuid']}' AND id={$_REQUEST['itemid']}";
		}
		if(strlen($sql)>0) {
			$result=$dbcon->executeQuery($sql);
			if($result) {
				$data=_dbData($result);
				if(count($data)>0) {
					if(strlen($data[0]['category'])==0 &&
						strlen($data[0]['menugroup'])==0) {
							$data[0]['isMenuGroup']=true;
						} else {
							$data[0]['isMenuGroup']=false;
						}
					unset($data[0]['userid']);
					unset($data[0]['doc']);
					unset($data[0]['doe']);
					echo json_encode($data[0]);
				}
			}
			$dbcon->freeResult($result);
			
		} else {
		}
		if($dbcon!=null) $dbcon->close();
		exit();
	} elseif($_REQUEST["action"]=="itemdelete" && isset($_REQUEST["menuid"]) && isset($_REQUEST["itemid"])) {
		if($_SESSION['SESS_PRIVILEGE_ID']<=3) {
			$sql="delete from "._dbTable('links')." where (site='{$_REQUEST['forsite']}' OR site='*') and menuid='{$_REQUEST['menuid']}' AND (id={$_REQUEST['itemid']} OR menugroup={$_REQUEST['itemid']})";
		} else {
			$sql="delete * from "._dbTable('links')." where site='{$_REQUEST['forsite']}' and menuid='{$_REQUEST['menuid']}' AND (id={$_REQUEST['itemid']} OR menugroup={$_REQUEST['itemid']})";
		}
		if(strlen($sql)>0) {
			$result=$dbcon->executeQuery($sql);
			if(!$result) {
				echo "Error Deleting MenuItem.";
			}
		} else {
		}
		flushPermissionCache();
		if($dbcon!=null) $dbcon->close();
		exit();
	} elseif($_REQUEST["action"]=="itemsave" && isset($_REQUEST["menuid"]) && isset($_REQUEST["itemid"])) {
		if($_REQUEST["itemid"]!=$_POST['id']) {
			exit("Misconfigured Save Request.");
		}
		unset($_POST['id']);
		
		if(isset($_POST['appsite'])) {
			$_POST['site']=$_POST['appsite'];
			unset($_POST['appsite']);
		}
		if(isset($_POST['isMenuGroup'])) {
			if($_POST['isMenuGroup']=="true") {
				$_POST['menugroup']="";
				$_POST['category']="";
				$_POST['blocked']="false";
				$_POST['onmenu']="true";
			}
			unset($_POST['isMenuGroup']);
		}
		if($_REQUEST["itemid"]<="0") {
			$_POST['userid']=$_SESSION['SESS_USER_ID'];
			$_POST['doc']=date("Y-m-d");
			$_POST['doe']=date("Y-m-d");
			
			$cols=array();
			$vals=array();
			foreach($_POST as $a=>$b) {
				array_push($cols,$a);
				array_push($vals,"'$b'");
			}
			$cols=implode(",",$cols);
			$vals=implode(",",$vals);
			
			$sql="INSERT INTO "._dbTable('links')." ($cols) VALUES ($vals)";
			
			$result=$dbcon->executeQuery($sql);
			if(!$result) {
				echo $dbcon->getError();
				echo "Error Creating MenuItem.";
			}
		} else {
			$_POST['userid']=$_SESSION['SESS_USER_ID'];
			$_POST['doe']=date("Y-m-d");
			
			$sets=array();
			foreach($_POST as $a=>$b) {
				array_push($sets,"$a='$b'");
			}
			$sets=implode(",",$sets);
			$sql="UPDATE "._dbTable('links')." SET $sets";
			$sql.=" where menuid='{$_REQUEST['menuid']}' AND id={$_REQUEST['itemid']}";
			
			$result=$dbcon->executeQuery($sql);
			if(!$result) {
				echo $dbcon->getError();
				echo "Error Updating MenuItem.";
			}
		}
		flushPermissionCache();
		if($dbcon!=null) $dbcon->close();
		exit();
	} 
	
	
	elseif($_REQUEST["action"]=="generators") {
		$lf=$_SESSION["APP_FOLDER"]["APPROOT"].$_SESSION["APP_FOLDER"]["APPS_CONFIG_FOLDER"]."menugenerator.json";
		
		if(file_exists($lf)) {
			$json=json_decode(file_get_contents($lf),true);
			
			foreach($json as $a=>$b) {
				$icon=$b['icon'];
				if(strlen($icon)>0) {
					$icon=MenuTree::findMedia($icon);
				}
				$s="<tr rel='{$a}'>";
				$s.="<td class='icon' name='icon' rel='{$b['icon']}' align=center><img src='{$icon}' width=20px height=20px alt='' /></td>";
				$s.="<td class='menu editable' name='menu' rel='{$b['menu']}'>{$b['menu']}</td>";
				$s.="<td class='table editable' name='table' rel='{$b['table']}'>{$b['table']}</td>";
				$s.="<td class='lnk editable' name='lnk' rel='{$b['lnk']}'>{$b['lnk']}</td>";
				if($b['enabled'])
					$s.="<td class='enabled' name='enabled' rel='{$b['enabled']}' align=center><input type=checkbox checked=true /></td>";
				else
					$s.="<td class='enabled' name='enabled' rel='{$b['enabled']}' align=center><input type=checkbox /></td>";
				$s.="<td><div class='deleteicon right' onclick=\"$(this).parents('tr').detach();\"></div></td>";
				$s.="</tr>";
				echo $s;
			}
		}
		exit();
	} elseif($_REQUEST["action"]=="savesources" && isset($_POST['menu'])) {
		//printArray($_POST);echo json_encode($_POST['menu']);
		foreach($_POST['menu'] as $a=>$b) {
			if($b['enabled']=="false")
				$_POST['menu'][$a]['enabled']=false;
			else
				$_POST['menu'][$a]['enabled']=true;
		}
		$lf=$_SESSION["APP_FOLDER"]["APPROOT"].$_SESSION["APP_FOLDER"]["APPS_CONFIG_FOLDER"]."menugenerator.json";
		if(is_writable($lf)) {
			file_put_contents($lf,json_encode($_POST['menu']));
		}
		flushPermissionCache();
		exit("Save Successfull");
	}
	
	elseif($_REQUEST["action"]=="menugroups" && isset($_REQUEST["menuid"])) {
		$menuid=$_REQUEST["menuid"];
		if($_SESSION['SESS_PRIVILEGE_ID']<=3) {
			$sql="select id,menuid,title,category,menugroup from "._dbTable('links')." where (site='{$_REQUEST['forsite']}' OR site='*') and menuid='{$_REQUEST['menuid']}' AND (menugroup IS NULL OR menugroup='') AND (category IS NULL OR category='') ORDER BY id";
		} else {
			$sql="select id,menuid,title,category,menugroup from "._dbTable('links')." where site='{$_REQUEST['forsite']}' and menuid='{$_REQUEST['menuid']}' AND (menugroup IS NULL OR menugroup='') AND (category IS NULL OR category='') ORDER BY id";
		}
		echo "<option value=''>No Group</option>";
		if(strlen($sql)>0) {
			$result=$dbcon->executeQuery($sql);
			if($result) {
				$data=_dbData($result);
				foreach($data as $d) {
					echo "<option value='{$d['id']}'>{$d['title']}</option>";
				}
			}
			$dbcon->freeResult($result);
		}
		exit();
	} elseif($_REQUEST["action"]=="linksuggestions") {
		
		
		exit("");
	} elseif($_REQUEST["action"]=="classsuggestions") {
		
		
		exit("");
	} elseif($_REQUEST["action"]=="categorysuggestions") {
		
		
		exit("");
	} 
	
	elseif($_REQUEST["action"]=="preview" && isset($_REQUEST['link'])) {
		$url="../index.php?site={$_REQUEST['forsite']}&popup=true&{$_REQUEST['link']}";
		//printArray($_REQUEST);
		//echo $url;
		header("Location:$url");
		exit();
	}
}
function flushPermissionCache() {
	$f=ROOT.CACHE_PERMISSIONS_FOLDER."{$_REQUEST['forsite']}/";
	if(is_dir($f)) {
		$fs=scandir($f);
		foreach($fs as $a) {
			if($a=="." || $a=="..") continue;
			unlink("{$f}{$a}");
		}
	}
}
?>
