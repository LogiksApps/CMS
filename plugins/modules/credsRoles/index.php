<?php
if(!defined('ROOT')) exit('No direct script access allowed');

loadModule("pages");

function pageSidebar() {
  $html="<div id='componentTree' class='componentTree list-group list-group-root well'>";
  $html.="<ul class='list-group'>";
  $html.="<div class='ajaxloading ajaxloading5'></div>";
  $html.="</ul>";
  $html.="</div>";
	return $html;
}
function pageContentArea() {
  return "<div id='componentSpace' class='componentSpace' style='padding-right: 10px;padding-left: 5px;'><div class='col-xs-12'><div class='row'>
		<div id='roleModel' class='panel-group roleTabModel' role='tablist' aria-multiselectable='false'>
			<div class='panel-heading panel-heading-bold'>Please Click On Left to Start</div>
		</div>
	</div></div></div>
	
	<script id='rolebox-template' type='text/x-handlebars-template'>
		<div class='panel panel-default' data-module='{{modulehash}}'>
			<div class='panel-heading' role='tab' id='{{modulehash}}'>
				<h4 class='panel-title'>
					<a class='accordion-toggle' role='button' data-toggle='collapse' data-parent='#roleModel' href='#collapse{{modulehash}}' aria-expanded='true' aria-controls='collapse{{modulehash}}'>{{module_title}}</a>
				</h4>
			</div>
			<div id='collapse{{modulehash}}' class='panel-collapse collapse' role='tabpanel' aria-labelledby='{{modulehash}}'>
				<div class='panel-body'>
					<!--<ul class='list-group'></ul>-->
				</div>
			</div>
		</div>
	</script>
	
	<script id='roleitem-template' type='text/x-handlebars-template'>
		<li class='list-group-item' guid='{{guid}}' data-module='{{module}}' data-category='{{category}}' data-activity='{{activity}}'  >
			<label>
				{{role_title}}
				<citie class='datalink' data-type='guid-users' data-value='{{guid}}'>[{{guid}}]</citie>
				{{#if allow}}
					<input class='pull-right' type='checkbox' name='roleCheckbox' data-hash='{{rolehash}}' checked />
				{{else}}
					<input class='pull-right' type='checkbox' name='roleCheckbox' data-hash='{{rolehash}}' />
				{{/if}}
			</label>
		</li>
	</script>
	";
}

$webPath=dirname(getWebPath(__FILE__))."/";

echo _css("credsRoles");

printPageComponent(false,[
		"toolbar"=>[
			//"loadTextEditor"=>["title"=>"Template","align"=>"right"],
			//"loadSQLEditor"=>["title"=>"Query","align"=>"right"],
			//"loadInfoComponent"=>["title"=>"About","align"=>"right"],
			//"loadPreviewComponent"=>["title"=>"Preview","align"=>"right"],

			["title"=>"Search Roles","type"=>"search","align"=>"right"],
			
			"reloadRoles"=>["icon"=>"<i class='fa fa-refresh'></i>"],
			"generateRoles"=>["icon"=>"<i class='fa fa-gears'></i>","tips"=>"Generate New Roles"],
			//"createTemplate"=>["icon"=>"<i class='fa fa-plus'></i>","tips"=>"Create New"],
			//"openExternal"=>["icon"=>"<i class='fa fa-external-link'></i>","class"=>"onsidebarSelect"],
			//"preview"=>["icon"=>"<i class='fa fa-eye'></i>","class"=>"onsidebarSelect onOnlyOneSelect","tips"=>"Preview Content"],
			//['type'=>"bar"],
			//"rename"=>["icon"=>"<i class='fa fa-terminal'></i>","class"=>"onsidebarSelect onOnlyOneSelect","tips"=>"Rename Content"],
// 			"deleteTemplate"=>["icon"=>"<i class='fa fa-trash'></i>","class"=>"onsidebarSelect"],
		],
		"sidebar"=>"pageSidebar",
		"contentArea"=>"pageContentArea"
	]);
echo "<script>FORSITE='{$_REQUEST["forsite"]}';</script>";
echo _js(["credsRoles"]);
?>