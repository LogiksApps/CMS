<?php
if(!defined('ROOT')) exit('No direct script access allowed');

if(!function_exists("getAPP_PROPS")) {
    
    function getApp_PROPS($cfgKey, $configFile = false, $defaultValue = "") {
        if(!$configFile) {
            $configFile = "apps.cfg";
        }
        if(!file_exists(CMS_APPROOT.$configFile)) return $defaultValue;
         
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
        
        if(isset($fConfig[$cfgKey])) return $fConfig[$cfgKey];
        return $defaultValue;
    }
    
    function getApp_VERSCODE() {
        return strtolower(str_replace(" ", "-",getApp_PROPS("APPS_VERS")));
    }
}
?>