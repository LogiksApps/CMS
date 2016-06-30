<?php
if(!defined('ROOT')) exit('No direct script access allowed');

checkServiceAccess();

if(!isset($_REQUEST["action"])) {
	printServiceErrorMsg("NotAcceptable","Action Not Defined.");
}

loadModuleLib("settingsCommon","api");

$cfgDir=ROOT.APPS_FOLDER."cms/".CFG_FOLDER;
$noShow=[];//"framework.cfg"

switch ($_REQUEST['action']) {
	case 'getlist':		
		$fs=scandir($cfgDir);
		$out=[];
		foreach ($fs as $f) {
			if(!is_dir($f)) {
				$ext=explode(".", $f);
				$ext=strtolower(end($ext));
				if($ext=="cfg") {
					if(in_array($f, $noShow)) continue;
					$cfgF=str_replace(".cfg", "", $f);
					$out[]=[
						"path"=>$cfgF,
						"name"=>toTitle(_ling($cfgF)),
					];
				}
			}
		}
		printServiceMsg(array_merge([["path"=>"apps",
						"name"=>"Application",]],$out));
	break;
	case 'fetch':
		if(!isset($_REQUEST["src"])) {
			printServiceErrorMsg("NotAcceptable","Component Type Not Defined.");
		}
		$cfgFile=$cfgDir."{$_REQUEST['src']}.cfg";
		if($_REQUEST['src']=="apps") $cfgFile=dirname($cfgDir)."/apps.cfg";
		
		if(file_exists($cfgFile)) {
			if(!is_writable($cfgFile)) {
				echo "<div class='errorBox detachParent alert alert-warning alert-dismissible' style='margin-top: 10px;margin-bottom: 10px;'>Config Source is Readonly.
				<button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times;</span></button>
				</div>";
			}
			echo printCFGForm($cfgFile);
		} else {
			echo "<div class='errorBox alert alert-danger'>Sorry, requested config not found.</div>";
		}
	break;
	case 'save':
		if(!isset($_REQUEST["src"])) {
			printServiceErrorMsg("NotAcceptable","Component Type Not Defined.");
		}
		$cfgFile=$cfgDir."{$_REQUEST['src']}.cfg";
		if($_REQUEST['src']=="apps") $cfgFile=dirname($cfgDir)."/apps.cfg";
		
		if(file_exists($cfgFile)) {
			if(!is_writable($cfgFile)) {
				echo "Config file is readonly.";
				return;
			}
			$data=ConfigFileReader::LoadFile($cfgFile);

			foreach ($_POST as $key => $value) {
				$key=explode(":", $key);
				$data[$key[0]][$key[1]]=$value;
			}

			if(ConfigFileReader::SaveCfgFile($cfgFile,$data)) echo "success";
			else echo "Sorry, saving failed. Please try again later.";
		} else {
			echo "Sorry, requested config not found.";
		}
	break;
}

?>