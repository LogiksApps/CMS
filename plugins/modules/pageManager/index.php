<?php
if(!defined('ROOT')) exit('No direct script access allowed');

loadModule("pages");

printPageComponent(false,[
		"toolbar"=>[
			"pages"=>["title"=>"Pages","align"=>"right"],
			"comps"=>["title"=>"Components","align"=>"right"],
			"layouts"=>["title"=>"Layouts","align"=>"right"],
			// ['type'=>"bar"],

			// ["title"=>"Search Site","type"=>"search","align"=>"left"]
			"refresh"=>["icon"=>"<i class='fa fa-refresh'></i>"],
			"display"=>["icon"=>"<i class='glyphicon glyphicon-th-large'></i>"],
			"createNew"=>["icon"=>"<i class='fa fa-plus'></i>","tips"=>"Create New"],
			//"openExternal"=>["icon"=>"<i class='fa fa-external-link'></i>","class"=>"onsidebarSelect"],
			//"rename"=>["icon"=>"<i class='fa fa-terminal'></i>","class"=>"onsidebarSelect onOnlyOneSelect","tips"=>"Rename Page"],
			//"clone"=>["icon"=>"<i class='fa fa-copy'></i>","class"=>"onsidebarSelect onOnlyOneSelect","tips"=>"Clone Page"],
			['type'=>"bar"],
			"trash"=>["icon"=>"<i class='fa fa-trash'></i>","class"=>"onsidebarSelect"],
		],
		//"sidebar"=>"pageSidebar",
		"contentArea"=>"pageContentArea"
	]);

echo _css("pageManager");
echo _js("pageManager");


function pageSidebar() {
	// <form role='search'>
	//     <div class='form-group'>
	//       <input type='text' class='form-control' placeholder='Search'>
	//     </div>
	// </form>
	return "<div id='componentTree' class='componentTree list-group list-group-root well'></div>";
}
function pageContentArea() {
	return "<div id='componentSpace' class='componentSpace'>
		<div class='col-md-3' data-name='new'></div>
		<div class='col-md-3' data-name='dev'></div>
		<div class='col-md-3' data-name='design'></div>
		<div class='col-md-3' data-name='done'></div>
	</div>
<script>
FORSITE='{$_REQUEST["forsite"]}';
</script>

	";
}
?>
