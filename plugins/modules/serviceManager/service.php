<?php
if(!defined('ROOT')) exit('No direct script access allowed');
checkServiceAccess();

$serviceCFG=ROOT.CFG_FOLDER."jsonConfig/services.json";

$jsonData=json_decode(file_get_contents($serviceCFG),true);

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
    if(!isset($_REQUEST['comptype']) || strtolower($_REQUEST['comptype'])=="local") {
      $_REQUEST['comptype']=$_REQUEST['forSite'];
    }
    
    $outJSON=[];
    if(isset($jsonData[strtoupper($_REQUEST['comptype'])])) {
      $_REQUEST['comptype']=strtoupper($_REQUEST['comptype']);
      $outJSON=$jsonData[$_REQUEST['comptype']];
    } elseif(isset($jsonData[strtolower($_REQUEST['comptype'])])) {
      $_REQUEST['comptype']=strtolower($_REQUEST['comptype']);
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
      $outJSON[$key]=array_merge($cfgDefaults,$cfg);
    }
    
    printServiceMsg(array_values($outJSON));
    break;
  case "findmore":
    $out=[];
    
    $f1=CMS_APPROOT."services/";
    if(is_dir($f1)) {
      $fss=scandir($f1);
      $fss=array_slice($fss,2);
      foreach($fss as $f) {
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
        }
      }
    }
    
    foreach($out as $key=>$cfg) {
      $cfg['type']=SITENAME;
      $cfg['readonly']=false;
      $out[$key]=array_merge($cfgDefaults,$cfg);
    }
    printServiceMsg(array_values($out));
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
}

?>