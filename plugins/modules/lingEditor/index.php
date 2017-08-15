<?php
if(!defined('ROOT')) exit('No direct script access allowed');

loadModule("pages");

printPageComponent(false,[
		"toolbar"=>[
			"createNew"=>["icon"=>"<i class='fa fa-plus'></i>","tips"=>"Create a new Language"],
			['type'=>"bar"],
			"saveFile"=>["icon"=>"<i class='fa fa-save'></i>","tips"=>"Save current file"],
			"resetFile"=>["icon"=>"<i class='fa fa-retweet'></i>","tips"=>"Reset current file"],

			//"save"=>["title"=>"Save","align"=>"right","icon"=>"<i class='fa fa-save'></i>"],
        "langDropdown"=>["title"=>"Language Selector","align"=>"right","type"=>"dropdown","options"=>[]],
		],
// 		"sidebar"=>"pageSidebar",
		"contentArea"=>"pageContentArea"
	]);

echo _css("lingEditor");
echo _js("lingEditor");

function pageSidebar() {
	return "<div id='componentTree' class='componentTree list-group list-group-root well'></div>";
}
function pageContentArea() {
	return "
		<div id='lingTable' class='lingTable table-responsive'><table class='table table-bordered'><thead><tr><th width=50%>String <i class='fa fa-plus pull-right' title='Add new string. You can also add new row by pressing enter in value' onclick='addNewString()'></i></th><th><span id='lingFileName'>Ling Value</span></th><td width=50px></td></tr></thead><tbody></tbody></table></div>
	";
}
?>
