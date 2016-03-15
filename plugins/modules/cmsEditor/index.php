<?php
if(!defined('ROOT')) exit('No direct script access allowed');

include dirname(__FILE__)."/config.php";

$editType=$_REQUEST['type'];

$forSite=$_REQUEST['forsite'];

$webpath=getWebPath(__DIR__)."/";

switch($editType) {
	case "edit":
		$srcFile=getAppFile($_REQUEST['src']);

		if(!file_exists($srcFile)) {
			echo "<h1 align=center>Sorry, source file not found</h1>";
			exit();
		}

		if(isset($_REQUEST['ext'])) {
			$ext=strtolower($_REQUEST['ext']);
		} else {
			$ext=explode(".", $_REQUEST['src']);
			$ext=strtolower(end($ext));
		}

		if(isset($editorConfig['mime-map'][$ext])) {
			$ext=$editorConfig['mime-map'][$ext];
		}

		$_REQUEST['ext']=$ext;
		$_REQUEST['path']=dirname($_REQUEST['src'])."/";

		if(substr($_REQUEST['path'], 0,1)=="/") $_REQUEST['path']=substr($_REQUEST['path'], 1);

		$finfo = finfo_open(FILEINFO_MIME_TYPE); // return mime type aka mimetype extension
		$srcInfo=finfo_file($finfo, $srcFile);
		finfo_close($finfo);

		$srcInfo=explode("/", $srcInfo);
		$_REQUEST['srcinfo']=$srcInfo[0];

		if(isset($editorConfig['layouts'][$_REQUEST['path']])) {
			include_once $editorConfig['layouts'][$_REQUEST['path']];
		} elseif(isset($editorConfig['mimes'][$_REQUEST['ext']])) {
			include_once $editorConfig['mimes'][$_REQUEST['ext']];
		} elseif(isset($editorConfig['srctype'][$_REQUEST['srcinfo']])) {
			include_once $editorConfig['srctype'][$_REQUEST['srcinfo']];
		} else {
			echo "<h1 align=center>Sorry, could not find any supported editor</h1>";
		}	
	break;
	case "new":
		if(!isset($_REQUEST['ext'])) $_REQUEST['ext']="text";
		if(isset($editorConfig['mime-map'][$_REQUEST['ext']])) {
			$_REQUEST['ext']=$editorConfig['mime-map'][$_REQUEST['ext']];
		}
		$_REQUEST['srcinfo']="text";
		$_REQUEST['src']="";

		if(isset($editorConfig['mimes'][$_REQUEST['ext']])) {
			include_once $editorConfig['mimes'][$_REQUEST['ext']];
		} elseif(isset($editorConfig['srctype'][$_REQUEST['srcinfo']])) {
			include_once $editorConfig['srctype'][$_REQUEST['srcinfo']];
		} else {
			echo "<h1 align=center>Sorry, could not find any supported editor</h1>";
		}
	break;
	default:
		echo "<h1 align=center>Sorry, Action Not Supported</h1>";
}




//printArray($_REQUEST);
?>
