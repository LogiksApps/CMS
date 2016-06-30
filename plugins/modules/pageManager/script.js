var lastComponent="";
$(function() {
	$($("#pgtoolbar .navbar-right>li")[0]).addClass("active");

	$('#componentTree .list-group-item').on('click', function() {
	    $('.glyphicon', this)
	      .toggleClass('glyphicon-folder-close')
	      .toggleClass('glyphicon-folder-open');
	  });

	$('#componentTree').delegate(".list-group-item input[name=selectFile]","change",function() {
		listItemAttr();
	});

	$('#componentTree').delegate(".list-group-item.list-file a","click",function() {
		$('#componentTree .list-group-item.active').removeClass('active');
		tag=$(this).closest(".list-group-item");
		if(lastComponent=="pages") {
			tag.addClass('active');
		}
		path=tag.data('path');
		title=$(this).text();
		openComponent(lastComponent, path, title);

		listItemAttr();
	});

	loadComponentTree('pages');
});

function listItemAttr() {
	if($('#componentTree .list-group-item.active').length>0) {
		$("#pgtoolbar .onsidebarActive").show();
	} else {
		$("#pgtoolbar .onsidebarActive").hide();
	}
}

function loadComponentTree(comp) {
	lastComponent=comp;
	$("#componentTree").html("<div class='ajaxloading5'></div>");
	$("#pgtoolbar .onsidebarSelect").hide();
	
	processAJAXQuery(_service("pageManager","getlist")+"&comptype="+lastComponent,function(txt) {
		fs=txt.Data;
		html="";html1="";
		$.each(fs,function(k,v) {
			kx=md5(k);
			if(v.folder) {
				html1+="<div class='list-group-item list-folder'><a href='#item-"+kx+"' data-toggle='collapse'><i class='glyphicon glyphicon-folder-close'></i>"+k+"</a></div>";
				html1+="<div class='list-group collapse' id='item-"+kx+"'>";
				$.each(v,function(m,n) {
					if(typeof n =="object") {
						html1+="<div class='list-group-item list-file' data-path='"+n.path+"'><a href='#'><i class='glyphicon glyphicon-file'></i>"+m+"</a><input type='checkbox' name='selectFile' class='pull-right' /></div>";//n.name
					}
				});
				html1+="</div>";
			} else {
				html+="<div class='list-group-item list-file' data-path='"+v.path+"'><a href='#'><i class='glyphicon glyphicon-file'></i>"+v.name+"</a><input type='checkbox' name='selectFile' class='pull-right' /></div>";
			}
		});
		$("#componentTree").html(html+html1);

		$("#componentTree .list-folder>a").each(function() {
			nx=$(this).closest(".list-folder").next().find(".list-file").length;
			$(this).append(" <span class='badge pull-right'>"+nx+"</span>");
		});

		if($("#pageEditor").attr("src")!=null) {
			$('#componentTree .list-group-item[data-path="'+$("#pageEditor").attr("src")+'.json"]').addClass("active")
		}
		listItemAttr();
	},"json");
}

function pgRefresh() {
	loadComponentTree(lastComponent);
}
function pgPages() {
	$("#pgtoolbar .navbar-right>li.active").removeClass('active');
	$($("#pgtoolbar .navbar-right>li")[0]).addClass("active");
	loadComponentTree("pages");
}
function pgComps() {
	$("#pgtoolbar .navbar-right>li.active").removeClass('active');
	$($("#pgtoolbar .navbar-right>li")[1]).addClass("active");
	loadComponentTree("comps");
}
function pgLayouts() {
	$("#pgtoolbar .navbar-right>li.active").removeClass('active');
	$($("#pgtoolbar .navbar-right>li")[2]).addClass("active");
	loadComponentTree("layouts");
}

function openComponent(type, path, title) {
	if(title==null) {
		title=path.split("/");
		title=title[title.length-1];
	}
	switch(type) {
		case "pages":
			lx=_link("modules/pageEditor")+"&comptype=pages&embed=true&src="+encodeURIComponent(path);
			$("#pgcontent").html("<div class='ajaxloading5'></div>");
			$("#pgcontent").load(lx);
		break;
		default:
			switch(lastComponent) {
				case "pages":
				break;
				case "comps":
					path="pages/comps/"+path;
				break;
				case "layouts":
					path="css/templates/"+path;
				break;
			}
			lx=_link("modules/cmsEditor")+"&type=edit&src="+encodeURIComponent(path);
			top.openLinkFrame(title,lx,true);
		break;
	}
	
}

function pgOpenExternal() {
	if($("#componentTree").find("input[type=checkbox][name=selectFile]:checked").length<=0) return;

	$("#componentTree").find("input[type=checkbox][name=selectFile]:checked").each(function() {
		file=$(this).closest(".list-file");

		path=file.data("path");
		title=file.text();
		lx=_link("modules/pageEditor")+"&comptype=pages&readonly=true&src="+encodeURIComponent(path);
		top.openLinkFrame(title,lx,true);
	});
	//file=$($("#componentTree").find("input[type=checkbox][name=selectFile]:checked")[0]).closest(".list-file");
}

function pgCreateNew() {
	lgksPrompt("What would you name for the new page.<br><h6>No Space or special characters allowed.</h6><h6>FORMAT: folder/filename</h6>","New Page",function(txt) {
		if(txt!=null && txt.length>0) {
			txt=txt.replace(/ /g,"_");//.replace(/[^\w\s]/gi, '')

			lx=_service("pageManager","create")+"&comptype="+lastComponent+"&src="+txt;
			processAJAXQuery(lx,function(dts) {
				pgRefresh();
			});
		}
	});
}

function pgTrash() {
	if($('#componentTree .list-group-item input[name=selectFile]:checked').length<=0) {return false;}
	
	lgksConfirm("Do you want to delete selected files?","Delete!",function(ans) {
		if(ans) {
			q=[];
			$('#componentTree .list-group-item input[name=selectFile]:checked').each(function() {
				q.push($(this).closest(".list-group-item").data("path"));
			});
			q="src="+q.join(",");
			lx=_service("pageManager","delete")+"&comptype="+lastComponent;
			processAJAXPostQuery(lx,q,function(dts) {
				pgRefresh();
			});
		}
	});
}

function pgClone() {
	if($('#componentTree .list-group-item input[name=selectFile]:checked').length<=0) {return false;}

	q=[];
	$('#componentTree .list-group-item input[name=selectFile]:checked').each(function() {
		q.push($(this).closest(".list-group-item").data("path"));
	});
	lx=_service("pageManager","clone")+"&comptype="+lastComponent+"&src="+q.join(",");
	processAJAXQuery(lx,function(dts) {
		pgRefresh();
	});
}
function pgRename() {
	if($('#componentTree .list-group-item input[name=selectFile]:checked').length<=0) {return false;}
	if($('#componentTree .list-group-item input[name=selectFile]:checked').length>1) {
		lgksToast("Please select only one file to rename.");
		return false;
	}

	lgksPrompt("And what would be the new name for page!<br><h6>No Space or special characters allowed.</h6><h6>FORMAT: folder/filename</h6>","New Page",function(txt) {
		if(txt!=null && txt.length>0) {
			txt=txt.replace(/ /g,"_");//.replace(/[^\w\s]/gi, '')

			q=[];
			$('#componentTree .list-group-item input[name=selectFile]:checked').each(function() {
				q.push($(this).closest(".list-group-item").data("path"));
			});
			if(q[0]==null) return false;

			lx=_service("pageManager","rename")+"&comptype="+lastComponent+"&src="+q[0]+"&new="+txt;
			processAJAXQuery(lx,function(dts) {
				pgRefresh();
			});
		}
	});
}