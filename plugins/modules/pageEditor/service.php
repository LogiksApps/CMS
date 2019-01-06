<?php
if(!defined('ROOT')) exit('No direct script access allowed');

checkServiceAccess();

if(!isset($_REQUEST["action"])) {
	printServiceErrorMsg("NotAcceptable","Action Not Defined.");
}
if(!isset($_REQUEST["src"])) {
	printServiceErrorMsg("NotAcceptable","Source Not Defined.");
}
if(!isset($_REQUEST["type"])) {
	printServiceErrorMsg("NotAcceptable","Data Type Not Defined.");
}

include __DIR__."/config.php";

$defnFile=getAppFile("pages/defn/".$_REQUEST["src"].".json");
if(!file_exists($defnFile)) {
	$_REQUEST["src"]=basename($_REQUEST["src"]);
	printServiceErrorMsg("NotAcceptable","Sorry, Source {$_REQUEST["src"]} not found.");
	return;
}

$srcName=basename($defnFile);
$srcName=str_replace(".json", "", $srcName);

switch ($_REQUEST['action']) {
	case "getFile":
		switch ($_REQUEST["type"]) {
			case 'info':
			case 'layout':
			case 'meta':
				$xFile=$defnFile;
				break;
			case 'markup':
				$xFile=getAppFile("pages/viewpage/{$srcName}.tpl");
				break;
			case 'code':
				$xFile=getAppFile("pages/viewpage/{$srcName}.php");
				break;
			case 'style':
				$xFile=getAppFile("css/comps/{$srcName}.css");
				break;
			case 'script':
			case 'javascript':
				$xFile=getAppFile("js/comps/{$srcName}.js");
				break;
			default:
				$xFile="";
		}
		printServiceMsg(['file'=>str_replace("#".ROOT.APPS_FOLDER.$_REQUEST['forsite']."/","","#{$xFile}")]);
		//
		break;
	case "savePage":
		//printArray($_POST);
		switch ($_REQUEST["type"]) {
			case 'info':
			case 'layout':
				$jsonPage=json_decode(file_get_contents($defnFile),true);
				$jsonNew=array_merge($jsonPage,$_POST);

				//printArray($jsonNew);
				$a=saveAppFile($defnFile,json_encode($jsonNew, JSON_PRETTY_PRINT|JSON_HEX_QUOT|JSON_UNESCAPED_SLASHES));
				if($a===false) {
					echo "failed";
				} else {
					echo "done";
				}
				break;
			
			case 'meta':
				$meta=[];
				$metaLength=[];
				foreach ($_POST as $key => $values) {
					foreach ($values as $nx => $vx) {
						if(strlen($vx)>0) $meta[$nx][$key]=$vx;
					}
				}
				$jsonPage=json_decode(file_get_contents($defnFile),true);
				$jsonPage['meta']=$meta;

				//printArray($jsonNew);
				$a=saveAppFile($defnFile,json_encode($jsonPage, JSON_PRETTY_PRINT|JSON_HEX_QUOT|JSON_UNESCAPED_SLASHES));
				if($a===false) {
					echo "failed";
				} else {
					echo "done";
				}
				break;

			case 'markup':
				$pgFile=getAppFile("pages/viewpage/{$srcName}.tpl");
				if(!is_dir(dirname($pgFile))) mkdir(dirname($pgFile),0777,true);
				if(file_exists($pgFile) && !is_writable($pgFile)) {
					echo "failed:Source File Write Protected";
					exit();
				}
				//printArray(urldecode($_POST['txt']));
				$a=saveAppFile($pgFile,$_POST['txt']);
				if($a===false) {
					echo "failed";
				} else {
					echo "done";
				}
				break;

			case 'code':
				$pgFile=getAppFile("pages/viewpage/{$srcName}.php");
				if(!is_dir(dirname($pgFile))) mkdir(dirname($pgFile),0777,true);
				if(file_exists($pgFile) && !is_writable($pgFile)) {
					echo "failed:Source File Write Protected";
					exit();
				}
				//printArray(urldecode($_POST['txt']));
				$text=$_POST['txt'];
				$a=saveAppFile($pgFile,$text);//"< ?php\n".."\n\n? >"
				//$a=saveAppFile($pgFile,$text);
				if($a===false) {
					echo "failed";
				} else {
					echo "done";
				}
				break;

			case 'style':
				$pgFile=getAppFile("css/comps/{$srcName}.css");
				if(!is_dir(dirname($pgFile))) mkdir(dirname($pgFile),0777,true);
				if(file_exists($pgFile) && !is_writable($pgFile)) {
					echo "failed:Source File Write Protected";
					exit();
				}
				//printArray(urldecode($_POST['txt']));
				$a=saveAppFile($pgFile,$_POST['txt']);
				if($a===false) {
					echo "failed";
				} else {
					echo "done";
				}
				break;

			case 'script':
			case 'javascript':
				$pgFile=getAppFile("js/comps/{$srcName}.js");
				if(!is_dir(dirname($pgFile))) mkdir(dirname($pgFile),0777,true);
				if(file_exists($pgFile) && !is_writable($pgFile)) {
					echo "failed:Source File Write Protected";
					exit();
				}
				//printArray(urldecode($_POST['txt']));
				$a=saveAppFile($pgFile,$_POST['txt']);
				if($a===false) {
					echo "failed";
				} else {
					echo "done";
				}
				break;
		}
	break;
	case "getsrc":
		switch ($_REQUEST["type"]) {
			case 'markup':
				$pgFile=getAppFile("pages/viewpage/{$srcName}.tpl");
				if(file_exists($pgFile)) {
					echo file_get_contents($pgFile);
				} else {
					echo "";
				}
				break;

			case 'code':
				$pgFile=getAppFile("pages/viewpage/{$srcName}.php");
				if(file_exists($pgFile)) {
					echo file_get_contents($pgFile);
				} else {
					echo "";
				}
				break;

			case 'style':
				$pgFile=getAppFile("css/comps/{$srcName}.css");
				if(file_exists($pgFile)) {
					echo file_get_contents($pgFile);
				} else {
					echo "";
				}
				break;

			case 'script':
			case 'javascript':
				$pgFile=getAppFile("js/comps/{$srcName}.js");
				if(file_exists($pgFile)) {
					echo file_get_contents($pgFile);
				} else {
					echo "";
				}
				break;
		}
	break;
}
?>