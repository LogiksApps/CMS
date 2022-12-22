var lastComponent="";
var lastRender="table";
$(function() {
	lastRender=localStorage.getItem('logikcms.pageManager.displaytype');
	if(lastRender==null) {
		lastRender="table";
	}

	$($("#pgtoolbar .navbar-right>li")[0]).addClass("active");
	
	$('#componentSpace').delegate("input[name=selectFile]","change",function() {
		listItemAttr();
	});

	$('#componentSpace').delegate(".list-file a","click",function() {
		tag=$(this).closest(".list-file");
		path=tag.data('path');
		title=$(this).text();
		openComponent(lastComponent, path, title);

		listItemAttr();
	});
	$('#componentSpace').delegate(".list-folder a","click",function() {
		tag=$(this).closest(".list-folder");
		folder=tag.data('folder');
		
		$('#componentSpace tr.list-file[data-folder="'+folder+'"]').toggle();
	});

	$('#componentSpace').delegate(".action a[cmd],.action i[cmd]","click",function() {
		tag=$(this).closest(".list-file");
		path=tag.data('path');
		cmd=$(this).attr('cmd');
		title=tag.find("a.fname").text();
		
		switch(cmd) {
			case "edit":
				openComponent(lastComponent, path, title);
			break;
			case "editcode":
				path=path.replace(".tpl",".php");
				openComponent(lastComponent, path, title+" php");
			break;
			case "clone":
				$('#componentSpace input[name=selectFile]').each(function() {
					this.checked=false;
				});
				tag.find("input[name=selectFile]")[0].checked=true;
				pgClone();
			break;
			case "rename":
				$('#componentSpace input[name=selectFile]').each(function() {
					this.checked=false;
				});
				tag.find("input[name=selectFile]")[0].checked=true;
				pgRename();
			break;
			case "preview":
				lx=_link(path.replace(".json","").replace("_","/"));
				lx=lx.split("?");
				window.open(lx[0]+"?site="+FORSITE);
			break;
		}
	});

	loadComponents('pages');
});

function listItemAttr() {
	if($('#pgworkspace .list-group-item.active').length>0) {
		$("#pgtoolbar .onsidebarActive").show();
	} else {
		$("#pgtoolbar .onsidebarActive").hide();
	}
	
	if($('#pgworkspace input[name=selectFile]:checked').length>0) {
		$("#pgtoolbar .onsidebarSelect").show();
		
		if($('#pgworkspace input[name=selectFile]:checked').length>1) {
			$("#pgtoolbar .onOnlyOneSelect").hide();
		}
	} else {
		$("#pgtoolbar .onsidebarSelect").hide();
	}
}

function loadComponents(comp) {
	localStorage.setItem('logikcms.pageManager.displaytype',lastRender);

	lastComponent=comp;
	$("#componentSpace").html("<div class='ajaxloading ajaxloading5'></div>");
	$("#pgtoolbar .onsidebarSelect").hide();
	
	processAJAXQuery(_service("pageManager","getlist")+"&comptype="+lastComponent,function(txt) {
		fs=txt.Data;
		//
		switch(lastRender) {
			case "cards":
				$("#pgtoolbar #toolbtn_display>i").attr("class","glyphicon glyphicon-th-list");
				renderCards(fs);
			break;
				
			case "table":
				$("#pgtoolbar #toolbtn_display>i").attr("class","glyphicon glyphicon-th-large");
				renderTable(fs);
			break;
			default:
				renderTable(fs);
		}
		
		listItemAttr();
	},"json");
}

function pgRefresh() {
	loadComponents(lastComponent);
}
function pgDisplay() {
	if($("#pgtoolbar #toolbtn_display>i").hasClass("glyphicon-th-large")) {
		$("#pgtoolbar #toolbtn_display>i").attr("class","glyphicon glyphicon-th-list");
		lastRender="cards";
		pgRefresh();
	} else {
		$("#pgtoolbar #toolbtn_display>i").attr("class","glyphicon glyphicon-th-large");
		lastRender="table";
		pgRefresh();
	}
}
function pgPages() {
	$("#pgtoolbar .navbar-right>li.active").removeClass('active');
	$($("#pgtoolbar .navbar-right>li")[0]).addClass("active");
	loadComponents("pages");
}
function pgComps() {
	$("#pgtoolbar .navbar-right>li.active").removeClass('active');
	$($("#pgtoolbar .navbar-right>li")[1]).addClass("active");
	loadComponents("comps");
}
function pgLayouts() {
	$("#pgtoolbar .navbar-right>li.active").removeClass('active');
	$($("#pgtoolbar .navbar-right>li")[3]).addClass("active");
	loadComponents("layouts");
}
function pgSnippets() {
	$("#pgtoolbar .navbar-right>li.active").removeClass('active');
	$($("#pgtoolbar .navbar-right>li")[2]).addClass("active");
	loadComponents("snippets");
}
function openComponent(type, path, title) {
	if(title==null) {
		title=path.split("/");
		title=title[title.length-1];
	}
	switch(type) {
		case "pages":
			lx=_link("modules/pageEditor")+"&comptype=pages&embed=true&src="+encodeURIComponent(path);
			//$("#pgcontent").html("<div class='ajaxloading5'></div>");
			//$("#pgcontent").load(lx);
			top.openLinkFrame(title,lx,true,false);
		break;
		default:
			switch(lastComponent) {
				case "pages":
				break;
				case "comps":
					path="pages/comps/"+path;
				break;
				case "snippets":
					path="pages/snippets/"+path;
				break;
				case "layouts":
					path="css/templates/"+path;
				break;
			}
			lx=_link("modules/cmsEditor")+"&type=autocreate&src="+encodeURIComponent(path);
			top.openLinkFrame(title,lx,true,false);
		break;
	}
	
}

function pgOpenExternal() {
	if($("#componentSpace").find("input[type=checkbox][name=selectFile]:checked").length<=0) return;

	$("#componentSpace").find("input[type=checkbox][name=selectFile]:checked").each(function() {
		file=$(this).closest(".list-file");

		path=file.data("path");
		title=file.text();
		lx=_link("modules/pageEditor")+"&comptype=pages&src="+encodeURIComponent(path);
		top.openLinkFrame(title,lx,true,false);
	});
	//file=$($("#componentSpace").find("input[type=checkbox][name=selectFile]:checked")[0]).closest(".list-file");
}

function pgCreateNew() {
	lgksPrompt("What would you name for the new page.<br><h6>No Space or special characters allowed.</h6><h6>FORMAT: folder/filename</h6>","New Page",function(txt) {
		if(txt!=null && txt.length>0) {
			txt=txt.replace(/ /g,"_").trim();//.replace(/[^\w\s]/gi, '')

			lx=_service("pageManager","create")+"&comptype="+lastComponent+"&src="+txt;
			processAJAXQuery(lx,function(dts) {
				try {
					if(dts!=null && dts.length>0) {
						dts=$.parseJSON(dts);
						if(dts.error!=null && dts.error.msg!=null) {
							if(dts.error.msg.toLowerCase().indexOf("failed to open stream:")>2) {
								lgksToast("Sorry, the folder is readonly. Can't create source files.");
							} else {
								lgksToast("Error occured during creation of the file.");
							}
						}
					}
				} catch(e) {
				}
				pgRefresh();
			});
		}
	});
}

function pgTrash() {
	if($('#componentSpace .list-file input[name=selectFile]:checked').length<=0) {return false;}
	
	lgksConfirm("Do you want to delete selected files?","Delete!",function(ans) {
		if(ans) {
			q=[];
			$('#componentSpace .list-file input[name=selectFile]:checked').each(function() {
				q.push($(this).closest(".list-file").data("path"));
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
	if($('#componentSpace .list-file input[name=selectFile]:checked').length<=0) {return false;}

	q=[];
	$('#componentSpace .list-file input[name=selectFile]:checked').each(function() {
		q.push($(this).closest(".list-file").data("path"));
	});
	lx=_service("pageManager","clone")+"&comptype="+lastComponent+"&src="+q.join(",");
	processAJAXQuery(lx,function(dts) {
		pgRefresh();
	});
}
function pgRename() {
	if($('#componentSpace .list-file input[name=selectFile]:checked').length<=0) {return false;}
	if($('#componentSpace .list-file input[name=selectFile]:checked').length>1) {
		lgksToast("Please select only one file to rename.");
		return false;
	}

	lgksPrompt("And what would be the new name for page!<br><h6>No Space or special characters allowed.</h6><h6>FORMAT: folder/filename</h6>","New Page",function(txt) {
		if(txt!=null && txt.length>0) {
			txt=txt.replace(/ /g,"_");//.replace(/[^\w\s]/gi, '')

			q=[];
			$('#componentSpace .list-file input[name=selectFile]:checked').each(function() {
				q.push($(this).closest(".list-file").data("path"));
			});
			if(q[0]==null) return false;

			lx=_service("pageManager","rename")+"&comptype="+lastComponent+"&src="+q[0]+"&new="+txt;
			processAJAXQuery(lx,function(dts) {
				pgRefresh();
			});
		}
	});
}

function renderCards(fs) {
	html="";html1="";
	$.each(fs,function(k,v) {
		kx=md5(k);
		if(v.folder) {
// 			html1+="<div class='list-group-item list-folder'><a href='#item-"+kx+"' data-toggle='collapse'><i class='glyphicon glyphicon-folder-close'></i>"+k+"</a></div>";
// 			html1+="<div class='list-group collapse' id='item-"+kx+"'>";
			$.each(v,function(m,n) {
				if(typeof n =="object") {
				// 	html1+="<div class='list-group-item list-file' data-path='"+n.path+"'><a href='#'><i class='glyphicon glyphicon-file'></i>"+n.title+"</a><input type='checkbox' name='selectFile' class='pull-right' />"+
				// 	    "<div class='list-file-details'>"+
    //     			        "<div class='page'>/"+n.name+"</div>"+
    //     			        "<div>Access : "+n.access+"</div>"+
    //     			        "<div>Layout : "+n.template+"</div>"+
    //     			    "</div>"+
				// 	    "</div>";
					html+="<div class='list-group-item list-file' data-path='"+n.path+"'><a href='#'><i class='glyphicon glyphicon-file'></i>"+n.title+"</a><input type='checkbox' name='selectFile' class='pull-right' />"+
					    "<div class='list-file-details'>"+
					        "<span class='pull-right' title='Page Status'>"+getBoolIcon(n.enabled)+"</span>"+
        			        "<div class='page'>/"+n.name+((n.slug)?"/["+n.slug+"]":"")+"</div>"+
        			        "<div><label>Access :</label> "+n.access+"</div>"+
        			        "<div><label>Layout :</label> "+n.template+"</div>"+
        			    "</div>"+
					    "</div>";
				}
			});
// 			html1+="</div>";
		} else {
			html+="<div class='list-group-item list-file' data-path='"+v.path+"'><a href='#'><i class='glyphicon glyphicon-file'></i>"+v.title+"</a><input type='checkbox' name='selectFile' class='pull-right' />"+
			    "<div class='list-file-details'>"+
			        "<span class='pull-right' title='Page Status'>"+getBoolIcon(v.enabled)+"</span>"+
			        "<div class='page'>/"+v.name+((v.slug)?"/["+v.slug+"]":"")+"</div>"+
			        "<div><label>Access :</label> "+v.access+"</div>"+
			        "<div><label>Layout :</label> "+v.template+"</div>"+
			    "</div>"+
			    "</div>";
		}
	});
	$("#componentSpace").html(html);//html1

	$("#componentSpace .list-folder>a").each(function() {
		nx=$(this).closest(".list-folder").next().find(".list-file").length;
		$(this).append(" <span class='badge pull-right'>"+nx+"</span>");
	});
}

function renderTable(fs) {
	html="<div><table class='table table-hover table-bordered table-condensed'>";
	html+="<thead><tr>";
	
	switch(lastComponent) {
	    case "pages":
	        html+="<th width=50px>SL#</th>";
    		html+="<th width=250px>Title</th>";
    		html+="<th class='source'>URI</th>";
    		html+="<th class='template'>Template</th>";
    		html+="<th class='access'>Access</th>";
    		html+="<th width=50px>Enabled</th>";
    		html+="<th width=50px>Status</th>";
    		html+="<th width=50px>Locked</th>";
    		html+="<th width=150px></th>";
	        break;
	    default:
	        html+="<th width=50px>SL#</th>";
    		html+="<th width=250px>Title</th>";
    		html+="<th>Source</th>";
    		html+="<th width=50px>Status</th>";
    		html+="<th width=50px>Locked</th>";
    		html+="<th width=150px></th>";
	}

	html+="</tr></thead>";
	html+="<tbody>";
	
	htmlFolders="";
	htmlFiles="";
	
	$.each(fs,function(k,v) {
		kx=md5(k);
		if(v.folder) {
			htmlFolders+="<tr class='list-folder' id='item-"+kx+"' data-folder='"+k+"'>";
			htmlFolders+="<td class='text-center'></td>";
			htmlFolders+="<td colspan=10><a><i class='glyphicon glyphicon-folder-close'></i>&nbsp;"+k+"</a></td>";
			htmlFolders+="<tr>";
			$.each(v,function(m,n) {
				if(typeof n =="object") {
					kx=md5(m);
					htmlFolders+="<tr class='list-file' id='item-"+kx+"' data-path='"+n.path+"' data-folder='"+k+"'>";
					htmlFolders+="<td class='text-center'><input type='checkbox' name='selectFile' /></td>";
					htmlFolders+="<td class='folder'><a class='fname' href='#'><i class='glyphicon glyphicon-file'></i>&nbsp;"+n.title+"</a></td>";
					htmlFolders+="<td>/"+n.name+((n.slug)?"/["+n.slug+"]":"")+"</td>";
					if(lastComponent=="pages") {
        			    htmlFolders+="<td>"+n.template+"</td>";
        			    htmlFolders+="<td>"+n.access+"</td>";
        			    htmlFolders+="<td class='text-center'>"+getBoolIcon(n.enabled)+"</td>";
        			}
					htmlFolders+="<td class='text-center'>"+getStatusIcon(n.status)+"</td>";
					htmlFolders+="<td class='text-center'>"+getBoolIcon(n.locked)+"</td>";
					htmlFolders+="<td class='action text-right'>"+getActions(n)+"</td>";
					htmlFolders+="<tr>";
				}
			});
		} else {
			htmlFiles+="<tr class='list-file' id='item-"+kx+"' data-path='"+v.path+"'>";
			htmlFiles+="<td class='text-center'><input type='checkbox' name='selectFile' /></td>";
			htmlFiles+="<td><a class='fname' href='#'><i class='glyphicon glyphicon-file'></i>&nbsp;"+v.title+"</a></td>";
			htmlFiles+="<td>/"+v.name+((v.slug)?"/["+v.slug+"]":"")+"</td>";
			if(lastComponent=="pages") {
			    htmlFiles+="<td>"+v.template+"</td>";
			    htmlFiles+="<td>"+v.access+"</td>";
			    htmlFiles+="<td class='text-center'>"+getBoolIcon(v.enabled)+"</td>";
			}
			htmlFiles+="<td class='text-center'>"+getStatusIcon(v.status)+"</td>";
			htmlFiles+="<td class='text-center'>"+getBoolIcon(v.locked)+"</td>";
			htmlFiles+="<td class='action text-right'>"+getActions(v)+"</td>";
			htmlFiles+="<tr>";
		}
	});
	html+=htmlFolders;
	html+=htmlFiles;
	html+="</tbody>";
	html+="</table></div>";
		
	$("#componentSpace").html(html);
}

function renderKanaban(fs) {
	
}

function getStatusIcon(v) {
	if(typeof(v)!="boolean") {
		if(v==null || v.toLowerCase()=="true" || v.toLowerCase()=="na") {
			v=true;
		} else {
			v=false;
		}
	}
	if(v) return "<i class='fa fa-check'></i>";
	else return "<i class='fa fa-times'></i>";
}
function getBoolIcon(v) {
	if(typeof(v)!="boolean") {
		if(v==null || v.toLowerCase()=="true") {
			v=true;
		} else {
			v=false;
		}
	}
	if(v) return "<i class='fa fa-check'></i>";
	else return "<i class='fa fa-times'></i>";

}
function getActions(v) {
	htmlX="";
	title="Me";
	switch(v.type) {
		case "pages":
			title=" Page";
		break;
		case "comps":
			title=" Component";
		break;
		case "layout":
			title=" Layout";
		break;
	}
	if(v.type=="pages") {
		htmlX+="<i class='fa fa-eye pull-left' cmd='preview' title='Preview'></i>";
	}
	htmlX+="<i class='fa fa-copy pull-left' cmd='clone' title='Clone "+title+"'></i>";
	htmlX+="<i class='fa fa-terminal pull-left' cmd='rename' title='Rename "+title+"'></i>";

	if(v.type=="comps") {
		htmlX+="<i class='fa fa-file-code-o' cmd='editcode' title='Edit "+title+" PHP Code'></i>";
		htmlX+="<i class='fa fa-file-text-o' cmd='edit' title='Edit "+title+" Template'></i>";
	} else {
		htmlX+="<i class='fa fa-pencil' cmd='edit' title='Edit "+title+"'></i>";
	}
	return htmlX;
}

