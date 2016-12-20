$(function() {
	pgRefresh();
});
function pgRefresh() {
	$("#domainTable").load(_service("domainManager","listall"),function() {
		$("input[type='checkbox'].switch","#domainTable tbody").bootstrapSwitch();
	});
}
function pgCreateNew() {
	$("#domainTable tbody").append($("#domainTable tfoot").html());
	$("#domainTable tbody tr[data-rowkey='NA']").attr('data-rowkey',md5(Math.ceil(Math.random()*1000000000)));
	$("input[type='checkbox'].switch","#domainTable tbody").bootstrapSwitch();
	showAlert();
}
function pgTrash() {
	row=$("input[type=radio][name=rowSelector]:checked","#domainTable tbody");
	if(row.length>0) {
		row.closest("tr").detach();
		pgSave();
	}
}

function pgSave() {
	q=[];
	$("#domainTable tbody tr").each(function() {
		tr=$(this);
		key=tr.data('rowkey');
		q.push(key+"[host]="+tr.find("[name=host]").val());
		q.push(key+"[appsite]="+tr.find("[name=app]").val());
		q.push(key+"[active]="+tr.find("[name=active]").is(':checked'));
	});
	processAJAXPostQuery(_service("domainManager","save"),q.join("&"),function(txt) {
		if(txt=="done") {
			lgksToast("Domain Map Updated");

			pgRefresh();
		} else {
			lgksToast(txt);
		}
	});

	$("#pgworkspace .alert").detach();
}

function showAlert() {
	if($("#pgworkspace .alert").length>0) return;

	$("#domainTable").parent().append("<div class='alert alert-warning alert-dismissible' style='margin:30px;'>Remember to save before leaving."+
				"<button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times;</span></button>"+
				"</div>");
}