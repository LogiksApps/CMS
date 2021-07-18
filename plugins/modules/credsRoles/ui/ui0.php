<?php
if(!defined('ROOT')) exit('No direct script access allowed');

loadModule("pages");

function pageSidebar() {
  $html="<div id='componentTree' class='componentTree list-group list-group-root well'>
            <div class='panel-heading panel-heading-bold'>Roles<input style='float:right;margin-right: -10px;border: 1px dotted #AAA;' onchange='searchRoles(this)' placeholder='Search ...' /></div>";
  $html.="<ul id='roleList' class='list-group'>";
  $html.="<div class='ajaxloading ajaxloading5'></div>";
  $html.="</ul>";
  $html.="</div>";
	return $html;
}
function pageContentArea() {
  return "<div id='componentSpace' class='componentSpace' style='padding-right: 10px;padding-left: 5px;width:100%;height;100%;'>
    <div class='col-md-4' style='height: 100%;overflow: auto;border-right: 1px solid #AAA;padding: 0px;background-color: #f5f5f5;'>
        <div class='panel-heading panel-heading-bold'>Modules<input style='float:right;margin-right: -10px;border: 1px dotted #AAA;' onchange='searchModules(this)' placeholder='Search ...' /></div><ul id='roleModel' ></ul></div>
    <div class='col-md-4' style='height: 100%;overflow: auto;border-right: 1px solid #AAA;padding: 0px;background-color: #f5f5f5;'>
        <div class='panel-heading panel-heading-bold'>Feature<input style='float:right;margin-right: -10px;border: 1px dotted #AAA;' onchange='searchFeatures(this)' placeholder='Search ...' /></div><ul id='activityList' ></ul></div>
    <div class='col-md-4' style='height: 100%;overflow: auto;padding: 0px;overflow: auto;background-color: #f5f5f5;'>
        <div class='panel-heading panel-heading-bold'><input class='pull-left' type='checkbox' id='checkAllPermissions' style='margin-right: 4px;' /> Permissions 
                <input style='float:right;margin-right: -10px;border: 1px dotted #AAA;' onchange='searchPermissions(this)' placeholder='Search ...' />
            </div><ul id='permissionList' ></ul></div>
    </div>";
}

if(!isset($_COOKIE['CREDROLES_UI'])) $uiType="by_roles";
else $uiType=$_COOKIE['CREDROLES_UI'];

echo _css("credsRoles");

printPageComponent(false,[
		"toolbar"=>[
			"loadOLDUI"=>["title"=>"OLD UI","align"=>"right"],

			//"searchRoles"=>["title"=>"Change View","type"=>"dropdown","align"=>"right","options"=>["by_roles"=>"By Roles","by_plugins"=>"By Plugins"]],
			//"searchRoles"=>["title"=>"Search Roles","type"=>"search","align"=>"right"],
			
			"reloadSidebar"=>["icon"=>"<i class='fa fa-refresh'></i>"],
// 			"generateRoles"=>["icon"=>"<i class='fa fa-gears'></i>","tips"=>"Generate New Roles"],
            // "clearRoleCache"=>["icon"=>"<i class='fa fa-magic'></i>","tips"=>"Clean Roles Cache"],
			//['type'=>"bar"],
			//"rename"=>["icon"=>"<i class='fa fa-terminal'></i>","class"=>"onsidebarSelect onOnlyOneSelect","tips"=>"Rename Content"],
// 			"deleteRoles"=>["icon"=>"<i class='fa fa-trash'></i>"],
			"downloadRoleCSV"=>["icon"=>"<i class='fa fa-download'></i>","tips"=>"Download as CSV"],	
			"uploadRoleCSV"=>["icon"=>"<i class='fa fa-upload'></i>","tips"=>"Download as CSV"],	
		],
		"sidebar"=>"pageSidebar",
		"contentArea"=>"pageContentArea"
	]);

echo "<script>FORSITE='".CMS_SITENAME."';uiType='".$uiType."';</script>";
echo _js(["credsRoles"]);
?>
<script>
function loadRoleModel(roleID) {
  loadRoleModuleList(roleID);
}
function loadRoleModuleList(roleID) {
  //<ul class="list-group"></ul>
  $("#roleModel").html("<div class='ajaxloading ajaxloading5'></div>");
  $("#activityList").html("");
  $("#permissionList").html("");
  
  processAJAXPostQuery(_service("credsRoles","list-modules-for-role"),"&roleid="+roleID,function(data) {
          $("#roleModel").html("");
          
          $.each(data.Data, function(a,b) {
              $("#roleModel").append("<li class='list-group-item'><a href='#' data-roleid='"+roleID+"' data-module='"+b.name+"'>"+toTitle(b.name)+" ["+b.count+"] <i class='fa fa-chevron-right pull-right'></i></a></li>");
          })
          $("#roleModel li a").click(function(e) {
            $(this).closest("#roleModel").find("li.active").removeClass("active");
            $(this).closest("li").addClass("active");
            loadActivtiesForRoleModule($(this).data("roleid"),$(this).data("module"));
          });
    },"json");
};
function loadActivtiesForRoleModule(roleid, module) {
  //list-permissions-role-module
  $("#activityList").html("<div class='ajaxloading ajaxloading5'></div>");
  $("#permissionList").html("");
  
  processAJAXPostQuery(_service("credsRoles","list-activity-role-module"),"&roleid="+roleid+"&module="+module,function(data) {
          $("#activityList").html("");
          
          $.each(data.Data, function(a,b) {
              $("#activityList").append("<li class='list-group-item'><a href='#' data-roleid='"+roleid+"' data-module='"+module+"' data-activity='"+b.activity+"'>"+b.title+" ["+b.count+"] <i class='fa fa-chevron-right pull-right'></i></a></li>");
          })
          $("#activityList li a").click(function(e) {
            $(this).closest("#activityList").find("li.active").removeClass("active");
            $(this).closest("li").addClass("active");
            loadPermissionforActivityRoleModule($(this).data("roleid"),$(this).data("module"),$(this).data("activity"));
          });
    },"json");
}
function loadPermissionforActivityRoleModule(roleid, module,activity) {
  $("#permissionList").html("<div class='ajaxloading ajaxloading5'></div>");
  processAJAXPostQuery(_service("credsRoles","list-permissions-activity-role-module"),"&roleid="+roleid+"&module="+module+"&activity="+activity,function(data) {
          $("#permissionList").html("");
          
          $.each(data.Data, function(a,b) {
              if(b.allow)
                $("#permissionList").append("<li class='list-group-item' data-roleid='"+roleid+"' data-module='"+module+"' data-activity='"+b.activity+"'><a href='#'>"+
                          "<input type='checkbox' name='roleCheckbox' data-hash='"+b.rolehash+"' checked /> "+ b.role_title+"</a></li>");
              else
                $("#permissionList").append("<li class='list-group-item' data-roleid='"+roleid+"' data-module='"+module+"' data-activity='"+b.activity+"'><a href='#'>"+
                          "<input type='checkbox' name='roleCheckbox' data-hash='"+b.rolehash+"' /> "+ b.role_title+"</a></li>");
          })
          $("#permissionList li a").click(function(e) {
            //$(this).closest("#permissionList").find("li.active").removeClass("active");
            //$(this).closest("li").addClass("active");
            //loadPermissionforActivityRoleModule($(this).data("roleid"),$(this).data("module"),$(this).data("activity"));
          });
    },"json");
}
function loadOLDUI() {
  $.cookie("CREDROLES_UI","ui1");
  window.location = _link("modules/credsRoles")+"&ui=ui1";
}
function searchRoles(src) {
    $("#roleList li").show();
    if($(src).val()!=null && $(src).val().length>1) {
        $("#roleList li").filter(function(a) {
        	return ($(this).text().toLowerCase().indexOf($(src).val().toLowerCase())<0);
        }).hide();
    }
}
function searchModules(src) {
    $("#roleModel li").show();
    if($(src).val()!=null && $(src).val().length>1) {
        $("#roleModel li").filter(function(a) {
        	return ($(this).text().toLowerCase().indexOf($(src).val().toLowerCase())<0);
        }).hide();
    }
}
function searchFeatures(src) {
    $("#activityList li").show();
    if($(src).val()!=null && $(src).val().length>1) {
        $("#activityList li").filter(function(a) {
        	return ($(this).text().toLowerCase().indexOf($(src).val().toLowerCase())<0);
        }).hide();
    }
}
function searchPermissions(src) {
    $("#permissionList li").show();
    if($(src).val()!=null && $(src).val().length>1) {
        $("#permissionList li").filter(function(a) {
        	return ($(this).text().toLowerCase().indexOf($(src).val().toLowerCase())<0);
        }).hide();
    }
}
</script>