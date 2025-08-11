<?php
if(!defined('ROOT')) exit('No direct script access allowed');

include_once __DIR__."/api.php";

handleActionMethodCalls();

function _service_list_scripts() {
    $finalList = [];
    $tempList = getScriptList(true);
    
    foreach($tempList as $k=>$v) {
        $finalList[$k] = [
                "group"=>$v['group'],
                "editable"=>$v['editable']
            ];
    }
    return $finalList;
}

function _service_test() {
    $nodeData = testNodeServer();
    
    if(!$nodeData) return false;
    
    return true;
}

function _service_stats() {
    return fetchNodeStats();
}
function _service_restart() {
    return restartNodeServer();
}
function _service_form_script() {
    if(!isset($_POST['src']) && strlen($_POST['src'])>0) {
        echo "Script Source Not Defined";
        exit();
    }
    if(!in_array($_POST['src'], array_keys(getScriptList()))) {
        echo "Script Source Not Found";
        exit();
    }
    
    $form = getScriptForm($_POST['src']);
    if($form) return file_get_contents($form);
    else return false;
}
function _service_run_script() {
    if(!isset($_POST['src']) && strlen($_POST['src'])>0) {
        echo "Script Source Not Defined";
        exit();
    }
    if(!in_array($_POST['src'], array_keys(getScriptList()))) {
        echo "Script Source Not Found";
        exit();
    }
    
    $result = runNodeScript($_POST['src'], $_POST, CMS_APPROOT);
    
    echo $result;
}
function _service_view_script() {
    if(!isset($_REQUEST['src']) && strlen($_REQUEST['src'])>0) {
        echo "Script Source Not Defined";
        exit();
    }
    $scriptList = getScriptList();
    if(!in_array($_REQUEST['src'], array_keys($scriptList))) {
        echo "Script Source Not Found";
        exit();
    }
    
    $script = $_REQUEST['src'];
    
    if($scriptList[$script]['group']=="root") {
        if($_SESSION['SESS_PRIVILEGE_NAME']=="root") {
            echo "<pre>";
            readfile($scriptList[$script]['file']);
            echo "</pre>";
        } else {
            echo "Root Scripts can not be viewed here";
        }
    } elseif(file_exists($scriptList[$script]['file'])) {
        echo "<pre>";
        readfile($scriptList[$script]['file']);
        echo "</pre>";
    } else {
        echo "Script Not Found";
    }
}

function _service_edit_script() {
    if(!isset($_REQUEST['src']) && strlen($_REQUEST['src'])>0) {
        echo "Script Source Not Defined";
        exit();
    }
    $scriptList = getScriptList();
    if(!in_array($_REQUEST['src'], array_keys($scriptList))) {
        echo "Script Source Not Found";
        exit();
    }
    
    $script = $_REQUEST['src'];
    
    if($scriptList[$script]['group']=="root") {
        echo "Not allowed to save script for root group";
    } else {
        $file = str_replace(CMS_APPROOT, "", $scriptList[$script]['file']);
        $lx = _link("modules/cmsEditor")."&type=edit&src={$file}";
        header("Location:$lx");
    }
}
?>