var currentSRC=null;
$(function() {
	currentSRC=$("#pgtoolbar .nav.navbar-right>li.active>a").data('cmd');

	$("#pgtoolbar .onRowSelect").hide();
});

function pgUsers() {
	window.location=_link(PAGE)+"&panel=users";
}
function pgAccess() {
	window.location=_link(PAGE)+"&panel=access";
}
function pgPrivileges() {
	window.location=_link(PAGE)+"&panel=privileges";
}

function pgRefresh() {
	$.each(LGKSReportsInstances,function(k,v) {
		v.datagridAction("refresh");
	});
}
function pgReload() {
	window.location.reload();
}

function pgCreateNew() {
	$("#credsEditor").attr("src",_link("modules/credsEditor/"+currentSRC+"/new"));
}

function pgTrash() {
	if($(".dataTable tbody .rowSelector input[name=rowSelector]:checked").length<=0) {
		lgksToast("Please select atleast one record to delete.");
		return;
	}
	q0=[];
	q1=[];
	$(".dataTable tbody .rowSelector input[name=rowSelector]:checked").each(function() {
		tr=$(this).closest("tr");
		q0.push(tr.find("td:eq(2)").data('value'));
		q1.push(tr.find("td[data-key=id]").data("value"));
	});

	lgksConfirm("Are you sure about deleting the following ("+q1.length+") "+currentSRC+"?<br><div class='paddedBox'>"+q0.join("<br>")+"</div>","",function(ans) {
		if(ans) {
			processAJAXPostQuery(_service("credsMaster","delete"),"&src="+currentSRC+"&q="+q1.join(","),function(txt) {
				if(txt!="done") {
					lgksToast(txt);
				}
				pgRefresh();
			});
		}
	});
}

function editRecord(tr,grid) {
	trID=tr.find("td[data-key=id]").data("value");
	trHash=tr.data('hash');
	
	$("#credsEditor").attr("src",_link("modules/credsEditor/"+currentSRC+"/"+trHash));
}
// function cloneRecord(tr,grid) {
// 	trID=tr.find("td[data-key=id]").data("value");
// 	trHash=tr.data('hash');

// 	$("#credsEditor").attr("src",_link("modules/credsEditor/"+currentSRC+"/clone/"+trHash));
// }


function openSidePanel() {
	$("#sliderPanel").removeClass("slide-out").addClass("slide-in");
}
function closeSidePanel() {
	pgRefresh();
	$("#sliderPanel").removeClass("slide-in").addClass("slide-out");
}