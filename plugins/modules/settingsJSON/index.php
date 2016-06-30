<?php
if(!defined('ROOT')) exit('No direct script access allowed');

loadModule("pages");

printPageComponent(false,[
		"toolbar"=>[
			"db"=>["title"=>"DB","align"=>"right","class"=>"active"],
			"fs"=>["title"=>"FS","align"=>"right"],
			//"log"=>["title"=>"LOG","align"=>"right"],
			"msg"=>["title"=>"MSG","align"=>"right"],
			"cache"=>["title"=>"CACHE","align"=>"right"],

			"refresh"=>["icon"=>"<i class='fa fa-refresh'></i>"],
			['type'=>"bar"],
			// "newCard"=>["icon"=>"<i class='fa fa-plus'></i>"],
			// "trash"=>["icon"=>"<i class='fa fa-trash'></i>"],
		],
		"contentArea"=>"pageContentArea"
	]);

echo _css(["cards","settingsJSON"]);
echo _js("settingsJSON");

function pageContentArea() {
	return "";
}
?>
