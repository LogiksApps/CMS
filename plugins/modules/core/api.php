<?php
if(!defined('ROOT')) exit('No direct script access allowed');

if(!function_exists("getAPP_PROPS")) {
    
    function getAppType() {
        return getApp_PROPS("APP_TYPE", "-", "default");
    }
    
    function getApp_VERSCODE() {
        return strtolower(str_replace(" ", "-",getApp_PROPS("APPS_VERS")));
    }
    
    function getApp_PROPS($cfgKey, $configFile = false, $defaultValue = "") {
        $fConfig = getStudioAppConfig();
        
        if(isset($fConfig[$cfgKey])) return $fConfig[$cfgKey];
        return $defaultValue;
    }
    
    function getStudioAppConfig($reload = false) {
        if(!isset($_SESSION["APPCONFIG"])) $_SESSION["APPCONFIG"] = [];
        
        if(APPS_STATUS==="development") $reload = true;
        
        if($reload===true) if(isset($_SESSION["APPCONFIG"][CMS_SITENAME])) unset($_SESSION["APPCONFIG"][CMS_SITENAME]);
        
        if(isset($_SESSION["APPCONFIG"][CMS_SITENAME])) return $_SESSION["APPCONFIG"][CMS_SITENAME];
        
        $configFile = "apps.cfg";
        
        if(file_exists(CMS_APPROOT.$configFile)) {
            $fConfig = [];
            $fData = file_get_contents(CMS_APPROOT.$configFile);
            
            $fData = explode("\n", $fData);
            foreach($fData as $row) {
                $row = explode("=", $row);
                if(count($row)<=1) continue;
                $key = $row[0];
                array_shift($row);
                $fConfig[$key] = implode("=", $row);
            }
            
            $fConfig["SITE"] = CMS_SITENAME;
            $fConfig["PATH"] = CMS_APPROOT;
            if(!isset($fConfig["APP_TYPE"])) $fConfig["APP_TYPE"] = "logiks-webapp";
            
            $appConfig = CMS_APPROOT."logiks.json";
            if(file_exists($appConfig)) {
                $appConfig = json_decode(file_get_contents($appConfig));
            } else $appConfig = [];
            
            $fConfig["CONFIG"] = $appConfig; 
            
            $_SESSION["APPCONFIG"][CMS_SITENAME] = $fConfig;
        } else {
            $appProps = [
                    "SITE"=> CMS_SITENAME,
                    "PATH"=> CMS_APPROOT,
                    "APP_TYPE"=> (file_exists(CMS_APPROOT."package.json")?"logiks-nodejs":"unknown"),
                ];
                
            $appConfig = CMS_APPROOT."logiks.json";
            if(file_exists($appConfig)) {
                $appConfig = json_decode(file_get_contents($appConfig));
            } else $appConfig = [];
            
            $appConfig["CONFIG"] = $appConfig;
                
            $_SESSION["APPCONFIG"][CMS_SITENAME] = $fConfig;
        }
        
        return $_SESSION["APPCONFIG"][CMS_SITENAME];
    }
    
    function getSiteList() {
    	$arr=scandir(ROOT.APPS_FOLDER);
    	unset($arr[0]);unset($arr[1]);
    	$out=array();
    	foreach($arr as $a=>$b) {
    	    if($b=="cms") continue;
    		if(is_file(ROOT.APPS_FOLDER.$b)) {
    			unset($arr[$a]);
    		} elseif(is_dir(ROOT.APPS_FOLDER.$b) && !file_exists(ROOT.APPS_FOLDER.$b."/apps.cfg")) {
    			unset($arr[$a]);
    		} else {
    			array_push($out,$b);
    		}
    	}
    	
    	$final = [];
    	foreach($out as $a) {
    	    $final[$a] = [
    	            "title"=>toTitle($a),
    	            "url"=>_link("", "", $a)
    	        ];
    	}
    	return $final;
    }
    
    function checkRootAccess() {
        if($_SESSION['SESS_PRIVILEGE_ID']<=2) {
            return true;
        } else {
            echo "<h3 align=center>Sorry, you need ROOT permissions to access this module</h3>";
            return false;
        }
    }
}
?>