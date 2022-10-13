<?php
if(!defined('ROOT')) exit('No direct script access allowed');

loadModuleLib("forms","api");

echo _css("credsEditor");
echo _js("credsEditor");


$slug=_slug("module/param1/param2/param3");
if(!isset($slug['param2'])) $slug['param2']="new";

$mode="new";
$form="user";
$title="Create New User";
$where=[];//'userid'=>"root"

switch ($slug['param1']) {
	case 'users':
		$form="user";
		if($slug['param2']=="new") {
			$mode="new";
			$title="New User";
		} else {
			$mode="update";
			$title="Update User";
			if(strlen($slug['param3'])==32) $where=['md5(id)'=>$slug['param3']];
			else $where=['id'=>$slug['param3']];
		}
		include_once __DIR__."/comps/editor.php";
		break;

	case 'privileges':
		$form="privilege";
		if($slug['param2']=="new") {
			$mode="new";
			$title="New Privilege";
		} else {
			$mode="update";
			$title="Update Privilege";
			if(strlen($slug['param3'])==32) $where=['md5(id)'=>$slug['param3']];
			else $where=['id'=>$slug['param3']];
		}
		include_once __DIR__."/comps/editor.php";
		break;

	case 'access':
		$form="access";
		if($slug['param2']=="new") {
			$mode="new";
			$title="New Access Rule";
		} else {
			$mode="update";
			$title="Update Access Rule";
			if(strlen($slug['param3'])==32) $where=['md5(id)'=>$slug['param3']];
			else $where=['id'=>$slug['param3']];
		}
		include_once __DIR__."/comps/editor.php";
		break;
	
	case 'group':case 'groups':
		$form="group";
		if($slug['param2']=="new") {
			$mode="new";
			$title="New Group";
		} else {
			$mode="update";
			$title="Update Group";
			if(strlen($slug['param3'])==32) $where=['md5(id)'=>$slug['param3']];
			else $where=['id'=>$slug['param3']];
		}
		include_once __DIR__."/comps/editor.php";
		break;
    
   case 'guid':
		$form="guid";
		if($slug['param2']=="new") {
			$mode="new";
			$title="New Group";
		} else {
			$mode="update";
			$title="Update Group";
			if(strlen($slug['param3'])==32) $where=['md5(id)'=>$slug['param3']];
			else $where=['id'=>$slug['param3']];
		}
		include_once __DIR__."/comps/editor.php";
		break;
		
	case 'roles':
		$form="roles";
		if($slug['param2']=="new") {
			$mode="new";
			$title="New Role";
		} else {
			$mode="update";
			$title="Update Role";
			if(strlen($slug['param3'])==32) $where=['md5(id)'=>$slug['param3']];
			else $where=['id'=>$slug['param3']];
		}
		include_once __DIR__."/comps/editor.php";
		break;
		
	default:
		echo "<h2 align=center><br><br><br>User Element Not Supported Yet</h2>";
		echo "<script>parent.openSidePanel();</script>";
		break;
}
?>
