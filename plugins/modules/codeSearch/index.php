<?php
if(!defined('ROOT')) exit('No direct script access allowed');
loadModule("pages");

function pageContentArea() {
    //return "<div class='ajaxloading ajaxloading5'>Searching Code Index ...</div>";
    return file_get_contents(__DIR__."/comps/searcharea.html");
}
function pageSidebar() {
    return "<div id='sidebarArea'>B</div>";
}

if(!isset($_REQUEST['query'])) {
    $_REQUEST['query']="";
}

echo _css(["codeSearch"]);
printPageComponent(false,[
// 		"toolbar"=>[
// 		    "reloadSearch"=>["icon"=>"<i class='fa fa-refresh'></i>"],
		    
// 			"loadSearchLocal"=>["title"=>"AppCode","align"=>"right","class"=>"active"],
// 			"loadSearchGithub"=>["title"=>"Github","align"=>"right"],
// 			//"loadSearchGlobal"=>["title"=>"Github","align"=>"right"],
// 			//["title"=>"Search Code","type"=>"search","align"=>"left"],
			
			
// // 			"generateRoles"=>["icon"=>"<i class='fa fa-gears'></i>","tips"=>"Generate New Roles"],
// 			//"createTemplate"=>["icon"=>"<i class='fa fa-plus'></i>","tips"=>"Create New"],
// 			//"openExternal"=>["icon"=>"<i class='fa fa-external-link'></i>","class"=>"onsidebarSelect"],
// 			//"preview"=>["icon"=>"<i class='fa fa-eye'></i>","class"=>"onsidebarSelect onOnlyOneSelect","tips"=>"Preview Content"],
// 			//['type'=>"bar"],
// 			//"rename"=>["icon"=>"<i class='fa fa-terminal'></i>","class"=>"onsidebarSelect onOnlyOneSelect","tips"=>"Rename Content"],
// // 			"deleteTemplate"=>["icon"=>"<i class='fa fa-trash'></i>","class"=>"onsidebarSelect"],
// 		],
        "toolbar"=>false,
		"sidebar"=>false,
		"contentArea"=>"pageContentArea"
	]);
echo _js(["codeSearch"]);
?>
<script>
const firstSearchTerm="<?=$_REQUEST['query']?>";
</script>