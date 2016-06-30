<?php
if(!defined('ROOT')) exit('No direct script access allowed');

include_once __DIR__."/commons.php";

loadModule("pages");

printPageComponent(false,[
		"toolbar"=>[
			["title"=>"Search Table ...","type"=>"search","align"=>"right","class"=>"onsidebarActive","tips"=>"Search individual column by -> column:term"],

			"dbInfo"=>["icon"=>"<i class='fa fa-info-circle'></i>","tips"=>"Database info","align"=>"right"],
			""=>["icon"=>"<i class='fa fa-table'></i>","tips"=>"Database tables","align"=>"right"],
			"tableQuery"=>["icon"=>"<i class='fa fa-code'></i>","tips"=>"Execute queries","align"=>"right"],
			// "dbTools"=>["icon"=>"<i class='fa fa-database'></i>","tips"=>"Additional Tools","align"=>"right"],

			//views, routines, events, triggers, 
			//designer

			
			"refresh"=>["icon"=>"<i class='fa fa-refresh'></i>"],
			// "createNew"=>["icon"=>"<i class='fa fa-plus'></i>","tips"=>"Create New Database Object"],//Tables,Views,Triggers,etc.
			// "clone"=>["icon"=>"<i class='fa fa-copy'></i>","class"=>"onsidebarSelect onOnlyOneSelect","tips"=>"Clone Me"],
			['type'=>"bar"],
			"trash"=>["icon"=>"<i class='fa fa-trash'></i>","class"=>"onsidebarSelect"],
		],
		"sidebar"=>"pageSidebar",
		"contentArea"=>"pageContentArea"
	]);

echo _css("dbEdit");
echo _js(["jquery.validate","dbEdit"]);


function pageSidebar() {
	// <form role='search'>
	//     <div class='form-group'>
	//       <input type='text' class='form-control' placeholder='Search'>
	//     </div>
	// </form>
	return "
	<div id='componentTree' class='componentTree list-group list-group-root well'>
 
	</div>
	";
}
function pageContentArea() {
	return "
<h2 align=center>Please load something to view its information.</h2>
	";
}
?>
