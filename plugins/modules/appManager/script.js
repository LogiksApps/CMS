var currentItem="localapps";
$(function() {
    $("#appTable").closest("table").find("thead").removeClass("hidden").hide();
    
	Handlebars.registerHelper('actionBtns', function(app) {
		html={
			"allow_delete":'<i class="fa fa-trash cmdAction pull-right" cmd="deleteApp" appkey="{{appkey}}" title="Delete App"></i>',
			"allow_clone":'<i class="fa fa-copy cmdAction pull-right" cmd="copyApp" appkey="{{appkey}}" title="Clone App"></i>',
		};
		finalHTML="";
		$.each(html,function(k,v) {
			if(app[k]==true) {
				finalHTML+=v;
			}
		});
		return finalHTML;
	});
	
	$("#pgworkspace").delegate(".cmdAction[cmd]","click",function(e) {
		cmd=$(this).attr("cmd");
		app=$(this).attr("appkey");
		
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
			case "cloneApp":
				processAJAXPostQuery(_service("appManager","cloneApp"),"app="+app,function(html) {
					
				});
				break;
			case "installAppImage":
			    loadMarketAppInfo($(this).text(),app);
			    break;
			case "exportApp":
				
				break;
			case "flushCache":
				
				break;
			case "deleteApp":
				
				break;
			default:
				lgksToast("App Action Not Defined.");
		}
	});
	
	$('#componentTree').delegate(".list-group-item.list-file a","click",function() {
		file=$(this).closest(".list-group-item");
		
		title=$(file).data("fullname");
		refid=$(file).data("refid");
		
		loadMarketAppInfo(title, refid);
	});
	
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
function reloadListUI() {
    if(currentItem=="localapps") {
        listApps();
    } else if(currentItem=="newapps") {
        listImages();
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

function loadMarketAppInfo(title, refid) {
	processAJAXPostQuery(_service("appManager","appInfo"),"refid="+refid,function(html) {
					lgksMsg(html,"App Image : "+title,{closeButton:true,buttons: false,className:'appmodal'});
				});
}


function removeApps() {
	
}

function installApp(refid) {
	processAJAXPostQuery(_service("appManager","install"),"refid="+refid,function(dataJSON) {
					console.log(dataJSON);
				});
}