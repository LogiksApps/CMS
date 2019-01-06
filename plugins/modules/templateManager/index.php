<?php
if(!defined('ROOT')) exit('No direct script access allowed');

loadModule("pages");

function pageSidebar() {
	// <form role='search'>
	//     <div class='form-group'>
	//       <input type='text' class='form-control' placeholder='Search'>
	//     </div>
	// </form>
	return "<div id='componentTree' class='componentTree list-group list-group-root well'></div>";
}
function pageContentArea() {
	return "<div id='componentSpace' class='componentSpace'><h2 align=center>Please load a template.</h2></div>
<script>
FORSITE='{$_REQUEST["forsite"]}';
</script>
	";
}

$webPath=dirname(getWebPath(__FILE__))."/";

loadModuleLib("cmsEditor","embed");

echo _css("templateManager");
echo _js(["cmsEditor","templateManager"]);

printPageComponent(false,[
		"toolbar"=>[
			"loadTextEditor"=>["title"=>"Template","align"=>"right"],
			"loadSQLEditor"=>["title"=>"Query","align"=>"right"],
			//"loadInfoComponent"=>["title"=>"About","align"=>"right"],
			//"loadPreviewComponent"=>["title"=>"Preview","align"=>"right"],

			// ["title"=>"Search Site","type"=>"search","align"=>"left"]
			"listTemplates"=>["icon"=>"<i class='fa fa-refresh'></i>"],
			"createTemplate"=>["icon"=>"<i class='fa fa-plus'></i>","tips"=>"Create New"],
			//"openExternal"=>["icon"=>"<i class='fa fa-external-link'></i>","class"=>"onsidebarSelect"],
			//"preview"=>["icon"=>"<i class='fa fa-eye'></i>","class"=>"onsidebarSelect onOnlyOneSelect","tips"=>"Preview Content"],
			['type'=>"bar"],
			//"rename"=>["icon"=>"<i class='fa fa-terminal'></i>","class"=>"onsidebarSelect onOnlyOneSelect","tips"=>"Rename Content"],
			"deleteTemplate"=>["icon"=>"<i class='fa fa-trash'></i>","class"=>"onsidebarSelect"],
		],
		"sidebar"=>"pageSidebar",
		"contentArea"=>"pageContentArea"
	]);

?>