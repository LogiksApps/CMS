<?php
if(!defined('ROOT')) exit('No direct script access allowed');
checkServiceAccess();

if(!isset($_REQUEST['comptype']) || strtolower($_REQUEST['comptype'])=="local") {
    $_REQUEST['comptype']=$_REQUEST['forSite'];
}

if(strtolower($_REQUEST['comptype'])=="globals" || strtolower($_REQUEST['comptype'])=="global") {
    $serviceCFG=ROOT.CFG_FOLDER."jsonConfig/services.json";
} else {
    $serviceCFG=CMS_APPROOT.CFG_FOLDER."services.json";
}

$jsonData = [];
if(file_exists($serviceCFG)) {
    $jsonData=json_decode(file_get_contents($serviceCFG),true);
}
if(!$jsonData) $jsonData = [];

$cfgDefaults=[
        "format"=>"json",
        "privilege_model"=>[],
        "access_control"=>"session",
        "cache"=>"true",
        "debug"=>"false",
        "autoformat"=>"false",
        "src"=>""
      ];
$cfgParams=[
        "format"=>["json","html","table","select","list","xml","raw","txt","css","js"],
        "privilege_model"=>[],
        "access_control"=>["public"=>"remote","private"=>"remote","apikey"=>"remote","development"=>"local","session"=>"local","postlogin"=>"local"],
        "cache"=>["true","false"],
        "debug"=>["true","false"],
        "autoformat"=>["true","false"],
        //"stype"=>["php","py","perl","js","rb"],
      ];

switch($_REQUEST['action']) {
  case "getlist":
    $outJSON=[];
    if(isset($jsonData[strtoupper($_REQUEST['comptype'])])) {
      $_REQUEST['comptype']=strtoupper($_REQUEST['comptype']);
      $outJSON=$jsonData[$_REQUEST['comptype']];
    } elseif(isset($jsonData[strtolower($_REQUEST['comptype'])])) {
      $_REQUEST['comptype']=strtolower($_REQUEST['comptype']);
      $outJSON=$jsonData[$_REQUEST['comptype']];
    } elseif(isset($jsonData[$_REQUEST['comptype']])) {
      $outJSON=$jsonData[$_REQUEST['comptype']];
    }
    
    foreach($outJSON as $key=>$cfg) {
      $cfg['skey']=$key;
      $cfg['type']=$_REQUEST['comptype'];
      if(strtoupper($_REQUEST['comptype'])=="GLOBALS") {
        $cfg['readonly']=true;
      } else {
        $cfg['readonly']=false;
      }
      $cfg['editable']=false;
      if($_REQUEST['comptype']=="GLOBALS") {
        $cfg["src"]="core";
      }
      $outJSON[$key]=array_merge($cfgDefaults,$cfg);
    }
    
    // $jsonServices = json_decode(file_get_contents($serviceCFG),true);
    foreach($outJSON as $a=>$b) {
        $f = getServicePath($b['skey'],$b['src']);
        // println("$a, $f");
        if(file_exists($f) && is_file($f)) {
            $outJSON[$a]['funcs'] = get_functions_in_file($f);
            $funcData = ["SERVICE"=>[],"NONE_SERVICE"=>[]];
            foreach($outJSON[$a]['funcs'] as $func) {
                if(substr($func,0,9)=="_service_") {
                    $funcData["SERVICE"][] = $func;
                } else {
                    $funcData["NONE_SERVICE"][] = $func;
                }
            }
            $html = "";
            $html .= ($funcData['SERVICE']?"<label class='funcName'>".implode("</label>, <label class='funcName'>",$funcData['SERVICE'])."</label>":"");
            $html .= ($funcData['NONE_SERVICE']?"<label class='funcName noneService'>".implode("</label>, <label class='funcName noneService'>",$funcData['NONE_SERVICE'])."</label>":"");
            $outJSON[$a]['funcList'] = $html;
        } elseif(file_exists($f) && is_dir($f)) {
            $fs = scandir($f);
            array_shift($fs);array_shift($fs);
            
            $outJSON[$a]['funcs'] = [];
            foreach($fs as $f) {
                $outJSON[$a]['funcs'][] = str_replace(".php", "", $f);
            }
            $outJSON[$a]['editable']=true;
            $outJSON[$a]['funcList'] = ($outJSON[$a]['funcs']?"<label class='funcName'>".implode("</label>, <label class='funcName'>",$outJSON[$a]['funcs'])."</label>":"");
        }
    }
    //exit();
    // printArray($outJSON);exit();
    
    printServiceMsg(["LIST"=>array_values($outJSON),"params"=>$cfgParams]);
    break;
  case "findmore":
    $out=[];
    
    $f1=CMS_APPROOT."services/";
    if(is_dir($f1)) {
      $fss=scandir($f1);
      $fss=array_slice($fss,2);
      foreach($fss as $f) {
        if(substr($f,0,1)=="." || substr($f,0,1)=="~") continue;
        $f=str_replace(".php","",$f);
        $out[$f]=["skey"=>$f,"src"=>"app"];
      }
    }
    
    $f1=CMS_APPROOT."plugins/modules/";
    if(is_dir($f1)) {
      $fss=scandir($f1);
      $fss=array_slice($fss,2);
      foreach($fss as $f) {
        if(file_exists("{$f1}{$f}/service.php")) {
          $out[$f]=["skey"=>$f,"src"=>"module"];
        } elseif(is_dir("{$f1}{$f}/services/")) {
          $out[$f]=["skey"=>$f,"src"=>"modservice", "readonly"=>true];
        }
      }
    }
    
    $f1=CMS_APPROOT."pluginsDev/modules/";
    if(is_dir($f1)) {
      $fss=scandir($f1);
      $fss=array_slice($fss,2);
      foreach($fss as $f) {
        if(file_exists("{$f1}{$f}/service.php")) {
            $out[$f]=["skey"=>$f,"src"=>"moduleDev"];
        } elseif(is_dir("{$f1}{$f}/services/")) {
            $out[$f]=["skey"=>$f,"src"=>"modserviceDev", "readonly"=>true];
        }
      }
    }
    
    foreach($out as $key=>$cfg) {
      $cfg['type']=SITENAME;
      if(!isset($cfg['readonly'])) $cfg['readonly']=false;
      $out[$key]=array_merge($cfgDefaults,$cfg);
    }
    printServiceMsg(["LIST"=>array_values($out),"params"=>$cfgParams]);
    break;
  case "update":
    if(!is_writable($serviceCFG)) {
      printServiceMsg("error:Configuration Can Not Be Saved As It is Readonly.");
      return;
    }
    if(isset($_POST['s'])) {
      $data=$_POST['s'];
      foreach($data as $a=>$b) {
        if(gettype($b['privilege_model'])=="string") {
          if(strlen($b['privilege_model'])<=0) {
            $data[$a]['privilege_model']=[];
          } else {
            $data[$a]['privilege_model']=explode(",",$b['privilege_model']);
          }
        }
      }
      $jsonData[CMS_SITENAME]=$data;
      
      $jsontxt=json_encode($jsonData,JSON_PRETTY_PRINT);
      
      $len=file_put_contents($serviceCFG,$jsontxt);
      if($len>1) {
        printServiceMsg("Successfully updated the Service Configuration");
      } else {
        printServiceMsg("error:Configuration Could Not Be Saved. May be it's readonly.");
      }
    } else {
      printServiceMsg("error:Configuration Missing");
    }
    break;
  case "spath":
    if(isset($_POST['skey']) && isset($_POST['type'])) {
      switch(strtolower($_POST['type'])) {
        case "app":
          printServiceMsg(_link("modules/cmsEditor")."&type=edit&src=/services/{$_POST['skey']}.php");
          break;
        case "module":
          printServiceMsg(_link("modules/cmsEditor")."&type=edit&src=/plugins/modules/{$_POST['skey']}/service.php");
          break;
        case "moduledev":
          printServiceMsg(_link("modules/cmsEditor")."&type=edit&src=/pluginsDev/modules/{$_POST['skey']}/service.php");
          break;
        default:
          printServiceMsg("");
      }
    } else {
      printServiceMsg("");
    }
    break;
  case "createservice":
    if(isset($_POST['name'])) {
      $skey = _slugify($_POST['name']).".php";
      $sFile = CMS_APPROOT."services/$skey";
      if(file_exists($sFile)) {
        printServiceMsg(["name"=>$skey,"uri"=>_link("modules/cmsEditor")."&type=edit&src=/services/{$skey}","msg"=>"Service already exists"]);
      } else {
        file_put_contents($sFile, file_get_contents(__DIR__."/service_template.txt"));
        if(file_exists($sFile)) {
          printServiceMsg(["name"=>$skey,"uri"=>_link("modules/cmsEditor")."&type=edit&src=/services/{$skey}","msg"=>"","status"=>"ok"]);
        } else {
          printServiceMsg(["name"=>$skey,"msg"=>"File creation error, check if service folder is readonly"]);
        }
      }
    } else {
      printServiceMsg("");
    }
    break;
}

function getServicePath($skey, $type) {
    switch(strtolower($type)) {
        case "app":
          return CMS_APPROOT."services/{$skey}.php";
          break;
        case "module":
          return CMS_APPROOT."plugins/modules/{$skey}/service.php";
          break;
        case "moduledev":
          return CMS_APPROOT."pluginsDev/modules/{$skey}/service.php";
          break;
        case "modservice":
          return CMS_APPROOT."plugins/modules/{$skey}/services/";
          break;
        case "modservicedev":
          return CMS_APPROOT."pluginsDev/modules/{$skey}/services/";
          break;
    }
    return false;
}

function get_functions_in_file($file, $sort = FALSE) {
    $file = file($file);
    $functions = array();
    foreach ($file as $line) {
        $line = trim($line);
        if (stripos($line, 'function ') !== false) {
            $function_name = str_ireplace([
                'public', 'private', 'protected',
                'static'
                    ], '', $line);

            if (!in_array($function_name, ['__construct', '__destruct'])) {
                $functions[] = trim(substr($function_name, 9, strpos($function_name, '(') - 9));
            }
        }
    }
    if ($sort) {
        asort($functions);
        $functions = array_values($functions);
    }
    return $functions;
}
?>