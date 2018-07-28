<?php
if(!defined('ROOT')) exit('No direct script access allowed');
loadModule("pages");

function pageContentArea() {
    return "<div class='ajaxloading ajaxloading5'>Searching Code Index ...</div>";
}
function pageSidebar() {
    return "<div id='sidebarArea'>B</div>";
}

if(!isset($_REQUEST['query'])) {
    $_REQUEST['query']="";
}

_css(["codeSearch"]);
printPageComponent(false,[
		"toolbar"=>[
		    "reloadRoles"=>["icon"=>"<i class='fa fa-refresh'></i>"],
		    
			//"loadTextEditor"=>["title"=>"Template","align"=>"right"],
			//"loadInfoComponent"=>["title"=>"About","align"=>"right"],
			["title"=>"Search Code","type"=>"search","align"=>"left"],
			
			
// 			"generateRoles"=>["icon"=>"<i class='fa fa-gears'></i>","tips"=>"Generate New Roles"],
			//"createTemplate"=>["icon"=>"<i class='fa fa-plus'></i>","tips"=>"Create New"],
			//"openExternal"=>["icon"=>"<i class='fa fa-external-link'></i>","class"=>"onsidebarSelect"],
			//"preview"=>["icon"=>"<i class='fa fa-eye'></i>","class"=>"onsidebarSelect onOnlyOneSelect","tips"=>"Preview Content"],
			//['type'=>"bar"],
			//"rename"=>["icon"=>"<i class='fa fa-terminal'></i>","class"=>"onsidebarSelect onOnlyOneSelect","tips"=>"Rename Content"],
// 			"deleteTemplate"=>["icon"=>"<i class='fa fa-trash'></i>","class"=>"onsidebarSelect"],
		],
		"sidebar"=>false,
		"contentArea"=>"pageContentArea"
	]);
_js(["codeSearch"]);
?>
<script>
$(function() {
    $("#pgToolbarSearch input").val("<?=$_REQUEST['query']?>");
    searchCode($("#pgToolbarSearch input").val());
});
function searchCode(term) {
    console.log(term);
}
</script>