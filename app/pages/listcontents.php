<?php
if (!defined('ROOT')) exit('No direct script access allowed');
session_check(true);

if(!isset($_REQUEST["forsite"])) {
	dispErrMessage("HostSite Is Required But Not Found","CMS Error",400);
	exit();
}
loadModule("page");

loadModule("editor");
loadEditor("ckeditor");

$btns=array();
$btns[sizeOf($btns)]=array("title"=>"Reload","icon"=>"reloadicon","tips"=>"Load Content List","onclick"=>"reloadList()");
$btns[sizeOf($btns)]=array("title"=>"Create","icon"=>"addicon","tips"=>"Create New Content","onclick"=>"createContent()");

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
</style>
<div style='width:22%;height:100%;float:left;overflow:auto;'>
	<table id=contentList class='datatable' width=100% border=0 cellpadding=3 cellspacing=0 style='margin:0px;'>
		<tbody>
		</tbody>
	</table>
</div>
<div id=editPage style='width:77%;height:100%;float:left;overflow:hidden;'>
	<ul>
		<li><a href='#viewer'>Viewer</a></li>
		<li><a href='#editor'>Editor</a></li>
	</ul>
	<div id=viewer class='tabPage'>
	</div>
	<div id=editor class='tabPage'>
		<div class='ajaxloading6 loading'></div>
		<table width=99% cellpadding=2 cellspacing=0 border=0 class='nostyle formTable' style='display:none;'>
			<tr><td colspan=10 align=right>
				<div id=articleName style='float:left;width:100px;font-size:1.5em;margin-top:10px;'>Article#0</div>
				<button style='width:100px;' onclick='saveContent(currentID);'>Save</button>
				<button style='width:100px;' onclick='viewContent(currentID);'>Revert</button>
			</td></tr>
			<tr><td colspan=10 align=right><hr/></td></tr>
			<tr><th align=left width=130px>Content Title</th><td colspan=3><input name=title type=text style='width:100%;' /></td></tr>
			<tr>
				<th align=left width=130px>Category</th>
				<td><input id=category name=category type=text style='width:100%;' /></td>
				<th width=130px align=center>Blocked</th>
				<td><select name='blocked' class='ui-widget-header' style='width:100%;'><option value='false'>False</option><option value='true'>True</option></select></td>
			</tr>
			<tr><th align=left width=130px>Article/Content</th></tr>
			<tr><td align=center colspan=4>
				<textarea id=richtextarea style='width:99%;height:330px;border:0px;'></textarea>
			</td></tr>
		</table>
	</div>
</div>
<div style='display:none'>
	<div id=infoBox>
		<h1 align=center>Content Manger</h1>
		<p style='width:500px;margin:auto;'>
			Content Manger lets you manage all sort of small and big contents and articles that you are push publishing while
			designing pages or components or while developing various pages via <b>content</b> service.
		</p>
	</div>
</div>
<script language=javascript>
currentID=0;
lnk="services/?scmd=blocks.contents&site=<?=SITENAME?>&forsite=<?=$_REQUEST["forsite"]?>";
listBtns="<div class='right deleteicon minibtn'></div><div class='right viewicon minibtn'></div>";
$(function() {
	$("#editPage").css("height",($(window).height()-45)+"px");
	$(".tabPage").css("height",($("#editPage").height()-28)+"px");
	$("#editPage").tabs();
	$("#editPage").tabs("disable",1);
	
	CKEDITOR.config.toolbar="WYSIWYG_NOSTYLE";
	loadEditor("richtextarea");
	
	var href="services/?scmd=autocomplete&site=<?=$_REQUEST["forsite"]?>&src=sqltbl&format=json&tbl=do_contents&cols=category";
	$("#category").autocomplete({
			minLength: 1,
			source:href,
			select: function( event, ui ) {
				return true;
			}
		});
	
	$("#contentList tbody").delegate("tr","dblclick", function() {
			id=$(this).attr('rel');
			viewContent(id);
		});
	
	$("#contentList tbody").delegate(".minibtn","click",function() {
			id=$(this).parents("tr").attr('rel');
			if(id==null || id<0) return;
			if($(this).hasClass('deleteicon')) {
				deleteContent(id);
			} else if($(this).hasClass('editicon')) {
				editContent(id);
				$("#editPage").tabs({selected:1});
			} else if($(this).hasClass('viewicon')) {
				viewContent(id);
			}
		});
	
	reloadList();
});
function reloadList() {
	currentID=0;
	resetEditor();
	$("#editPage").tabs({"selected":0});
	$("#viewer").html($("#infoBox").clone().html());
	
	l=lnk+"&action=list";
	$("#contentList tbody").html("<tr><td class='ajaxloading3'><br/><br/><br/><br/>Listing ...</td></tr>");
	$("#loadingmsg").show();
	$("#contentList tbody").load(l, function() {
			html=listBtns;
			$("#contentList tbody td:last-child").append(html);
			$("#loadingmsg").hide();
		});
}
function viewContent(id) {
	resetEditor();
	$("#contentList tr.active").removeClass("active");
	
	if(id==null || id<=0) return;
	
	currentID=id;
	l=lnk+"&action=fetch&id="+currentID;
	$("#loadingmsg").show();
	$("#viewer").html("<div class='ajaxloading6 loading'></div>");
	processAJAXQuery(l,function(data) {
			json=$.parseJSON(data);
			if(json!=null) {
				html="<h2 class='clr_green' align=center style='margin:0px;padding:0px;'>"+json.title+"</h2>";
				html+=json.text;
				$("#viewer").html(html);
				
				$("#editor #articleName").html("Article#"+currentID);
				
				$("#editor input[name=title]").val(json.title);
				$("#editor input[name=category]").val(json.category);
				$("#editor select[name=blocked]").val(json.blocked);
				editor.setData(json.text);
				$("#editPage").tabs("enable",1);
				
				$("#editor .loading").hide();
				$("#editor .formTable").show();
				
				$("#contentList tr[rel="+currentID+"]").addClass("active");
			} else {
				html="<h3 class='clr_pink' style='margin:auto;margin-top:50px;width:80%;height:30px;padding-top:20px;'>Failed To Loading Content For Article#"+id+"</h3>";
				$("#viewer").html(html);
			}
			$("#loadingmsg").hide();
		});
}
function saveContent(id) {
	if(id==null || id<=0) return;
	l=lnk+"&action=save&id="+id;
	q="&data="+encodeURIComponent(editor.getData());
	
	$("#editor").find("input, textarea, select").each(function() {
			if($(this).attr('name')!=null)
				q+="&"+$(this).attr('name')+"="+encodeURIComponent($(this).val());
		});
	$("#editor .formTable").hide();
	$("#editor .loading").show();
	processAJAXPostQuery(l,q,function(msg) {
			if(msg.length>0) lgksAlert(msg);
			
			html="<h2 class='clr_green' align=center style='margin:0px;padding:0px;'>"+$("#editor input[name=title]").val()+"</h2>";
			html+=editor.getData();
			$("#viewer").html(html);
			
			html=$("#editor input[name=title]").val()+" ["+$("#editor input[name=category]").val()+"]";
			html+=listBtns;
			$("#contentList tr[rel="+id+"] td").html(html);
			
			$("#contentList tr[rel="+id+"]").removeClass("okicon");
			$("#contentList tr[rel="+id+"]").removeClass("notokicon");
			if($("#editor select[name=blocked]").val()=="true") {
				$("#contentList tr[rel="+id+"]").addClass("notokicon");
			} else {
				$("#contentList tr[rel="+id+"]").addClass("okicon");
			}
			
			$("#editor .loading").hide();
			$("#editor .formTable").show();
		});
}
function createContent() {
	lgksPrompt("Please give a title for the new Article!","New Article !",null,function(txt) {
			if(txt!=null && txt.length>0) {
				l=lnk+"&action=create&title="+txt;
				processAJAXQuery(l,function(data) {
						json=$.parseJSON(data);
						if(json!=null && json.id>0) {
							html="<tr rel='"+json.id+"' class='notokicon editable'>";
							html+="<td style='padding-left:25px'>";
							html+=txt+listBtns;
							html+="</td></tr>";
							
							$("#contentList tbody").append(html);
						} else {
							lgksAlert("There was error creating article with name <b>"+txt+"</b>. <br/>Please try again.");
						}
					});
			}
		});
}
function deleteContent(id) {
	title=$("#contentList tr[rel="+id+"] td").text();
	lgksConfirm("Are you sure about deleting <b>"+title+"</b>?<br/>This is irreversible.","Delete Content ?",function() {
			l=lnk+"&action=delete&id="+id;
			processAJAXQuery(l,function(data) {
					if(data.length>0) lgksAlert(data);
					else {
						if(currentID==id) {
							currentID=0;
							resetEditor();
							$("#editPage").tabs({"selected":0});
							$("#viewer").html($("#infoBox").clone().html());
						}
						$("#contentList tr[rel="+id+"]").detach();
					}
				});
		});
	
}
function resetEditor() {
	$("#editor").find("input, textarea").val("");
	$("#editor").find("select[name=blocked]").val("false");
	//editorReadOnly(true);
	$("#editor .formTable").hide();
	$("#editor .loading").show();
}
function editorReadOnly(readOnly) {
	if(readOnly) {
		editor.document.$.body.disabled=true;
		editor.document.$.body.contentEditable=false;
		editor.document.$.designMode="off";
	} else {
		editor.document.$.body.disabled=false;
		editor.document.$.body.contentEditable=true;
		editor.document.$.designMode="on";
	}
}
</script>
<?php
}
?>
