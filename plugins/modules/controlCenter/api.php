<?php
if(!defined('ROOT')) exit('No direct script access allowed');

if(!function_exists("loadNodeEnvironment")) {
    
    function loadNodeEnvironment($reload = true) {
        if($reload && isset($_SESSION['NODE_PORT'])) unset($_SESSION['NODE_PORT']);
        
        if(!isset($_SESSION['NODE_PORT'])) {
            $f = ROOT."config/jsonConfig/nodejs.json";
            if(!file_exists($f)) {
                return false;
            }
            
            $fileData = json_decode(file_get_contents($f), true);
            $_SESSION['NODE_ENV'] = $fileData;
            $_SESSION['NODE_PORT'] = $fileData['LOCAL_PORT'];
            return true;
        }
        return true;
    }
    
    function getScriptList($reload = false) {
        if($reload && isset($_SESSION['NODE_SCRIPT_LIST'])) unset($_SESSION['NODE_SCRIPT_LIST']);
        
        if(isset($_SESSION['NODE_SCRIPT_LIST'])) return $_SESSION['NODE_SCRIPT_LIST'];
        
        
        loadNodeEnvironment();
        
        $scriptDirs = [
                "root"=>ROOT.$_SESSION['NODE_ENV']['SCRIPT_PATH'],
                "approot"=>CMS_APPROOT.$_SESSION['NODE_ENV']['SCRIPT_PATH'],
            ];
        
        $finalsList = [];
        foreach($scriptDirs as $grp=>$scriptDir) {
            if(file_exists($scriptDir) && is_dir($scriptDir)) {
                $fs = scandir($scriptDir);
                array_shift($fs);array_shift($fs);
                
                foreach($fs as $f) {
                    $fName = explode("/", $f);
                    $fName = end($fName);
                    
                    $finalsList[$fName] = [
                            "file"=>$scriptDir.$f,
                            "group"=>$grp,
                            "editable"=>($grp!="root")
                        ];
                }
            }
        }
        
        $_SESSION['NODE_SCRIPT_LIST'] = $finalsList;
        
        return $_SESSION['NODE_SCRIPT_LIST'];
    }
    
    function getScriptForm($src) {
        $src = explode(".", $src);
        unset($src[count($src)-1]);
        $src = implode(".", $src);
        
        $scriptDir = ROOT.$_SESSION['NODE_ENV']['SCRIPT_PATH'];
        $scriptForm = $scriptDir.$src.".json";
        if(file_exists($scriptForm)) return $scriptForm;
        else return false;
    }
    
    function testNodeServer() {
        $url = "http://localhost:{$_SESSION['NODE_PORT']}/";
        
        $ch = curl_init($url);
        
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type:application/json',
        ));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $resultStr = curl_exec($ch);
        
        return json_decode($resultStr, true);
    }
    
    function fetchNodeStats() {
        $url = "http://localhost:{$_SESSION['NODE_PORT']}/stats";
        
        $ch = curl_init($url);
        
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type:application/json',
        ));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $resultStr = curl_exec($ch);
        
        return json_decode($resultStr, true);
    }
    
    function restartNodeServer() {
        $url = "http://localhost:{$_SESSION['NODE_PORT']}/restart";
        
        $ch = curl_init($url);
        
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type:application/json',
        ));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $resultStr = curl_exec($ch);
        
        return $resultStr;
    }
    
    function runNodeScript($script, $data = [], $path = false) {
        if(!$path) $path = ROOT;
        
        loadNodeEnvironment();
        
        $scriptList = getScriptList();
        
        if(!isset($scriptList[$script])) {
            return "Script Not Found";
        }
        
        $data['path'] = $path;
        $data['src'] = str_replace(ROOT, "", $scriptList[$script]['file']);
        //printArray($scriptList);printArray($data);exit();
        
        $url = "http://localhost:{$_SESSION['NODE_PORT']}/run";
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type:application/json',
        ));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $resultStr = curl_exec($ch);
        
        return trim($resultStr);
    }
    
    function runNodeCMD($script, $data = [], $path = false) {
        if(!$path) $path = ROOT;
        
        loadNodeEnvironment();
        
        $data['path'] = $path;
        $data['src'] = $script;
        //printArray($scriptList);printArray($data);exit();
        
        $url = "http://localhost:{$_SESSION['NODE_PORT']}/run";
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type:application/json',
        ));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $resultStr = curl_exec($ch);
        
        return trim($resultStr);
    }
}

?>