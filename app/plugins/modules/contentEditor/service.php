<?php
if(!defined('ROOT')) exit('No direct script access allowed');
checkServiceSession();
isAdminSite();
checkUserSiteAccess($_REQUEST['forsite'],true);
user_admin_check(true);

if(isset($_REQUEST["action"]) && isset($_REQUEST['src'])) {
	loadModule("dbcon");
	$dbCon=getDBControls($_REQUEST['forsite']);
	$cfgArr=loadFolderConfig($_REQUEST['forsite']);
	
	$json=$cfgArr['APPROOT']."misc/jsondb/contents.json";
	if(file_exists($json)) {
		$json=json_decode(file_get_contents($json),true);
	} else {
		printServiceErrorMsg("DataNotFound");
		exit();
	}
	if(!array_key_exists($_REQUEST['src'],$json)) {
		printServiceErrorMsg("DataNotFound");
		exit();
	}
	$srcTable=$json[$_REQUEST['src']]['src'];
	$cols=$json[$_REQUEST['src']]['cols'];
	$arr=array();
	switch($_REQUEST["action"]) {
			case "fetch":
				$sql=$dbCon->_selectQ($srcTable,$cols,null,"id");
				$res=$dbCon->executeQuery($sql);
				$data=_dbData($res);
				$dbCon->freeResult($res);
				
				if(count($data)>0) $arr=$data;
				else $arr="<h3 align=center>No Content Found</h3>";
				break;
			case "create":
				$_POST['country']=$_POST['title'];
				$_POST['dtoc']=date("Y-m-d H:i:s");
				$_POST['dtoe']=$_POST['dtoc'];
				$_POST['likes']=0;
				$_POST['blocked']='false';
				
				$sql=$dbCon->_insertQ1($srcTable,$_POST);
				$res=$dbCon->executeQuery($sql);
				if(!$res) $arr="Sorry, Creating Record Failed";
				break;
			case "update":
				$_POST['title']=$_POST['country'];
				$_POST['dtoe']=date("Y-m-d H:i:s");
				if($_FILES['banner_photo']['error']==0) {
					$photoDir=$cfgArr['APPROOT'].$cfgArr['APPS_USERDATA_FOLDER']."places_photos/photos/";
					if(is_writable($photoDir)) {
						$ext=end(explode(".",$_FILES['banner_photo']['name']));
						$targetPath=$photoDir.$_POST['country'].".{$ext}";//md5($_POST['country'].$_POST['dtoe'])
						$a=move_uploaded_file($_FILES["banner_photo"]["tmp_name"],$targetPath);
						if($a) $_POST['banner_photo']="places_photos/photos/".$_POST['country'].".{$ext}";
						else $_POST['banner_photo']="places_photos/photos/default.png";
					}
				}
				$sql=$dbCon->_updateQ($srcTable,$_POST,array("id"=>$_REQUEST['id']));
				
				$res=$dbCon->executeQuery($sql);
				if(!$res) $arr="Sorry, Updating Failed ";
				break;
			case "editor":
				include dirname(__FILE__)."/editor.php";
				break;
			case "delete":
				if(isset($_REQUEST['ref'])) {
					$sql=$dbCon->_deleteQ($srcTable,array("id"=>$_REQUEST['ref']));
					$res=$dbCon->executeQuery($sql);
					if(!$res) $arr="Sorry, Blocking/Unblocking Failed";
				}
				break;
			case "block":
				if(isset($_REQUEST['ref'])) {
					$sql=$dbCon->_updateQ($srcTable,array("blocked"=>$_REQUEST['block']),array("id"=>$_REQUEST['ref']));
					$res=$dbCon->executeQuery($sql);
					if(!$res) $arr="Sorry, Blocking/Unblocking Failed";
				}
				break;
			case "filter":
				$filter=$json[$_REQUEST['src']]['filter'];
				$sql=$dbCon->_selectQ($srcTable,$filter,null,$filter);
				$res=$dbCon->executeQuery($sql);
				$data=_dbData($res);
				$dbCon->freeResult($res);
				
				if(count($data)>0) {
					if($_REQUEST['format']=="select") {
						echo "<option value=''>All</option>";
						foreach($data as $a) {
							echo "<option value='{$a['continent']}'>{$a['continent']}</option>";
						}
					} else {
						$arr=$data;
					}
				}
				else $arr="<h3 align=center>No Content Found</h3>";
				break;
	}
	if(count($arr)>0)
		printServiceMsg($arr);
} else {
	printErr("WrongFormat","Requested Format Ommits Required Fields.");
}
?>