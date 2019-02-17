var currentSRC=null;
var PAGE="modules/credsManager";
var refreshNeeded=false;
$(function() {
	currentSRC=$("#pgtoolbar .nav.navbar-right>li.active>a").data('cmd');

	$("#pgtoolbar .onRowSelect").hide();
});

function pgUsers() {
	window.location=_link(PAGE+"/users")+"&panel=users";
}
function pgAccess() {
	window.location=_link(PAGE+"/access")+"&panel=access";
}
function pgPrivileges() {
	window.location=_link(PAGE+"/privileges")+"&panel=privileges";
}
function pgGroups() {
	window.location=_link(PAGE+"/groups")+"&panel=groups";
}
function pgGuid() {
	window.location=_link(PAGE+"/guid")+"&panel=guid";
}
function pgRefresh() {
	refreshNeeded=false;
	$.each(LGKSReportsInstances,function(k,v) {
		v.datagridAction("refresh");
	});
}
function pgReload() {
	window.location.reload();
}

function pgCreateNew() {
	refreshNeeded=true;
	$("#credsEditor").attr("src",_link("modules/credsManager/"+currentSRC+"/new"));
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
			processAJAXPostQuery(_service("credsManager","delete"),"&src="+currentSRC+"&q="+q1.join(","),function(txt) {
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
	
	waitingDialog.show('Loading Editor ...');
	$("#credsEditor").attr("src",_link("modules/credsManager/"+currentSRC+"/edit/"+trHash));
}
function editPWD(tr,grid) {
	trID=tr.find("td[data-key=id]").data("value");
	trHash=tr.data('hash');
	
	waitingDialog.show('Loading Password Manager ...');
	$("#credsEditor").attr("src",_link("modules/credsManager/"+currentSRC+"/pwd/"+trHash));
}
function infoRecord(tr,grid) {
	trID=tr.find("td[data-key=id]").data("value");
	trHash=tr.data('hash');
	
	waitingDialog.show('Loading User Details ...');
	$("#credsEditor").attr("src",_link("modules/credsManager/"+currentSRC+"/userinfo/"+trHash));
}
// function cloneRecord(tr,grid) {
// 	trID=tr.find("td[data-key=id]").data("value");
// 	trHash=tr.data('hash');

// 	$("#credsEditor").attr("src",_link("modules/credsManager/"+currentSRC+"/clone/"+trHash));
// }
function viewUsers(tr,grid) {
	trID=tr.find("td[data-key=id]").data("value");
	trHash=tr.data('hash');
	
	waitingDialog.show('Finding Users ...');//,{onHide: function () {alert('Callback!');}}
	$("#credsEditor").attr("src",_link("modules/credsManager/"+currentSRC+"/listusers/"+trHash));
}

function openSidePanel() {
	waitingDialog.hide();
	$("#sliderPanel").removeClass("slide-out").addClass("slide-in");
}
function closeSidePanel() {
	if(refreshNeeded) pgRefresh();
	$("#sliderPanel").removeClass("slide-in").addClass("slide-out");
}