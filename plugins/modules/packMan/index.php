<?php
if(!defined('ROOT')) exit('No direct script access allowed');

loadModule("pages");

function pageSidebar() {
	return "<div id='componentTree' class='componentTree list-group list-group-root well'></div>";
}

function pageContentArea() {
	return "<div class='table-responsive' style='padding-right: 6px;'><table class='table table-striped table-hover table-condensed'>
	<thead><tr>
		<th width=50px>SL#</th>
		<th>Title</th>
		<th width=150px>Build</th>
		<th width=150px>Type</th>
		<th width=150px>Category</th>
		<th width=100px>Uninstallable</th>
		<th width=100px>Status</th>
		<th width=180px>Installed</th>
		<th width=150px>-</th>
	</tr></thead>
	<tbody id='packageTable'>
		<tr><td colspan=20><h2 align=center>Checking Installation ...</h2></td></tr>
	</tbody>
	</table></div>
	";
}

//Installed : Search, Configure (feature/localize), Editor (cms.php), Disable, Remove, Goto Market, Report Issue
//Repos : Search, Install, View (from market in iframe), Goto Market

echo _js(["packMan"]);
echo _css(["packMan"]);

printPageComponent(false,[
		"toolbar"=>[
			"searchPackages"=>["title"=>"Search Packages","type"=>"search","align"=>"right"],
	
			"loadInstalled"=>["title"=>"Installed","align"=>"right","class"=>"active"],
			"loadRepo"=>["title"=>"Repo","align"=>"right"],
			//"loadStore"=>["title"=>"eStore","align"=>"right"],
			"loadUploader"=>["title"=>"Upload","align"=>"right"],
		
			"listPackages"=>["icon"=>"<i class='fa fa-refresh'></i>"],
			['type'=>"bar"],
			//["createContent"=>["icon"=>"<i class='fa fa-plus'></i>","tips"=>"Create New"],
			//["openExternal"=>["icon"=>"<i class='fa fa-external-link'></i>","class"=>"onsidebarSelect"],
			//["preview"=>["icon"=>"<i class='fa fa-eye'></i>","class"=>"onsidebarSelect onOnlyOneSelect","tips"=>"Preview Content"],
			//
			//["rename"=>["icon"=>"<i class='fa fa-terminal'></i>","class"=>"onsidebarSelect onOnlyOneSelect","tips"=>"Rename Content"],
// 			"removePackage"=>["icon"=>"<i class='fa fa-trash'></i>","class"=>"onsidebarSelect"],
		],
		"sidebar"=>false,//"pageSidebar",
		"contentArea"=>"pageContentArea"
	]);
?>
<script id="packageRowTemplate" type="text/x-handlebars-template">
	{{#each data}}
	<tr class='{{#if has_error}}danger{{/if}}' packid='{{packid}}'>
		<th>{{@index}}</th>
		<td>{{name}}</td>
		<td>{{build}}</td>
		<td>{{type}}</td>
		<td>{{category}}</td>
		<td>{{is_installed}}</td>
		<td>{{status}}</td>
		<td>{{created_on}}</td>
		<td>
			{{#if is_installed}}<i class="fa fa-info-circle fa-2x cmdAction pull-left" cmd="infoPlugin" packid="{{packid}}" title="Plugin Info"></i>{{/if}}
			{{#if is_editable}}<i class="fa fa-pencil fa-2x cmdAction pull-left" cmd="editPlugin" packid="{{packid}}" title="Edit Plugin"></i>{{/if}}
			{{#if is_configurable}}<i class="fa fa-gear fa-2x cmdAction pull-left" cmd="configurePlugin" packid="{{packid}}" title="Configure Plugin"></i>{{/if}}
		</td>
	</tr>
	{{/each}}
</script>