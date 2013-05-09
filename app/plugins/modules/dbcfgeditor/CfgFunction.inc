<?php
if(!defined('ROOT')) exit('No direct script access allowed');

include_once ROOT."config/timezones.php";
include_once ROOT."config/datetimes.php";

class CfgFunction {
	public function getFontList($f=null) {
		$f=ROOT.FONTS_FOLDER;
		$arr=array();
		$fs=scandir($f);
		unset($fs[0]);unset($fs[1]);
		foreach($fs as $b) {
			if(is_file($f.$b)) {
				if(strpos(strtolower($b),".ttf")!=strlen($b)-4) continue;
				$t=str_replace("_"," ",$b);
				$t=substr($t,0,strlen($t)-4);
				$t=ucwords($t);
				$arr[$t]=$b;
			} elseif(is_dir($f.$b)) {
				$f1="{$f}{$b}/";
				$fs1=scandir($f1);
				unset($fs1[0]);unset($fs1[1]);
				foreach($fs1 as $x) {
					if(strpos(strtolower($x),".ttf")!=strlen($x)-4) continue;
					$t=str_replace("_"," ",$x);
					$t=substr($t,0,strlen($t)-4);
					$t=ucwords($t);
					$arr[$t]="{$b}/{$x}";
				}
			}
		}
		natsort($arr);
		return $arr;
	}
}
class CfgSpecialFunction {
	
	public static $funcArr=array("scandir","listfile");
	
	public static function scandir($dir=null) {
		if($dir==null) return array();
		$dir=explode("#",$dir);
		
		$fs=array();
		if(isset($dir[1])) {
			$ds=scandir($dir[0]);
			unset($ds[0]);unset($ds[1]);
			foreach($ds as $a) {
				$ext=pathinfo($a);
				if($ext['extension']==$dir[1]) $fs[substr($a,0,strlen($a)-strlen($ext['extension'])-1)]=$a;
			}
		} else {
			$ds=scandir($dir[0]);
			unset($ds[0]);unset($ds[1]);
			foreach($ds as $a) {
				$fs[$a]=$a;
			}
		}
		return $fs;
	}
	
	public static function listfile($file=null) {
		if($file==null) return array();
		$lst=array();
		$f="";
		if(file_exists(ROOT.MISC_FOLDER.$file)) {
			$f=ROOT.MISC_FOLDER.$file;
		} elseif(file_exists(ROOT.CFG_FOLDER.$file)) {
			$f=ROOT.CFG_FOLDER.$file;
		} elseif(file_exists(APPROOT.APPS_MISC_FOLDER.$file)) {
			$f=APPROOT.APPS_MISC_FOLDER.$file;
		} elseif(file_exists(APPROOT.APPS_CONFIG_FOLDER.$file)) {
			$f=APPROOT.APPS_CONFIG_FOLDER.$file;
		}
		if(strlen($f)>0) {
			$data=file_get_contents($f);
			$data=explode("\n",$data);
			foreach($data as $a=>$b) {
				$b=explode("=",$b);
				unset($data[$a]);
				if(isset($b[1])) $data[$b[0]]=$b[1];
				else $data[$b[0]]=$b[0];
			}
			$lst=$data;
		}
		return $lst;
	}
}
?>
