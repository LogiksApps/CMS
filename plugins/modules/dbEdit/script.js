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

	pgDbInfo();
	loadTableList('pages');
});

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
	
	processAJAXQuery(_service("dbEdit","dbList")+"&comptype="+lastComponent,function(txt) {
		fs=txt.Data;
		if(fs==null || fs.length<=0) {
			$("#componentTree").html("");
			return;
		}
		html="";html1="";
		$.each(fs,function(k,v) {
			if(v.length<=0) return;
			kx=md5(k);
			
			html1+="<div class='list-group-item list-folder'><a href='#item-"+kx+"' data-toggle='collapse'><i class='glyphicon glyphicon-folder-close'></i>&nbsp;&nbsp;"+toTitle(k)+"</a></div>";
			html1+="<div class='list-group-folder collapse' id='item-"+kx+"'>";
			$.each(v,function(m,n) {
				if(typeof n =="object") {
					html1+="<div class='list-group-item list-file' data-schema='"+k+"/"+n.tbl+"'><a href='#'><i class='fa fa-table'></i>&nbsp;&nbsp;"+n.tbl+"</a><input type='checkbox' name='selectFile' class='pull-right' /></div>";
				} else {
					html1+="<div class='list-group-item list-file' data-schema='"+k+"/"+n+"'><a href='#'><i class='fa fa-table'></i>&nbsp;&nbsp;"+n+"</a><input type='checkbox' name='selectFile' class='pull-right' /></div>";
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
	lx=_service("dbEdit","panel")+"&panel=dbTable&src="+tblPath;
	$("#pgcontent").load(lx);

	$("#pgtoolbar .navbar-right>li.active").removeClass("active")
	$($("#pgtoolbar .navbar-right>li")[1]).addClass("active");
}

function pgRefresh() {
	//window.document.location.reload();

	loadTableList('pages');
}

function pgDbInfo() {
	switchPanel(0);

	lx=_service("dbEdit","panel")+"&panel=dbInfo";
	$("#pgcontent").load(lx);
}

function pgTableQuery() {
	switchPanel(2);

	lx=_service("dbEdit","panel")+"&panel=dbQuery";
	$("#pgcontent").load(lx);
}
function pgDbTools() {
	switchPanel(3);

	lx=_service("dbEdit","panel")+"&panel=dbTools";
	$("#pgcontent").load(lx);
}
function pgSearch(qS) {
	switchPanel(-1);

	lx=_service("dbEdit","panel")+"&panel=dbSearch&q="+qS+"&src="+openTableScehema;
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
			lx=_service("dbEdit","deleteTable");
			processAJAXPostQuery(lx,q,function(dts) {
				pgRefresh();
			});
		}
	});
}
function switchPanel(nx) {
	if(typeof saveQueryLocal=="function") {
		saveQueryLocal();
	}

	$("#pgtoolbar .navbar-right>li.active").removeClass("active")
	$($("#pgtoolbar .navbar-right>li")[nx]).addClass("active");
}