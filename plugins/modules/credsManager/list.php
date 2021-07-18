<?php
if(!defined('ROOT')) exit('No direct script access allowed');

loadModule("pages");
loadModuleLib("reports","api");

if(!isset($_REQUEST['panel'])) $_REQUEST['panel']="users";

define("ADMIN_PRIVILEGE_ID",3);

$pageOptions=[
		"toolbar"=>[
			"users"=>["title"=>"Users","align"=>"right","class"=>($_REQUEST['panel']=="users")?"active":""],
			"privileges"=>["title"=>"Privileges","align"=>"right","class"=>($_REQUEST['panel']=="privileges")?"active":""],
			"roles"=>["title"=>"Roles","align"=>"right","class"=>($_REQUEST['panel']=="roles")?"active":""],
			"access"=>["title"=>"Access","align"=>"right","class"=>($_REQUEST['panel']=="access")?"active":""],
			"groups"=>["title"=>"Groups","align"=>"right","class"=>($_REQUEST['panel']=="groups")?"active":""],
            "guid"=>["title"=>"Orgs","align"=>"right","class"=>($_REQUEST['panel']=="guid")?"active":""],
			// ['type'=>"bar"],

			// ["title"=>"Search Site","type"=>"search","align"=>"left"]
			 "reload"=>["icon"=>"<i class='fa fa-refresh'></i>"],
			 "createNew"=>["icon"=>"<i class='fa fa-plus'></i>","tips"=>"Create New"],
			['type'=>"bar"],
			"trash"=>["icon"=>"<i class='fa fa-trash'></i>"],//,"class"=>"onRowSelect"
		],
		"contentArea"=>"pageContentArea"
	];

if($_SESSION["SESS_PRIVILEGE_ID"]<=ADMIN_PRIVILEGE_ID) {//ADMIN_USERIDS
	
} else {
    unset($pageOptions['toolbar']['guid']);
    unset($pageOptions['toolbar']['access']);
}

printPageComponent(false,$pageOptions);

echo _css("credsManager");
echo _js("credsManager");

function pageContentArea() {
	if($_SESSION["SESS_PRIVILEGE_ID"]<=ADMIN_PRIVILEGE_ID) {
		$rpt=__DIR__."/panels/{$_REQUEST['panel']}_root.json";
	} else {
		$rpt=__DIR__."/panels/{$_REQUEST['panel']}.json";
	}

	if(!file_exists($rpt)) {
		return "<h3 align=center>Sorry, no access enabled or requested panel not found for you.</h3>";
	}
	ob_start();
	echo "<div class='col-xs-12'>";// report-notoolbar
	echo "<div class='row'>";
	echo _css("reports");
	echo "<div class='reportholder' style='width:100%;height:100%;'>";
	$a=printReport($rpt,"core");
	if(!$a) {
		echo "<h3 align=center>Panel Source Corrupted</h3>";
	}
	echo "</div>";
	echo _js(["FileSaver","html2canvas","reports"]);
	echo "</div>";
	echo "<div id='sliderPanel' class='sliderPanel'><iframe id='credsEditor' width=100% height=100% style='width:100%;height:100%;' frameborder=0 ></iframe></div>";
	echo "</div>";
	$html=ob_get_contents();
	ob_end_clean();
	
	return $html;
}
?>
