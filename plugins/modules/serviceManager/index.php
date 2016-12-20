<?php
if(!defined('ROOT')) exit('No direct script access allowed');

loadModule("pages");

function pageContentArea() {
	$html="<div><table class='table table-hover table-bordered table-condensed'>";
	$html.="<thead><tr>";
	$html.="<th>Service:</th>";
	$html.="<th>Src</th>";
	$html.="<th>Format</th>";
	$html.="<th>Debug</th>";
	$html.="<th>Cache</th>";
	$html.="<th>Autoformat</th>";
	$html.="<th>Access Logic</th>";
	$html.="<th align=center style='width:50px;'>#</th>";
	$html.="</tr></thead>";
	$html.="<tbody id='serviceList'>";
	$html.="</tbody>";
	$html.="</table></div>";
	
	return $html;
}
function pageSidebar() {
	return "";
}

echo _css(["serviceManager"]);
echo _js(["handlebars","serviceManager"]);

printPageComponent(false,[
		"toolbar"=>[
			"loadLocal"=>["title"=>"App","align"=>"right","class"=>"active"],
			"loadGlobal"=>["title"=>"Global","align"=>"right"],

			// ["title"=>"Search Site","type"=>"search","align"=>"left"]
			"listServices"=>["icon"=>"<i class='fa fa-refresh'></i>"],
			['type'=>"bar"],
			"findServices"=>["icon"=>"<i class='fa fa-search-plus'></i>","tips"=>"Find and add new services"],
			['type'=>"bar"],
			"saveServiceConfig"=>["icon"=>"<i class='fa fa-save'></i>","tips"=>"Save service config."],
			//"openExternal"=>["icon"=>"<i class='fa fa-external-link'></i>","class"=>"onsidebarSelect"],
			//"preview"=>["icon"=>"<i class='fa fa-eye'></i>","class"=>"onsidebarSelect onOnlyOneSelect","tips"=>"Preview Content"],
			//
			//"rename"=>["icon"=>"<i class='fa fa-terminal'></i>","class"=>"onsidebarSelect onOnlyOneSelect","tips"=>"Rename Content"],
			//"deleteContent"=>["icon"=>"<i class='fa fa-trash'></i>","class"=>"onsidebarSelect"],
		],
		"sidebar"=>false,
		"contentArea"=>"pageContentArea"
	]);
?>