<?php
if(!defined('ROOT')) exit('No direct script access allowed');
checkServiceAccess();

loadModule("packages");

$apps=getSiteList();//_session("siteList");
//printArray($apps);exit();

define("PACKAGE_CACHE_PERIOD",86400);

$trashPath = ROOT.APPS_FOLDER.".trash/";
if(!is_dir($trashPath)) mkdir($trashPath,0777,true);

switch($_REQUEST["action"]) {
	case "listImages":
		if(function_exists("estore_list_apps")) {
			if(isset($_REQUEST['recache']) && $_REQUEST['recache']=="true") {
				$appImages=estore_list_apps(true);
			} else {
				$appImages=estore_list_apps();
			}
			
			$appListFinal=[];//["_"=>[]]
			foreach($appImages as $a=>$b) {
				//$category=current(explode("_",$b['name']));
				//if($category=="") $category="_";
				//if($b['name']=="Apps_CMS") continue;
				
				$appListFinal[]=[//[$category]
					"hashid"=>$b['id'],
					"name"=>$b['name'],
					"appimage"=>$b['appimage'],
					"package"=>$b['package'],
					"descs"=>$b['description'],
					"category"=>$b['category'],
					"keywords"=>$b['keywords'],
					"type"=>$b['type'],
// 					"refid"=>$a,
					"homepage"=>$b['homepage'],
					"logo_url"=>$b["logo_url"],
					"license"=>$b['license'],
					"pricing_type"=>$b['pricing_type'],
					"pricing_cost"=>$b['pricing_cost'],
					"core_build"=>$b["core_build"],
				// 	"download_stable"=>$b["download_stable"],
				// 	"download_nightly"=>$b["download_nightly"],
					"langs"=>$b["langs"],
					"release_status"=>$b["release_status"],
					"release_vers"=>$b["release_vers"],
					"release_updated"=>_date(current(explode(" ",$b["release_updated"]))),
					"installed"=>"NA",
// 					"noinstall"=>(in_array(strtoupper($b['name']),["APPS_CMS"])),
					//"last_update"=>date('m/d/Y', $b['updatedAt']),
          //"since"=>date('m/d/Y', $b['createdAt']),
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
	case "uploadAppImage":
	    if(isset($_FILES['attachment'])) {
	        $tempDir = _dirTemp("appManager");
	        $targetPath = $tempDir.$_FILES['attachment']["name"];
	       // move_uploaded_file($_FILES['attachment']['tmp_name'],$targetPath);
	        if(file_exists($targetPath)) {
	            $_SESSION['APPMANAGER-FILE'] = $targetPath;
	            $refid = time();
                //printServiceMsg(["msg"=>"Unziping uploaded archive","nextstep"=>"unzip","refid"=>time()]);
                echo "Uploaded successfully, Unzipping<script>parent.appInstallSteps('{$refid}','unzip');</script>";
	        }
	    } else {
	        echo "Failed to upload file<script>parent.lgksAlert('Failed to upload file');</script>";
	    }
	    break;
	case "installAppImage":
	    //download
	    //unzip
	    //coping
	    //configuring
	        //jsonConfig
	    //validating
	        //sqlFolder
            //schema
            //permissions
	    //install-addons
	        //dependencies
		if(isset($_POST['refid'])) {
		    if(isset($_POST['stepid'])) {
		        $stepid = strtolower($_POST['stepid']);
		        unset($_POST['stepid']);
		    } else {
		        $stepid = "download";
		    }
			$refid=$_POST['refid'];
			if(strlen($refid)<=0) {
			    printServiceMsg(["error"=>"Installation candidate not defined","refid"=>$refid]);
			    return;
			}
			
			$appImages=estore_list_apps();
            $appSelected = false;
            foreach($appImages as $var) {
                if(isset($var['package']) && strtolower($var['package']) == strtolower($_POST['refid'])) {
                    $appSelected = $var;
                    break;
                }
            }
            if(!$appSelected) {
                printServiceMsg(["error"=>"Installation candidate not found","refid"=>$refid]);
                return;
            }
            
            //printArray([$appSelected,$_POST['refid'],$appImages]);exit();
            
            set_time_limit(0); 
            
            $tempDir = _dirTemp("appManager");
            
            if($stepid!="download") {
                if(!isset($_SESSION['APPMANAGER-PARAMS'])) {
                    printServiceMsg(["error"=>"Installation parameters not found<br>Try installing again.","refid"=>$refid]);
                    return;
                }
            }
            
            switch($stepid) {
                case "download":
                    $_SESSION['APPMANAGER-FILE'] = "";
                    
                    $_SESSION['APPMANAGER-PARAMS'] = [
                            "appname"=>time()."_".$refid,
                            "db"=>false,
                            "cache"=>false,
                            "msg"=>false,
                        ];
                        
                    foreach(["db","cache","msg","fs"] as $a) {
                        if(isset($_POST[$a])) {
                            $_POST[$a] = json_decode($_POST[$a],true);
                            if(!is_array($_POST[$a])) unset($_POST[$a]);
                        }
                    }
                    $_SESSION['APPMANAGER-PARAMS'] = array_merge($_SESSION['APPMANAGER-PARAMS'],$_POST);
                    
                    if(!is_dir($tempDir)) mkdir($tempDir,0777,true);
                    if(!is_dir($tempDir)) {
                        printServiceMsg(["error"=>"Could not create tmp directory","refid"=>$refid]);
                        return;
                    }
                    
                    $downloadURL = $appSelected['download_stable'];
                    if(!$downloadURL) {
                        printServiceMsg(["error"=>"No downloadable candidate found","refid"=>$refid]);
                        return;
                    }
                    
                    $downloadFile = "{$tempDir}{$refid}.zip";
                    if(file_exists($downloadFile)) {
                        if((time() - filemtime($downloadFile))>=PACKAGE_CACHE_PERIOD) {
                           unlink($downloadFile); 
                        } else {
                            $_SESSION['APPMANAGER-FILE'] = $downloadFile;
                            printServiceMsg(["msg"=>"Unziping downloaded archive","nextstep"=>"unzip","refid"=>$refid]);
                            return;
                        }
                    }
                    file_put_contents($downloadFile, fopen($downloadURL, 'r'));
                    
                    if(file_exists($downloadFile)) {
                        $_SESSION['APPMANAGER-FILE'] = $downloadFile;
                        printServiceMsg(["msg"=>"Unziping downloaded archive","nextstep"=>"unzip","refid"=>$refid]);
                    } else {
                        printServiceMsg(["error"=>"Failed to download the application. <br>Check with server Admin","refid"=>$refid]);
                    }
                    break;
                case "unzip":
                    $_SESSION['APPMANAGER-CACHE'] = "";
                    if(isset($_SESSION['APPMANAGER-FILE']) && strlen($_SESSION['APPMANAGER-FILE'])>0 && file_exists($_SESSION['APPMANAGER-FILE'])) {
                        $downloadedFile = $_SESSION['APPMANAGER-FILE'];
                        
                        if(is_dir($tempDir."cache/")) {
                            deleteFolder($tempDir."cache/");
                        }
                        if(!is_dir($tempDir."cache/")) mkdir($tempDir."cache/",0777,true);
                        
                        $zip = new ZipArchive;
                        $res = $zip->open($downloadedFile);
                        if ($res === TRUE) {
                          $zip->extractTo($tempDir."cache/");
                          $zip->close();
                          
                          $fs = scandir($tempDir."cache/");
                          if(isset($fs[2])) {
                              $_SESSION['APPMANAGER-CACHE'] = $tempDir."cache/{$fs[2]}/";
                          }
                          printServiceMsg(["msg"=>"Creating app instance","nextstep"=>"coping","refid"=>$refid]);
                        } else {
                          printServiceMsg(["error"=>"Unzip Failed. Try installing again.","refid"=>$refid]);
                        }
                    } else {
                        printServiceMsg(["error"=>"Unable to find downloaded resource. Try installing again.","refid"=>$refid]);
                    }
                    break;
                case "coping":
                    $_SESSION['APPMANAGER-APPDIR'] = "";
                    if(isset($_SESSION['APPMANAGER-CACHE']) && strlen($_SESSION['APPMANAGER-CACHE'])>0 && is_dir($_SESSION['APPMANAGER-CACHE'])) {
                        $cacheDir = $_SESSION['APPMANAGER-CACHE'];
                        $targetDir = ROOT.APPS_FOLDER."{$_SESSION['APPMANAGER-PARAMS']['appname']}/";
                        if(is_dir($targetDir)) {
                            printServiceMsg(["error"=>"Target app directory for {$refid} already exists","refid"=>$refid]);
                            return;
                        }
                        // printArray([$cacheDir,$targetDir]);
                        ob_start();
                        copyFolder($cacheDir,$targetDir,false);
                        $out = ob_get_contents();
                        ob_clean();
                        if(strlen($out)>0) {
                            printServiceMsg(["error"=>strip_tags($out),"refid"=>$refid]);
                            return;
                        } else {
                            $_SESSION['APPMANAGER-APPDIR'] = $targetDir;
                            deleteFolder($cacheDir);
                            printServiceMsg(["msg"=>"Configuring the app","nextstep"=>"configuring","refid"=>$refid]);
                        }
                    } else {
                        printServiceMsg(["error"=>"Unable to find cache resource. Try installing again.","refid"=>$refid]);
                    }
                    break;
                case "configuring":
                    if(isset($_SESSION['APPMANAGER-APPDIR']) && strlen($_SESSION['APPMANAGER-APPDIR'])>0 && is_dir($_SESSION['APPMANAGER-APPDIR'])) {
                        $logiksConfig = $_SESSION['APPMANAGER-APPDIR']."logiks.json";
                        if(file_exists($logiksConfig)) {
                            $logiksConfig = json_decode(file_get_contents($logiksConfig),true);
                            if(!$logiksConfig) {
                                
                            }
                        } else {
                            $logiksConfig = [];
                        }
                        $_SESSION['APPMANAGER-PARAMS'] = array_merge($_SESSION['APPMANAGER-PARAMS'],$logiksConfig);
                        $logiksConfig = $_SESSION['APPMANAGER-PARAMS'];
                        
                        if(isset($logiksConfig['db']) && $logiksConfig['db']) {
                            insertAppConfig("db",$logiksConfig['appname'],["app"=>$logiksConfig['db']]);
                        }
                        if(isset($logiksConfig['cache']) && $logiksConfig['cache']) {
                            insertAppConfig("cache",$logiksConfig['appname'],["app"=>$logiksConfig['cache']]);
                        }
                        if(isset($logiksConfig['msg']) && $logiksConfig['msg']) {
                            insertAppConfig("message",$logiksConfig['appname'],["app"=>$logiksConfig['msg']]);
                        }
                        if(isset($logiksConfig['fs']) && $logiksConfig['fs']) {
                            insertAppConfig("fs",$logiksConfig['appname'],["app"=>$logiksConfig['fs']]);
                        }
                        if(isset($logiksConfig['services']) && $logiksConfig['services']) {
                            insertAppConfig("services",$logiksConfig['appname'],$logiksConfig['services']);
                        }
                        
                        printServiceMsg(["msg"=>"Validating the app","nextstep"=>"validating","refid"=>$refid]);
                    } else {
                        printServiceMsg(["error"=>"Unable to find created app folder. Try installing again.","refid"=>$refid]);
                    }
                    break;
                case "validating":
                    $logiksConfig = $_SESSION['APPMANAGER-PARAMS'];
                    $sqlFolder = $_SESSION['APPMANAGER-APPDIR']."sql/";
                    
                    if(is_dir($sqlFolder)) {
                        //Install SQL Tables from the folder
                    }
                    
                    if(isset($logiksConfig['schema']) && $logiksConfig['schema']) {
                        //Install addon tables from schema
                    }
                    
                    if(isset($logiksConfig['permissions']) && $logiksConfig['permissions']) {
                        //Generate Permissions for App
                    }
                    
                    // printArray([$sqlFolder,$logiksConfig]);
                    printServiceMsg(["msg"=>"Installing additional packages","nextstep"=>"install-addons","refid"=>$refid]);
                    break;
                case "install-addons":
                    $logiksConfig = $_SESSION['APPMANAGER-PARAMS'];
                    
                    if(isset($logiksConfig['dependencies']) && $logiksConfig['dependencies']) {
                        $dependencies = $logiksConfig['dependencies'];
                        if(is_string($dependencies)) {
                            $dependencies = explode(",",$dependencies);
                            array_flip($dependencies);
                        }
                        
                        //$dependencies
                    }
                    
                    printServiceMsg(["msg"=>"Finalizing the installation","nextstep"=>"completed","refid"=>$refid]);
                    break;
                case "completed":
                    printServiceMsg(["msg"=>"App Installation is complete<br>Reload the page for updating the cms dropdown","refid"=>$refid]);
                    break;
                default:
                    printServiceMsg(["error"=>"Installation Failed, Wrong StepID","refid"=>$refid]);
            }
            ////installLogiksAppImage($refid)
		} else {
			printServiceMsg(["error"=>"Sorry, could not find the refid, try again later.","refid"=>""]);
		}
		break;
}
?>