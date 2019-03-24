<?php
$slug=_slug("module/src/type");

if(!isset($slug['src'])) {
	print_error("Sorry, Setting Source not defined.");
	exit();
}

include __DIR__."/api.php";

if(!in_array($slug['src'],$supportedCFGTypes)) {
	print_error("Sorry, Setting Source not supported.");
	exit();
}

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

echo _css("settings");
echo "<script>cfgSrcType='{$slug['src']}';cfgSrcTypeValue='{$slug['type']}';</script>";
echo _js("settings");


function pageSidebar() {
	return "<div id='componentTree' class='componentTree list-group list-group-root well'></div>";
}
function pageContentArea() {
	return "";
}
?>