<?php
if(!defined('ROOT')) exit('No direct script access allowed');
session_check(true);
user_admin_check();

loadModule("page");

$params["toolbar"]="printToolbar";
$params["contentarea"]="printContent";

$layout="apppage";

_js(array("jquery.multiselect"));
_css(array("jquery.multiselect"));

printPageContent("apppage",$params);

//<button onclick="$('#infoBox').dialog({resizable:false,width:600,height:'auto',modal:true,});" style='width:100px;' ><div class='infoicon'>About</div></button>
function printToolbar() { ?>
<button onclick="reloadTable()" style='width:100px;' ><div class='reloadicon'>Reload</div></button>
||
<button onclick="createContent()" title="Create Content" style="width:100px;" ><div class='addicon'>Create</div></button>
<?php
}
function printContent() {
	$webPath=getWebPath(__FILE__);
?>
<style>
.minibtn {
	padding-left:0px;
}
h5,h6 {
	margin:0px;
	padding:0px;
	color:#888;
}
input {
	border:1px solid #aaa;
	width:95%;height:22px;
}
select {
	width:95%;height:24px;
}
textarea {
	border:1px solid #aaa;
	width:94%;
	resize:none;
}
input[type=checkbox] {
	width:25px;
}
table tr, table td {
	border:0px;
}
table th {
	text-align:center;
}
#page .toolbar>.left {
	padding-top:0px;
}
</style>
<table id=contentTable class='datatable' width=99% cellpadding=2 cellspacing=0 border=0 style='margin:5px;border:2px solid #aaa;margin-bottom:50px;'>
	<thead>
		<tr class='ui-widget-header'>
			<th width=100px>GID</th>
			<th>Title</th>
			<th>Continent</th>
			<th width=40px>Blocked</th>
			<th width=130px>Created</th>
			<th width=130px>Edited</th>
			<th width=30px>&nbsp;</th>
			<!--<th width=50px>Language</th>
			<th>Author</th>
			<th width=70px>Till Date</th>
			<th width=25px>Blocked</th>
			<th width=70px>--A--</th>
			-->
		</tr>
	</thead>
	<tbody>

	</tbody>
</table>
<div style='display:none;'>
	<div id=infoBox title='About Version Control' >
		<!--<b>App Version Control</b> is used to control the Integration Of Version Control Systems aka Revision control With your app-site <b><?=strtoupper($_REQUEST["forsite"])?></b>.
		<p>
			<b>What is Version Control Systems aka Revision control?</b><br/>
			Revision control, also known as version control and source control (and an aspect of software configuration management),
			is the management of changes to documents, computer programs, large web sites, and other collections of information.
		</p>
		<p>
			<b>More info on Revision control.</b><br/>
			Please visit wikipedia site <a href='http://en.wikipedia.org/wiki/Revision_control'>Revision Control</a> for more information on
			Revision control.
		</p>
		<p>
			<b>Which Revision Control Systems are supported?</b><br/>
			As of now Git is supported. For more info in please visit corrosponding wikipedia link <a href='http://en.wikipedia.org/wiki/Git_(software)'>Git_(Software)</a>.
		</p>
		<p>
			<b>Any dependency?</b><br/>
			The supported Revision Control System is enabled only if you have a working copy of the Binaries installed on your system.
		</p>
		<p>
			Thank you.
		</p>-->
	</div>
</div>
<script language=javascript>
$(function() {
	$("#contentTable").delegate("input[type=checkbox]","change",function() {
		ref=$(this).parents("tr").find("td[name=id]").attr("rel");
		v=$(this).is(":checked");
		l1=getServiceCMD("contentEditor")+"&src=<?=$_REQUEST['src']?>&action=block&format=json&ref="+ref+"&block="+v;
		processAJAXQuery(l1,function(txt) {
			if(txt.length>0) reloadTable();
		});
	});
	$("#contentTable").delegate("a.minibtn1","click",function() {
		tr=$(this).parents("tr");
		ref=tr.find("td[name=id]").attr("rel");
		title=tr.find("td[name=title]").text();
		
		//alert(title);
		if($(this).hasClass("editicon")) {
			editContent(ref,title);
		} else if($(this).hasClass("deleteicon")) {
			lgksConfirm("Do you want to delete <br/>"+title,"Delete",function() {
				l1=getServiceCMD("contentEditor")+"&src=<?=$_REQUEST['src']?>&action=delete&format=json&ref="+ref;
				processAJAXQuery(l1,function(txt) {
					if(txt.length>0) reloadTable();
					else tr.detach();
				});
			});
		}
	});
	$("#page .toolbar .right").html("<select id=filter style='width:150px;height:24px;'></select>");
	$("#filter").load(getServiceCMD("contentEditor")+"&src=<?=$_REQUEST['src']?>&action=filter&format=select");
	$("#filter").change(function() {
		v=this.value;
		if(v.length<=0) {
			$("#contentTable tbody tr").show();
		} else {
			$("#contentTable tbody tr").hide();
			$("#contentTable tbody tr").each(function() {
				if($("td[name=continent]",this).text()==v) $(this).show();
			});
		}
	});
	reloadTable();
});
function reloadTable() {
	$("#contentTable tbody").html("<tr><td colspan=10 class='ajaxloading3'></td></tr>");
	$("#contentTable tbody").load(getServiceCMD("contentEditor")+"&src=<?=$_REQUEST['src']?>&action=fetch&format=table",
		function() {
			$("#contentTable tbody tr").each(function() {
				$(this).append("<td><a class='minibtn1 editicon'></a><a class='minibtn1 deleteicon'></a></td>");
			});
		});
}
function createContent() {
	lgksPrompt("Please provide a new title/country for the Place!","New Country",null,function(txt) {
		if(txt.length>0) {
			l=getServiceCMD("contentEditor")+"&src=<?=$_REQUEST['src']?>&action=create&format=json";
			q="title="+txt;
			processAJAXPostQuery(l,q,function(txt) {
					if(txt.length>0) lgksAlert(txt);
					else reloadTable();
				});
		}
	});
}
var dlg=null;
function editContent(id,title) {
	l=getServiceCMD("contentEditor")+"&src=<?=$_REQUEST['src']?>&action=editor&ref="+id;
	dlg=lgksOverlayFrame(l,"Edit : "+title);
	//parent.openInNewTab("Edit : "+title,l);
}
function closeEditor() {
	reloadTable();
	dlg.dialog("close");
}
</script>
<?php } ?>