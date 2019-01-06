currentLog=null;
$(function() {
	if($("#pgtoolbar a[data-drop]").length>0) {
		dx=$($("#pgtoolbar a[data-drop]")[0]).data("drop");
		pgLoggers($("#pgtoolbar a[data-drop]")[0], dx);
	}
	$("#componentTree").delegate(".list-group-item[data-path] a","click",function() {
		pth=$(this).closest(".list-group-item[data-path]").data("path");
		loadLog(pth,currentLog);
	});
});
function pgRefresh() {
	pgLoggers($("#pgtoolbar a[data-drop]")[0], currentLog);
}
//Load Logger
function pgLoggers(ele, src) {
	$("#logDataTable").html("<tr><td colspan=100><h2 align=center>Please load a log resource.</h2></td></tr>");
	$("#componentTree").html("<div class='ajaxloading5'></div>");

	currentLog=src;
	lx=_service("logBook","listLog")+"&src="+src;
	processAJAXQuery(lx,function(txt) {
		try {
			json=$.parseJSON(txt);
			html="";
			$.each(json.Data.list,function(k,v) {
				ttl=v.replace(".log","");
				ttl=ttl.split("-");
				if(ttl.length>3) {
					ttl=ttl.splice(1);
					ttl=ttl.join("/");
				}
				html+="<div class='list-group-item list-file' data-path='"+v+"'><a href='#'><i class='glyphicon glyphicon-file'></i>"+ttl+"</a><input type='checkbox' name='selectFile' class='pull-right' /></div>";
			});
			$("#componentTree").html(html);
		} catch(e) {
			console.error(e);
		}
	});
}
function loadLog(srcFile,srcLog) {
	$("#logDataTable").closest("table").find("caption").html(srcLog.toUpperCase()+" LOG :: "+srcFile);
	$("#logDataTable").html("<tr><td colspan=100><br><br><div class='ajaxloading'></div></td></tr>");

	lx=_service("logBook","loadLog")+"&src="+srcLog+"&file="+srcFile;
	processAJAXQuery(lx,function(txt) {
		$("#logDataTable").html(txt);
	});
}
function pgDownload() {
	if($("#componentTree").find("input[type=checkbox][name=selectFile]:checked").length!=1) return;
	file=$($("#componentTree").find("input[type=checkbox][name=selectFile]:checked")[0]).closest(".list-file").data("path");

	$("#componentTree").find("input[type=checkbox][name=selectFile]:checked");
	lx=_service("logBook","downloadLog")+"&src="+currentLog+"&file="+file;
	window.open(lx);
}
function pgTrash() {
	if($("#componentTree").find("input[type=checkbox][name=selectFile]:checked").length<=0) return;

	q=[];
	$("#componentTree").find("input[type=checkbox][name=selectFile]:checked").each(function() {
		q.push($(this).closest(".list-file").data("path"));
	});
	if(q.length<=0) return;

	$("#logDataTable").html("<tr><td colspan=100><br><br><div class='ajaxloading'></div></td></tr>");

	lx=_service("logBook","deleteLog");
	processAJAXPostQuery(lx,"&src="+currentLog+"&file="+q.join(","),function(txt) {
		//lgksToast(txt);
		pgLoggers($("#pgtoolbar a[data-drop]")[0], currentLog);
	});
}