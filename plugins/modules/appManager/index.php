<?php
if(!defined('ROOT')) exit('No direct script access allowed');

loadModule("pages");

function pageContentArea() {
	
	
	return "<h1 align=center>Not installed correctly</h1>";
}
function pageSidebar() {
	return "";
}


echo _js(["appManager"]);

printPageComponent(false,[
		"toolbar"=>[
			"searchApps"=>["title"=>"Search Apps","type"=>"search","align"=>"right"],
	
			"loadLocalApps"=>["title"=>"Installed","align"=>"right","class"=>"active"],
			"loadMarket"=>["title"=>"New Apps","align"=>"right"],
		
			"listApps"=>["icon"=>"<i class='fa fa-refresh'></i>"],
			['type'=>"bar"],
			//["createContent"=>["icon"=>"<i class='fa fa-plus'></i>","tips"=>"Create New"],
			//["openExternal"=>["icon"=>"<i class='fa fa-external-link'></i>","class"=>"onsidebarSelect"],
			//["preview"=>["icon"=>"<i class='fa fa-eye'></i>","class"=>"onsidebarSelect onOnlyOneSelect","tips"=>"Preview Content"],
			//
			//["rename"=>["icon"=>"<i class='fa fa-terminal'></i>","class"=>"onsidebarSelect onOnlyOneSelect","tips"=>"Rename Content"],
			"removeApps"=>["icon"=>"<i class='fa fa-trash'></i>","class"=>"onsidebarSelect"],
		],
		"sidebar"=>false,//"pageSidebar",
		"contentArea"=>"pageContentArea"
	]);
?>