<?php
if(!defined('ROOT')) exit('No direct script access allowed');

if(!function_exists("perspectives_list")) {
    
    if(isset($_SESSION['SESSION_PERSPECTIVES'])) unset($_SESSION['SESSION_PERSPECTIVES']);
    
    function perspectives_active() {
        $perspectiveList = perspectives_list();
        if(!$perspectiveList) return "studio";
        
        $currentPerspective = $_SESSION['SESSION_PERSPECTIVE_DEFAULT'];
        
        if(isset($_GET["perspective"])) {
            $currentPerspective = $_GET["perspective"];
        } elseif(isset($_COOKIE["LOGIKCMS-PERSPECTIVE-ACTIVE"])) {
            $currentPerspective = $_COOKIE["LOGIKCMS-PERSPECTIVE-ACTIVE"];
        }
        
        if(!isset($perspectiveList[$currentPerspective])) {
            $currentPerspective = array_keys($perspectiveList)[0];
        }
        
        return $currentPerspective;//$_SESSION['SESSION_PERSPECTIVE_DEFAULT'];
    }
    
    function perspectives_list() {
        if(isset($_SESSION['SESSION_PERSPECTIVES'])) return $_SESSION['SESSION_PERSPECTIVES'];
        
        $configJSON = [];
        $configFile = APPROOT."config/perspectives.json";
        if(file_exists($configFile)) {
            $configJSON = json_decode(file_get_contents($configFile), true);
            if(!$configJSON) $configJSON = ["LIST"=>[], "DEFAULT"=>"studio"];
            
            foreach($configJSON['LIST']  as $key=>$config) {
                $configJSON['LIST'][$key] = $config = array_merge([
                        "title"=> "--",
    			        "icon"=> "fa fa-cube fa-fw",
    			        "src"=> "sidebarMenu",
    			        "disabled"=> false,
    			        "privilege"=>"*",
    			        "dashboard"=>"dashboard",
    			        "required_module"=>""
                    ], $config);
                    
                if($config['disabled']) unset($configJSON['LIST'][$key]);
                
                if($config['privilege']!="*") {
                    $config['privilege'] = explode(",", $config['privilege']);
                    if(!in_array($_SESSION['SESS_PRIVILEGE_NAME'], $config['privilege'])) {
                        unset($configJSON['LIST'][$key]);
                    }
                }
                
                if(strlen($config['required_module'])>0) {
                    if(!checkModule($config['required_module'])) {
                        unset($configJSON['LIST'][$key]);
                    }
                }
            }
            
            if(!$configJSON['LIST'] || count($configJSON['LIST'])<=0) return [];
            
            $_SESSION['SESSION_PERSPECTIVES'] = $configJSON['LIST'];
            
            if(!isset($configJSON['LIST'][$configJSON['DEFAULT']])) {
                $configJSON['DEFAULT'] = array_keys($configJSON['LIST'])[0];
            }
            
            $_SESSION['SESSION_PERSPECTIVE_DEFAULT'] = $configJSON['DEFAULT'];
        } else {
            $_SESSION['SESSION_PERSPECTIVE_DEFAULT'] = "studio";
        }
        
        return $configJSON['LIST'];
    }
    
    function perspectives_headers() {
        $currentPerspective = perspectives_active();
        $html = "";
        
        return $html;
    }
    
    function perspectives_sidebar() {
        $currentPerspective = perspectives_active();
        $perspectiveList = perspectives_list();
        
        if(!isset($perspectiveList[$currentPerspective]) || 
            !isset($perspectiveList[$currentPerspective]['sidebar']) || 
            count($perspectiveList[$currentPerspective]['sidebar'])<=0) {
            return false;
        }
        
        return $perspectiveList[$currentPerspective]['sidebar'];
    }
    
    function perspectives_dashboard() {
        $currentPerspective = perspectives_active();
        $perspectiveList = perspectives_list();
        
        if(!isset($perspectiveList[$currentPerspective]) || 
            !isset($perspectiveList[$currentPerspective]['dashboard']) || 
            count($perspectiveList[$currentPerspective]['dashboard'])<=0) {
            return "dashboard";
        }
        
        return $perspectiveList[$currentPerspective]['dashboard'];
    }
}
?>