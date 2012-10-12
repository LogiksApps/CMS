<?php
if(!defined('ROOT')) exit('No direct script access allowed');
checkServiceSession();
checkUserSiteAccess($_REQUEST['forsite'],true);
user_admin_check(true);

if(isset($_REQUEST["action"])) {
	loadModule("dbcon");loadFolderConfig();
	$lf=$_SESSION["APP_FOLDER"]["APPROOT"].$_SESSION["APP_FOLDER"]["APPS_MISC_FOLDER"]."lookups/";
	
	if($_REQUEST["action"]=="list") {
		if(!is_dir($lf)) {
			if(mkdir($lf,0777,true)) {
				chmod($lf,0777);
			}
			echo "<option value='#'>No Lookups Defined</option>";
		} else {
			$fs=scandir($lf);
			unset($fs[0]);unset($fs[1]);
			if(count($fs)<=0) {
				echo "<option value='#'>No Lookups Defined</option>";
			} else {
				foreach($fs as $a) {
					$t=$a;
					$t=str_replace(".dat","",$t);
					$t=ucwords($t);
					echo "<option value='$a'>$t</option>";
				}
			}
		}
		exit();
	} elseif($_REQUEST["action"]=="data" && isset($_REQUEST["lookup"])) {
		$f=$lf.$_REQUEST["lookup"];
		if(file_exists($f)) {
			readfile($f);
			exit();
		} else {
			exit("No Lookup File Content Found. May be it was deleted earlier.");
		}
	} elseif($_REQUEST["action"]=="delete" && isset($_REQUEST["lookup"])) {
		$f=$lf.$_REQUEST["lookup"];
		if(file_exists($f)) {
			if(!unlink($f)) {
				exit("The Lookup File Is ReadOnly. It Could Not Be Deleted.");
			}
			exit();
		} else {
			exit("No Lookup File Content Found. May be it was deleted earlier.");
		}
	} elseif($_REQUEST["action"]=="save" && isset($_REQUEST["lookup"]) && isset($_POST["data"])) {
		$f=$lf.$_REQUEST["lookup"];
		if(is_writable($f)) {
			$data=$_POST['data'];
			$data=cleanText($data);
			$a=file_put_contents($f,$data);
			if($a==strlen($data)) echo "success";
			else {
				exit("Error In Updating Lookup File. Try Again.");
			}
		} else {
			exit("The Lookup File Is ReadOnly. It Could Not Be Updated.");
		}
		exit();
	} elseif($_REQUEST["action"]=="blank" && isset($_REQUEST["lookup"])) {
		$f=$lf.$_REQUEST["lookup"].".dat";
		if(file_exists($f)) {
			exit("<b>{$_REQUEST["lookup"]}</b> Lookup Exists Already, Please change the name.");
		}
		$a=file_put_contents($f,"");
		if(file_exists($f)) {
			echo "success";
			chmod($f,0666);
		} else {
			exit("Error In Updating Lookup File. Try Again.<br/>May Be The Lookup Directory Is ReadOnly.");
		}
		exit();
	} elseif($_REQUEST["action"]=="upload") {
		if(count($_FILES)>0) { 
			$uArr=saveUploadedLookupFile($lf); 
			if(sizeOf($uArr)>0) {
				exit("<script>parent.clearUploadField({$uArr[0]});</script>Succefully Uploaded Lookup File");
			} else {
				exit("Sorry, Lookup File Could Not Be Uploaded.");
			}
			exit(); 
		}
		else exit("Error, No File Upload Found");
	}
}

function saveUploadedLookupFile($target) {
	$uArr=array();
	foreach($_FILES as $a=>$file) {
		$name=$file["name"];
		$name=explode(".",$name);
		unset($name[sizeOf($name)-1]);
		$name=implode(".",$name);
		$name=str_replace(".","_",$name).".dat";
		$file["name"]=$name;
		$a=basicUpload($file,$target);
		if(strlen($a)>2) {
			array_push($uArr,$name);
		}
	}
	return $uArr;
}
function basicUpload($fileArr,$targetDir) {
	$result = 0;
	$targetPath = $targetDir.basename($fileArr['name']);
	if(file_exists($targetPath)) {
		rename($targetPath,$targetDir."~".basename($targetPath));
	}
	if(@move_uploaded_file($fileArr['tmp_name'], $targetPath)) {
		$result = 1;
		chmod($targetPath,0666);
	}
	if($result) return $targetPath;
	else return "";
}
?>
