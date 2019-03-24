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
					<a class='accordion-toggle' role='button' data-toggle='collapse' data-parent='#roleModel' href='#collapse{{modulehash}}' aria-expanded='true' aria-controls='collapse{{modulehash}}'>{{group_title}}</a>
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
	
	<script id='modulebox-template' type='text/x-handlebars-template'>
		<div class='panel panel-default' data-module='{{modulehash}}'>
			<div class='panel-heading' role='tab' id='{{modulehash}}'>
				<h4 class='panel-title'>
					<a class='accordion-toggle' role='button' data-toggle='collapse' data-parent='#roleModel' href='#collapse{{modulehash}}' aria-expanded='true' aria-controls='collapse{{modulehash}}'>{{group_title}}</a>
				</h4>
			</div>
			<div id='collapse{{modulehash}}' class='panel-collapse collapse' role='tabpanel' aria-labelledby='{{modulehash}}'>
				<div class='panel-body'>
					<!--<ul class='list-group'></ul>-->
				</div>
			</div>
		</div>
	</script>
	
	<script id='moduleitem-template' type='text/x-handlebars-template'>
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

if(!isset($_COOKIE['CREDROLES_UI'])) $uiType="by_roles";
else $uiType=$_COOKIE['CREDROLES_UI'];

echo _css("credsRoles");

printPageComponent(false,[
		"toolbar"=>[
      "loadNewUI"=>["title"=>"New UI","align"=>"right"],

			"searchRoles"=>["title"=>"Change View","type"=>"dropdown","align"=>"right","options"=>["by_roles"=>"By Roles","by_plugins"=>"By Plugins"]],
			//"searchRoles"=>["title"=>"Search Roles","type"=>"search","align"=>"right"],
			
			"reloadSidebar"=>["icon"=>"<i class='fa fa-refresh'></i>"],
			"generateRoles"=>["icon"=>"<i class='fa fa-gears'></i>","tips"=>"Generate New Roles"],
			//['type'=>"bar"],
			//"rename"=>["icon"=>"<i class='fa fa-terminal'></i>","class"=>"onsidebarSelect onOnlyOneSelect","tips"=>"Rename Content"],
// 			"deleteRoles"=>["icon"=>"<i class='fa fa-trash'></i>"],
		],
		"sidebar"=>"pageSidebar",
		"contentArea"=>"pageContentArea"
	]);

echo "<script>FORSITE='".CMS_SITENAME."';uiType='".$uiType."';</script>";
echo _js(["credsRoles"]);
?>
<script>
$(function() {
    $("#pgToolbarSearch").on('submit', function (e) {
        e.preventDefault();
        return false;
    });
  $("#pgToolbarSearch input").keyup(function(e) {
    if(e.keyCode==13) return false;
    vs=$("#pgToolbarSearch input").val();
		if(vs==null || vs.length<=0) {
			$(".tab-pane.active .panel").show();
			return;
		}
		$(".tab-pane.active .panel:not([data-module^='"+vs+"'])").hide();
		$(".tab-pane.active .panel[data-module^='"+vs+"']").show();
	});
  
	$("#toolbtn_searchRoles a").click(function() {
			$.cookie("CREDROLES_UI",$(this).data("drop"));
			uiType=$(this).data("drop");
			reloadSidebar();
		});
});
function loadNewUI() {
  $.cookie("CREDROLES_UI","ui0");
  window.location = _link("modules/credsRoles")+"&ui=ui0";
}
</script>