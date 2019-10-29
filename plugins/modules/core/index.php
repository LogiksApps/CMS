<?php
if(!defined('ROOT')) exit('No direct script access allowed');

if(!function_exists("setupCMSEnviroment")) {
	function setupCMSEnviroment() {
		if(!isset($_SESSION['PLUGINCHECK'])) {
			$checkPlugins=["navigator","pages","forms","reports","datagrid",];
			$checkPluginPass=true;
			foreach($checkPlugins as $plugin) {
				if(!checkModule($plugin)) {
					$checkPluginPass=false;
					println("Plugin Missing :: {$plugin}");
				}
			}
			if(!$checkPluginPass) {
				exit("<hr>Please install the above plugins to start using CMS. More information can be found at <a href='http://openlogiks.org'>openlogiks.org</a>");
			} else {
				$_SESSION['PLUGINCHECK']=true;
			}
		}
		
		if(defined("PAGE") && (PAGE=="login" || PAGE=="register" || PAGE=="forgotpwd")) {
			return true;
		}
		
		if(!isset($_SESSION['SESS_PRIVILEGE_NAME'])) $_SESSION['SESS_PRIVILEGE_NAME']="";
		if(!isset($_SESSION['SESS_ACCESS_SITES'])) $_SESSION['SESS_ACCESS_SITES']=[];
		if(!isset($_SESSION['SESS_USER_NAME'])) $_SESSION['SESS_USER_NAME']="";
		//printArray($_SESSION);exit();
		//echo CMSSITE;
		
		if(isset($_REQUEST["forsite"])) {
		    $forSite=$_REQUEST["forsite"];

// 		    if($_REQUEST["forsite"]==SITENAME) {
// 		    	$lx=_link("","&site=".SITENAME."&forsite=".DEFAULT_SITE);
// 		    	header("Location:{$lx}");
// 		    }
		} elseif(defined("SERVICE_ROOT")) {
				$_REQUEST["forsite"]=SITENAME;
				$forSite=SITENAME;
		} else {
		    $forSite=DEFAULT_SITE;
		    $_REQUEST["forsite"]=DEFAULT_SITE;
		}
		$_REQUEST['forSite']=$forSite;
		$_GET['forSite']=$forSite;
		
		if(defined("SERVICE_ROOT")) {
			if(isset($_REQUEST['scmd']) && $_REQUEST['scmd']=="auth") {
				return true;
			}
		}
		
		$arr=[];
		$siteList=[];
		if($_SESSION['SESS_PRIVILEGE_NAME']=="root") {
				$arr=scandir(ROOT.APPS_FOLDER);
		} else {
				$arr=$_SESSION['SESS_ACCESS_SITES'];
		}
		foreach($arr as $b) {
				if(file_exists(ROOT.APPS_FOLDER.$b."/apps.cfg")) {
						$t=ucwords($b);
						$lnk=SiteLocation."?site=cms&forsite=$b";
						$siteList[$b]=['title'=>$t,'url'=>$lnk];
				}
		}

		if(count($siteList)>0 && !array_key_exists($forSite, $siteList)) {
			echo "<h5 class='errormsg'>Site <b>'".$forSite."'</b> Does Not Have Access rights for you.<br>Redirecting ...</h5>";
			if(count($siteList)>0) {
				header("Location:".$siteList[array_keys($siteList)[0]]['url']);
			} else {
				header("Location:".SiteLocation);
			}
			//trigger_logikserror("Site <b>'".$forSite."'</b> Does Not Have Access rights for you.<a href='"._link("")."'>Go Back</a>",E_ERROR);
			exit();
		}
		$_SESSION['siteList']=$siteList;

		$f=ROOT.CFG_FOLDER."/jsonConfig/db.json";
		if(file_exists($f)) {
			$jsonDB=json_decode(file_get_contents($f),true);

			if(isset($jsonDB[$_REQUEST['forsite']])) {
				foreach ($jsonDB[$_REQUEST['forsite']] as $dbKey => $dbParams) {
					Database::connect($dbKey,$dbParams);
				}
			}
		}
        
        define("CMS_APPROOT",ROOT.APPS_FOLDER.$forSite."/");
		define("CMS_SITENAME",$_REQUEST['forsite']);
		
		if($_SESSION['SESS_PRIVILEGE_NAME']!="root") {
			unset($siteList["cms"]);
		}

        _session("siteList",$siteList);
        
        $cfgData = ConfigFileReader::LoadFile(CMS_APPROOT."apps.cfg");
        if(isset($cfgData['DEFINE'])) {
            $_SESSION["SITEPARAMS"] = $cfgData['DEFINE'];
        }
		
		$moduleDir=getLoaderFolders('pluginPaths',"modules");
		$moduleDir[]="apps/{$forSite}/plugins/modules/";
		
		$_ENV['MODULE_DIRS']=$moduleDir;
		$_ENV['LOADERS_PLUGINPATHS']['modules']=$moduleDir;
	}
	function checkServiceAccess() {
		//Check Session and Role Controls
		checkServiceSession();
		
		if(!user_admin_check()) {
			printServiceErrorMsg(403, "You are not an Admin.");
			exit();
		}
		
		$acp=$_SESSION['SESS_ACCESS_SITES'];
		if(!in_array($_REQUEST['forsite'],$acp)) {
			printServiceErrorMsg(403, "{$_REQUEST['forsite']} is not available for you to adminster");
			exit();
		}
	}
	function getAppFile($file) {
		if(substr($file, 0, 1)!="/") $file="/{$file}";
		return ROOT.APPS_FOLDER.$_REQUEST['forsite'].$file;
	}
	function saveAppFile($src,$content) {
		if(!is_dir(dirname($src))) {
			mkdir(dirname($src),0777,true);
		}
		if(!file_exists($src)) {
			file_put_contents($src,"");
		}
		if(!is_writable($src)) {
			return false;
		}
		$a=file_put_contents($src, $content);
		
		//Save history of file
		_db(true)->_insertQ1(_dbTable("cache_editor",true),[
				"guid"=>$_SESSION['SESS_GUID'],
				"site"=>CMS_SITENAME,
				"client_ip"=>$_SERVER['REMOTE_ADDR'],
				"filepath"=>$src,
				"content"=>$content,
				"src_hash"=>md5($src),
				"content_hash"=>md5($content),
				"disksize"=>$a,
				"created_by"=>$_SESSION['SESS_USER_ID'],
				"created_on"=>date("Y-m-d H:i:s"),
				"edited_by"=>$_SESSION['SESS_USER_ID'],
				"edited_on"=>date("Y-m-d H:i:s"),
			])->_RUN();
		
		//delete old versions
		$maxHist=getConfig("MAX_EDITOR_HISTORY_PER_FILE");
		if($maxHist==null || $maxHist<=0) $maxHist=100;
		
		$tbl=_dbTable("cache_editor",true);
		$sql="SELECT id FROM {$tbl} WHERE filepath='{$src}' ORDER BY id DESC LIMIT 1000 OFFSET 50";
		$sql="DELETE FROM $tbl WHERE id IN ($sql)";
		_dbQuery($sql,true);
		
		if($a===false) return false;
		return true;
	}

	setupCMSEnviroment();
}
?>
