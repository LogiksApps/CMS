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
			$where=['md5(id)'=>$slug['param3']];
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
			$where=['md5(id)'=>$slug['param3']];
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
			$where=['md5(id)'=>$slug['param3']];
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
			$where=['md5(id)'=>$slug['param3']];
		}
		include_once __DIR__."/comps/editor.php";
		break;
		
	default:
		echo "<h2 align=center><br><br><br>User Element Not Supported Yet</h2>";
		break;
}
?>
