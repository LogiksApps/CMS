<?php
if(!defined('ROOT')) exit('No direct script access allowed');
checkServiceAccess();

define("MAX_DEPTH",8);
$_ENV['HIDDEN']=[
		"tmp",
		".git",
	];
$_ENV['NOSCAN']=[
        "usermedia",
		"tmp",
		"temp",
		".git",
// 		".install",
		".."
	];

switch ($_REQUEST['action']) {
	case 'listFiles':
		if(checkUserRoles("FILES","LIST")) {
			loadHelpers("files");

			$APPPATH=ROOT.APPS_FOLDER.$_GET['forsite']."/";
			printServiceMsg(array_reverse(scanFolderTree($APPPATH)));
		} else {
			printServiceMsg([]);
		}
		break;
  case "newtopdir":
    if(isset($_POST['fname'])) {
      $_POST['path'] = $_POST['fname'];
    } else {
      echo "Folder name not found";
      exit();
    }
	case "newFolder":
		if(isset($_POST['path'])) {
			$fDir=str_replace("//","/",CMS_APPROOT.$_POST['path']);
			if(file_exists($fDir)) {
				echo "Folder with same name exists at the given path.";
				return;
			}
      
			mkdir($fDir,0777,true);
			if(!is_dir($fDir)) {
				echo "New Folder Could Not Be created.";
			} else {
				_log("New Folder : {$_POST['path']} #".CMS_SITENAME." @{$_SESSION['SESS_USER_ID']}","files");
				echo "FILE:{$_POST['path']}";
			}
		} else {
			echo "New Path Not Found.";
		}
		break;
	case "newFile":
		if(isset($_POST['path'])) {
			$fDir=str_replace("//","/",CMS_APPROOT.$_POST['path']);
			if(file_exists($fDir)) {
				echo "File with same name exists at the given path.";
				return;
			}
			file_put_contents($fDir,"");
			if(!is_file($fDir)) {
				echo "New Folder Could Not Be created.";
			} else {
				_log("New File : {$_POST['path']} #".CMS_SITENAME." @{$_SESSION['SESS_USER_ID']}","files");
				echo "FILE:{$_POST['path']}";
			}
		} else {
			echo "New Path Not Found.";
		}
		break;
	case "rm":
    if(isset($_POST['path'])) {
			$fDir=str_replace("//","/",CMS_APPROOT.$_POST['path']);
			if(!file_exists($fDir)) {
				echo "File/Folder does not exist.";
				return;
			}
      deleteFolder($fDir);
      if(!file_exists($fDir)) echo "Deleted file successfully";
      else echo "Error deleting file. Check if its readonly";
		} else {
			echo "New Path Not Found.";
		}
		break;
	case "cp":
    if(isset($_POST['path']) && isset($_POST['newpath'])) {
      $f1=str_replace("//","/",CMS_APPROOT.$_POST['path']);
      $f2=str_replace("//","/",CMS_APPROOT.$_POST['newpath']);
      
      $f1 = explode("/",$f1);
      if($f1[count($f1)-1]=="") {
        unset($f1[count($f1)-1]);
        $f1 = implode("/",$f1);
      } else {
        $f1 = implode("/",$f1);
      }
      
      $f2 = $f2.basename($f1);
      
      $a = copy($f1,$f2);
      if($a) {
        echo "Copy successfull";
      } else {
        echo "Copy failed";
      }
    } else {
      echo "Path Not Found.";
    }
		break;
	case "mv":
    if(isset($_POST['path']) && isset($_POST['newpath'])) {
      $f1=str_replace("//","/",CMS_APPROOT.$_POST['path']);
      $f2=str_replace("//","/",CMS_APPROOT.$_POST['newpath']);
      
      $f1 = explode("/",$f1);
      if($f1[count($f1)-1]=="") {
        unset($f1[count($f1)-1]);
        $f1 = implode("/",$f1);
      } else {
        $f1 = implode("/",$f1);
      }
      
      $f2 = $f2.basename($f1);

      $a = rename($f1,$f2);
      if($a) {
        echo "Move successfull";
      } else {
        echo "Move failed";
      }
    } else {
      echo "Path Not Found.";
    }
		break;
	case "clone":
    if(isset($_POST['path'])) {
      $f1 = str_replace("//","/",CMS_APPROOT.$_POST['path']);
      $f2 = str_replace("//","/",CMS_APPROOT.$_POST['path']);
      $f2 = explode(".",$f2);
      if(isset($f2[count($f2)-2])) {
        $f2[count($f2)-2] .= "_copy";
        $f2 = implode(".",$f2);
        
        $a = copy($f1,$f2);
        if($a) {
          echo "Clone successfull";
        } else {
          echo "Clone failed";
        }
      } else {
        echo "Error cloning the file";
      }
    } else {
      echo "Path Not Found.";
    } 
		break;
  case "cpdir":
    if(isset($_POST['path']) && isset($_POST['newpath'])) {
      $f1=str_replace("//","/",CMS_APPROOT.$_POST['path']);
      $f2=str_replace("//","/",CMS_APPROOT.$_POST['newpath']);
      
      $f1 = explode("/",$f1);
      if($f1[count($f1)-1]=="") {
        unset($f1[count($f1)-1]);
        $f1 = implode("/",$f1);
      } else {
        $f1 = implode("/",$f1);
      }
      
      $f2 = $f2.basename($f1);

      $a = copyFolder($f1,$f2);
    } else {
      echo "Path Not Found.";
    }
    break;
  case "mvdir":
    if(isset($_POST['path']) && isset($_POST['newpath'])) {
      $f1=str_replace("//","/",CMS_APPROOT.$_POST['path']);
      $f2=str_replace("//","/",CMS_APPROOT.$_POST['newpath']);
      
      $f1 = explode("/",$f1);
      if($f1[count($f1)-1]=="") {
        unset($f1[count($f1)-1]);
        $f1 = implode("/",$f1);
      } else {
        $f1 = implode("/",$f1);
      }
      
      $f2 = $f2.basename($f1);

      $a = copyFolder($f1,$f2);
      unlink($f1);
    } else {
      echo "Path Not Found.";
    }
    break;
	case "rename"://rename
    if(isset($_POST['path']) && isset($_POST['newname'])) {
      $f1=str_replace("//","/",CMS_APPROOT.$_POST['path']);
      $f2=str_replace("//","/",CMS_APPROOT.$_POST['path']);
      $f2 = explode("/",$f2);
      
      if($f2[count($f2)-1]=="") {
        unset($f2[count($f2)-1]);
        $f1 = implode("/",$f2);
      }
      
      $f2[count($f2)-1] = clean($_POST['newname']);
      $f2 = implode("/",$f2);
      
      $a = rename($f1,$f2);
      if($a) {
        echo "Rename successfull";
      } else {
        echo "Rename failed";
      }
    } else {
      echo "New Path Not Found.";
    }
		break;
  case "permissions"://permissions
		break;
}
function scanFolderTree($folder,$depth=0) {
	if($depth>MAX_DEPTH) return [];
	
	$files=array();
	$folders=array();
	if(is_dir($folder)) {
		$out=scandir($folder);
		$out=array_splice($out, 2);
		asort($out);
		foreach($out as $key => $value) {
		    if(in_array($value,$_ENV['NOSCAN'])) continue;
		    
			$bf=$folder.$value;
			$bf=str_replace(ROOT.APPS_FOLDER."{$_GET['forsite']}/", "", $bf);
			if(in_array($bf, $_ENV['HIDDEN'])) continue;
			
			if(is_dir($folder.$value)) {
				$folders[$value]=scanFolderTree($folder.$value."/",$depth+1);
			} else {
				$files[]=$value;
			}
		}
	}
	$files = array_reverse($files);
	return array_merge_recursive(array_reverse($folders),$files);
}
/* 
 * php delete function that deals with directories recursively
 */
function deleteFolder($target) {
    if(is_dir($target)){
//         $files = glob( $target . '*', GLOB_MARK); //GLOB_MARK adds a slash to directories returned
//         foreach( $files as $file ) {
//             deleteFolder( $file );      
//         }
        $files = scandir($target);
        $files = array_slice($files,2);
      
        foreach( $files as $file ) {
          if(is_dir($target.$file)) 
            deleteFolder($target.$file."/");
          else
            deleteFolder($target.$file);
        };
        
        rmdir( $target );
    } elseif(is_file($target)) {
        unlink( $target );  
    }
    return file_exists($target);
}

/* 
 * php copying function that deals with directories recursively
 */
function copyFolder($source, $dest, $overwrite = false,$basePath = ""){
    if(!is_dir($basePath . $dest)) //Lets just make sure our new folder is already created. Alright so its not efficient to check each time... bite me
    mkdir($basePath . $dest);
    if($handle = opendir($basePath . $source)){        // if the folder exploration is sucsessful, continue
        while(false !== ($file = readdir($handle))){ // as long as storing the next file to $file is successful, continue
            if($file != '.' && $file != '..'){
                $path = $source . '/' . $file;
                if(is_file($basePath . $path)){
                    if(!is_file($basePath . $dest . '/' . $file) || $overwrite)
                    if(!@copy($basePath . $path, $basePath . $dest . '/' . $file)){
                        echo '<font color="red">File ('.$path.') could not be copied, likely a permissions problem.</font>';
                    }
                } elseif(is_dir($basePath . $path)){
                    if(!is_dir($basePath . $dest . '/' . $file))
                    mkdir($basePath . $dest . '/' . $file); // make subdirectory before subdirectory is copied
                    copyFolder($path, $dest . '/' . $file, $overwrite, $basePath); //recurse!
                }
            }
        }
        closedir($handle);
    }
}
?>
