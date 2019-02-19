var currentItem="localapps";
$(function() {
    $("#appTable").closest("table").find("thead").removeClass("hidden").hide();
    
	Handlebars.registerHelper('actionBtns', function(app) {
		html={
			"allow_delete":'<i class="fa fa-trash cmdAction pull-right" cmd="deleteApp" appkey="'+app.appkey+'" title="Delete App"></i>',
      "allow_archive":'<i class="fa fa-archive cmdAction pull-right" cmd="archiveApp" appkey="'+app.appkey+'" title="Archive App"></i>',
			"allow_clone":'<i class="fa fa-copy cmdAction pull-right" cmd="cloneApp" appkey="'+app.appkey+'" title="Clone App"></i>',
      "allow_rename":'<i class="fa fa-terminal cmdAction pull-right" cmd="renameApp" appkey="'+app.appkey+'" title="Rename App"></i>',
		};
		finalHTML="";
		$.each(html,function(k,v) {
			if(app[k]==true) {
				finalHTML+=v;
			}
		});
		return finalHTML;
	});
  
  Handlebars.registerHelper('htmltable', function(appimage) {
    finalHTML = [];
    
    finalHTML.push("<table class='table table-stripped table-hover'>");
    cols = Object.keys(appimage);
    $.each(cols, function(a,b) {
      if(["hashid","logo_url","homepage","descs"].indexOf(b)>=0) return;
      if(["homepage1"].indexOf(b)>=0) {
        finalHTML.push("<tr data-refid='"+b.hashid+"'><td>"+toTitle(b)+"</td><td class='"+b+"'><a href='"+appimage[b]+"' target=_blank><i class='fa fa-external-link'></i></a></td></tr>");
      } else {
        finalHTML.push("<tr data-refid='"+b.hashid+"'><td>"+toTitle(b)+"</td><td class='"+b+"'>"+appimage[b]+"</td></tr>");
      }
    });
    finalHTML.push("</table>");
    
    return finalHTML.join("");
  });
	
	$("#pgworkspace").delegate(".cmdAction[cmd]","click",function(e) {
		cmd=$(this).attr("cmd");
		app=$(this).attr("appkey");
    uuid=$(this).closest("tr").attr("uuid");

		switch(cmd) {
			case "editApp":
				processAJAXPostQuery(_service("appManager","appEditor"),"app="+app,function(html) {
					lgksMsg(html,"App Editor #"+app,{closeButton:true,buttons: {
                    "Cancel":function(e) {
                      return true;
                    },
                    "Save":function(e) {
                        form=$(e.target).closest(".bootbox").find("form");
                        q=["app="+app];
                        $("input[name],select[name],textarea[name]",form).each(function() {
                          q.push($(this).attr("name")+"="+encodeURIComponent($(this).val()));
                        });
                        processAJAXPostQuery(_service("appManager","saveApp"),q.join("&"),function(dataJSON) {
                          lgksToast(dataJSON.Data.msg);
                          if(dataJSON.Data.error) {

                          } else {
                            listApps();
                            bootbox.hideAll();
                          }
                        },"json");
                        return false;
                    }
                }});
				});
				break;
      case "configureApp":
				parent.openLinkFrame("Config:"+app,_link("/modules/settings/apps")+"&forsite="+app);
				break;
			case "flushCache":
				lgksMsg("<div class='row text-center' style='padding-right:15px'>"+
            "<div class='col-md-3'><button class='btn btn-danger' onclick='purgeCache(1,this)' data-app='"+app+"'>Purge Config</button></div>"+
            "<div class='col-md-3'><button class='btn btn-info' onclick='purgeCache(2,this)' data-app='"+app+"'>Purge Cache</button></div>"+
            "<div class='col-md-3'><button class='btn btn-danger' onclick='purgeCache(3,this)' data-app='"+app+"'>Purge Templates</button></div>"+
            "<div class='col-md-3'><button class='btn btn-info' onclick='purgeCache(4,this)' data-app='"+app+"'>Purge Appcache</button></div>"+
            "</div>","Purge Cache");
				break;
      case "cloneApp":
        appID = app;
        lgksPrompt("<p class='alert alert-info'>Unexpected bad things will happen if you don’t read this!</p>"+
                   "<div style='font-size: 14px;font-weight: normal;'><p>This action cannot be undone. <br>+ This will clone the "+
                   appID+" application with same database. <br>+ UserMedia will not be transfered. <br>+ You will need to reconfigure the DB later before you start accessing the app.</p><p>Please give a new name for the new app</p></div>", 
                   "Cloning App", function(ans) {
                  if(ans && ans.length>1) {
                        processAJAXPostQuery(_service("appManager","cloneApp"),"app="+appID+"&name="+ans,function(data) {
                            if(data.indexOf("success")>=0) {
                              reloadListUI();
                              lgksToast("Cloned App Successfully");
                            } else {
                              lgksToast(data);
                            }
                          });
                  }
          });
				break;
      case "archiveApp":
        appID = app;
        lgksPrompt("<p class='alert alert-warning'>Unexpected bad things will happen if you don’t read this!</p>"+
                   "<div style='font-size: 14px;font-weight: normal;'><p>This will archive the "+
                   appID+" application. You can not access this app unless you restore it again.</p><p>Please type in the UUID "+uuid+" of the app to confirm.</p></div>", 
                   "Archive App", function(ans) {
                  if(ans==uuid) {
                        processAJAXPostQuery(_service("appManager","archiveApp"),"app="+appID+"&uuid="+ans,function(data) {
                            if(data.indexOf("success")>=0) {
                              reloadListUI();
                              lgksToast("Archived App Successfully");
                            } else {
                              lgksToast(data);
                            }
                          });
                  } else {
                    lgksToast("UUID Mismatch. Try again");
                  }
          });
				break;
			case "deleteApp":
        appID = app;
        lgksPrompt("<p class='alert alert-danger'>Unexpected bad things will happen if you don’t read this!</p>"+
                   "<div style='font-size: 14px;font-weight: normal;'><p>This action cannot be undone. This will permanently delete the "+
                   appID+" application, database, cache and stats.</p><p>Please type in the UUID "+uuid+" of the app to confirm.</p></div>", 
                   "Delete App", function(ans) {
                  if(ans==uuid) {
                        processAJAXPostQuery(_service("appManager","deleteApp"),"app="+appID+"&uuid="+ans,function(data) {
                            if(data.indexOf("success")>=0) {
                              reloadListUI();
                              lgksToast("Deleted App Successfully");
                            } else {
                              lgksToast(data);
                            }
                          });
                  } else {
                    lgksToast("UUID Mismatch. Try again");
                  }
          });
				break;
      case "restoreApp":
				lgksMsg("<ul class='list-group' style='padding-right:15px'>"+
            "<div class='list-group-item'><button class='btn btn-warning' onclick='restoreApp(1,this)' data-app='"+app+"'><i class='fa fa-undo'></i> Restore</button> Restore the app and clear from archive</div>"+
            "<div class='list-group-item'><button class='btn btn-danger' onclick='restoreApp(2,this)' data-app='"+app+"'><i class='fa fa-undo'></i> Restore</button> Restore the app and still keep in archive</div>"+
            "</div>","Restore App");
				break;
      case "renameApp":
        appID = app;
        lgksPrompt("<p class='alert alert-info'>Unexpected bad things will happen if you don’t read this!</p>"+
                   "<div style='font-size: 14px;font-weight: normal;'><p>This action cannot be undone. <br>Renaming of the app renames all related resources also</p>"+
                   "<p>Please give a new name for the app</p></div>", 
                   "Renaming App", function(ans) {
                  if(ans && ans.length>1) {
                        processAJAXPostQuery(_service("appManager","renameApp"),"app="+appID+"&name="+ans,function(data) {
                            if(data.indexOf("success")>=0) {
                              reloadListUI();
                              lgksToast("Renamed App Successfully");
                            } else {
                              lgksToast(data);
                            }
                          });
                  }
          });
        break;
      case "exportApp":
      	break;
        
      case "appimageInfo":
        $(this).closest("td").find(".appinfo").modal();
        break;
			default:
				lgksToast("App Action Not Defined.");
		}
	});
	
// 	$('#componentTree').delegate(".list-group-item.list-file a","click",function() {
// 		file=$(this).closest(".list-group-item");
		
// 		title=$(file).data("fullname");
// 		refid=$(file).data("refid");
		
// 		loadMarketAppInfo(title, refid);
// 	});
	
	//listApps();
	//listImages();
	loadLocalApps();
});
function loadLocalApps() {
  $("#appTable").html("<tr><td colspan=20><div class='ajaxloading ajaxloading5'>Fetching Apps</div></td></tr>");
	
	$("#pgtoolbar .navbar-right li").removeClass("active");
	$("#pgtoolbar .navbar-right a#toolbtn_loadLocalApps").parent().addClass("active");
	
	currentItem="localapps";
	
	listApps();
}
function loadAppImages() {
  $("#appTable").html("<tr><td colspan=20><div class='ajaxloading ajaxloading5'>Fetching Available App List</div></td></tr>");
	
	$("#pgtoolbar .navbar-right li").removeClass("active");
	$("#pgtoolbar .navbar-right a#toolbtn_loadAppImages").parent().addClass("active");
	
	currentItem="newapps";
	
	listImages();
}
function loadArchived() {
  $("#appTable").html("<tr><td colspan=20><div class='ajaxloading ajaxloading5'>Fetching Archived Apps</div></td></tr>");
	
	$("#pgtoolbar .navbar-right li").removeClass("active");
	$("#pgtoolbar .navbar-right a#toolbtn_loadArchived").parent().addClass("active");
	
	currentItem="archivedapps";
  
  listArchivedApps();
}
function reloadListUI() {
    if(currentItem=="localapps") {
        listApps();
    } else if(currentItem=="newapps") {
        listImages();
    } else if(currentItem=="archivedapps") {
        listArchivedApps();
    } else {
        lgksToast("Not supported");
    }
}

function relistImages() {
	listImages(true);
}
function listImages(recache) {
  $("#appTable").closest("table").find("thead").hide();
  $("#appTable").closest("table").find("#app2").show();
    
	$("#appTable").html("<tr><td colspan=20><div class='ajaxloading ajaxloading5'>Fetching Available App Images</div></td></tr>");
	
	if(recache===true) {
		lx=_service("appManager","listImages")+"&recache=true";
	} else {
		lx=_service("appManager","listImages");
	}
	
	processAJAXQuery(lx,function(dataJSON) {
		tmplCode = Handlebars.compile($("#imageRowTemplate").html());
		html=tmplCode({"appimages":dataJSON.Data});
		
		$("#appTable").html(html);
		
		$("#appTable tr").each(function() {
			$(this).find("th").html($(this).index()+1);
		});
	},"json");
}
function listApps() {
  $("#appTable").closest("table").find("thead").hide();
  $("#appTable").closest("table").find("#app1").show();
    
	$("#appTable").html("<tr><td colspan=20><div class='ajaxloading ajaxloading5'>Fetching Apps</div></td></tr>");
	
	processAJAXQuery(_service("appManager","listApps"),function(dataJSON) {
		tmplCode = Handlebars.compile($("#appRowTemplate").html());
		html=tmplCode({"apps":dataJSON.Data});
		
		$("#appTable").html(html);
		
		$("#appTable tr").each(function() {
			$(this).find("th").html($(this).index()+1);
		});
		
	},"json");
}
function listArchivedApps() {
  $("#appTable").closest("table").find("thead").hide();
  $("#appTable").closest("table").find("#app1").show();
    
	$("#appTable").html("<tr><td colspan=20><div class='ajaxloading ajaxloading5'>Fetching Apps</div></td></tr>");
	
	processAJAXQuery(_service("appManager","listArchivedApps"),function(dataJSON) {
		tmplCode = Handlebars.compile($("#archivedRowTemplate").html());
		html=tmplCode({"apps":dataJSON.Data});
		
		$("#appTable").html(html);
		
		$("#appTable tr").each(function() {
			$(this).find("th").html($(this).index()+1);
		});
		
	},"json");
}

function restoreApp(restoreIndex, src) {
  app = $(src).data('app');
  if(app == null) {
    $(".my-modal").modal("hide");
    return;
  }
  $(".my-modal").modal("hide");
  
  processAJAXPostQuery(_service("appManager","restoreApp"),"app="+app+"&type="+restoreIndex,function(data) {
        if(data.indexOf("success")>=0) {
          reloadListUI();
          lgksToast("Restored App Successfully");
        } else {
          lgksToast(data);
        }
      });
}

function purgeCache(cacheIndex,src) {
  app = $(src).data('app');
  if(app == null) {
    $(".my-modal").modal("hide");
    return;
  }
  switch(cacheIndex) {
    case 1://Config
      processAJAXQuery(_service("cleaner","PURGE:CONFIGS")+"&forsite="+app, function(data) {
          if(data.Data=='done') {
            lgksToast("Purged Config Cache Successfully");
          } else {
            lgksToast(data.Data);
          }
        },"json");
      break;
    case 2://Cache
      processAJAXQuery(_service("cleaner","PURGE:CACHE")+"&forsite="+app, function(data) {
          if(data.Data=='done') {
            lgksToast("Purged Misc Cache Successfully");
          } else {
            lgksToast(data.Data);
          }
        },"json");
      break;
    case 3://Templates
      processAJAXQuery(_service("cleaner","PURGE:TEMPLATES")+"&forsite="+app, function(data) {
          if(data.Data=='done') {
            lgksToast("Purged Templates Cache Successfully");
          } else {
            lgksToast(data.Data);
          }
        },"json");
      break;
    case 4://Appcache
      processAJAXQuery(_service("cleaner","PURGE:APPCACHE")+"&forsite="+app, function(data) {
          if(data.Data=='done') {
            lgksToast("Purged AppCache Successfully");
          } else {
            lgksToast(data.Data);
          }
        },"json");
      break;
  }
}

//App Installation
function installAppImage(refid) {
  if(typeof lgksLoader == "function") lgksLoader("Installing appimage, please wait ...","");
  else lgksMsg("Installing appimage, please wait ...");
  
	processAJAXPostQuery(_service("appManager","installAppImage"),"refid="+refid,function(dataJSON) {
					console.log(dataJSON);
          $(".modal").modal("hide");
				},"json");
}