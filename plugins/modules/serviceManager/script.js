var lastComponent="local";
var tempCopiedRecords={};
var classCopied="active";
var classFound="warning";
var classEdited="danger";
$(function() {
	$("#serviceList").delegate("td .cmdbtn[cmd]","click",function(e) {
		if($(this)==null || $(this).length<=0) {
			return;
		}
		cmd=$(this).attr("cmd");
		tr=$(this).closest("tr");
		skey=tr.data('key');
		
		switch(cmd) {
			case "copyService":
				tempCopiedRecords[skey]=tr;
				$("#serviceList").find('tr[data-key='+skey+']').addClass(classCopied);
				lgksToast("Record Copied. Please open Local Services and Save it for finalising.");
				break;
			case "detachService":
				if(tempCopiedRecords[skey]!=null) {
					delete tempCopiedRecords[skey];
				}
				tr.detach();
				break;
			case "removeService":
				if(tempCopiedRecords[skey]!=null) {
					delete tempCopiedRecords[skey];
				}
				tr.detach();
				break;
      case "openEditor":
        type = $(this).data("path");
        processAJAXPostQuery(_service("serviceManager","spath"),"&type="+type+"&skey="+skey, function(data) {
          if(data.Data!=null && data.Data.length>2) {
            parent.openLinkFrame(skey+".php",data.Data,true);
          }
        },"json");
        break;
      case "editServiceConfig":
        editServiceConfig(this);
        break;
		}
	});
	
	Handlebars.registerHelper('is_status', function(msg, matchMsg, options) {
        if(msg === matchMsg || msg===true)
            return true;
        else
            return false;
    });
  Handlebars.registerHelper('is_not', function(msg, matchMsg, options) {
        if(msg === matchMsg || msg===true)
            return false;
        else
            return true;
    });
	Handlebars.registerHelper('is_notnull', function(msg, matchMsg, options) {
        if(msg !=null && msg.length>0)
            return true;
        else
            return false;
    });
  
  Handlebars.registerHelper('select', function(msg, matchMsg, options) {
        htmlSelector= [];
        return "<select class='form-control'>"+htmlSelector.join("")+"</select>";
    });
  
	loadLocal();
});

function resetServiceList() {
  tempCopiedRecords={};
  listServices();
}

function listServices() {
	$("#serviceList").html("<tr><th colspan=10><div class='ajaxloading ajaxloading5'></div></th></tr>");
	processAJAXQuery(_service("serviceManager","getlist")+"&comptype="+lastComponent,function(txt) {
		
		tmpl = Handlebars.compile(getRowTemplate(""));
		
		html = tmpl({"LIST":txt.Data.LIST});
		
		$("#serviceList").html(html);
		
		if(lastComponent=="local") {
			$.each(tempCopiedRecords,function(k,tr) {
				tr=tr.attr("class",classCopied);
				$("#serviceList").append(tr);
				$("#serviceList tr.active td.action").find("i.fa-copy").replaceWith("<i class='fa fa-remove cmdbtn' cmd='detachService'></i>");
			});
		} else {
			$.each(tempCopiedRecords,function(k,tr) {
				$("#serviceList").find('tr[data-key='+k+']').addClass(classCopied);
			});
		}
	},"json");
}
function findServices() {
	$("#serviceList").append("<tr><th colspan=10><div class='ajaxloading ajaxloading5'></div></th></tr>");
	processAJAXQuery(_service("serviceManager","findmore"),function(txt) {
		
		tmpl = Handlebars.compile(getRowTemplate());
		
		$.each(txt.Data.LIST,function(k,v) {
			if($("#serviceList tr[data-key="+v.skey+"]").length>0) {
				delete txt.Data.LIST[k];
			} else {
        txt.Data.LIST[k].clz = classFound;
      }
		});
		
		html = tmpl({"LIST":txt.Data.LIST});
		$("#serviceList").find(".ajaxloading").closest("tr").detach();
		$("#serviceList").append(html);
	},"json");
}
function loadLocal() {
	$("#pgtoolbar .navbar-right li").removeClass("active");
	$("#pgtoolbar #toolbtn_loadLocal").parent().addClass("active");
	
	$("#pgtoolbar #toolbtn_saveServiceConfig").show();
	$("#pgtoolbar #toolbtn_findServices").show();
	
	lastComponent="local";
	
	return listServices();
}
function loadGlobal() {
	$("#pgtoolbar .navbar-right li").removeClass("active");
	$("#pgtoolbar #toolbtn_loadGlobal").parent().addClass("active");
	
	$("#pgtoolbar #toolbtn_saveServiceConfig").hide();
	$("#pgtoolbar #toolbtn_findServices").hide();
	
	lastComponent="globals";
	
	return listServices();
}
function saveServiceConfig() {
	if($("#serviceList").find("tr."+classCopied).length<=0) {
		
	}
	if($("#serviceList").find("tr."+classFound).length<=0) {
		
	}
	if($("#serviceList").find("tr."+classEdited).length<=0) {
		
	}
	q=[];
	$("input[name],select[name]","#serviceList").each(function() {
		if($(this).attr("type")=="checkbox") {
			if(this.checked) {
				q.push($(this).attr("name")+"=true");
			} else {
				q.push($(this).attr("name")+"=false");
			}
		} else {
			q.push($(this).attr("name")+"="+$(this).val());
		}
	});
	
	processAJAXPostQuery(_service("serviceManager","update"),q.join("&"),function(txt) {
		if(txt.Data!=null && txt.Data.toLowerCase().indexOf("error")<0) {
      tempCopiedRecords={};
      
			lgksToast("Service Configuration Updated Successfully");
			loadLocal();
		} else {
			if(txt.Data!=null && txt.Data.length>2) {
				lgksToast(txt.Data.substr(6));
			} else {
				lgksToast("Error Updating Service Configuration");
			}
		}
	},"json");
}

function getRowTemplate(clz) {
	return $("#service-row").html();
}

function createNewService() {
  lgksPrompt("Give a new name for service (no space or special characters)", "New Service", function(ans) {
      if(ans) {
        processAJAXPostQuery(_service("serviceManager","createservice"),"name="+ans, function(data) {
          if(data.Data!=null && data.Data.name != null) {
            skey = data.Data.name;
            uri =  data.Data.uri;
            if(uri!=null && uri.length>0) {
              parent.openLinkFrame(skey,uri,true);
            }
            if(data.Data.msg!=null && data.Data.msg.length>0) {
              lgksToast(data.Data.msg);
            }
            if(data.Data.status=="ok") {
              findServices();
            }
          } else {
            lgksToast("Service could not be created");
          }
        },"json");
      }
    });
}

function editServiceConfig(src) {
  cmd=$(src).attr("cmd");
  tr=$(src).closest("tr");
  skey=tr.data('key');

  $("#serviceConfigEditor .serviceTitle").html(skey);
  
  $("#serviceConfigEditor *[name=skey]").val(skey);
  $("#serviceConfigEditor *[name=format]").val($(tr).find("input[data-name='format']").val());
  $("#serviceConfigEditor *[name=access_control]").val($(tr).find("input[data-name='access_control']").val());
  $("#serviceConfigEditor *[name=privilege_model]").val($(tr).find("input[data-name='privilege_model']").val());
  
  $("#serviceConfigEditor").modal();
}
function updateService(srcBTN) {
	skey = $("#serviceConfigEditor *[name=skey]").val();
	tr = $("#serviceList tr[data-key='"+skey+"']");console.log(tr);
	if(tr.length>0) {
		$(tr).find("input[data-name='format']").val($("#serviceConfigEditor *[name=format]").val());
		$(tr).find("input[data-name='access_control']").val($("#serviceConfigEditor *[name=access_control]").val());
		$(tr).find("input[data-name='privilege_model']").val($("#serviceConfigEditor *[name=privilege_model]").val());
		
		$(tr).find("td[data-name='format']").text($("#serviceConfigEditor *[name=format]").val());
		$(tr).find("td[data-name='access_control']").text($("#serviceConfigEditor *[name=access_control]").val());
		$(tr).find("div[data-name='privilege_model']").text($("#serviceConfigEditor *[name=privilege_model]").val());
    
    $(tr).addClass(classEdited);
	}
  $("#serviceConfigEditor").modal("hide");
}