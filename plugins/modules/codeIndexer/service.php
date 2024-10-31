<?php
if(!defined('ROOT')) exit('No direct script access allowed');

include_once __DIR__."/api.php";

handleActionMethodCalls();

function _service_check() {
    if(!checkCodeIndex("core")) startCodeIndex("core");
}

function _service_index() {
    if(!isset($_REQUEST['src'])) $_REQUEST['src'] = "core";
    
    $indexSrc = "core";
    $dir = ROOT."api";
    $includeOnly = false;
    
    switch($_REQUEST['src']) {
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
    
    $a = generateIndex($srcFiles, $indexSrc);
    
    if($a) return "Source Index Created Successfully";
    else return "Error creating source index";
}
?>