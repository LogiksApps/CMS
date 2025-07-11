<?php
if(!defined('ROOT')) exit('No direct script access allowed');

loadModuleLib("controlCenter", "api");

handleActionMethodCalls();

function _service_list() {
    $toolList = [
            "clearcache1"=> [
                "label"=>"Clear All Cache",
                "type"=> "method",
                "src"=> "toolsClearCache"
            ],
            "x1"=> [
                "type"=> "divider",
            ],
        ];
        
    $packageFile = CMS_APPROOT."package.json";
    
    if(file_exists($packageFile)) {
        try {
            $packageJSON = json_decode(file_get_contents($packageFile), true);
            if(!$packageJSON) $packageJSON = [];
            
            _cache('PACKAGE_'+CMS_SITENAME, $packageJSON);
            
            if(isset($packageJSON['scripts'])) {
                foreach($packageJSON['scripts'] as $k=>$v) {
                    $toolList["package_$k"] = [
                            "label"=> toTitle($k),
                            "type"=> "package",
                            "src"=> $k
                        ];
                }
            }
        } catch(Exception $e) {
        }
    }
    
    return $toolList;
}

function _service_run_package() {
    $packageJSON = _cache('PACKAGE_'+CMS_SITENAME);
    if(!$packageJSON || !isset($packageJSON['scripts'])) return "Script Not Defined";
    
    if(!isset($packageJSON['scripts'][$_POST['cmd']])) return "Script Not Found";
    
    $script = $packageJSON['scripts'][$_POST['cmd']];
    $results = runNodeCMD($script, [], CMS_APPROOT);
    
    return $results;
}

function _service_run_ctrl() {
    $results = runNodeScript($_POST['cmd'], [], CMS_APPROOT);
    
    return $results;
}

?>