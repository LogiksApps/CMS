<?php
if(!defined('ROOT')) exit('No direct script access allowed');

//checkServiceAccess();

if(!isset($_REQUEST["action"])) {
	printServiceErrorMsg("NotAcceptable","Action Not Defined.");
}

switch($_REQUEST["action"]) {
	case "upload":
		$siteList=_session("siteList");
		if($siteList==null || !array_key_exists($_REQUEST['forsite'],$siteList)) {
			printUploadMsg("Unauthorized Access to {$_REQUEST['forsite']}");
			exit();
		}
		$uploadPath=CMS_APPROOT.$_POST['path'];
		$uploadPath=str_replace('//','/',$uploadPath);
		if(!file_exists($uploadPath) || !is_dir($uploadPath)) {
			mkdir($uploadPath,0777,true);
		}
		$done=doUpload($uploadPath);
		if($done=="DONE") {
			printUploadMsg('DONE');
		} else {
			if(is_array($done)) {
				$done=implode(", ",$done);
			}
			printUploadMsg("Error Uploading Some Files : {$done}");
		}
		break;
}
function doUpload($uploadPath) {
	$ans=0;
	$ansArr=[];
	foreach($_FILES['files']['name'] as $a=>$b) {
		$finalTmp=$_FILES['files']['tmp_name'][$a];
		$finalName=$b;
		$finalError=$_FILES['files']['error'][$a];
		if($finalError==0) {
			$finalPath=$uploadPath.$finalName;
			//println("$finalTmp $finalPath");$a=true;
			$a=move_uploaded_file($finalTmp,$finalPath);
			if(!$a) {
				$ansArr[]=$b;
				$ans+=1;
			}
		} else {
			$ansArr[]=$b."#".$finalError;
			$ans+=$finalError;
		}
	}
	if($ans>0) return $ansArr;
	else return "DONE";
}
function printUploadMsg($msg) {
	echo "{$msg}<script>parent.uploadMsg('{$msg}');</script>";
}
?>