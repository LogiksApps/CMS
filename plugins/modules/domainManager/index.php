<?php
if(!defined('ROOT')) exit('No direct script access allowed');

loadModule("pages");

printPageComponent(false,[
		"toolbar"=>[
			"refresh"=>["icon"=>"<i class='fa fa-refresh'></i>"],
			"createNew"=>["icon"=>"<i class='fa fa-plus'></i>","tips"=>"Create New Domain App Map"],
			['type'=>"bar"],
			"trash"=>["icon"=>"<i class='fa fa-trash'></i>"],



			"save"=>["title"=>"Save","align"=>"right","icon"=>"<i class='fa fa-save'></i>"],
		],
		//"sidebar"=>"pageSidebar",
		"contentArea"=>"pageContentArea"
	]);

echo _css("domainManager");
echo _js("domainManager");


function pageSidebar() {
	return "<div id='componentTree' class='componentTree list-group list-group-root well'></div>";
}
function pageContentArea() {
	return "
		<div id='domainTable' class='domainTable'></div>
	";
}
?>
