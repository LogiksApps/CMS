<?php
if(!defined('ROOT')) exit('No direct script access allowed');

loadModule("pages");

function pageContentArea() {
	$html=file_get_contents(__DIR__."/layout.html");
  
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
			"resetServiceList"=>["icon"=>"<i class='fa fa-refresh'></i>"],
			['type'=>"bar"],
			"findServices"=>["icon"=>"<i class='fa fa-search-plus'></i>","tips"=>"Find and add new services"],
			['type'=>"bar"],
            "createNewService"=>["icon"=>"<i class='fa fa-plus'></i>","tips"=>"Create new service"],
			"saveServiceConfig"=>["icon"=>"<i class='fa fa-save'></i>","tips"=>"Save service config"],
			//"openExternal"=>["icon"=>"<i class='fa fa-external-link'></i>","class"=>"onsidebarSelect"],
			//"preview"=>["icon"=>"<i class='fa fa-eye'></i>","class"=>"onsidebarSelect onOnlyOneSelect","tips"=>"Preview Content"],
			//"rename"=>["icon"=>"<i class='fa fa-terminal'></i>","class"=>"onsidebarSelect onOnlyOneSelect","tips"=>"Rename Content"],
			//"deleteContent"=>["icon"=>"<i class='fa fa-trash'></i>","class"=>"onsidebarSelect"],
		],
		"sidebar"=>false,
		"contentArea"=>"pageContentArea"
	]);
?>
<script>

</script>