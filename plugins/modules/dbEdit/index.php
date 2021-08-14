<?php
if(!defined('ROOT')) exit('No direct script access allowed');

if(Database::checkConnection()<=1) {
	if(Database::checkConnection("core")<=0) {
		print_error("Sorry, DB Connection required for Content Module");
		return;
	}
}

include_once __DIR__."/commons.php";

loadModule("pages");

$f=ROOT.CFG_FOLDER."/jsonConfig/db.json";
$dbJSON = file_get_contents($f);
$dbJSON = json_decode($dbJSON,true);

$dbList = array_keys($dbJSON['GLOBALS']);
if(isset($dbJSON[CMS_SITENAME])) {
	$dbList = array_merge($dbList, array_keys($dbJSON[CMS_SITENAME]));
}
$dbList = array_flip($dbList);
foreach($dbList as $a=>$b) {
	$dbList[$a] = ucwords($a);
}
if(isset($dbList['app'])) {
	$default = "app";
} else {
	$default = "core";
}

printPageComponent(false,[
		"toolbar"=>[
			"changeDatabase"=>["type"=>"dropdown","tips"=>"Change Database","options"=>$dbList, "title"=>ucwords($default)],
			"refresh"=>["icon"=>"<i class='fa fa-refresh'></i>"],
			
			["title"=>"Search Table ...","type"=>"search","align"=>"right","class"=>"onsidebarActive","tips"=>"Search individual column by -> column:term"],

			"dbInfo"=>["icon"=>"<i class='fa fa-info-circle'></i>","tips"=>"Database info","align"=>"right"],
			""=>["icon"=>"<i class='fa fa-table'></i>","tips"=>"Database tables","align"=>"right"],
			"tableQuery"=>["icon"=>"<i class='fa fa-code'></i>","tips"=>"Execute queries","align"=>"right"],

// 			"dbTools"=>["icon"=>"<i class='fa fa-database'></i>","tips"=>"Additional Tools","align"=>"right"],
			//routines, events, triggers, 
			//designer

			
			
			
			"createNew"=>["icon"=>"<i class='fa fa-plus'></i>","tips"=>"Create New Object"],//Tables,Views,Triggers,etc.
      		"exportData"=>["icon"=>"<i class='fa fa-upload'></i>","tips"=>"Export Data"],
     		"importData"=>["icon"=>"<i class='fa fa-download'></i>","tips"=>"Import Data"],
			['type'=>"bar"],
			"saveSchema"=>["icon"=>"<i class='fa fa-file-export'></i>","tips"=>"Save Schema to SQL Folder usefull for migration scripts"],
			"migrateSchema"=>["icon"=>"<i class='fa fa-not-equal'></i>","tips"=>"Compare and import schema changes and data from schema"],
			
			['type'=>"bar"],
			"cloneTable"=>["icon"=>"<i class='fa fa-copy'></i>","class"=>"onsidebarSelect onOnlyOneSelect","tips"=>"Clone Table"],
			"trash"=>["icon"=>"<i class='fa fa-trash'></i>","class"=>"onsidebarSelect"],
		],
		"sidebar"=>"pageSidebar",
		"contentArea"=>"pageContentArea"
	]);

echo _css(["dbEdit", "font-awesome5"]);
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
<script>
var dkey = "<?=$default?>";
function cloneTable() {
    
}
function saveSchema() {
    processAJAXQuery(_service("dbEdit","dumpSchema")+"&dkey="+dkey,function(txt) {
		lgksAlert(txt);
	});
}
function migrateSchema() {
    parent.openLinkFrame("Migrator", _link("modules/migrator"), true);
}
</script>