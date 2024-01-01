var lastDBMS=null;
var openTableScehema=null;
var currentDBQueryPanel="structure";

$(function() {
	$("#pgtoolbar .onsidebarSelect").hide();
	$($("#pgtoolbar .navbar-right>li")[0]).addClass("active");
	
	$('#componentTree').delegate(".list-group-item input[name=selectFile]","change",function() {
		listItemAttr();
	});

	$('#componentTree').delegate(".list-group-item.list-file a","click",function() {
		$('#componentTree .list-group-item.active').removeClass('active');

		tag=$(this).closest(".list-group-item");
		if(lastComponent=="pages") {
			tag.addClass('active');
		}

		listItemAttr();

		schema=tag.data('schema');
		title=$(this).text();
		openTable(schema);
	});

	$("#pgtoolbar form#pgToolbarSearch").submit(function() {
		sx=$(this).find("input[type=text]").val();
		pgSearch(sx);
		return false;
	});

	$("#toolbtn_changeDatabase li>a[data-drop='"+dkey+"']").addClass("active");
	
	initEvents();
	
	if(preloadSrc==null || preloadSrc.length<=2) {
	    pgDbInfo();
	} else {
	    $("#pgcontent").html("<div align=center><br><br><br><div class='ajaxloading ajaxloading5'></div></div>");
	    openTable(preloadSrc);
	}
	
	loadTableList('pages');
});
function initEvents() {
	$("#pgcontent").delegate(".searchContent td.action i[cmd]","click",function(e) {
		cmd=$(this).attr('cmd');
		selectedRecord=$(this).closest("tr");
		
		switch(cmd) {
			case "deleteRecord":
				lgksConfirm("Are sure about deleting the selected record?","Delete Record!",function(txt) {
					key=$(selectedRecord).data("key");
					col=$(selectedRecord).data("col");
					src=$(selectedRecord).closest("#dataContent").data("src");
					if(src==null || src.length<=0) return;
					if(txt) {
						q=col+"="+key;
						lx=_service("dbEdit","deleteRecord")+"&dkey="+dkey+"&src="+src;
						processAJAXPostQuery(lx,q,function(txt) {
							if(txt=="success") {
								loadDataContent(currentDBQueryPanel);
								lgksToast("Data deleted successfully");
							} else {
								lgksToast(txt);
							}
						});
					}
				})
			break;
			case "editRecord":
				key=$(selectedRecord).data("key");
				col=$(selectedRecord).data("col");
				src=$(selectedRecord).closest("#dataContent").data("src");
				if(src==null || src.length<=0) return;
				if(col=="id") {
					loadDataContent("edit","&src="+src+"&refid="+key);
				} else {
					lgksToast("Sorry, editing is supported only if the table has ID column.");
				}
			break;
		}
	});
}

function listItemAttr() {
	if($('#componentTree .list-group-item input[name=selectFile]:checked').length>0) {
		$("#pgtoolbar .onsidebarSelect").show();

		if($('#componentTree .list-group-item input[name=selectFile]:checked').length>1) {
			$("#pgtoolbar .onOnlyOneSelect").hide();
		}
	} else {
		$("#pgtoolbar .onsidebarSelect").hide();
	}
	if($('#componentTree .list-group-item.active').length>0) {
		$("#pgtoolbar .onsidebarActive").show();
	} else {
		$("#pgtoolbar .onsidebarActive").hide();
	}
}

function loadTableList(comp) {
	lastComponent=comp;
	$("#componentTree").html("<div class='ajaxloading5'></div>");
	
	processAJAXQuery(_service("dbEdit","dbList")+"&dkey="+dkey+"&comptype="+lastComponent,function(txt) {
		fs=txt.Data;
		if(fs==null || fs.length<=0) {
			$("#componentTree").html("");
			return;
		}
		html="";html1="";
		$.each(fs,function(k,v) {
			if(v.length<=0) return;
			kx=md5(k);
			
			html1+="<div class='list-group-item list-folder'><a href='#item-"+kx+"' data-toggle='collapse'><i class='glyphicon glyphicon-folder-close'></i>"+toTitle(k)+"</a></div>";
			html1+="<div class='list-group-folder collapse' id='item-"+kx+"'>";
			$.each(v,function(m,n) {
				if(typeof n =="object") {
					html1+="<div class='list-group-item list-file' data-schema='"+k+"/"+n.tbl+"' title='"+n.tbl+"'><a href='#'><i class='fa fa-table'></i><span class='text'>"+n.tbl+"</span></a><input type='checkbox' name='selectFile' class='pull-right' /></div>";
				} else {
					html1+="<div class='list-group-item list-file' data-schema='"+k+"/"+n+"' title='"+n+"'><a href='#'><i class='fa fa-table'></i><span class='text'>"+n+"</span></a><input type='checkbox' name='selectFile' class='pull-right' /></div>";
				}
			});
			html1+="</div>";
		});
		$("#componentTree").html(html+html1);

		if($("#pageEditor").attr("src")!=null) {
			$('#componentTree .list-group-item[data-schema="'+$("#pageEditor").attr("src")+'.json"]').addClass("active")
		}
		listItemAttr();
	},"json");
}

function openTable(tblPath) {
	openTableScehema=tblPath;
	lx=_service("dbEdit","panel")+"&dkey="+dkey+"&panel=dbTable&src="+tblPath;
	$("#pgcontent").load(lx);

	$("#pgtoolbar .navbar-right>li.active").removeClass("active")
	$($("#pgtoolbar .navbar-right>li")[1]).addClass("active");
}

function pgRefresh() {
	//window.document.location.reload();

	loadTableList('pages');
	pgDbInfo();
}

function pgDbInfo() {
	switchPanel(0);

	lx=_service("dbEdit","panel")+"&dkey="+dkey+"&panel=dbInfo";
	$("#pgcontent").load(lx);
}

function pgTableQuery() {
	switchPanel(2);

	lx=_service("dbEdit","panel")+"&dkey="+dkey+"&panel=dbQuery";
	$("#pgcontent").load(lx);
}
function pgDbTools() {
	switchPanel(3);

	lx=_service("dbEdit","panel")+"&dkey="+dkey+"&panel=dbTools";
	$("#pgcontent").load(lx);
}
function pgSearch(qS) {
	switchPanel(-1);
	$("#pgcontent").html("<div class='text-center'><br><br><i class='fa fa-spinner fa-spin fa-4x'></i></div>");

	lx=_service("dbEdit","panel")+"&dkey="+dkey+"&panel=dbSearch&q="+qS+"&src="+openTableScehema;
	$("#pgcontent").load(lx);
}
function pgTrash() {
	if($('#componentTree .list-group-item input[name=selectFile]:checked').length<=0) {return false;}
	
	lgksConfirm("Do you want to delete selected tables?","Delete!",function(ans) {
		if(ans) {
			q=[];
			$('#componentTree .list-group-item input[name=selectFile]:checked').each(function() {
				q.push($(this).closest(".list-group-item").data("schema"));
			});
			q="&src="+q.join(",");
			lx=_service("dbEdit","deleteTable")+"&dkey="+dkey;
			processAJAXPostQuery(lx,q,function(dts) {
				pgRefresh();
			});
		}
	});
}

function changeDatabase() {
	dkey = $("#toolbtn_changeDatabase li>a.active").data("drop");
	$("#toolbtn_changeDatabase .btn").text($("#toolbtn_changeDatabase li>a.active").text())
	
	pgDbInfo();
	loadTableList('pages');
}

function switchPanel(nx) {
	if(typeof saveQueryLocal=="function") {
		saveQueryLocal();
	}

	$("#pgtoolbar .navbar-right>li.active").removeClass("active")
	$($("#pgtoolbar .navbar-right>li")[nx]).addClass("active");
}

function exportData() {
  parent.openLinkFrame("Export Data",_link("modules/dataMigrator")+"&panel=export",true)
}

function importData() {
  parent.openLinkFrame("Import Data",_link("modules/dataMigrator")+"&panel=import",true)
}

function saveSchema() {
    processAJAXQuery(_service("dbEdit","dumpSchema")+"&dkey="+dkey,function(txt) {
		lgksAlert(txt);
	});
}
function migrateSchema() {
    parent.openLinkFrame("Migrator", _link("modules/migrator"), true);
}

//Tables,Triggers,Functions,etc.
function createNew() {
	lx=_service("dbEdit","panel")+"&dkey="+dkey+"&panel=create";
	$("#pgcontent").load(lx);
}

function createView() {
    query = $("#queryText").val();
    if(query==null || query.length<=0) {
        lgksAlert("Select Query Not Found");
        return;
    }
    
    queryType = query.toUpperCase().split(" ")[0];
    if(queryType!="SELECT") {
        lgksAlert("View can be generated only from a Select Query");
        return;
    }
    
    lx=_service("dbEdit","panel")+"&dkey="+dkey+"&panel=create_view";
	$("#pgcontent").load(lx, function(data) {
	    $("#view_query").val(query);
	});
}
