<?php
if(!defined('ROOT')) exit('No direct script access allowed');
checkServiceSession();

if(SITENAME!="cms") {
	printServiceMSG("ONLY CMS can access this service.");
	return;
}

$fDir=CMS_APPROOT.TEMPLATE_FOLDER;

switch($_REQUEST["action"]) {
	case "list":
		$fs=scandir($fDir);
		
		$fData=["NoGroup"=>[]];
		foreach($fs as $f) {
			if(strlen($f)>2) {
				$extArr=explode(".",$f);
				$ext=strtolower(end($extArr));
				if($ext=="tpl") {
					$sqlF=$fDir.$f.".sql";
					if(!file_exists($sqlF)) {
						$sqlF="";
					}
					if(count($extArr)>2) {
						$fData[$extArr[0]][]=[
							"title"=>str_replace(".tpl","",$f),
							"slug"=>$f,
// 							"file"=>$fDir.$f,
							"sql"=>basename($sqlF),
							"size"=>filesize($fDir.$f),
						];
					} else {
						$fData["NoGroup"][]=[
							"title"=>str_replace(".tpl","",$f),
							"slug"=>$f,
// 							"file"=>$fDir.$f,
							"sql"=>basename($sqlF),
							"size"=>filesize($fDir.$f),
						];
					}
				}
			}
		}
		printServiceMSG($fData);
		break;
	case "fetchTXT":
		if(isset($_POST['slug'])) {
			$f=$fDir.$_POST['slug'];
			if(file_exists($f)) {
				readfile($f);
			} else {
				echo "";
			}
		}
		break;
	case "fetchSQL":
		if(isset($_POST['slug'])) {
			$f=$fDir.$_POST['slug'].".sql";
			if(file_exists($f)) {
				readfile($f);
			} else {
				echo "";
			}
		}
		break;
	case "delete":
		if(isset($_POST['slug']) && strlen($_POST['slug'])>0) {
			$slugs=explode(",",$_POST['slug']);
			
			$ff=[];
			foreach($slugs as $s) {
				$ff[]=$fDir.$s;
				$ff[]=$fDir.$s.".sql";
			}
			$ff=array_flip($ff);
			foreach($ff as $f=>$s) {
				if(file_exists($f)) {
					$ff[$f]=1;
					unlink($f);
					if(file_exists($f)) {
						$ff[$f]=1;
					} else {
						$ff[$f]=0;
					}
				} else {
					$ff[$f]=0;
				}
			}
			if(array_sum($ff)<=0) {
				echo "Requested templates deleted successfully.";
			} else {
				echo "error: Sorry, some of the requested templates could not be deleted.";
			}
		} else {
			echo "error: Reference Not Found";
		}
		break;
	case "create":
		if(isset($_POST['slug'])) {
			$slug=_slugify($_POST['slug']);
			
			$_POST['slug']=$_POST['slug'].".tpl";
			
			$f=$fDir.$_POST['slug'];
			$sx=file_put_contents($f," ");
			
			if(file_exists($f)) {
				return $_POST['slug'];
			} else {
				echo "error: Create failed. Try again later.";
			}
		} else {
			echo "error: Reference Not Found";
		}
		break;
	case "save":
		if(isset($_POST['slug'])) {
			$slug=$_POST['slug'];
			$ext=strtolower($_POST['srctype']);
			
			$fl=$fDir.str_replace(".tpl.tpl",".tpl","{$_POST['slug']}.{$ext}");
			
			$sx=file_put_contents($fl,$_POST['text']);
			
			if(file_exists($fl)) {
				echo "Update successfull";
			} else {
				echo "error: Create failed. Try again later.";
			}
		} else {
			echo "error: Reference Not Found";
		}
		break;
	case "preview":
		break;
}
?>