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
        $_POST['code'] = urldecode($_POST['code']);
        switch($_POST['lang']) {
            case "php":
                $code = "chdir(CMS_APPROOT);\n{$_POST['code']}";
                eval($code);
                break;
            case "js":
                $_POST['code'] = str_replace("'", '\"', $_POST['code']);
                $codeToRun = "cd ".CMS_APPROOT.";\nnode --no-deprecation --max-old-space-size=50 -e '{$_POST['code']}'";
                $output = shell_exec($codeToRun);
                echo $output;
                break;
            case "py":
                $_POST['code'] = str_replace("'", '\"', $_POST['code']);
                $codeToRun = "cd ".CMS_APPROOT.";\npython -Sc '{$_POST['code']}'";
                $output = shell_exec($codeToRun);
                echo $output;
                break;
            case "py3":
                $_POST['code'] = str_replace("'", '\"', $_POST['code']);
                $codeToRun = "cd ".CMS_APPROOT.";\python3 -Sc '{$_POST['code']}'";
                $output = shell_exec($codeToRun);
                echo $output;
                break;
            case "bash":
                $codeToRun = "cd ".CMS_APPROOT.";\n{$_POST['code']}";
                $output = shell_exec($codeToRun);
                echo $output;
                break;
            default:
                echo "Requested Language '{$_POST['lang']}' is not supported yet.";
        }
        
    } catch(Exception $e) {
        var_dump($e);
    }
}
?>