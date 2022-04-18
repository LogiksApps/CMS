<?php
if(!defined('ROOT')) exit('No direct script access allowed');
checkServiceAccess();

loadModuleLib("logiksIDE", "api");

handleActionMethodCalls([]);

function _service_listItems() {
    if(!isset($_REQUEST["typekey"])) $_REQUEST["typekey"] = "na";
    
    $arrCompsList = getCompsList();
    
    if(!isset($arrCompsList[$_REQUEST["typekey"]])) {
        return [
            "items"=> [],
            "datakey"=>$_REQUEST["typekey"],
            "groups"=>false,
            "site"=>CMS_SITENAME
        ];;
    }
    
    if(isset($_REQUEST['refresh']) && $_REQUEST['refresh']=="true") {
        _service_refreshList();
    } elseif(!isset($_SESSION['CMS_SRC_LISTS']) || 
            !isset($_SESSION['CMS_SRC_LISTS'][CMS_SITENAME]) || 
            !isset($_SESSION['CMS_SRC_LISTS'][CMS_SITENAME][$_REQUEST["typekey"]])) {
        _service_refreshList();
    }
    
    $data = [
            "items"=> $_SESSION['CMS_SRC_LISTS'][CMS_SITENAME][$_REQUEST["typekey"]],
            "datakey"=>$_REQUEST["typekey"],
            "groups"=>$arrCompsList[$_REQUEST["typekey"]]['groups'],
            "site"=>CMS_SITENAME
        ];
        
    return $data;
}

function _service_refreshList() {
    if(!isset($_REQUEST["typekey"]) || $_REQUEST["typekey"]=="na") return false;
    
    if(!isset($_SESSION['CMS_SRC_LISTS'])) $_SESSION['CMS_SRC_LISTS'] = [];
    if(!isset($_SESSION['CMS_SRC_LISTS'][CMS_SITENAME])) $_SESSION['CMS_SRC_LISTS'][CMS_SITENAME] = [];
    
    $arrCompsList = getCompsList();
    
    $_SESSION['CMS_SRC_LISTS'][CMS_SITENAME][$_REQUEST["typekey"]] = getSourceFileList(strtolower($_REQUEST["typekey"]), $arrCompsList[$_REQUEST["typekey"]]['groups']);
    
    return true;
}

function _service_createItemPanel() {
    if(!isset($_REQUEST["typekey"])) return "Type Not Supported";
    
    
    echo "asdsad asd asd qwe";
}

function _service_createItem() {
    if(!isset($_REQUEST["typekey"])) return ["status"=>false, "msg"=>"Type Not Supported"];
    
    
    return ["status"=>true, "title"=> "", "link"=> ""];
}

function _service_openItem() {
    if(!isset($_REQUEST["typekey"])) return ["status"=>false, "msg"=>"Type Not Supported"];
    if(!isset($_REQUEST["path"])) return ["status"=>false, "msg"=>"File Not Supported"];
    
    $resultData = getSourceEditorLink($_REQUEST["path"], strtolower($_REQUEST["typekey"]));
    
    if($resultData) {
        $title = $resultData['title'];
        $link = $resultData['link'];
        $file = $resultData['file'];
        
        return ["status"=>true, "title"=> $title, "link"=> $link, "file"=> $file];
    } else {
        return ["status"=>false, "msg"=>"Source Not Supported"];
    }
}
?>