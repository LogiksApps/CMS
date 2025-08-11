<?php
if(!defined('ROOT')) exit('No direct script access allowed');
checkServiceAccess();

handleActionMethodCalls();

function _service_runcode() {
    if(!isset($_POST['code'])) {
        echo "Error finding code";
        exit();
    }
    if(!isset($_POST['lang'])) {
        echo "Error finding language";
        exit();
    }
    try {
        $_POST['lang'] = _slugify($_POST['lang']);
        switch($_POST['lang']) {
            case "php":
                $code = "chdir(CMS_APPROOT);\n".urldecode($_POST['code']);
                eval($code);
                break;
            default:
                echo "Requested Language '{$_POST['lang']}' is not supported yet.";
        }
        
    } catch(Exception $e) {
        var_dump($e);
    }
}
?>