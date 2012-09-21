<?php
if (!defined('ROOT')) exit('No direct script access allowed');

/*
 * This module helps converts the loaded Database Connection Into Targeted Site's Database Connection.
 * Loading this module, will enable programmer to use the same Old dbCon as targeted site's Database Connection.
 */

if(!function_exists("loadSiteDB")) {
	function getDBControls($site=null,$isolated=false) {
		return loadSiteDB($site,$isolated);
	}
	function loadSiteDB($site=null,$isolated=false) {
		if($site==null) $site=$_REQUEST["forsite"];
		$dbFile=ROOT.APPS_FOLDER.$site."/config/db.cfg";
		if(file_exists($dbFile)) {
			$con=null;
			if($isolated) {
				$config=parseConfigFile($dbFile);
				$con=new Database($config["DB_DRIVER"]['value']);
				$con->connect($config["DB_USER"]['value'],$config["DB_PASSWORD"]['value'],$config["DB_HOST"]['value'],$config["DB_DATABASE"]['value']);
			} else {
				LoadConfigFile($dbFile);
				$con=new Database($GLOBALS['DBCONFIG']["DB_DRIVER"]);
				$con->connect($GLOBALS['DBCONFIG']["DB_USER"],$GLOBALS['DBCONFIG']["DB_PASSWORD"],$GLOBALS['DBCONFIG']["DB_HOST"],$GLOBALS['DBCONFIG']["DB_DATABASE"]);
			}
			return $con;
		} else {
			return false;
		}
	}
	function loadFolderConfig($site=null) {
		if($site==null) $site=$_REQUEST["forsite"];
		$cfgFile=ROOT.APPS_FOLDER.$_REQUEST["forsite"]."/config/folders.cfg";
		if(file_exists($cfgFile)) {
			$data=file_get_contents($cfgFile);
			$data=explode("\n",$data);
			$fldrs=array();
			$fldrs["APPROOT"]=ROOT.APPS_FOLDER.$_REQUEST["forsite"]."/";
			foreach($data as $s) {
				if(substr($s,0,2)=="//") continue;
				if(substr($s,0,1)=="#") continue;
				$s=substr($s,0,strlen($s));
				if(strlen($s)>0 && strpos($s,"=")>0) {
					$n1=strpos($s, "=");
					$name=substr($s,0,$n1);
					$value=substr($s,$n1+1);
					$fldrs[$name]=$value;
				}
			}
			$_SESSION["APP_FOLDER"]=$fldrs;
			return $fldrs;
		} else {
			return false;
		}
	}
	function getSiteConfigFor($cfg,$site=null) {
		if($site==null) $site=$_REQUEST["forsite"];
		$cfgFile=ROOT.APPS_FOLDER.$_REQUEST["forsite"]."/config/$cfg.cfg";
		if(file_exists($cfgFile)) {
			$data=file_get_contents($cfgFile);
			$data=explode("\n",$data);
			$fldrs=array();
			foreach($data as $s) {
				if(substr($s,0,2)=="//") continue;
				if(substr($s,0,1)=="#") continue;
				$s=substr($s,0,strlen($s));
				if(strlen($s)>0 && strpos($s,"=")>0) {
					$n1=strpos($s, "=");
					$name=substr($s,0,$n1);
					$value=substr($s,$n1+1);
					$fldrs[$name]=$value;
				}
			}
			return $fldrs;
		} else {
			return false;
		}
	}
	function flushSiteCache($folder) {
		$folders=loadFolderConfig();
		$cacheFolder=ROOT.CACHE_FOLDER;
		$cache=CacheManager::singleton();
		
		if($folders["APPS_CACHE_FOLDER"]) {
			$cacheFolder=$folders["APPROOT"].$folders["APPS_CACHE_FOLDER"];
		}
		$f=$cacheFolder.$folder."/";
		$f=str_replace("//","/",$f);
		$fs=scandir($f);
		unset($fs[0]);unset($fs[1]);
		foreach($fs as $ff) {
			$ff=$f.$ff;
			unlink($ff);
		}
	}
	function flushSiteCacheFile($cacheName) {
		$folders=loadFolderConfig();
		$cacheFolder=ROOT.CACHE_FOLDER;
		$cache=CacheManager::singleton();
		
		$cacheID=$cache->getCacheID($cacheName);
		
		if($folders["APPS_CACHE_FOLDER"]) {
			$cacheFolder=$folders["APPROOT"].$folders["APPS_CACHE_FOLDER"]."var/";
			if(!file_exists($cacheFolder) && mkdir($cacheFolder, 0777, true)) {
				chmod($cacheFolder, 0777);
			}
		}
		
		$f=$cacheFolder.$cacheID;
		if(file_exists($f)) {
			unlink($f);
		}
	}
}
?>
