<?php
if(!defined('ROOT')) exit('No direct script access allowed');

if(!function_exists("getCodeIndex")) {
  
    function searchCodeIndex($prefix, $src="core") {
        $codeIndex = getCodeIndex($src);
        
        return preg_array_key_exists("/^{$prefix}/",$codeIndex);
    }
  
    function getCodeIndex($src=SITENAME) {
        if(isset($_SESSION["CMS_INDEX_{$src}"])) {
            return $_SESSION["CMS_INDEX_{$src}"];
        }
        
        $indexFile = getCodeIndexFile($src);
        if(!file_exists($indexFile)) return [];
        
        $methodList = json_decode(file_get_contents($indexFile), true);
        $_SESSION["CMS_INDEX_{$src}"] = $methodList;
        return $_SESSION["CMS_INDEX_{$src}"];
    }
    
    function getCodeIndexFile($src=SITENAME) {
        if(!is_dir(ROOT.TMP_FOLDER."indexes/")) mkdir(ROOT.TMP_FOLDER."indexes/",0777,true);
      
        switch($src) {
            case "core":
                $indexFile = ROOT.TMP_FOLDER."indexes/core.json";
                return $indexFile;
                break;
            default:
                $indexFile = ROOT.TMP_FOLDER."indexes/{$src}.json";
                return $indexFile;
        }
    }
    
    function checkCodeIndex($indexSrc = "core") {
        $indexFile = getCodeIndexFile($src);
        if(!file_exists($indexFile)) return false;
        else return true;
    }
    
    function startCodeIndex($indexSrc = "core") {
        $dir = ROOT."api";
        $includeOnly = false;
        
        switch($indexSrc) {
            case "core":
                $indexSrc = "core";
                $dir = [ROOT."api"];
                break;
            default:
                $indexSrc = CMS_SITENAME;
                $dir = [CMS_APPROOT."plugins", CMS_APPROOT."plugins"];
                $includeOnly = ["api.php"];
        }
        
        $srcFiles = getCodeFiles($dir, $includeOnly);
        
        return generateIndex($srcFiles, $indexSrc);
    }
    
    function generateIndex($srcFiles, $indexSrc) {
        $methodList = [];
        foreach($srcFiles as $f) {
            if(substr($f, strlen($f)-3)=="php") {
                $fContent = file_get_contents($f);
                
                $tokenArr = [];
                
                if(strpos($fContent, "class ")!==false) {
                    preg_match_all('/class (\w+)/', $fContent, $classArr);
                    preg_match_all('/public static function (\w+)/', $fContent, $tokenArr);
                    
                    if(!isset($classArr[1][1])) continue;
                    
                    $className = $classArr[1][1];
                    
                    $finalFuncs = [];
                    foreach($tokenArr[1] as $func) {
                        $finalFuncs["{$className}::$func"] = [$func, $f];
                    }
                    // printArray([$className, $finalFuncs]);
                    
                    if(isset($tokenArr[1])) $methodList = array_merge($methodList, $finalFuncs);
                } else {
                    preg_match_all('/function (\w+)/', $fContent, $tokenArr);
                
                    $finalFuncs = [];
                    foreach($tokenArr[1] as $func) {
                        $finalFuncs[$func] = [$func, $f];
                    }
                    
                    if(isset($tokenArr[1])) $methodList = array_merge($methodList, $finalFuncs);
                }
            } elseif(substr($f, strlen($f)-3)=="inc") {
                preg_match_all('/class (\w+)/', $fContent, $classArr);
                preg_match_all('/public static function (\w+)/', $fContent, $tokenArr);
                
                if(!isset($classArr[1][1])) continue;
                
                $className = $classArr[1][1];
                
                $finalFuncs = [];
                foreach($tokenArr[1] as $func) {
                    $finalFuncs["{$className}::$func"] = [$func, $f];
                }
                // printArray([$className, $finalFuncs]);
                
                if(isset($tokenArr[1])) $methodList = array_merge($methodList, $finalFuncs);
            }
        }
        
        ksort($methodList);
        
        $indexFile = getCodeIndexFile($indexSrc);
        $a = file_put_contents($indexFile, json_encode($methodList));
    
        if($a>0) return true;
        else return false;
    }
  
    function getCodeFiles($dir, $includeOnly=false){
        $fs = [];
        foreach($dir as $f) {
            if(is_dir($f)) {
                $temp = listAllCodeFiles($f, $includeOnly);
                $fs = array_merge($fs, $temp);
            }
        }
        return $fs;
    }
  
    function listAllCodeFiles($dir, $includeOnly=false){
        $results = array();
        $files = scandir($dir);
        $files = array_splice($files, 2);
        
        if($includeOnly===false) {
            foreach($files as $key => $value){
                if(in_array($value, ["vendors", "loaders"])) continue;
                if(!is_dir($dir. DIRECTORY_SEPARATOR.$value)){
                    $results[] = $dir.DIRECTORY_SEPARATOR.$value;
                } elseif(is_dir($dir.DIRECTORY_SEPARATOR.$value)) {
                    $temp = listAllCodeFiles($dir.DIRECTORY_SEPARATOR.$value);
                    $results = array_merge($results, $temp);
                }
            }
        } else {
            foreach($files as $key => $value){
                if(!is_dir($dir. DIRECTORY_SEPARATOR.$value)){
                    if(!in_array($value, $includeOnly)) continue;
                    
                    $results[] = $dir.DIRECTORY_SEPARATOR.$value;
                } elseif(is_dir($dir.DIRECTORY_SEPARATOR.$value)) {
                    $temp = listAllCodeFiles($dir.DIRECTORY_SEPARATOR.$value, $includeOnly);
                    $results = array_merge($results, $temp);
                }
            }
        }
        
        return $results;
    }
    
    function preg_array_key_exists($pattern, $array) {
        $keys = array_keys($array);
        $results = [];
        foreach ($keys as $key) {
            if (preg_match($pattern, $key) == 1) {
                $results[$key] = $array[$key];
            }
        }
            
        return $results;
    }
}
?>