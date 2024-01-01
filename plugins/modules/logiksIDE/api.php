<?php
if(!defined('ROOT')) exit('No direct script access allowed');

if(!function_exists("getCompsList")) {
    
    define("CMS_APPS_TYPE", getApp_PROPS("APPS_TYPE", false, "webapp"));
    
    function getCompsList() {
        $data = [];
        switch(CMS_APPS_TYPE) {
            case "website":
                $data = [
                    "page"=>[
                        "label"=>"Pages",
                        "icon"=>"window-maximize",
                        "groups"=>false,
                    ],
                    "controller"=>[
                        "label"=>"Controllers",
                        "icon"=>"code",
                        "groups"=>false,
                    ],
                    "helper"=>[
                        "label"=>"Helpers",
                        "icon"=>"star",
                        "groups"=>false,
                    ],
                    "component"=>[
                        "label"=>"Components",
                        "icon"=>"cube",
                        "groups"=>true,
                    ],
                    "service"=>[
                        "label"=>"Services",
                        "icon"=>"rss",
                        "groups"=>true,
                        "help_link"=>"https://github.com/Logiks/Logiks-Core/wiki/Services"
                    ],
                    "apisource"=>[
                        "label"=>"API Sources",
                        "icon"=>"link",
                        "groups"=>true,
                        "dependent_module"=>"apibox",
                        "help_link"=>"https://github.com/Logiks/Logiks-Core/wiki/Services"
                    ]
                ];
                break;
            case "webapp":case "webportal":
                $data = [
                    "User_Flow"=>[
                        "label"=>"Flowboard",
                        "icon"=>"fas fa-project-diagram",
                        "groups"=>false,
                    ],
                    "navigations"=>[
                        "label"=>"Navigations",
                        "icon"=>"bars",
                        "groups"=>false,
                    ],
                    "controller"=>[
                        "label"=>"Controllers",
                        "icon"=>"code",
                        "groups"=>false,
                    ],
                    "helper"=>[
                        "label"=>"Helpers",
                        "icon"=>"star",
                        "groups"=>false,
                    ],
                    "component"=>[
                        "label"=>"Components",
                        "icon"=>"cube",
                        "groups"=>true,
                    ],
                    "service"=>[
                        "label"=>"Services",
                        "icon"=>"rss",
                        "groups"=>true,
                        "help_link"=>"https://github.com/Logiks/Logiks-Core/wiki/Services"
                    ],
                    "apisource"=>[
                        "label"=>"API Sources",
                        "icon"=>"link",
                        "groups"=>true,
                        "dependent_module"=>"apibox",
                        "help_link"=>"https://github.com/Logiks/Logiks-Core/wiki/Services"
                    ]
                ];
                break;
            case "digioffice":case "bizsuite":
                $data = [
                    "navigations"=>[
                        "label"=>"Navigations",
                        "icon"=>"bars",
                        "groups"=>false,
                    ],
                    "controller"=>[
                        "label"=>"Controllers",
                        "icon"=>"code",
                        "groups"=>false,
                    ],
                    "helper"=>[
                        "label"=>"Helpers",
                        "icon"=>"star",
                        "groups"=>false,
                    ],
                    "component"=>[
                        "label"=>"Components",
                        "icon"=>"cube",
                        "groups"=>true,
                    ],
                    "service"=>[
                        "label"=>"Services",
                        "icon"=>"rss",
                        "groups"=>true,
                        "help_link"=>"https://github.com/Logiks/Logiks-Core/wiki/Services"
                    ],
                    "apisource"=>[
                        "label"=>"API Sources",
                        "icon"=>"link",
                        "groups"=>true,
                        "dependent_module"=>"apibox",
                        "help_link"=>"https://github.com/Logiks/Logiks-Core/wiki/Services"
                    ]
                ];
                break;
            case "mapp":
                $data = [
                    "navigations"=>[
                        "label"=>"Navigations",
                        "icon"=>"bars",
                        "groups"=>false,
                    ],
                    "controller"=>[
                        "label"=>"Controllers",
                        "icon"=>"code",
                        "groups"=>false,
                    ],
                    "helper"=>[
                        "label"=>"Helpers",
                        "icon"=>"star",
                        "groups"=>false,
                    ],
                    "component"=>[
                        "label"=>"Components",
                        "icon"=>"cube",
                        "groups"=>true,
                    ],
                    "service"=>[
                        "label"=>"Services",
                        "icon"=>"rss",
                        "groups"=>true,
                        "help_link"=>"https://github.com/Logiks/Logiks-Core/wiki/Services"
                    ],
                    "apisource"=>[
                        "label"=>"API Sources",
                        "icon"=>"link",
                        "groups"=>true,
                        "dependent_module"=>"apibox",
                        "help_link"=>"https://github.com/Logiks/Logiks-Core/wiki/Services"
                    ],
                    "mappSettings"=> [
                        "label"=>"Mobile Settings",
                        "icon"=>"link",
                        "groups"=>true,
                        "dependent_module"=>"mappSettings",
                    ]
                ];
                break;
            default:
                $data = [
                    "controller"=>[
                        "label"=>"Controllers",
                        "icon"=>"code",
                        "groups"=>false,
                    ],
                    "helper"=>[
                        "label"=>"Helpers",
                        "icon"=>"star",
                        "groups"=>false,
                    ],
                    "component"=>[
                        "label"=>"Components",
                        "icon"=>"cube",
                        "groups"=>true,
                    ],
                    "service"=>[
                        "label"=>"Services",
                        "icon"=>"rss",
                        "groups"=>true,
                        "help_link"=>"https://github.com/Logiks/Logiks-Core/wiki/Services"
                    ],
                    "apisource"=>[
                        "label"=>"API Sources",
                        "icon"=>"link",
                        "groups"=>true,
                        "dependent_module"=>"apibox",
                        "help_link"=>"https://github.com/Logiks/Logiks-Core/wiki/Services"
                    ]
                ];
                break;
        }
        
        foreach($data as $a=>$b) {
            $data[$a] = array_merge([
                    "dependent_module"=>"",
                ], $b);
            if($data[$a]['dependent_module'] && strlen($data[$a]['dependent_module'])>0) {
                if(!checkModule($data[$a]['dependent_module'])) {
                    unset($data[$a]);
                }
            }
        }
        
        return $data;
    }
    
    function getSourceFileList($type, $group = false) {
        $listData = [];
    
        switch($type) {
            case "services":case "service":
                $fs = scandir(CMS_APPROOT."services/");
                $fs = array_splice($fs, 2);
                
                $fs = prepareSourceList("services/", $fs);
                $listData['app'] = $fs;
                
                $listData['modules'] = [];
                // $listData['pluginsDev'] = [];
                
                $dir = [CMS_APPROOT."plugins/modules/", CMS_APPROOT."pluginsDev/modules/"];
                foreach($dir as $d) {
                    if(is_dir($d)) {
                        $fs = scandir($d);
                        $fs = array_splice($fs, 2);
                        
                        foreach($fs as $f) {
                            if(file_exists("{$d}{$f}/service.php")) {
                                $listData['modules'][] = processSourceItem("{$d}{$f}/service.php", str_replace("#".CMS_APPROOT, "/", "#{$d}{$f}/"), $f);
                            } elseif(is_dir("{$d}{$f}/actions/")) {
                                $listData['modules'][] = processSourceItem("{$d}{$f}/actions/", str_replace("#".CMS_APPROOT, "/", "#{$d}{$f}/"), $f);
                            }
                        }
                    }
                }
                
                if(!$group) {
                    $fData = [];
                    foreach($listData as $a=>$b) {
                        $fData = array_merge($fData, $b);
                    }
                    $listData = $fData;
                }
                break;
            
            case "components":case "component":
                $fs = scandir(CMS_APPROOT."plugins/widgets/");
                $fs = array_splice($fs, 2);
                
                $fs = prepareSourceList("plugins/widgets/", $fs);
                $listData['app'] = $fs;
                
                $fs = scandir(CMS_APPROOT."pluginsDev/widgets/");
                $fs = array_splice($fs, 2);
                
                $fs = prepareSourceList("pluginsDev/widgets/", $fs);
                $listData['app'] = array_merge($listData['app'], $fs);
                
                $dir = [CMS_APPROOT."plugins/modules/", CMS_APPROOT."pluginsDev/modules/"];
                foreach($dir as $d) {
                    if(is_dir($d)) {
                        $fs = scandir($d);
                        $fs = array_splice($fs, 2);
                        
                        foreach($fs as $f) {
                            if(file_exists("{$d}{$f}/comps/") && is_dir("{$d}{$f}/comps/")) {
                                $listData['modules'][] = processSourceItem("{$d}{$f}/service.php", str_replace("#".CMS_APPROOT, "/", "#{$d}{$f}/"), $f);
                            }
                        }
                    }
                }
                
                if(!$group) {
                    $fData = [];
                    foreach($listData as $a=>$b) {
                        $fData = array_merge($fData, $b);
                    }
                    $listData = $fData;
                }
                break;
        }
        
        return $listData;
    }
    
    function getSourceEditorLink($filePath, $type) {
        $link = false;
        $file = false;
        $title = "";
        
        //http://logiks.dev.silkdemo.in/modules/cmsEditor?site=studio&forsite=portal&type=edit&src=%2Fservices%2Fverify.php
        switch($type) {
            case "services":
            case "service":
                $file = "{$filePath}.php";
                $link = _link("modules/cmsEditor")."&type=edit&src={$file}&forsite=".CMS_SITENAME;
                break;
            
            case "components":
            case "component":
                $file = "{$filePath}.php";
                $link = _link("modules/cmsEditor")."&type=edit&src={$file}&forsite=".CMS_SITENAME;
                break;
            
            default:
                return false;
        }
        
        $title = basename($file);
        
        return [
                "file"=>$file,
                "link"=>$link,
                "title"=>$title,
            ];
    }
    
    
    
    //Utility functions
    function prepareSourceList($basePath, $fs) {
        $pathList = [];
        foreach($fs as $f) $pathList[] = $basePath;
        return array_map("processSourceItem",$fs, $pathList);
    }
    
    function processSourceItem($file, $basePath, $title = false) {
        $fileArr = explode(".", basename($file));
        if(count($fileArr)>1) {
            $file = implode(array_splice($fileArr, 0, count($fileArr)-1));
        }
        if(!$title) $title = $file;
        return ["title"=>$title, "type"=>strtolower($_REQUEST["typekey"]), "path"=>"{$basePath}{$file}", "icon"=>"file"];
    }
    
    function getFileCountInDir($f) {
        if(file_exists($f)) {
            $fs = scandir($f);
            return count($fs)-2;
        }
        return 0;
    }
    function getDBRecordsCount($tbl, $dbKey="app", $where = []) {
        $data = _db($dbKey)->_selectQ($tbl, "count(*) as count", $where)->_get();
        if($data) return $data[0]["count"];
        return 0;
    }
}
?>