<?php
if(!defined('ROOT')) exit('No direct script access allowed');

checkServiceAccess();

$lingDir=ROOT.APPS_FOLDER.CMS_SITENAME."/misc/i18n/";
if(!is_dir($lingDir)) {
  mkdir($lingDir,0777,true);
}
// $cfgFile=ROOT.CFG_FOLDER."jsonConfig/domainmap.json";
// $domainMap=json_decode(file_get_contents($cfgFile),true);	

switch ($_REQUEST['action']) {
  case 'listLing':
      $fs=scandir($lingDir);
      $fss=[];
      foreach($fs as $f) {
        $ext=explode(".",$f);
        $ext=strtolower(end($ext));
        if($ext=="ling") {
          $f=str_replace(".ling","",$f);
          $fss[$f]=_ling($f);
        }
      }
      printServiceMsg($fss);
    break;
  case "lingFile":
    if(isset($_POST['file'])) {
      $f=$lingDir."{$_POST['file']}.ling";
      if(!file_exists($f)) {
        $a=file_put_contents($f,"#"+file);
        if($a<=0) {
          printServiceMsg(["error"=>"Can't create ling file."]);
        }
      }
      $fData=[];
      $data=file_get_contents($f);
      $data=explode("\n",$data);
      foreach($data as $row) {
        if(strlen($row)>0) {
          if(substr($row,0,1)=="#") {
            $fData[]=[
              "title"=>$row,
              "value"=>"",
              "hidden"=>true
            ];
          } else {
            $row=explode("=>",$row);
            if(!isset($row[1])) $row[1]="";
            $fData[]=[
              "title"=>$row[0],
              "value"=>$row[1],
              "hidden"=>false
            ];
          }
        }
      }
      printServiceMsg($fData);
    } else {
      printServiceMsg([]);
    }
    break;
  case "saveFile":
    if(isset($_POST['file'])) {
      $f=$lingDir."{$_POST['file']}.ling";
      unset($_POST['file']);
      
      $data=[];
      foreach($_POST as $a=>$b) {
        if(substr($a,0,1)=="#") {
          $data[]=$a;
        } else {
          $data[]="{$a}=>{$b}";
        }
      }
      $data=implode("\n",$data);
      $a=file_put_contents($f,$data);
      if($a<=0) {
        printServiceMsg("Can't save ling file.");
      } else {
        printServiceMsg("Successfully Saved");
      }
    } else {
      printServiceMsg("Ling Reference Not Found");
    }
    break;
}
?>