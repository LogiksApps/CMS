var uniStatus=true;
$(function() {
	$("#roleModel").delegate("input[name=checkAll]","change",function(e) {
			uniStatus=this.checked;
			$(this).closest(".list-group.list-group-activity").find("input[name=roleCheckbox]").each(function() {
				roleHash=$(this).data("hash");
				this.checked=uniStatus;
				processAJAXPostQuery(_service("credsRoles","save"),roleHash+"="+uniStatus,function(ans) {
					try {
						json=$.parseJSON(ans);
						if(json.Data=="success") {
							lgksToast("Update Successfull");
						} else {
							lgksToast("Sorry, could not update the role.");
						}
					} catch($e) {
						lgksToast("Sorry, could not update the role.");
					}
				});
			});
		});
  $("#checkAllPermissions").change(function() {
    uniStatus=this.checked;
    
    $("#permissionList input[name=roleCheckbox]").each(function() {
        roleHash=$(this).data("hash");
				this.checked=uniStatus;
				processAJAXPostQuery(_service("credsRoles","save"),roleHash+"="+uniStatus,function(ans) {
					try {
						json=$.parseJSON(ans);
						if(json.Data=="success") {
							lgksToast("Update Successfull");
						} else {
							lgksToast("Sorry, could not update the role.");
						}
					} catch($e) {
						lgksToast("Sorry, could not update the role.");
					}
				});
    });
  });
	$("#roleModel,#permissionList").delegate("input[name=roleCheckbox]","change",function(e) {
		roleHash=$(this).data("hash");
		status=$(this).is(":checked");
		processAJAXPostQuery(_service("credsRoles","save"),roleHash+"="+status,function(ans) {
			try {
				json=$.parseJSON(ans);
				if(json.Data=="success") {
					lgksToast("Updated");
				} else {
					lgksToast("Sorry, could not update the role.");
				}
			} catch($e) {
				lgksToast("Sorry, could not update the role.");
			}
		});
	});
  
  reloadSidebar();
  
});
function reloadSidebar() {
  $("#roleModel").html("<div class='ajaxloading ajaxloading5'></div>");
	switch(uiType) {
		case "by_plugins":
			loadModuleList();
			break;
		case "by_users":
		default:
			loadPrivilegeList();
			break;
	}
}
function loadModuleList() {
	$("#componentTree>ul").html("<div class='ajaxloading ajaxloading5'></div>");
	processAJAXQuery(_service("credsRoles","list-modules"),function(data) {
		html=[];
		sideBarTmpl=Handlebars.compile("<li class='list-group-item' title='Privilege : {{title}} ({{id}})' ><a href='#{{privilegehash}}' data-hash='{{name}}'>{{title}} <i class='fa fa-chevron-right pull-right'></i></a></li>")
		$.each(data.Data, function(k, v) {
			html.push(sideBarTmpl(v));
		});
		$("#componentTree>ul").html(html.join(""));

		$("#componentTree .list-group-item:first-child").addClass("active");
		$("#componentTree .list-group-item a").click(function() {
				hash=$(this).data("hash");
				$("#componentTree .list-group-item.active").removeClass("active");
				$(this).closest("li").addClass("active");
				loadRoleForModule(hash);
				return false;
			});
		loadRoleForModule($("#componentTree .list-group-item:first-child a").data("hash"));
	},'json');
}
function loadPrivilegeList() {
  $("#componentTree>ul").html("<div class='ajaxloading ajaxloading5'></div>");
  processAJAXQuery(_service("credsRoles","list-privileges"),function(data) {
        html=[];
        sideBarTmpl=Handlebars.compile("<li class='list-group-item' title='Privilege : {{title}} ({{id}})' ><a href='#{{privilegehash}}' data-hash='{{privilegehash}}'>{{title}} <i class='fa fa-chevron-right pull-right'></i></a></li>")
        $.each(data.Data, function(k, v) {
          html.push(sideBarTmpl(v));
        });
        $("#componentTree>ul").html(html.join(""));
    
        $("#componentTree .list-group-item:first-child").addClass("active");
        $("#componentTree .list-group-item a").click(function() {
            hash=$(this).data("hash");
            $("#componentTree .list-group-item.active").removeClass("active");
            $(this).closest("li").addClass("active");
            loadRoleModel(hash);
            return false;
          });
        loadRoleModel($("#componentTree .list-group-item:first-child a").data("hash"));
      },"json");
}
function loadRoleModel(roleID) {
  $("#roleModel").html("<div class='panel-heading panel-heading-bold'>Role :: "+$("#componentTree .list-group-item.active").text().trim()+"</div><div class='ajaxloading ajaxloading5'></div>");
  processAJAXPostQuery(_service("credsRoles","list-roles"),"&roleid="+roleID,function(data) {
      htmlTemplate=Handlebars.compile($("#rolebox-template").html());
      itemTemplate=Handlebars.compile($("#roleitem-template").html());
    
      $("#roleModel .ajaxloading").detach();
      $.each(data.Data, function(k, v) {
            if($("#roleModel").find(".panel[data-module='"+v.modulehash+"']").length<=0) {
              $("#roleModel").append(htmlTemplate(v));
            }
            itemHTML=itemTemplate(v);
            activityid=v.activity.split(".")[0];
            
            if($("#roleModel").find(".panel[data-module='"+v.modulehash+"']").find(".panel-collapse .list-group[data-activity='"+activityid+"']").length<=0) {
              htmlUL="<ul class='list-group list-group-activity' data-activity='"+activityid+"'><li class='list-group-item active'>"+activityid.toUpperCase()+"<input type='checkbox' name='checkAll' class='pull-right' /></li></ul>";
              $("#roleModel").find(".panel[data-module='"+v.modulehash+"']").find(".panel-collapse .panel-body").append(htmlUL);
            }
//             $("#roleModel").find(".panel[data-module='"+v.modulehash+"']").find(".panel-collapse .list-group").append(itemHTML);
            $("#roleModel").find(".panel[data-module='"+v.modulehash+"']").find(".panel-collapse .list-group[data-activity='"+activityid+"']").append(itemHTML);
          });
    
        divs=$("#roleModel>.panel").sort(function(a,b){
                return $(a).find(".panel-heading").text().trim().toLowerCase().localeCompare($(b).find(".panel-heading").text().trim().toLowerCase());
            });
        $("#roleModel>.panel").detach();
        $("#roleModel").append(divs);
    
        $("#roleModel>.panel .list-group").each(function() {
                divLS=$(this).find(".list-group-item:not(.active)").sort(function(a,b){
                          txt1=$(a).data("category").toLowerCase()+$(a).data("activity").toLowerCase();
                          txt2=$(b).data("category").toLowerCase()+$(b).data("activity").toLowerCase();
                          return txt1.localeCompare(txt2);
                      });
                $(this).find(".list-group-item:not(.active)").detach();
                $(this).append(divLS);
          });
		
					$("input[name=checkAll]","#roleModel").each(function() {
						if($(this).closest(".list-group.list-group-activity").find("input[name=roleCheckbox]").length==$(this).closest(".list-group.list-group-activity").find("input[name=roleCheckbox]:checked").length) {
							this.checked=true;
						}
					});
    },"json");
}
function loadRoleForModule(moduleSlug) {
	$("#roleModel").html("<div class='panel-heading panel-heading-bold'>Module :: "+$("#componentTree .list-group-item.active").text().trim()+"</div><div class='ajaxloading ajaxloading5'></div>");
  processAJAXPostQuery(_service("credsRoles","list-roles-modules"),"&module="+moduleSlug,function(data) {
      htmlTemplate=Handlebars.compile($("#modulebox-template").html());
      itemTemplate=Handlebars.compile($("#moduleitem-template").html());
    
      $("#roleModel .ajaxloading").detach();
      $.each(data.Data, function(k, v) {
            if($("#roleModel").find(".panel[data-module='"+v.modulehash+"']").length<=0) {
              $("#roleModel").append(htmlTemplate(v));
            }
            itemHTML=itemTemplate(v);
            activityid=v.activity.split(".")[0];
            
            if($("#roleModel").find(".panel[data-module='"+v.modulehash+"']").find(".panel-collapse .list-group[data-activity='"+activityid+"']").length<=0) {
              htmlUL="<ul class='list-group list-group-activity' data-activity='"+activityid+"'><li class='list-group-item active'>"+activityid.toUpperCase()+"<input type='checkbox' name='checkAll' class='pull-right' /></li></ul>";
              $("#roleModel").find(".panel[data-module='"+v.modulehash+"']").find(".panel-collapse .panel-body").append(htmlUL);
            }
//             $("#roleModel").find(".panel[data-module='"+v.modulehash+"']").find(".panel-collapse .list-group").append(itemHTML);
            $("#roleModel").find(".panel[data-module='"+v.modulehash+"']").find(".panel-collapse .list-group[data-activity='"+activityid+"']").append(itemHTML);
          });
    
        divs=$("#roleModel>.panel").sort(function(a,b){
                return $(a).find(".panel-heading").text().trim().toLowerCase().localeCompare($(b).find(".panel-heading").text().trim().toLowerCase());
            });
        $("#roleModel>.panel").detach();
        $("#roleModel").append(divs);
    
        $("#roleModel>.panel .list-group").each(function() {
                divLS=$(this).find(".list-group-item:not(.active)").sort(function(a,b){
                          txt1=$(a).data("category").toLowerCase()+$(a).data("activity").toLowerCase();
                          txt2=$(b).data("category").toLowerCase()+$(b).data("activity").toLowerCase();
                          return txt1.localeCompare(txt2);
                      });
                $(this).find(".list-group-item:not(.active)").detach();
                $(this).append(divLS);
          });
		
			$("input[name=checkAll]","#roleModel").each(function() {
					if($(this).closest(".list-group.list-group-activity").find("input[name=roleCheckbox]").length==$(this).closest(".list-group.list-group-activity").find("input[name=roleCheckbox]:checked").length) {
						this.checked=true;
					}
				});
    },"json");
}
function generateRoles() {
	
}