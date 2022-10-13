<?php
if(!defined('ROOT')) exit('No direct script access allowed');

if(!function_exists("fetchRoleList")) {
    
    function generateRoleModel() {
        $pluginDirs = [
                CMS_APPROOT."plugins/modules/",
                CMS_APPROOT."pluginsDev/modules/",
            ];
        
        $logiksFileList = [];
        
        if(file_exists(CMS_APPROOT."config/roles.json")) {
            $logiksFileList[CMS_APPROOT."config/roles.json"] = "app";
        }
        
        foreach($pluginDirs as $dir) {
            $fs = scandir($dir);
            array_shift($fs);array_shift($fs);
            
            foreach($fs as $f) {
                $f1 = $dir.$f."/logiks.json";
                $f2 = $dir.$f."/package.json";
                
                if(file_exists($f1)) {
                    $logiksFileList[$f1] = $f;
                } elseif(file_exists($f2)) {
                    $logiksFileList[$f2] = $f;
                }
            }
        }
        
        $finalPolicyList = [];
        foreach($logiksFileList as $f=>$mod) {
            try {
                $jsonRoles = json_decode(file_get_contents($f), true);
                
                if(isset($jsonRoles['policies'])) {
                    foreach($jsonRoles['policies'] as $a=>$b) {
                        $finalPolicyList[$a] = $b;
                    }
                }
            } catch(Exception $e) {
            }
        }
        
        foreach($finalPolicyList as $policyStr=>$defaultValue) {
            RoleModel::getInstance()->registerPolicy($policyStr, CMS_SITENAME, $_SESSION['SESS_USER_ID'], $_SESSION['SESS_GUID'], $defaultValue);
        }
        
        return true;
    }
    
    function fetchRoleList() {
        
    }
    
    function fetchModuleList() {
        
    }
    
    function fetchRoleRules() {
        
    }
}
?>