<?php
if(!defined('ROOT')) exit('No direct script access allowed');

//updateUserMetas();

$slug=_slug("mod/panel/task/refid");

if(strlen($slug['panel'])==0) {
	$slug['panel']="users";
	$_REQUEST['panel']="users";
} else {
	$_REQUEST['panel']=$slug['panel'];
}
if(strlen($slug['task'])==0) {
	$slug['task']="list";
}

$taskFile=$slug['task'];
switch(strtolower($slug['task'])) {
	case "new":case "edit":
		$taskFile="form";
		break;
	case "listusers":
		$taskFile="comps/listusers";
		break;
	case "userinfo":
		$taskFile="comps/userinfo";
		break;
	case "pwd":
		$taskFile="comps/pwd";
		break;
	default:
		$taskFile="list";
}

//printArray($slug);printArray($_REQUEST);

$f=__DIR__."/{$taskFile}.php";
include $f;
?>