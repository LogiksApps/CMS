<?php
if(!defined('ROOT')) exit('No direct script access allowed');
checkServiceAccess();

$apps=_session("siteList");
if(!isset($_GET['forsite']) || !in_array($_GET['forsite'],array_keys($apps))) {
	printServiceMsg(['msg'=>"Sorry, could not find the app or you do not have permission to operate on the app","error"=>true]);
	return;
}

loadModule("packages");

define("PACKAGE_CACHE_PERIOD",86400);

if(!isset($_REQUEST['src'])) $_REQUEST['src']="installed";
if(!isset($_REQUEST['type'])) $_REQUEST['type']="modules";

switch($_REQUEST["action"]) {
	case "types":
		$result=[];
		
		switch(strtolower($_REQUEST['src'])) {
			case "installed":
				$result=["modules"=>"modules"];
				break;
			case "repos":
				$result=["modules"=>"modules"];
				break;
		}
		printServiceMsg($result);
		break;
	case "categories":
		$result=[];
		
		switch(strtolower($_REQUEST['src'])) {
			case "installed":
				$result=["General"=>"general"];
				break;
			case "repos":
				$result=["General"=>"general"];
				break;
		}
		printServiceMsg($result);
		break;
	case "getlist":
		$result=[];
		
		switch(strtolower($_REQUEST['src'])) {
			case "installed":
				$result=fetch_package_list($_REQUEST['type'],false,true);
				usort($result, "sortPackages");
				
				foreach($result as $a=>$b) {
				    unset($result[$a]['path']);
				    unset($result[$a]['fullpath']);
				    unset($result[$a]['logiksinfo']);
				}
				break;
			case "repos":
				break;
		}
		
		if(isset($_REQUEST['q']) && strlen($_REQUEST['q'])>0) {
			$q=$_REQUEST['q'];
			foreach($result as $a=>$b) {
				if(strpos($a,$q)===false) {
					unset($result[$a]);
				}
			}
		}
		
		printServiceMsg($result);
		break;
	case "packinfo":
		if(isset($_POST['packid'])) {
			include_once __DIR__."/pages/packinfo.php";
		} else {
			echo "<h3 align=center>No Package Could Be Indentified</h3>";
		}
		break;
	case "storeinfo":
		if(isset($_POST['packid'])) {
			include_once __DIR__."/pages/storeinfo.php";
		} else {
			echo "<h3 align=center>No Package Could Be Indentified</h3>";
		}
		break;
	case "archive":
	    if(isset($_POST['packid'])) {
	        $packageInfo = fetch_package_info_fromid($_POST['packid']);
	        $fullpath = $packageInfo['fullpath'];
	        $fname = basename($fullpath);
	        if(substr($fname,0,1)=="~") {
	            //unarchive
	            $newpath = dirname($fullpath)."/".substr($fname,1);
	            $msg = "Restoration complete";
	        } else {
	            //archive
	            $newpath = dirname($fullpath)."/~".$fname;
	            $msg = "Archiving complete";
	        }
	        if(file_exists($newpath)) {
	            printServiceMsg(["msg"=>"Package with same name exists"]);
	            return;
	        }
	        $a = rename($fullpath,$newpath);
	        if($a) {
	            printServiceMsg(["msg"=>"Archiving Complete"]);
	        } else {
	            printServiceMsg(["msg"=>"Archiving Failed, may be you don't have permissions"]);
	        }
	    } else {
	        printServiceMsg(["msg"=>">No Package Could Be Indentified"]);
	    }
	    break;
	case "reinstall":case "reconfigure":
	    if(isset($_POST['packid'])) {
	        $packageInfo = fetch_package_info_fromid($_POST['packid']);
	        $fullpath = $packageInfo['fullpath'];
	        
	        $msg = configure_package($fullpath);
	        printServiceMsg(["msg"=>$msg]);
	    } else {
	        printServiceMsg(["msg"=>">No Package Could Be Indentified"]);
	    }
	    break;
	case "upload":
	    if(isset($_FILES["attachment"])) {
	        if($_FILES["attachment"]['error']>0) {
	            $msg = "Error Uploading file";
                echo "{$msg}<script>parent.lgksAlert('{$msg}');</script>";
	        } else {
	            $tempDir = _dirTemp("packages");
                $downloadFile = "{$tempDir}".$_FILES["attachment"]['name'];
            
                $extArr = explode(".",$_FILES["attachment"]['name']);
                $ext = strtolower(end($extArr));
                if(!in_array($ext,["zip","gzip"])) {
                    $msg = "Only zip files can be uploaded";
                    echo "{$msg}<script>parent.lgksAlert('{$msg}');</script>";
                    return;
                }
            
	            move_uploaded_file($_FILES["attachment"]['tmp_name'],$downloadFile);
	            
	            if(file_exists($downloadFile)) {
                    installPackageZipFile($downloadFile);
                } else {
                    $msg = "Attachment could not be downloaded";
                    echo "{$msg}<script>parent.lgksAlert('{$msg}');</script>";
                }
	        }
	    } else {
	        $msg = "Attachment not found";
            echo "{$msg}<script>parent.lgksAlert('{$msg}');</script>";
	    }
	    break;
    case "attachuri":
        if(isset($_POST["attachment"])) {
            $tempDir = _dirTemp("packages");
            $downloadFile = "{$tempDir}".basename($_POST["attachment"]);
            if(!is_dir($tempDir)) mkdir($tempDir,0777,true);
            
            file_put_contents($downloadFile, fopen($_POST["attachment"], 'r'));
            
            if(file_exists($downloadFile)) {
                installPackageZipFile($downloadFile);
            } else {
                $msg = "Attachment could not be downloaded";
                echo "{$msg}<script>parent.lgksAlert('{$msg}');</script>";
            }
        } else {
            $msg = "Attachment not found";
            echo "{$msg}<script>parent.lgksAlert('{$msg}');</script>";
        }
	    break;
	case "installFromStore":
	    printServiceMsg(["msg"=>">No Package Could Be Indentified"]);
	    break;
}

function installPackageZipFile($downloadFile) {
    $tempDir = _dirTemp("packages");
    $cacheDir = $tempDir."cache/".basename($downloadFile)."/";
    
    if(is_dir($cacheDir)) {
        deleteFolder($cacheDir);
    }
    if(!is_dir($cacheDir)) mkdir($cacheDir,0777,true);
    
    $zip = new ZipArchive;
    $res = $zip->open($downloadFile);
    if ($res === TRUE) {
      $zip->extractTo($cacheDir);
      $zip->close();
      
      $fs = scandir($cacheDir);
      $fs = array_splice($fs,2);
      if(count($fs)==1) {
        $cacheDir = $cacheDir."{$fs[0]}/";
        $fs = scandir($cacheDir);
        $fs = array_splice($fs,2);
      } elseif(count($fs)>1) {
        
      } else {
          $msg = "Error unzipping the zip file";
          echo "{$msg}<script>parent.lgksAlert('{$msg}');</script>";
          return;
      }
      
      if(in_array("logiks.json",$fs)) {
        $fname = basename($cacheDir);
        $fnameNew = str_replace("-master","",str_replace(".zip","",$fname));
        $targetDir = CMS_APPROOT."plugins/modules/{$fnameNew}/";
        
        if(file_exists($targetDir) && is_dir($targetDir)) {
            $msg = "Target module folder already exists. Please remove that first.";
            echo "{$msg}<script>parent.lgksAlert('{$msg}');</script>";
            return;
        }
        
        if(file_exists($downloadFile)) unlink($downloadFile);
          
        $a = rename($cacheDir,$targetDir);
          
        if(is_dir($targetDir)) {
            $msg = configure_package($targetDir);
            echo "{$msg}<script>parent.lgksAlert('{$msg}');</script>";
        } else {
            $msg = "Could not copy into modules folder. Check permissions.";
            echo "{$msg}<script>parent.lgksAlert('{$msg}');</script>";
        }
      } else {
        $msg = "Uploaded package is corrupted. Cannot install this one.";
        echo "{$msg}<script>parent.lgksAlert('{$msg}');</script>";
        return;
      }
    } else {
      $msg = "Unzip Failed. Try installing again.";
      echo "{$msg}<script>parent.lgksAlert('{$msg}');</script>";
    }
}

function sortPackages($a, $b) {
    if($a['category']==$b['category']) {
        return (strtolower($a['name'])>strtolower($b['name']));
    } else {
        return (strtolower($a['category'])<strtolower($b['category']));
    }
    
}
?>