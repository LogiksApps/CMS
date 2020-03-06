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
		<th width=170px>Package</th>
		<th width=150px>Build</th>
		<th width=150px>Type</th>
		<th width=150px>Category</th>
		<th width=100px>Status</th>
		<th width=180px>Installed</th>
		<th width=250px>-</th>
	</tr></thead>
	<tbody id='packageTable'>
		<tr><td colspan=20><h2 align=center>Checking Installation ...</h2></td></tr>
	</tbody>
	</table></div>";
}

//Installed : Search, Configure (feature/localize), Editor (cms.php), Disable, Remove, Goto Market, Report Issue
//Repos : Search, Install, View (from market in iframe), Goto Market

echo _js(["pluginManager"]);
echo _css(["pluginManager"]);

printPageComponent(false,[
		"toolbar"=>[
			"searchPackages"=>["title"=>"Search Packages","type"=>"search","align"=>"right"],
	
			"loadInstalled"=>["title"=>"Installed","align"=>"right","class"=>"active"],
// 			"loadGlobal"=>["title"=>"Global","align"=>"right"],
// 			"loadDev"=>["title"=>"Dev","align"=>"right"],
			
			"loadRepo"=>["title"=>"eStore","align"=>"right"],
			"loadUploader"=>["title"=>"Upload","align"=>"right"],
// 			"loadUploader"=>["title"=>"Upload","align"=>"right"],
		
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
<style>
#packageTable .fa {
    font-size: 20px;
}
</style>
<script id="packageRowTemplate" type="text/x-handlebars-template">
	{{#each data}}
	<tr class='{{#if has_error}}danger{{/if}} {{category}} {{type}} {{status}}' packid='{{packid}}'>
		<th>{{@index}}</th>
		<td class='name'>{{name}}</td>
		<td class='packageid'>{{packageid}}</td>
		<td class='vers'>{{vers}}</td>
		<td class='type'>{{type}}</td>
		<td class='category'>{{category}}</td>
		<td class='status'>{{status}}</td>
		<td class='created_on'>{{created_on}}</td>
		<td class='actions'>
			{{#if has_info}}<i class="fa fa-info-circle cmdAction pull-left" cmd="infoPlugin" packid="{{packid}}" packageid="{{package}}" title="Plugin Info"></i>{{/if}}
			
			{{#if is_archivable}}<i class="fa fa-archive cmdAction pull-right" cmd="archivePlugin" packid="{{packid}}" packageid="{{package}}" title="Archive Plugin"></i>{{/if}}
			{{#if is_archived}}<i class="fa fa-undo cmdAction pull-right" cmd="archivePlugin" packid="{{packid}}" packageid="{{package}}" title="Restore Plugin"></i>{{/if}}
			
			{{#if is_editable}}<i class="fa fa-pencil cmdAction pull-right" cmd="editPlugin" packid="{{packid}}" packageid="{{package}}" title="Edit Plugin"></i>{{/if}}
			{{#if is_configurable}}<i class="fa fa-gear cmdAction pull-right" cmd="configurePlugin" packid="{{packid}}" packageid="{{package}}" title="Configure Plugin"></i>{{/if}}
			
			
			{{#if bugs}}<a href='{{bugs}}' target=_blank><i class="fa fa-bug pull-left" packid="{{packid}}" title="Plugin Issues"></i></a>{{/if}}
			{{#if homepage}}<a href='{{homepage}}' target=_blank><i class="fa fa-link pull-left" packid="{{packid}}" title="Plugin Issues"></i></a>{{/if}}
			{{#if docs}}<a href='{{docs}}' target=_blank><i class="fa fa-book pull-left" packid="{{packid}}" title="Plugin Issues"></i></a>{{/if}}
		</td>
	</tr>
	{{/each}}
</script>
<script>
$(function() {
    currentType="installed";
    
	$("#pgtoolbar .nav.navbar-left").append("<li style='margin-top: 3px;width: 170px;'><select class='form-control' id='typeDropdown'></select></li>");
	$("#pgtoolbar .nav.navbar-left").append("<li style='margin-top: 3px;width: 170px;margin-left:10px;'><select class='form-control' id='filterDropdown'></select></li>");
	
	//<option value='widgets'>Widgets</option>
	$("#typeDropdown").append("<option value='modules'>Modules</option><option value='vendors'>Vendors</option><option value='packages'>Packages</option>");
	$("#typeDropdown").change(listPackages);
	
	$("#filterDropdown").append("<option value=''>No Filter</option>"+
	                    "<option value='error'>With Error</option>"+
	                    "<option value='local'>Local Only</option>"+
	                    "<option value='local-dev'>Local-Dev Only</option>"+
	                    "<option value='global'>Global Only</option>"+
	                    "<option value='global-dev'>Global Dev Only</option>"+
	                    "<option value='archives'>Archives Only</option>");
    
	$("#filterDropdown").val("");
	$("#filterDropdown").change(filterPackages);
	
	$("#packageTable").delegate(".cmdAction[cmd]","click",function(e) {
		cmd=$(this).attr("cmd");
		packid=$(this).attr("packid");
		refer = $(this).attr("refer");
		packageID = $(this).attr("packageid");
		switch(cmd) {
		    case "infoPlugin":
		        lgksLoader("Looking up the info");
				processAJAXPostQuery(_service("pluginManager","packinfo"),"packid="+packid,function(ans) {
				    lgksLoaderHide();
					lgksMsg(ans);
				});
				break;
			case "configurePlugin":
				parent.openLinkFrame("CONFIG:"+packageID,_link("modules/settings/plugins/"+packageID));
				break;
			case "editPlugin":
				parent.openLinkFrame(toTitle(packageID),_link("modules/"+packageID));
				break;
			case "archivePlugin":
			    archivePackage(packid);
			    break;
			default:
				lgksToast("Plugin Action Not Defined.");
		}
	});
	$("body").delegate(".packageActionButton[data-packid]","click", function() {
	    cmd=$(this).attr("cmd");
		packid=$(this).data("packid");
		$(".modal").modal("hide");
		
		switch(cmd) {
		    case "reinstallPackage":
		        lgksConfirm("<p class='alert alert-warning'>Unexpected bad things will happen if you don’t read this!</p>"+
                   "<div style='font-size: 14px;font-weight: normal;'><p>This action cannot be undone. <br>This will reinstall the selected package. This will clear the tables if they have data and delete any associated datas.</p></div>", 
                   "Reinstalling Package", function(ans) {
                        if(ans) {
                            lgksLoader("Reinstalling Selected Package");
            		        processAJAXPostQuery(_service("pluginManager","reinstall"),"packid="+packid,function(ans) {
            				    lgksLoaderHide();
            					lgksToast(ans.Data.msg);
            					listPackages();
            				},"json");
                        }
                      });
		        break;
		    case "archivePackage":
		        archivePackage(packid);
		        break;
		}
	});
	$('#pgToolbarSearch').submit(function() {
		return false;
	});
	$("#pgToolbarSearch").keyup(function(e) {
			if(e.keyCode==13) {
				searchPackages($("#pgToolbarSearch input").val());
				return false;
			}
		});
	
    listPackages();
});
function archivePackage(packid) {
    lgksConfirm("<p class='alert alert-info'>Unexpected bad things will happen if you don’t read this!</p>"+
       "<div style='font-size: 14px;font-weight: normal;'><p>This action will archive/unarchive the package. <br> Any functionality using this package will get impacted.</p></div>", 
       "Reinstalling Package", function(ans) {
            if(ans) {
                lgksLoader("Archiving/Unarchiving the package");
            	processAJAXPostQuery(_service("pluginManager","archive"),"packid="+packid,function(ans) {
            	    lgksLoaderHide();
            		lgksToast(ans.Data.msg);
            		listPackages();
            	},"json");
            }
          });
}
function listPackages() {
	$("#packageTable").html("<tr><td colspan=20><div class='ajaxloading ajaxloading5'>Fetching Packages</div></td></tr>");
	$("#categoryDropdown").show();
	
	$("#filterDropdown").val("");
	
	processAJAXQuery(_service("pluginManager","getlist")+"&src="+currentType+"&type="+$("#typeDropdown").val(),function(dataJSON) {
		tmplCode = Handlebars.compile($("#packageRowTemplate").html());
		html=tmplCode({"data":dataJSON.Data});
		$("#packageTable").html(html);
		
		if($("#packageTable tr").length<=0) {
			$("#packageTable").html("<tr><td colspan=20><h3 align=center>No packages found</h3></td></tr>");
		} else {
			$("#packageTable tr").each(function() {
				$(this).find("th").html($(this).index()+1);
			});
		}
	},"json");
}
function filterPackages() {
    switch($("#filterDropdown").val()) {
        case "error":
            $("#packageTable tr").hide();
            $("#packageTable tr.danger").show();
            break;
        case "global":
            $("#packageTable tr").hide();
            $("#packageTable tr.global").show();
            break;
        case "local":
            $("#packageTable tr").hide();
            $("#packageTable tr.local").show();
            break;
        case "local-dev":
            $("#packageTable tr").hide();
            $("#packageTable tr.local-dev").show();
            break;
        case "global-dev":
            $("#packageTable tr").hide();
            $("#packageTable tr.global-dev").show();
            break;
        case "archives":
            $("#packageTable tr").hide();
            $("#packageTable tr.archives, #packageTable tr.ARCHIVE").show();
            break;
        default:
            $("#packageTable tr").show();
    }
}
function searchPackages(txt) {
	if(txt==null || txt.length<=0) {
	    $("#packageTable tr").show();
		return;
	}
	
	$("#packageTable tr").hide();
    $("#packageTable tr:contains('"+txt.toLowerCase()+"')").show();
}
function loadInstalled() {
	$("#packageTable").html("<tr><td colspan=20><div class='ajaxloading ajaxloading5'>Indexing Packages</div></td></tr>");
	
	$("#pgtoolbar .navbar-right li").removeClass("active");
	$("#pgtoolbar .navbar-right a#toolbtn_loadInstalled").parent().addClass("active");
	$("#pgtoolbar li.categoryDropown").addClass("hidden");
	
	currentType="installed";
	
	$("#categoryDropdown").load(_service("pluginManager","categories","select")+"&src="+currentType+"&type="+$("#typeDropdown").val(), function() {
		listPackages();
	});
}
</script>