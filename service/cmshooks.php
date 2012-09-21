<?php
if(!defined('ROOT')) exit('No direct script access allowed');
checkServiceSession();

loadHelpers("imageprops");

if(isset($_REQUEST["action"]) && isset($_REQUEST['forsite'])) {
	checkUserSiteAccess($_REQUEST['forsite'],true);
	loadModule("dbcon");
	$folders=loadFolderConfig();
	
	$result=array();
	$result['status']="";
	$result['msg']="";
	
	$hooksFolder=$folders["APPROOT"].$folders["APPS_MISC_FOLDER"]."hooks/";
	
	if(!is_dir($hooksFolder)) {
		if(mkdir($hooksFolder,0777,true)) {
			chmod($hooksFolder,0777);
		}
	}
	if(!is_dir($hooksFolder)) {
		$arr=array(
				"Error"=>"Failed To Find Base Folder.",
			);
		printFormattedArray($arr);
		exit();
	}
	if($_REQUEST["action"]=="listhooks") {
		$p=$hooksFolder;
		if(file_exists($p) && is_dir($p)) {
			$str="";
			$fs=scandir($p);
			unset($fs[0]);unset($fs[1]);		
			if(count($fs)>0) {
				$str="<ul>";
				foreach($fs as $a) {
					$str.="<li><h3>$a</h3>";
					$fs1=scandir($p.$a);
					unset($fs1[0]);unset($fs1[1]);
					if(count($fs)>0) {
						$str.="<ul>";
						foreach($fs1 as $b) {
							$th="{$a}/{$b}";
							$str.="<li fl='$th'>$b</li>";
						}
						$str.="</ul>";
					}			
					$str.="</li>";
				}
				$str.="</ul>";
			} else {
				$str="<h3 align=center>0 States Found</h3>";
			}
			echo $str;
		} else {
			echo "<h3 align=center>Hooks Not Supported</h3>";
		}
		exit();
	} elseif($_REQUEST["action"]=="fetch" && isset($_REQUEST["fetch"])) {
		$f=$hooksFolder.$_REQUEST["fetch"];
		if(file_exists($f) && is_file($f)) {
			echo file_get_contents($f);
		}
		exit();
	} elseif($_REQUEST["action"]=="save" && isset($_REQUEST["save"]) && isset($_POST["code"])) {
		$f=$hooksFolder.$_REQUEST["save"];
		if(file_exists($f) && is_file($f)) {
			if(is_writable($f)) {
				$data=$_POST['code'];
				$data=cleanText($data);
				$a=file_put_contents($f,$data);
				if(!$a) echo "Error While Saving Hook To File.";			
			} else {
				echo "Target Hook File Is Not Writable.";
			}
		} else {
			echo "Could Not Find Target Hook File.";
		}
		exit();
	} elseif($_REQUEST["action"]=="delete" && isset($_REQUEST["delete"])) {
		$f=$hooksFolder.$_REQUEST["delete"];
		if(file_exists($f) && is_file($f)) {
			$a=unlink($f);
			if(!$a) echo "Error While Deleting Hook To File.";	
		}
		exit();
	} elseif($_REQUEST["action"]=="block" && isset($_REQUEST["block"])) {
		$f=$hooksFolder.$_REQUEST["block"];
		if(file_exists($f) && is_file($f)) {
			if(strpos($f,"~")>2) {
				$a=rename($f,dirname($f)."/".substr(basename($f),1));
				if(!$a) echo "Error While UnBlocking Hook.";
			} else {
				$a=rename($f,dirname($f)."/~".basename($f));
				if(!$a) echo "Error While Blocking Hook.";
			}
		}
		exit();
	} elseif($_REQUEST["action"]=="create" && isset($_REQUEST["create"])) {
		$f=$hooksFolder.$_REQUEST["create"];
		$d=dirname($f);
		if(!(file_exists($d) && is_dir($d))) {
			if(mkdir($d,0777,true)) {
				chmod($d,0777);
			}
		}
		file_put_contents($f,"");
		@chmod($f,0777);
		exit();
	}
}
printErr("WrongFormat");
exit();

?>
