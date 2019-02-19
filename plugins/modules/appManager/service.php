<?php
if(!defined('ROOT')) exit('No direct script access allowed');
checkServiceAccess();

loadModule("packages");

$apps=_session("siteList");

switch($_REQUEST["action"]) {
	case "listImages":
		if(function_exists("listLogiksAppImages")) {
			if(isset($_REQUEST['recache']) && $_REQUEST['recache']=="true") {
				$appImages=listLogiksAppImages(true);
			} else {
				$appImages=listLogiksAppImages();
			}
			
			$appListFinal=[];//["_"=>[]]
			foreach($appImages as $a=>$b) {
				$category=current(explode("_",$b['name']));
				if($category=="") $category="_";
				//if($b['name']=="Apps_CMS") continue;
				
				$appListFinal[]=[//[$category]
					"hashid"=>$b['id'],
					"name"=>$b['name'],
					"full_name"=>$b['full_name'],
					"descs"=>$b['description'],
					"category"=>$category,
					"refid"=>$a,
					"url"=>$b['html_url'],
					"installed"=>0,
					"noinstall"=>(in_array(strtoupper($b['name']),["APPS_CMS"])),
					"last_update"=>current(explode("T",$b['pushed_at'])),
					"timestamp"=>$b['pushed_at'],
				];
			}
			printServiceMsg($appListFinal);
		} else {
			printServiceMsg([]);
		}
		break;
	case "listApps":
		$appsFinal=[];
    $_SESSION['SECUREAPPLIST'] = [];
		foreach($apps as $k=>$app) {
      if($_SESSION['SESS_PRIVILEGE_ID']!=1) {
        if(in_array($k,$_SESSION['SESS_ACCESS_SITES'])) continue;
      }
      
			$cfg=ROOT.APPS_FOLDER.$k."/apps.cfg";
			if(file_exists($cfg)) {
			    $cfgArr=LogiksConfig::parseConfigFile($cfg);
          $UUID = ceil(rand(1000,9999));
          $appsFinal[]=[
            "appkey"=>$k,
            "title"=>$cfgArr['DEFINE-APPS_NAME']['value'],
            "vers"=>$cfgArr['DEFINE-APPS_VERS']['value'],
            "devmode"=>$cfgArr['DEFINE-DEV_MODE_ENABLED']['value'],
            "status"=>$cfgArr['DEFINE-APPS_STATUS']['value'],
            "published"=>$cfgArr['DEFINE-PUBLISH_MODE']['value'],
            "router"=>$cfgArr['DEFINE-APPS_ROUTER']['value'],
            "access"=>$cfgArr['DEFINE-ACCESS']['value'],
            "url"=>SiteLocation."?site={$k}",
            "urlcms"=>$app['url'],
            "readonly"=>(!is_writable($cfg)),
            "database"=>0,
            "msgs"=>0,
            "cache"=>0,
            "domain"=>0,
            "services"=>0,
            "uuid"=>$UUID,
            "allow_rename"=>($k!=SITENAME),
            "allow_clone"=>($k!=SITENAME),
            "allow_delete"=>false,//($_SESSION['SESS_PRIVILEGE_ID']==1 && $k!=SITENAME)
            "allow_archive"=>($k!=SITENAME),
            //"cfg"=>$cfgArr
          ];
        $_SESSION['SECUREAPPLIST']["app/{$k}"] = $UUID;
			}
		}
		printServiceMsg($appsFinal);
		break;
  case "listArchivedApps":
    $appsFinal=[];
    $_SESSION['SECUREAPPLIST'] = [];
    $apps = scandir(ROOT.APPS_FOLDER.".trash/");
    $apps = array_slice($apps,2);
		foreach($apps as $k) {
			$cfg=ROOT.APPS_FOLDER.".trash/".$k."/apps.cfg";
			if(file_exists($cfg)) {
			    $cfgArr=LogiksConfig::parseConfigFile($cfg);
          $UUID = ceil(rand(1000,9999));
          $appsFinal[]=[
            "appkey"=>$k,
            "title"=>$cfgArr['DEFINE-APPS_NAME']['value'],
            "vers"=>$cfgArr['DEFINE-APPS_VERS']['value'],
            "devmode"=>$cfgArr['DEFINE-DEV_MODE_ENABLED']['value'],
            "status"=>$cfgArr['DEFINE-APPS_STATUS']['value'],
            "published"=>$cfgArr['DEFINE-PUBLISH_MODE']['value'],
            "router"=>$cfgArr['DEFINE-APPS_ROUTER']['value'],
            "access"=>$cfgArr['DEFINE-ACCESS']['value'],
            "url"=>SiteLocation."?site={$k}",
            "urlcms"=>"#",
            "readonly"=>(!is_writable($cfg)),
            "database"=>0,
            "msgs"=>0,
            "cache"=>0,
            "domain"=>0,
            "services"=>0,
            "uuid"=>$UUID,
            "allow_clone"=>false,
            "allow_delete"=>true,
            "allow_archive"=>false,
            //"cfg"=>$cfgArr
          ];
          $_SESSION['SECUREAPPLIST'][".trash/{$k}"] = $UUID;
			}
		}
		printServiceMsg($appsFinal);
    break;
	case "appEditor":
		if(isset($_POST['app']) && in_array($_POST['app'],array_keys($apps))) {
			$app=$_POST['app'];unset($_POST['app']);
			include_once __DIR__."/editor.php";
		} else {
			echo "<h2 class='errorBox'>Sorry, could not find the app, or you do not have permission to operate on the app</h2>";
		}
		break;
	case "saveApp":
		if(isset($_POST['app']) && in_array($_POST['app'],array_keys($apps))) {
			$app=$_POST['app'];unset($_POST['app']);
			
			$cfg=ROOT.APPS_FOLDER."{$app}/apps.cfg";
			if(!file_exists($cfg) || !is_writable($cfg)) {
				printServiceMsg(['msg'=>"App configuration not found or is readonly.","error"=>true]);
				return;
			}
			
			$out=[];
			foreach($_POST as $a=>$b) {
				$c=explode("-",$a);
				$x=$c[0];unset($c[0]);
				$y=implode("-",$c);
				$out[$x][]="{$y}={$b}";
			}
			$text="";
			foreach($out as $a=>$b) {
				$text.="[$a]\n".implode("\n",$b)."\n";
			}
			//printArray($out);
			$a=file_put_contents($cfg,$text);
			if($a>1) {
				printServiceMsg(['msg'=>"App configuration updated successfully."]);
			} else {
				printServiceMsg(['msg'=>"Error updating app configuration.","error"=>true]);
			}
		} else {
			echo "<h2 class='errorBox'>Sorry, could not find the app, or you do not have permission to operate on the app</h2>";
		}
    break;
  case "renameApp":
    if(isset($_POST['app']) && isset($_POST['name']) && in_array($_POST['app'],array_keys($apps))) {
      $app=$_POST['app'];unset($_POST['app']);
			$newApp = $_POST['name'];unset($_POST['name']);
      $newApp = _slugify($newApp);
      
      $newAppPath = ROOT.APPS_FOLDER.$newApp;
      if(file_exists($newAppPath) && is_dir($newAppPath)) {
        echo "New App already exists. Try some other name.";
        return;
      }
      $oldPath = ROOT.APPS_FOLDER.$app;
      if(!file_exists($oldPath) || !is_dir($oldPath)) {
        echo "Source App does not exist.";
        return;
      }
      
      rename($oldPath,$newAppPath);
      
      if(!is_dir($newAppPath) || !is_file($newAppPath."/apps.cfg")) {
        echo "Error renaming.";
        return;
      }
      
      renameAppConfig("db",$app,$newApp);
      renameAppConfig("cache",$app,$newApp);
      renameAppConfig("message",$app,$newApp);
      renameAppConfig("fs",$app,$newApp);
      renameAppConfig("errorlogs",$app,$newApp);
      renameAppConfig("services",$app,$newApp);
      
			echo "{$app} successfully renamed to {$newApp}";
    } else {
      echo "Renaming command structure error";
    }
    break;
	case "cloneApp":
		if(isset($_POST['app']) && isset($_POST['name']) && in_array($_POST['app'],array_keys($apps))) {
			$app=$_POST['app'];unset($_POST['app']);
			$newApp = $_POST['name'];unset($_POST['name']);
      $newApp = _slugify($newApp);
      
      $newAppPath = ROOT.APPS_FOLDER.$newApp;
      if(file_exists($newAppPath) && is_dir($newAppPath)) {
        echo "New App already exists. Try some other name.";
        return;
      }
      $oldPath = ROOT.APPS_FOLDER.$app;
      if(!file_exists($oldPath) || !is_dir($oldPath)) {
        echo "Source App does not exist.";
        return;
      }

      copyFolder($oldPath,$newAppPath);
      
      if(!is_dir($newAppPath) || !is_file($newAppPath."/apps.cfg")) {
        echo "Error cloning.";
        return;
      }
      
      //Remove UserData
      if(is_dir($newAppPath."/usermedia")) {
        deleteFolder($newAppPath."/usermedia/");
      }
      if(is_dir($newAppPath."/userdata")) {
        deleteFolder($newAppPath."/userdata/");
      }
      
      //database Todos
        //Create DB if possible
        //Copy tables if possible
        //Attach db to app
      
      cloneAppConfig("db",$app,$newApp);
      cloneAppConfig("cache",$app,$newApp);
      cloneAppConfig("message",$app,$newApp);
      cloneAppConfig("fs",$app,$newApp);
      cloneAppConfig("errorlogs",$app,$newApp);
      cloneAppConfig("services",$app,$newApp);

			echo "{$app} successfully cloned to {$newApp}";
		} else {
      echo "Clone command structure error";
    }
	  break;
  case "archiveApp":
    if(isset($_POST['app']) && in_array($_POST['app'],array_keys($apps))) {
      $app=$_POST['app'];unset($_POST['app']);
      $newApp = $app."_T".time();//.ceil(rand(1000,9999));
      
      $oldPath = ROOT.APPS_FOLDER.$app;
      if(!file_exists($oldPath) || !is_dir($oldPath)) {
        echo "Source App does not exist.";
        return;
      }
      
      $newAppPath = ROOT.APPS_FOLDER.".trash/".$newApp;
      
      copyFolder($oldPath,$newAppPath);
      
      if(!is_dir($newAppPath) || !is_file($newAppPath."/apps.cfg")) {
        echo "Error archiving the app";
        return;
      }
      deleteFolder("{$oldPath}/");
      if(file_exists($oldPath)) {
        echo "{$app} Archived successfully but could not be removed from apps folder<br>May be app is readonly";
      } else {
        echo "{$app} Archived successfully";
      }
    } else {
      echo "Archive command structure error";
    }
    break;
  case "restoreApp":
    if(isset($_POST['app'])) {
      if(!isset($_POST['type'])) $_POST['type'] = 1;
      $app=$_POST['app'];unset($_POST['app']);
      
      $oldPath = ROOT.APPS_FOLDER.".trash/".$app;
      if(!file_exists($oldPath) || !is_dir($oldPath)) {
        echo "Archived App does not exist.";
        return;
      }
      
      $appNew = explode("_T",$app);
      $appNew = $appNew[0];
      $appPath = ROOT.APPS_FOLDER.$appNew;
      if(is_dir($appPath)) {
        echo "Restoration Apppath already exists";
        return;
      }
      
      copyFolder($oldPath,$appPath);
      if(!is_dir($appPath)) {
        echo "Restoration Error";
        return;
      }
      
      switch($_POST['type']) {
        case 1://restore and keep
          break;
        case 2://restore and clear
          deleteFolder("{$oldPath}/");
          break;
      }
      
      echo "{$app} Restored successfully<br>This only restores the files, please setup the db manually";
    } else {
      echo "Restore command structure error";
    }
    break;
  case "deleteApp":
    if(isset($_POST['app'])) {
      $app=$_POST['app'];unset($_POST['app']);
      
      $appPath = ROOT.APPS_FOLDER.".trash/".$app."/";
      if(!file_exists($appPath) || !is_dir($appPath)) {
        echo "Archived App does not exist.";
        return;
      }
      deleteFolder($appPath);
      if(file_exists($appPath)) {
        echo "Error deleting app<br>May be app is readonly";
      } else {
        $appNew = explode("_T",$app);
        $appNew = $appNew[0];
        
        $appPath1 = ROOT.APPS_FOLDER.$appNew;
        if(!is_dir($appPath1)) {
          deleteAppConfig("db",$appNew);
          deleteAppConfig("cache",$appNew);
          deleteAppConfig("message",$appNew);
          deleteAppConfig("fs",$appNew);
          deleteAppConfig("errorlogs",$appNew);
          deleteAppConfig("services",$appNew);
          
          echo "{$app} Deleted successfully<br>Also removed related configurations";
        } else {
          echo "{$app} Deleted successfully<br>But config was not deleted as its being used by other app";
        }
      }
    } else {
      echo "Delete command structure error";
    }
    break;
  
  //Market Info
	case "appInfo":
		if(isset($_POST['refid'])) {
			$refid=$_POST['refid'];
			include_once __DIR__."/appinfo.php";
		} else {
			echo "<h2 class='errorBox'>Sorry, could not find the refid, try again later.</h2>";
		}
		break;
	case "install":
		if(isset($_POST['refid'])) {
			$refid=$_POST['refid'];
			printServiceMsg(installLogiksAppImage($refid));
		} else {
			printServiceMsg(["error"=>"Sorry, could not find the refid, try again later."]);
		}
		break;
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

function cloneAppConfig($configName, $appOld, $appNew) {
  $cfgFile = ROOT.CFG_FOLDER."jsonConfig/{$configName}.json";
  if(!file_exists($cfgFile)) return;

  $jsonConfig = file_get_contents($cfgFile);
  $jsonConfig = json_decode($jsonConfig,true);

  if(isset($jsonConfig[$appOld])) {
    $jsonConfig[$appNew] = $jsonConfig[$appOld];

    file_put_contents($cfgFile,json_encode($jsonConfig,JSON_PRETTY_PRINT));
  }
}
function renameAppConfig($configName, $appOld, $appNew) {
  $cfgFile = ROOT.CFG_FOLDER."jsonConfig/{$configName}.json";
  if(!file_exists($cfgFile)) return;

  $jsonConfig = file_get_contents($cfgFile);
  $jsonConfig = json_decode($jsonConfig,true);

  if(isset($jsonConfig[$appOld])) {
    $jsonConfig[$appNew] = $jsonConfig[$appOld];
    unset($jsonConfig[$appOld]);

    file_put_contents($cfgFile,json_encode($jsonConfig,JSON_PRETTY_PRINT));
  }
}
function deleteAppConfig($configName, $appOld) {
  $cfgFile = ROOT.CFG_FOLDER."jsonConfig/{$configName}.json";
  if(!file_exists($cfgFile)) return;

  $jsonConfig = file_get_contents($cfgFile);
  $jsonConfig = json_decode($jsonConfig,true);

  if(isset($jsonConfig[$appOld])) {
    unset($jsonConfig[$appOld]);

    file_put_contents($cfgFile,json_encode($jsonConfig,JSON_PRETTY_PRINT));
  }
}
?>