<?php
if(!defined('ROOT')) exit('No direct script access allowed');

include __DIR__."/api.php";

loadModule("pages");

$loggers=getLoggerList();
$loggers=array_flip($loggers);
foreach ($loggers as $key => $value) {
	$loggers[$key]=toTitle(_ling($key));
}

printPageComponent(false,[
		"toolbar"=>[
			"loggers"=>["title"=>"Loggers","align"=>"right","type"=>"dropdown","options"=>$loggers],
			// "pages"=>["title"=>"Pages","align"=>"right"],
			// "comps"=>["title"=>"Components","align"=>"right"],
			// "layouts"=>["title"=>"Layouts","align"=>"right"],
			// ['type'=>"bar"],

			// ["title"=>"Search Site","type"=>"search","align"=>"left"]
			"refresh"=>["icon"=>"<i class='fa fa-refresh'></i>"],
			['type'=>"bar"],
			"download"=>["icon"=>"<i class='fa fa-download'></i>","class"=>"onsidebarSelect onOnlyOneSelect"],
			"trash"=>["icon"=>"<i class='fa fa-trash'></i>","class"=>"onsidebarSelect"],
		],
		"sidebar"=>"pageSidebar",
		"contentArea"=>"pageContentArea"
	]);

echo _css("logBook");
echo _js("logBook");

function pageSidebar() {
	return "
	<div id='componentTree' class='componentTree list-group list-group-root well'>
	</div>
	";
}
function pageContentArea() {
	return "
		<table class='table table-hover table-striped table-condensed' width=100%>
		<caption></caption>
		<thead>
			<tr>
				<th width=150px>Date/Time</th>
				<th width=100px>Type/Level</th>
				<th>Message</th>
				<th width=100px></th>
			</tr>
		</thead>
		<tbody id='logDataTable'></tbody>
		</table>
	";
}
?>
