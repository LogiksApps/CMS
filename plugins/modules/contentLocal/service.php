<?php
if(!defined('ROOT')) exit('No direct script access allowed');
checkServiceSession();

include_once __DIR__."/api.php";

$TARGETDIR=ROOT.APPS_FOLDER.$_REQUEST['forsite']."/misc/contents/";
if(!is_dir($TARGETDIR)) {
  $a=mkdir($TARGETDIR,0777,true);
  if(!$a) {
    printServiceMsg(["error"=>true,"msg"=>"Content Folder Could Not Be Created."]);
    return;
  }
}

loadHelpers("countries");
$localeList=getLocaleList();
foreach($localeList as $key=>$locale) {
  switch($key) {
    case "en-gb":
      $key="gb";
      break;
    case "gsw-berne":
      $key="ch";
      break;
    case "pt-pt":
      $key="pt";
      break;
    case "pt-br":
      $key="br";
      break;
    case "xx-lolspeak":
      $key="xx";
      break;
    case "zh-hans":
      $key="zh";
      break;
    case "zh-hant":
      $key="zh";
      break;
  }
  $localeList[$key]=$locale;
}
switch($_REQUEST["action"]) {
  case "locales":
    $finalList=[];
    foreach($localeList as $key=>$locale) {
      if(strpos($key,"-")>0) continue;
      $finalList[$locale[0]]=strtolower($key);
    }
    printServiceMsg($finalList);
    break;
  case "list":
    $fs=scandir($TARGETDIR);
    array_shift($fs);array_shift($fs);

    $fData=[];
    foreach($fs as $a=>$b) {
      $ext=explode(".",$b);
      $ext=strtolower(end($ext));
      
      $title=str_replace(".md","",str_replace(".html","",str_replace(".htm","",str_replace("~","",basename($b)))));
      $bx=explode("_",$title);
      $category=$bx[0];
      $tags="";
      $locale="GB";
      $flag=SiteLocation."media/flags/gb.gif";
      $country="United Kingdom";
      $blocked=false;
      $type=$ext;
      
      if(substr($b,0,1)=="~") {
        $blocked=true;
      }

      if(count($bx)>1) {
        $locale=strtoupper($bx[count($bx)-1]);
        if(strlen($locale)==2) {
          $flagData=getCountry($locale);
          
          $locale=$flagData['short'];
          $country=$flagData['name'];
          $flag=SiteLocation.$flagData['flag'];
        }
        
        if(count($bx)>2) {
          $category=implode(" ",array_slice($bx,0,count($bx)-1));
        }
      }
      
      if(isset($localeList[strtolower($locale)])) {
        $title=$localeList[strtolower($locale)][0];
      } else {
        $title="English";
      }
      
      $fData[$category][$b]=[
        "id"=>md5($b),
        "slug"=>$b,
        "title"=>$title." [".strtolower($locale)."]",
        "category"=>$category,
        "tags"=>$tags,
        "vers"=>"1",
        "locale"=>$locale,
        "country"=>$country,
        "flag"=>$flag,
        "type"=>$type,
        "blocked"=>$blocked,
        "created_on"=>date("Y-m-d H:i:s",filectime($TARGETDIR.$b)),
        "edited_on"=>date("Y-m-d H:i:s",filemtime($TARGETDIR.$b)),
      ];
    }
    printServiceMSG($fData);
  break;
  case "fetchTXT":
    if(isset($_POST['slug'])) {
			$f=$TARGETDIR.$_POST['slug'];
			if(file_exists($f)) {
        echo file_get_contents($f);
      } else {
        echo "error: File does not exist";
      }
		} else {
			echo "error: Reference Not Found";
		}
  break;
  case "create":
		if(!checkUserRoles("contentLocal","content","CREATE")) {
			echo "error: You lack permission for this action";
			return;
		}
    if(isset($_POST['fname']) && isset($_POST['locale']) && isset($_POST['type'])) {
      $slug=$_POST['fname']."_".strtolower($_POST['locale']).".".strtolower($_POST['type']);
      $f=$TARGETDIR.$slug;
      if(file_exists($f)) {
        echo "error: Content File for same language already exists";
        return;
      }
      file_put_contents($f,"");
      if(file_exists($f)) {
        echo $slug;
      } else {
        echo "error: Create failed. Try again later.";
      }
    } else {
      echo "error: Reference Not Found";
    }
  break;
  case "save":
		if(!checkUserRoles("contentLocal","content","UPDATE")) {
			echo "error: You lack permission for this action";
			return;
		}
    if(isset($_POST['slug']) && isset($_POST['txt']) && strlen($_POST['txt'])>1) {
			$f=$TARGETDIR.$_POST['slug'];
			if(file_exists($f)) {
        $a=file_put_contents($f,$_POST['txt']);
        if($a>0) {
          echo "Successfully updated content.";
        } else {
          echo "error: Update failed. Try again later.";
        }
      } else {
        echo "error: File does not exist";
      }
		} else {
			echo "error: Reference Not Found";
		}
  break;
  case "block":
		if(!checkUserRoles("contentLocal","content","UPDATE")) {
			echo "error: You lack permission for this action";
			return;
		}
    if(isset($_POST['slug'])) {
      $f=$TARGETDIR.$_POST['slug'];
			if(file_exists($f)) {
        $newF=$TARGETDIR."~".$_POST['slug'];
        cp($f,$newF);
        if(file_exists($newF)) {
          echo "Block successfull";
        } else {
          echo "error: Block failed";
        }
      } else {
        echo "error: File does not exist";
      }
    } else {
      echo "error: Reference Not Found";
    }
  break;
  case "unblock":
		if(!checkUserRoles("contentLocal","content","UPDATE")) {
			echo "error: You lack permission for this action";
			return;
		}
    if(isset($_POST['slug'])) {
      $f=$TARGETDIR.$_POST['slug'];
			if(file_exists($f)) {
        $newF=$TARGETDIR.substr($_POST['slug'],1);
        cp($f,$newF);
        if(file_exists($newF)) {
          echo "Unblock successfull";
        } else {
          echo "error: Unblock failed";
        }
      } else {
        echo "error: File does not exist";
      }
    } else {
      echo "error: Reference Not Found";
    }
  break;
  case "delete":
		if(!checkUserRoles("contentLocal","content","DELETE")) {
			echo "error: You lack permission for this action";
			return;
		}
		if(isset($_POST['slug'])) {
			$slugs=explode(",",$_POST['slug']);
			if(count($slugs)<=1) {
				$f=$TARGETDIR.$_POST['slug'];
				if(file_exists($f)) {
					unlink($f);
					echo "Deleted Successfully";
				} else {
					echo "error: File does not exist";
				}
			} else {
				foreach($slugs as $f1) {
					$f=$TARGETDIR.$f1;
					if(file_exists($f)) {
						unlink($f);
					}
				}
				echo "Deleted Successfully";
			}
		} else {
			echo "error: Reference Not Found";
		}
  break;
}
?>
