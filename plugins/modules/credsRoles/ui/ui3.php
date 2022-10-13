<?php
if(!defined('ROOT')) exit('No direct script access allowed');

if(!defined("ADMIN_PRIVILEGE_ID")) define("ADMIN_PRIVILEGE_ID", 5);

if(!isset($_REQUEST['roleids'])) $_REQUEST['roleids'] = "";

loadModule("pages");

function pageContentArea() {
    return file_get_contents(__DIR__."/templates/ui3.html");
}

$toolbar = [
            "reloadPage"=>["icon"=>"<i class='fa fa-refresh'></i>"],
			"createNewRole"=>["icon"=>"<i class='fa fa-plus'></i>","title"=>"New Role"],
			
			"buildRoleModel"=>["icon"=>"<i class='fa fa-gears'></i>","title"=>"Build", "tips"=>"Find and import New Roles"],
			"roleStats"=>["icon"=>"<i class='fa fa-info'></i>","title"=>"Stats", "tips"=>"Show Role/ACL Status"],
			
            // "clearRoleCache"=>["icon"=>"<i class='fa fa-magic'></i>","tips"=>"Clean Roles Cache"],
			//['type'=>"bar"],
			//"rename"=>["icon"=>"<i class='fa fa-terminal'></i>","class"=>"onsidebarSelect onOnlyOneSelect","tips"=>"Rename Content"],
			//"downloadRoleCSV"=>["icon"=>"<i class='fa fa-download'></i>","tips"=>"Download as CSV"],	
			//"uploadRoleCSV"=>["icon"=>"<i class='fa fa-upload'></i>","tips"=>"Download as CSV"],	
		];

// if($_SESSION['SESS_PRIVILEGE_ID']>ADMIN_PRIVILEGE_ID) {
//     unset($toolbar['selectGUID']);
// }

printPageComponent(false,[
        "toolbar"=>$toolbar,
		//"sidebar"=>"pageSidebar",
		"contentArea"=>"pageContentArea"
	]);


echo _css(["credsRoles"]);
echo _js(["credsRoles"]);
//echo "<script>FORSITE='".CMS_SITENAME."';uiType='".$uiType."';</script>";
?>
<script>
var uiType = "by_roles1";
var ROLE_LIST = [];
var ROLE_PERMISSIONS = [];
var MODULE_LIST = [];
var MODULE_ACTIONS_LIST = [];
var HIDDEN_ROLES = [];
var PRELOAD_ROLEIDS = "<?=$_REQUEST['roleids']?>";
$(function() {
    PRELOAD_ROLEIDS = PRELOAD_ROLEIDS.split(",");
    if(PRELOAD_ROLEIDS[0].length<=0) PRELOAD_ROLEIDS = [];
    
    $("#searchRoles").change(searchInRoles);
    
    $("#role_main_view").delegate(".panel.role-col", "click", function(e) {
        var roleid = $(this).data("roleid");
        
        PRELOAD_ROLEIDS = [roleid];
        
        // loadRoleData();
        generateLayout2();
    });
    
    generateLayout();
});
function oldStart() {
    if(PRELOAD_ROLEIDS.length>0) {
        $("#toolbtn_selectRoles").detach();
    }
    
    $("#toolbtn_selectRoles").delegate(".dropdown-menu li a", "click", function(a,b) {
        showHiddenRoleColumn($(this).data('drop'), $(this).data('privilegehash'));
    });
    
    loadHiddenRoles();
    generateLayout();
}
function reloadPage() {
    window.location.reload();
}
function generateLayout() {
    $("#role-list .role-col").detach();
    $("#role-body").html("");
    $("#permission_block").html("");
    $(".noroles_found").detach();
    
    processAJAXQuery(_service("credsRoles","list-roles-main"),function(data) {
            ROLE_LIST = data.Data.ROLES;
            if(ROLE_LIST.length<=0) {
                // $("#role_main_view").hide();
                $("#role_main_view").prepend("<h3 class='text-center noroles_found'>No Roles are defined yet, start by defining one!</h3>");
                return;
            }
            $("#role_main_view").show();
            $.each(ROLE_LIST, function(a,b) {
                if(HIDDEN_ROLES.indexOf(`${b.privilegehash}$$${b.name}`)>=0) return;
                if(PRELOAD_ROLEIDS.length>0 && PRELOAD_ROLEIDS.indexOf(b.id)<0) return;
                
                $("#role-body").append(`<div class='col-md-3'><div class='panel panel-default role-col' data-roleid='${b.id}' data-rolename='${b.name}' data-privilegehash='${b.privilegehash}'>
                    <div class='panel-body'>${b.title} <span class='label label-info pull-right' style='margin-top: -7px;margin-right: -7px;'>${b.users} Users</span></div>
                </div></div>`);
                
                // $("#role-list").append(`<th class='role-col' data-roleid='${b.id}' data-rolename='${b.name}' data-privilegehash='${b.privilegehash}'>
                //         <h5>${b.title}</h5>
                //         <span class='show_users'>${b.users} Users</span>
                //         <div class="btn-group btn-options">
                //           <button type="button" class="btn dropdown-toggle pull-right" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                //             <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                //           </button>
                //           <ul class="dropdown-menu">
                //             <li class='hide_role_column'><a href="#">Hide Column</a></li>
                //             <li class='show_users'><a href="#">Show Users</a></li>
                //             <!--<li class='role_edit'><a href="#">Edit Role</a></li>-->
                //             <li class='role_clone'><a href="#">Clone Role</a></li>
                //             <li role="separator" class="divider"></li>
                //             <li class='role_delete'><a href="#">Delete Role</a></li>
                //           </ul>
                //         </div>
                //     </th>`);
            });
            
            //loadRoleData();
    },"json");
}
function generateLayout2() {
    $("#role-list .role-col").detach();
    // $("#role-body").html("");
    $(".role_permission_table").removeClass("hidden");
    $(".noroles_found").detach();
    
    processAJAXQuery(_service("credsRoles","list-roles-main"),function(data) {
            ROLE_LIST = data.Data.ROLES;
            if(ROLE_LIST.length<=0) {
                $("#role_main_view").hide();
                $("#role_main_view").parent().prepend("<h3 class='text-center noroles_found'>No Roles are defined yet, start by defining one!</h3>");
                return;
            }
            $("#role_main_view").show();
            $.each(ROLE_LIST, function(a,b) {
                if(HIDDEN_ROLES.indexOf(`${b.privilegehash}$$${b.name}`)>=0) return;
                if(PRELOAD_ROLEIDS.length>0 && PRELOAD_ROLEIDS.indexOf(parseInt(b.id))<0) return;
                
                $("#role-list").append(`<th class='role-col' data-roleid='${b.id}' data-rolename='${b.name}' data-privilegehash='${b.privilegehash}'>
                        <h5>${b.title}</h5>
                        <span class='show_users'>${b.users} Users</span>
                        <button type="button" class="btn show_users" style="margin: 7px;margin-right: 16px;font-size: 19px;margin-top: 4px;">
                            <i class="fa fa-users" aria-hidden="true"></i>
                          </button>
                        <div class="btn-group btn-options">
                          <button type="button" class="btn dropdown-toggle pull-right" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                          </button>
                          <ul class="dropdown-menu">
                            <!--<li class='hide_role_column'><a href="#">Hide Column</a></li>-->
                            <li class='show_users'><a href="#">Show Users</a></li>
                            <!--<li class='role_edit'><a href="#">Edit Role</a></li>-->
                            <!--<li class='role_clone'><a href="#">Clone Role</a></li>-->
                            <!--<li role="separator" class="divider"></li>-->
                            <!--<li class='role_delete'><a href="#">Delete Role</a></li>-->
                          </ul>
                        </div>
                    </th>`);
            });
            
            if(PRELOAD_ROLEIDS.length>0) {
                $(".hide_role_column").detach();
            }
            
            loadRoleData();
    },"json");
}
function loadRoleData() {
    $("#permission_block").html("");
    //roleids
    var COLOR_CLASS = ["warning", "success", "info", "danger"];
    processAJAXPostQuery(_service("credsRoles","list-roles-permissions"), "roleids="+PRELOAD_ROLEIDS.join(","),function(data) {
            //console.log(data);
            ROLE_PERMISSIONS = data.Data.PERMISSIONS;
            var ROLE_COUNT = ROLE_LIST.length+1;
            $.each(ROLE_PERMISSIONS, function(module,activities) {
                var moduleTitle = module.split(/(?=[A-Z])/).join(" ").replace("_", " ").toUpperCase();
                MODULE_LIST.push(module);
                
                var htmlBody = `<tr class="view role_row" data-module='${module}'>
                    <th class="positionSticky">${moduleTitle}</th>`;
                    
                $.each(ROLE_LIST, function(a,role) {
                    if(HIDDEN_ROLES.indexOf(`${role.privilegehash}$$${role.name}`)>=0) return;
                    if(PRELOAD_ROLEIDS.length>0 && PRELOAD_ROLEIDS.indexOf(parseInt(role.id))<0) return;
                    
                    htmlBody += `<td>
                        <label class="containerBlock" data-roleid='${role.id}' data-rolename='${role.name}' data-privilegehash='${role.privilegehash}'>
                            <input class='role_all_checkbox' type="checkbox" data-rolename='${role.name}' data-privilegehash='${role.privilegehash}'>
                            <span class="checkmark"></span>
                        </label>
                    </td>`;// checked="checked" plusCheck
                });
                
                htmlBody += "</tr>";
    
                htmlBody += `<tr class="fold role_row" data-module='${module}'>
                    <td colspan="${ROLE_COUNT}" class="foldBlock"><table>`;
                
                $.each(activities, function(activityName, actions) {
                    //console.log(row);
                    MODULE_ACTIONS_LIST.push(activityName);
                    $.each(actions, function(actionName, permissions) {
                        //console.log(activityName, actionName, permissions);
                        var rowTips = actionName;
                        var rowTitle = toTitle(actionName.replace(/_/g, ' '));
                        var rowClass = COLOR_CLASS[MODULE_ACTIONS_LIST.length%COLOR_CLASS.length];
                        htmlBody += `<tr class='activityRow' data-activity='${activityName}' data-module='${module}'>
                                    <th class="positionSticky">
                                        <div class="subText">
                                            <h6>${rowTitle}</h6>
                                            <i class="fa fa-info-circle" aria-hidden="true" data-toggle="tooltip" title="${rowTips}"></i>
                                        </div>
                                        <citie class='label label-activity label-${rowClass}' data-activity='${activityName}'>#${activityName}</citie>
                                    </th>`;
                        
                        $.each(ROLE_LIST, function(a,role) {
                            if(HIDDEN_ROLES.indexOf(`${role.privilegehash}$$${role.name}`)>=0) return;
                            if(PRELOAD_ROLEIDS.length>0 && PRELOAD_ROLEIDS.indexOf(parseInt(role.id))<0) return;
                            
                            if(permissions[role.privilegehash]!=null) {
                                if(permissions[role.privilegehash].allow=="true") {
                                    htmlBody += `<td>
                                        <label class="containerBlock">
                                            <input class='role_ctrl_checkbox' type="checkbox"  checked="checked" data-privilegehash='${role.privilegehash}' data-refid='${permissions[role.privilegehash].id}'>
                                            <span class="checkmark"></span>
                                        </label>
                                    </td>`;
                                } else {
                                    htmlBody += `<td>
                                        <label class="containerBlock ">
                                            <input class='role_ctrl_checkbox' type="checkbox" data-privilegehash='${role.privilegehash}' data-refid='${permissions[role.privilegehash].id}'>
                                            <span class="checkmark"></span>
                                        </label>
                                    </td>`;
                                }
                                // plusCheck minusCheck
                            }
                        });
                        
                        htmlBody += `</tr>`;
                    });
                });
                
                htmlBody += `</table></td></tr>`;
                $("#permission_block").append(htmlBody);
                
                updateUIRowBlock($("#permission_block").find(`tr.view[data-module='${module}'] td`));
            });
            
            $(".fold-table tr.view").on("click", function() {
                $(this).toggleClass("open").next(".fold").toggleClass("open");
            });
            $('[data-toggle="tooltip"]').tooltip();
            
            $(".role_ctrl_checkbox").change(updateRoleControl);
            $(".role_all_checkbox").change(updateRoleControlAll);
            $(".show_users").click(showRoleUsers);
            $(".role_edit").click(editRole);
            $(".role_delete").click(deleteRole);
            $(".role_clone").click(cloneRole);
            $(".hide_role_column").click(hideRoleColumn);
            
            $("#permission_block .label-activity").click(function() {
                var module = $(this).closest("tr.role_row").data("module");
                var activity = $(this).data("activity");
                
                $(`tr.activityRow.active`).removeClass("active");
                $(`tr.activityRow[data-activity='${activity}'][data-module='${module}']`).toggleClass("active")
            });
    },"json");
}

function updateRoleControl(checkBoxEvent) {
    var privilegehash = $(checkBoxEvent.target).data("privilegehash");
    var refid = $(checkBoxEvent.target).data("refid");
    var isChecked = $(checkBoxEvent.target).is(":checked");
    
    processAJAXPostQuery(_service("credsRoles","update-role"), "refid="+refid+"&allow="+isChecked,function(data) {
        lgksToast(data.Data);
    }, "json");

    setTimeout(function() {
        updateUIRowBlock(checkBoxEvent.target);
    }, 500);
}

function updateRoleControlAll(checkBoxEvent) {
    var privilegehash = $(checkBoxEvent.target).data("privilegehash");
    var module = $(checkBoxEvent.target).closest("tr.role_row").data("module");
    var isChecked = $(checkBoxEvent.target).is(":checked");
    
    processAJAXPostQuery(_service("credsRoles","update-role-module"), "privilegehash="+privilegehash+"&module="+module+"&allow="+isChecked,function(data) {
        lgksToast(data.Data);
    }, "json");
    
    $(`tr.role_row[data-module='${module}'].fold td .role_ctrl_checkbox[data-privilegehash='${privilegehash}']`).each(function() {
        this.checked = isChecked;
    });
    setTimeout(function() {
        updateUIRowBlock(checkBoxEvent.target);
    }, 500);
}

function updateUIRowBlock(element) {
    var module = $(element).closest("tr.role_row").data("module");
    var privilegehash = $(element).data("privilegehash");
    
    var selectedRoles = {};
    if(privilegehash!=null) {
        $(`tr.role_row[data-module=data-module='${module}'}].fold .role_ctrl_checkbox[data-privilegehash=${privilegehash}]`).each(function(k1, ele) {
            if(selectedRoles[$(ele).data("privilegehash")]==null) selectedRoles[$(ele).data("privilegehash")] = [];
            selectedRoles[$(ele).data("privilegehash")].push($(ele).is(":checked"));
        });
    } else {
        $(`tr.role_row[data-module='${module}'].fold .role_ctrl_checkbox`).each(function(k1, ele) {
            if(selectedRoles[$(ele).data("privilegehash")]==null) selectedRoles[$(ele).data("privilegehash")] = [];
            selectedRoles[$(ele).data("privilegehash")].push($(ele).is(":checked"));
        });
    }
    
    $.each(selectedRoles, function(role, permissions) {
        $(`tr.role_row[data-module='${module}'].view td .role_all_checkbox[data-privilegehash='${role}']`)
                    .closest("label").removeClass("minusCheck").removeClass("plusCheck");
        $(`tr.role_row[data-module='${module}'].view td .role_all_checkbox[data-privilegehash='${role}']`)[0].checked = false;
        
        if(permissions.length == permissions.filter(a=>a).length) {
            $(`tr.role_row[data-module='${module}'].view td .role_all_checkbox[data-privilegehash='${role}']`)[0].checked = true;
        } else if(permissions.length == permissions.filter(a=>!a).length) {
            $(`tr.role_row[data-module='${module}'].view td .role_all_checkbox[data-privilegehash='${role}']`).removeAttr("checked");
            $(`tr.role_row[data-module='${module}'].view td .role_all_checkbox[data-privilegehash='${role}']`)[0].checked = false;
        } else {
            $(`tr.role_row[data-module='${module}'].view td .role_all_checkbox[data-privilegehash='${role}']`).closest("label").addClass("minusCheck");
        }
    });
}

function searchInRoles() {
    var stxt = $("#searchRoles").val();
    
    if(stxt==null || stxt.length<=0) {
        $(".activityRow").show();
        $(".view.role_row").show();
        $(`tr.role_row`).removeClass("open");
    } else {
        var stxt = $("#searchRoles").val().toLowerCase();

        $(".activityRow").hide();
        $(".view.role_row").hide();
        
        $(`tr.fold .positionSticky`).each(function() {
            if($(this).text().toLowerCase().trim().indexOf(stxt)>=0) {
                var module = $(this).closest("tr.role_row").data("module");
                $(`tr.role_row[data-module='${module}']`).addClass("open");
                $(this).closest(".activityRow").show();
                
                $(this).closest(".fold.role_row").prev().show();
            }
        });
    }
}

function showRoleUsers(ele) {
    var roleid = $(ele.target).closest(".role-col").data("roleid");
    var roleName = $(ele.target).closest(".role-col").data("rolename");
    //console.log($(ele.target).closest(".role-col"), roleid);
    $("#role-modal .modal-title").html("Active Users With Role - "+roleName);
    $("#role-modal .modal-body").html("");
    
    processAJAXPostQuery(_service("credsRoles","role-users"), "roleids="+roleid,function(data) {
        var htmlData = [`<table class='table table-striped'>
            <thead>
                
            </thead>
            <tbody>`];
        if(data.Data && data.Data.length>0) {
            $.each(data.Data, function(a,row) {
                var sl = a+1;
                htmlData.push(`<tr><td>${row.name}</td><td>${row.userid}</td></tr>`);//<th width='30px'>${sl}</th>
            });
        } else {
            htmlData.push(`<tr><td class='text-center' colspan=100>No Users found for this Role - ${roleName}</td></tr>`);
        }
        
        htmlData.push("<tbody></table>");
        
        $("#role-modal .modal-body").html(htmlData.join(""));
    }, "json");
    
    $("#role-modal").modal("show");
}

function editRole(ele) {
    var roleid = $(ele.target).closest("th.role-col").data("roleid");
    var roleName = $(ele.target).closest("th.role-col").data("rolename");
    
    //lgksOverlayFrame(_link("modules/credsManager/roles/new"), "Create New Role");
}

function deleteRole(ele) {
    var roleid = $(ele.target).closest("th.role-col").data("roleid");
    var roleName = $(ele.target).closest("th.role-col").data("rolename");
    
    lgksConfirm("Do you want to delete the Role - "+roleName, "Delete Role", function(ans) {
        if(ans) {
            processAJAXPostQuery(_service("credsRoles","role-delete"), "roleid="+roleid,function(data) {
                if(data.Data=="success") {
                    generateLayout();
                } else {
                    lgksAlert(data.Data);
                }
            }, "json");
        }
    });
}

function createNewRole() {
    lgksPrompt("Please give a new name for Role?", "Create Role", function(ans) {
        if(ans) {
            processAJAXPostQuery(_service("credsRoles","role-create-new"), "role_name="+ans,function(data) {
                if(data.Data=="success") {
                    generateLayout();
                } else {
                    lgksAlert(data.Data);
                }
            }, "json");
        }
    });
}

function cloneRole(ele) {
    var roleid = $(ele.target).closest("th.role-col").data("roleid");
    var roleName = $(ele.target).closest("th.role-col").data("rolename");
    
    lgksPrompt(`Please give a new name for Cloned Role from ${roleName}.`, "Clone Role", function(ans) {
        if(ans) {
            processAJAXPostQuery(_service("credsRoles","role-create-clone"), "role_name="+ans+"&src_role="+roleid,function(data) {
                if(data.Data=="success") {
                    generateLayout();
                } else {
                    lgksAlert(data.Data);
                }
            }, "json");
        }
    });
}

function loadHiddenRoles() {
    if(PRELOAD_ROLEIDS.length>0) {
        HIDDEN_ROLES = [];
        return;
    }
    
    HIDDEN_ROLES = localStorage.getItem("credsRoles_HIDDEN_ROLES");
    if(HIDDEN_ROLES==null) HIDDEN_ROLES = [];
    else HIDDEN_ROLES = JSON.parse(HIDDEN_ROLES);
    
    renderSelectRoleDropdown();
}

function hideRoleColumn(ele) {
    if(PRELOAD_ROLEIDS.length>0) {
        HIDDEN_ROLES = [];
        return;
    }
    
    var roleid = $(ele.target).closest("th.role-col").data("roleid");
    var roleName = $(ele.target).closest("th.role-col").data("rolename");
    var privilegehash = $(ele.target).closest("th.role-col").data("privilegehash");
    
    $(`label[data-privilegehash='${privilegehash}'], input[data-privilegehash='${privilegehash}']`).closest("td,th").hide();
    $(`.role-col[data-privilegehash='${privilegehash}']`).hide();
    
    HIDDEN_ROLES.push(privilegehash+"$$"+roleName);
    saveHiddenRoles();
    
    renderSelectRoleDropdown();
}

function showHiddenRoleColumn(roleKey, privilegeHash) {
    if(PRELOAD_ROLEIDS.length>0) {
        HIDDEN_ROLES = [];
        return;
    }
    
    if(roleKey=="--all--") {
        HIDDEN_ROLES = [];
        
        renderSelectRoleDropdown();
        
        saveHiddenRoles();
        generateLayout();
    } else if(HIDDEN_ROLES.indexOf(roleKey)>=0) {
        delete HIDDEN_ROLES[HIDDEN_ROLES.indexOf(roleKey)];
        HIDDEN_ROLES = Object.values(HIDDEN_ROLES)
        // console.log(HIDDEN_ROLES);
        
        renderSelectRoleDropdown();
        
        saveHiddenRoles();
        generateLayout();
    } else {
        lgksToast("Error finding Role, try reloading the page");
    }
}

function saveHiddenRoles() {
    if(PRELOAD_ROLEIDS.length>0) {
        HIDDEN_ROLES = [];
        return;
    }
    
    localStorage.setItem("CREDROLES_HIDDEN_ROLES", JSON.stringify(HIDDEN_ROLES));
}

function renderSelectRoleDropdown() {
    if(PRELOAD_ROLEIDS.length>0) {
        HIDDEN_ROLES = [];
        return;
    }
    
    if(HIDDEN_ROLES && HIDDEN_ROLES.length>0) {
        $("#toolbtn_selectRoles .dropdown-menu").html(`<li class='border_bottom'><a data-drop="--all--"  data-privilegehash="--all--" href="#"><b>SHOW ALL</b></a></li>`);
        
        $.each(HIDDEN_ROLES, function(a,b) {
            var bArr = b.split("$$");
            $("#toolbtn_selectRoles .dropdown-menu").append(`<li><a data-drop="${b}"  data-privilegehash="${bArr[0]}" href="#">${bArr[1]}</a></li>`);
        });
        $("#toolbtn_selectRoles button").html("Hidden Roles ("+($("#toolbtn_selectRoles .dropdown-menu li").length-1)+") <span class='caret'></span>");
    } else {
        $("#toolbtn_selectRoles button").html("Hidden Roles (0) <span class='caret'></span>");
    }
}

function buildRoleModel() {
    lgksConfirm("Are you sure about starting build process now, this may take some time for <b>"+$("#role-body .role-col").length+" Roles</b>, and will take little over <b>"+($("#role-body .role-col").length*5+60)+" Secs</b> for complete process (approximate).\n\nDo you want to continue?", "Start Building Policies", function(a) {

        $(".ajaxloading").detach();
        $("#role_main_view").hide();
        $("#role_main_view").parent().prepend("<div class='ajaxloading ajaxloading5'>Building Role Models, please be patient !!!</div>");
        
        processAJAXQuery(_service("credsRoles","generate"), function(data) {
                lgksAlert(data.Data);
                
                generateLayout();
                $(".ajaxloading").detach();
                $("#role_main_view").show();
            }, "json");
    });
}

function roleStats() {
    $("#role-modal .modal-title").html("Role Control Stats");
    $("#role-modal .modal-body").html("");
    
    processAJAXPostQuery(_service("credsRoles","role-stats"), "",function(data) {
        var htmlData = [`<table class='table table-striped'>
            <tbody>`];
        $.each(data.Data, function(a,b) {
            var sl = a+1;
            htmlData.push(`<tr><th>${a}</th><td>${b}</td></tr>`);//<th width='30px'>${sl}</th>
        });
        
        htmlData.push("<tbody></table>");
        
        $("#role-modal .modal-body").html(htmlData.join(""));
    }, "json");
    
    $("#role-modal").modal("show");
}
</script>