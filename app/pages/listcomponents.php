<?php
if(!defined('ROOT')) exit('No direct script access allowed');

if(!isset($_REQUEST["forsite"])) {
	dispErrMessage("HostSite Is Required But Not Found","CMS Error",400);
	exit();
}
user_admin_check(true);
checkUserSiteAccess($_REQUEST['forsite'],true);

loadModule("page");

loadModule("editor");
loadEditor("codemirror");

$btns=array();
$btns[sizeOf($btns)]=array("title"=>"Reload","icon"=>"reloadicon","tips"=>"Load Component List","onclick"=>"reloadList()");
$btns[sizeOf($btns)]=array("title"=>"Create","icon"=>"addicon","tips"=>"Create New Component","onclick"=>"createComponent()");
$btns[sizeOf($btns)]=array("bar"=>"&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;");
$btns[sizeOf($btns)]=array("bar"=>"&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;");
$btns[sizeOf($btns)]=array("bar"=>"&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;");
$btns[sizeOf($btns)]=array("title"=>"Clear","icon"=>"clearicon","tips"=>"Clear Editor And Reset Changes","onclick"=>"resetEditor();");
$btns[sizeOf($btns)]=array("title"=>"Save","icon"=>"saveicon","tips"=>"Save Component","onclick"=>"saveComponent(currentComp);");

$layout="apppage";
$params=array("toolbar"=>$btns,"contentarea"=>"printContent");

printPageContent($layout,$params);

function printContent() {
?>
<style>
.tabPage {
	width:100%;height:500px;
	overflow:auto;
	padding:0px !important;margin:0px !important;
}
input[type=text] {
	border:1px solid #aaa;
	width:95%;
}
select {
	width:95%;
	height:22px;
}
.minibtn {
	width:0px;height:20px;
}
table tr,table tr td {
	border:0px;
}
.loading {
	width:100%;height:100%;
}
.CodeMirror-scroll {
	font-size:13px;
}
</style>
<div style='width:20%;height:100%;float:left;overflow:auto;'>
	<table id=componentList class='datatable' width=100% border=0 cellpadding=3 cellspacing=0 style='margin:0px;'>
		<tbody>
		</tbody>
	</table>
</div>
<div id=editPage style='width:80%;height:100%;float:right;overflow:hidden;'>
	<textarea id=richtextarea style='width:99%;height:68%;border:0px;'></textarea>
</div>

<div style='display:none'>
</div>
<script language=javascript>
currentComp=null;
lnk=getServiceCMD("blocks.components");
listBtns="<div title='delete' class='right deleteicon minibtn'></div><div title='Clone Component' class='right cloneicon minibtn'></div><div title='Edit/View Code' class='right viewicon minibtn'></div>";
$(function() {
	$("#richtextarea").css("height",($(window).height()-$("#toolbar").height()));
	loadCodeEditor("richtextarea","php");

	$("#componentList tbody").delegate("tr","dblclick", function() {
			id=$(this).attr('rel');
			editComponent(id);
		});
	$("#componentList tbody").delegate(".minibtn","click",function() {
			id=$(this).parents("tr").attr('rel');
			if(id==null || id<0) return;
			if($(this).hasClass('deleteicon')) {
				deleteComponent(id);
			} else if($(this).hasClass('editicon')) {
				editComponent(id);
			} else if($(this).hasClass('viewicon')) {
				editComponent(id);
			} else if($(this).hasClass('cloneicon')) {
				cloneComponent(id);
			}
		});
	reloadList();
});
function reloadList() {
	resetEditor();
	l=lnk+"&action=list";
	$("#componentList tbody").html("<tr><td class='ajaxloading3'><br/><br/><br/><br/>Listing ...</td></tr>");
	$("#loadingmsg").show();
	$("#componentList tbody").load(l, function() {
			html=listBtns;
			$("#componentList tbody td:last-child").append(html);
			$("#loadingmsg").hide();
		});
}
function editComponent(p) {
	if(p==null || p.length<=0) return;
	currentComp=p;
	resetEditor();
	$("#componentList tr.active").removeClass("active");

	l=lnk+"&action=data&comp="+p;
	$("#loadingmsg").show();
	processAJAXQuery(l,function(data) {
			editor.setValue(data);
			editor.setOption("readOnly", false);

			$("#componentList tr[rel='"+p+"']").addClass("active");

			$("#loadingmsg").hide();
		});
}
function saveComponent(p) {
	if(p==null || p.length<=0) {
		lgksAlert("Nothing To Edit Or Save.");
		return;
	}

	l=lnk+"&action=save&comp="+p;
	q="&data="+encodeURIComponent(editor.getValue());

	$("#loadingmsg").show();
	processAJAXPostQuery(l,q,function(data) {
			if(data.length>0) {
				if(typeof lgksToast=="function") lgksToast(data,{position: "top-right"});
				else lgksAlert(data);
			}
			$("#loadingmsg").hide();
		});
}

function deleteComponent(p) {
	if(p==null || p.length<=0) {
		lgksAlert("Nothing To Delete.");
		return;
	}
	lgksConfirm("Are you Sure about deleting the components. <br/>They might be in use by some pages.<br/><div style='width:100%;height:70px;border:1px solid #aaa;overflow:auto;'><h3>"+p+"</h3></div><br/>Deleting them will not remove them from Menus though.",
				"Delete Component",function() {
						l=lnk+"&action=delete&comp="+p;
						$("#loadingmsg").show();
						resetEditor();
						processAJAXQuery(l,function(data) {
								if(data.length>0) lgksAlert(data);
								else $("#componentList tr[rel='"+p+"']").detach();
								$("#loadingmsg").hide();
							});
				});
}

function cloneComponent(p) {
	if(p==null || p.length<=0) {
		lgksAlert("Nothing To Delete.");
		return;
	}
	lgksPrompt("Please give a title for the new Component!<br/>No Space In Name Is Allowed.","Clone Component !",null,function(txt) {
			if(txt!=null && txt.length>0) {
				l=lnk+"&action=clone&comp="+p+"&newComp="+txt;
				$("#loadingmsg").show();
				processAJAXQuery(l,function(data) {
						if(data.length>0) lgksAlert(data);
						else {
							html="<tr rel='"+txt+".php' class='okicon'>";
							html+="<td style='padding-left:25px'>";
							html+=txt+listBtns;
							html+="</td></tr>";
							$("#componentList tbody").append(html);
						}
						$("#loadingmsg").hide();
					});
			}
		});
}


function createComponent() {
	lgksPrompt("Please give a title for the new Component!<br/>No Space In Name Is Allowed.","New Component !",null,function(txt) {
			if(txt!=null && txt.length>0) {
				l=lnk+"&action=blank&comp="+txt.split(" ").join("_");
				$("#loadingmsg").show();
				processAJAXQuery(l,function(data) {
						if(data.length>0) lgksAlert(data);
						else {
							html="<tr rel='"+txt+".php' class='okicon'>";
							html+="<td style='padding-left:25px'>";
							html+=txt+listBtns;
							html+="</td></tr>";
							$("#componentList tbody").append(html);
						}
						$("#loadingmsg").hide();
					});
			}
		});
}

function resetEditor() {
	editor.setValue("");
	editor.setOption("readOnly", true);
}
</script>
<?php
}
?>

