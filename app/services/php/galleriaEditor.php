<?php
if (!defined('ROOT')) exit('No direct script access allowed');
checkServiceSession();
checkUserSiteAccess($_REQUEST['forsite'],true);
user_admin_check(true);

loadHelpers("imageprops");

if(isset($_REQUEST["action"]) && isset($_REQUEST['forsite'])) {
	loadModule("dbcon");
	$folders=loadFolderConfig();
	
	$result=array();
	$result['status']="";
	$result['msg']="";
	
	$baseFolder="galleries/";
	$bannerFolder=$folders["APPROOT"].$folders["APPS_MEDIA_FOLDER"].$baseFolder;
	$relFolder=SiteLocation.APPS_FOLDER.$_REQUEST['forsite']."/".$folders["APPS_MEDIA_FOLDER"].$baseFolder;
	
	if(!is_dir($bannerFolder)) {
		if(mkdir($bannerFolder,0777,true)) {
			chmod($bannerFolder,0777);
		}
	}
	if(!is_dir($bannerFolder)) {
		$arr=array(
				"Error"=>"Failed To Find Base Folder.",
			);
		printFormattedArray($arr);
		exit();
	}
	if($_REQUEST["action"]=="listsets") {
		$arr=array();
		$fs=scandir($bannerFolder);
		$fs=array_reverse($fs);
		unset($fs[count($fs)-1]);unset($fs[count($fs)-1]);
		$fs=array_reverse($fs);
		foreach($fs as $a=>$b) {
			$fs[$b]="{$b}";
			unset($fs[$a]);
		}
		printFormattedArray($fs);
		exit();
	} elseif($_REQUEST["action"]=="listphotos" && isset($_REQUEST['photoset'])) {
		$f=$bannerFolder.$_REQUEST['photoset']."/";
		scanFixSet($f);
		
		$photos=array();
		$fs=scandir($f."large/");
		$fs=array_reverse($fs);
		unset($fs[count($fs)-1]);unset($fs[count($fs)-1]);
		$fs=array_reverse($fs);
		foreach($fs as $p) {
			$fname=explode(".",$p);
			$fname=$fname[0];
			$thumb=$fname.".png";
			$txt=$fname.".txt";
			
			$src="{$relFolder}{$_REQUEST['photoset']}/thumbs/$thumb";
			$txt=$f."text/$txt";
			
			if(file_exists($txt)) {
				$txt=file_get_contents($txt);
			} else {
				$txt="";
			}
			$imgData=getimagesize($f."large/$p");
			
			$descs="{$imgData[0]}x{$imgData[1]}";
			$descs="<div class='description'>$descs</div>";
			
			$html="<div class='thumbnail' rel='$fname' style='background-image:url($src);' title='$txt'>$descs</div>";
			echo $html;
		}
		exit();
	} elseif($_REQUEST["action"]=="viewphoto" && isset($_REQUEST['photoset']) && isset($_REQUEST['photo'])) {
		$f="";
		$src="";
		$fs=scandir($bannerFolder.$_REQUEST['photoset']."/large/");
		foreach($fs as $p) {
			$fname=strstr($p,".",-1);
			if($fname==$_REQUEST['photo']) {
				$f=$bannerFolder.$_REQUEST['photoset']."/large/$p";
				$src="{$relFolder}{$_REQUEST['photoset']}/large/$p";
				$txt=$bannerFolder.$_REQUEST['photoset']."/text/{$fname}.txt";
				break;
			}
		}
		if(strlen($f)>0) {
			$imgData=getimagesize($f);
			
			$imgDimensions="Title :: {$_REQUEST['photo']}\n";
			$imgDimensions.="Dimension :: {$imgData[0]}x{$imgData[1]}\n";
			$imgDimensions.="Type :: {$imgData['mime']}";
			
			$imgData["width"]=$imgData[0];
			$imgData["height"]=$imgData[1];
			$imgData["wh"]=$imgData[3];
			
			$html="";
			if(isset($_REQUEST['orginal']) && $_REQUEST['orginal']=="true") {
				$html="<img src='$src' {$imgData[3]} alt='No Media Found' title='$imgDimensions' />";
			} else {
				$html="<img src='$src' width=100% height=99% alt='No Media Found' title='$imgDimensions' />";
			}
			$imgData['html']=$html;
			
			if(file_exists($txt)) {
				$imgData["description"]=file_get_contents($txt);
			} else {
				$imgData["description"]="";
			}
			echo json_encode($imgData);
		}
		exit();
	} elseif($_REQUEST["action"]=="setDescription" && isset($_REQUEST['photoset']) && isset($_REQUEST['photo']) && isset($_REQUEST['data'])) {
		$f=$bannerFolder.$_REQUEST['photoset']."/text/".$_REQUEST['photo'].".txt";
		if(is_writable($f)) {
			file_put_contents($f,$_REQUEST['data']);
		} else {
			echo "Sorry, Description Is ReadOnly.";
		}
		exit();
	} elseif($_REQUEST["action"]=="deletePhoto" && isset($_REQUEST['photoset']) && isset($_REQUEST['photo'])) {
		$txt=$bannerFolder.$_REQUEST['photoset']."/text/".$_REQUEST['photo'].".txt";
		$large=$bannerFolder.$_REQUEST['photoset']."/large/".getPhoto($bannerFolder,"large");
		$thumb=$bannerFolder.$_REQUEST['photoset']."/thumbs/".getPhoto($bannerFolder,"thumbs");
		
		if(file_exists($txt)) {
			unlink($txt);
		}
		if(file_exists($large)) {
			unlink($large);
		}
		if(file_exists($thumb)) {
			unlink($thumb);
		}
		
		if(file_exists($large) || file_exists($thumb) || file_exists($txt)) {
			$result['msg']="Error Deleting Media.";
		}
		
		if(strlen($result['msg'])>0) $result['status']="Error";
		else $result['status']="ok";
		
		echo json_encode($result);
		exit();
	} elseif($_REQUEST["action"]=="createSet" && isset($_REQUEST['photoset'])) {
		$f=$bannerFolder.$_REQUEST['photoset']."/";
		if(is_dir($f) && file_exists($f."config.cfg")) {
			$result['msg']="The given name is not unique. Try some other name.";
		} else {
			if(mkdir($f,0777,true)) {
				chmod($f,0777);
			}
			if(!file_exists($f)) {
				$result['msg']="Error Creating New PhotoSet <b>{$_REQUEST['photoset']}</b>";
			} else {
				scanFixSet($f);
			}
		}
		if(strlen($result['msg'])>0) $result['status']="Error";
		else $result['status']="ok";
		echo json_encode($result);
		exit();
	} elseif($_REQUEST["action"]=="deleteSet" && isset($_REQUEST['photoset'])) {
		loadHelpers("files");
		$f=$bannerFolder.$_REQUEST['photoset'];
		if(file_exists($f)) {
			deleteDir($f);
		}
		if(file_exists($f)) {
			$result['msg']="Error Deleting PhotoSet <b>{$_REQUEST['photoset']}</b>";
		}
		if(strlen($result['msg'])>0) $result['status']="Error";
		else $result['status']="ok";
		echo json_encode($result);
		exit();
	} elseif($_REQUEST["action"]=="cloneSet") {
		exit();
	} elseif($_REQUEST["action"]=="renameSet") {
		exit();
	} elseif($_REQUEST["action"]=="upload" && isset($_REQUEST['photoset'])) {
		$f=$bannerFolder.$_REQUEST['photoset']."/";
		scanFixSet($f);
		
		$attrs=getAttributes($f);
		
		printArray($_POST);
		echo $f;
		printArray($_FILES);
		
		foreach($_FILES as $title=>$src) {
			$src['name']=str_replace(" ","_",$src['name']);
			
			$ip=new ImageProps();
			$ip->load($src['tmp_name']);
			$type=$ip->getImageType();
			$image=$ip->getImage();
			
			$targetFile=$f."large/".$src['name'];
			if(file_exists($targetFile)) {
				$targetFile=$f."large/"._randomId()."_".$src['name'];
			}
			
			$fname=basename($targetFile);
			$fname=explode(".",$fname);
			unset($fname[count($fname)-1]);
			$fname=implode(".",$fname);
			
			$ip->resize($attrs['LARGE_WIDTH'],$attrs['LARGE_HEIGHT']);
			$ip->save($targetFile,$type);
			
			$targetFile=$f."thumbs/".$fname.".png";
			$ip->setImage($image);
			$ip->resize($attrs['THUMB_WIDTH'],$attrs['THUMB_HEIGHT']);
			$ip->save($targetFile,$type);
			
			if(isset($_POST[$title])) {
				$targetFile=$f."text/".$fname.".txt";
				file_put_contents($targetFile,$_POST[$title]);
			}
		}
		if(isset($_REQUEST['js'])) {
			echo "<script>parent.{$_REQUEST['js']}();</script>";
		}
		exit();
	} 
}

function scanFixSet($f) {
	$fr=array();
	$fr[1]['path']=$f."large/";
	$fr[2]['path']=$f."thumbs/";
	$fr[3]['path']=$f."text/";
	
	foreach($fr as $a=>$ff) {
		$p=$ff['path'];
		if(!is_dir($p)) {
			if(mkdir($p,0777,true)) {
				chmod($p,0777);
				$fr[$a]['count']=0;
			}
		} else {
			$ffs=scandir($p);
			$fr[$a]['count']=count($ffs)-2;
		}
	}
	if($fr[1]['count']>$fr[2]['count'] || $fr[1]['count']>$fr[3]['count']) {
		$ffs=scandir($fr[1]['path']);
		unset($ffs[0]);unset($ffs[1]);
		foreach($ffs as $p) {
			$fname=$p;
			$fname=explode(".",$fname);
			$fname=$fname[0];
			$large=$fr[1]['path'].$p;
			$txt=$fr[3]['path'].$fname.".txt";
			$thumb=$fr[2]['path'].$fname.".png";
			if(!file_exists($txt)) {
				file_put_contents($txt,"");
				if(file_exists($txt))
					chmod($txt,0777);
			}
			if(!file_exists($thumb)) {
				createThumb($large,$thumb);
				if(file_exists($thumb))
					chmod($thumb,0777);
			}
		}
	}
	
	$cfg=$f."config.cfg";
	
	if(!file_exists($cfg)) {
		$cfgData="LARGE_WIDTH=800
LARGE_HEIGHT=600
THUMB_WIDTH=148
THUMB_HEIGHT=148
ENABLE_TEXT=true
";
		file_put_contents($cfg,$cfgData);
		chmod($cfg,0777);
	}
	
	
	if($fr[1]['count']>$fr[2]['count'] || $fr[1]['count']>$fr[3]['count']) {
		return false;
	} else {
		return true;
	}
}
function getAttributes($f) {
	$arr=array();
	$cfg=$f."config.cfg";
	if(file_exists($cfg)) {
		$cfg=file_get_contents($cfg);
		$cfg=explode("\n",$cfg);
		foreach($cfg as $s) {
			if(strlen($s)>0) {
				$s=explode("=",$s);
				$arr[$s[0]]=$s[1];
			}
		}
	}
	
	if(!isset($arr["LARGE_WIDTH"])) {
		$arr["LARGE_WIDTH"]=800;
	}
	if(!isset($arr["LARGE_HEIGHT"])) {
		$arr["LARGE_HEIGHT"]=600;
	}
	if(!isset($arr["THUMB_WIDTH"])) {
		$arr["THUMB_WIDTH"]=148;
	}
	if(!isset($arr["THUMB_HEIGHT"])) {
		$arr["THUMB_HEIGHT"]=148;
	}
	if(!isset($arr["ENABLE_TEXT"])) {
		$arr["ENABLE_TEXT"]="true";
	}
	return $arr;
}
function createThumb($src,$thumb) {
	if(!file_exists($src)) return false;
	$ip=new ImageProps();
	$ip->load($src);
	$ip->createThumb($thumb);
	return true;
}
function getPhoto($bannerFolder,$dir='large/') {
	$fs=scandir($bannerFolder.$_REQUEST['photoset']."/$dir/");
	foreach($fs as $p) {
		//$fname=strstr($p,".",-1);
		$fname=explode(".",$fname);
		$fname=$fname[0];
		if($fname==$_REQUEST['photo']) {
			return $p;
		}
	}
	return "";
}

?>
