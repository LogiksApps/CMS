<?php
if(!defined('ROOT')) exit('No direct script access allowed');

loadModule("pages");

function pageContentArea() {
	return "<h1 align=center>Checking installation ...</h1>";
}

//Installed : Search, Configure (feature/localize), Editor (cms.php), Disable, Remove, Goto Market, Report Issue
//Repos : Search, Install, View (from market in iframe), Goto Market

echo _js(["packMan"]);
echo _css(["packMan"]);

printPageComponent(false,[
		"toolbar"=>[
			"searchPackages"=>["title"=>"Search Packages","type"=>"search","align"=>"right"],
	
			"loadInstalled"=>["title"=>"Installed","align"=>"right","class"=>"active"],
			//"loadGlobal"=>["title"=>"Global","align"=>"right"],
		
			"listPackages"=>["icon"=>"<i class='fa fa-refresh'></i>"],
			['type'=>"bar"],
			//["createContent"=>["icon"=>"<i class='fa fa-plus'></i>","tips"=>"Create New"],
			//["openExternal"=>["icon"=>"<i class='fa fa-external-link'></i>","class"=>"onsidebarSelect"],
			//["preview"=>["icon"=>"<i class='fa fa-eye'></i>","class"=>"onsidebarSelect onOnlyOneSelect","tips"=>"Preview Content"],
			//
			//["rename"=>["icon"=>"<i class='fa fa-terminal'></i>","class"=>"onsidebarSelect onOnlyOneSelect","tips"=>"Rename Content"],
// 			"removePackage"=>["icon"=>"<i class='fa fa-trash'></i>","class"=>"onsidebarSelect"],
		],
		"sidebar"=>false,//"pageSidebar",
		"contentArea"=>"pageContentArea"
	]);
?>