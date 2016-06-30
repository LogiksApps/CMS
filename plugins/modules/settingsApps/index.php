<?php
if(!defined('ROOT')) exit('No direct script access allowed');

loadModule("pages");

printPageComponent(false,[
		"toolbar"=>[
			 //"save"=>["title"=>"Save","align"=>"right","icon"=>"<i class='fa fa-save'></i>","class"=>"onsidebarSelect onsidebarActive"],
			// "comps"=>["title"=>"Components","align"=>"right"],
			// "layouts"=>["title"=>"Layouts","align"=>"right"],
			// ['type'=>"bar"],

			// ["title"=>"Search Site","type"=>"search","align"=>"left"]
			"refresh"=>["icon"=>"<i class='fa fa-refresh'></i>"],
			['type'=>"bar"],
			"purge"=>["icon"=>"<i class='fa fa-recycle'></i>"],
		],
		"sidebar"=>"pageSidebar",
		"contentArea"=>"pageContentArea"
	]);

echo _css("settingsApps");
echo _js("settingsApps");


function pageSidebar() {
	return "<div id='componentTree' class='componentTree list-group list-group-root well'></div>";
}
function pageContentArea() {
	return "";
}
?>
