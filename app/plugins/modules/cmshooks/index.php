<?php
if(!defined('ROOT')) exit('No direct script access allowed');
session_check(true);
user_admin_check();

loadModule("editor");
loadEditor("codemirror");//editarea,codemirror,ckeditor,nicedit,tinymce

loadModule("page");

$params["toolbar"]="printToolbar";
$params["contentarea"]="printContent";

$layout="apppage";

printPageContent("apppage",$params);

function printToolbar() { ?>
<button onclick="reloadList()" style='width:100px;' ><div class='reloadicon'>Reload</div></button>
||
<button onclick="createHook()" title="Create New Hook" style="width:100px;" ><div class='addicon'>Create</div></button>
<button onclick="deleteHook()" title="Delete Hook" style="width:100px;" ><div class='deleteicon'>Delete</div></button>
||
<button onclick="$('#infoBox').dialog({resizable:false,width:500,height:'auto',modal:true,});" style='width:100px;' ><div class='infoicon'>About</div></button>
<?php
}
function printContent() { 
	$webPath=getWebPath(__FILE__);
?>
<style>
#pgworkspace {
	overflow:hidden;
}
.list {
	width:20%;
	height:99%;
	margin-top:1px;
	float:left;
}
.tabs {
	width:79%;
	height:99%;
	margin-top:1px;
	margin-left:3px;
	float:left;
}
.tabspace {
	overflow:auto;
}
.tabspace.ui-tabs-panel.ui-widget-content.ui-corner-bottom {
	padding:0px;
	border:0px;
}
.hooklist {
	overflow:auto;
}
.hooklist ul {	
	padding:3px;
	margin:0px;
	padding-left:10px;
	list-style-type:square;
}
.hooklist ul li {
	margin-left:15px;
	cursor:pointer;
	padding-bottom:1px;	
}
.hooklist ul>li>ul>li:hover {
	text-decoration:underline;
}
.hooklist ul ul {
	list-style:none;
	list-style-type:none;
}
.hooklist ul li h3 {
	padding:0px;
	margin:0px;
	cursor:pointer;
	color:maroon;
	text-align:left;
}
.CodeMirror-scroll {
	font-size:14px;
}
</style>
<div class='photoList ui-widget-header' style='width:100%;height:100%;background:white;'>
	<div class='list ui-widget-content'>
		<div id=hooklist class='hooklist'></div>
	</div>
	<div class='tabs'>
		<ul>
			<li><a href='#editor' onclick=''>Hook Code</a></li>
			<li><a class='editprops edit' onclick="editCode();">Edit</a></li>
			<li><a class='editprops save' onclick="saveCode();">Save</a></li>
			<li><a class='editprops delete' onclick="deleteHook();">Delete</a></li>
			<li><a class='editprops block' onclick="blockHook(this);">Block</a></li>
			<li><a class='editprops close' onclick="closeHook();">Close</a></li>
			<li><a class='hookprops' onclick="createHook();">Create</a></li>
			<li><a id=loadedFile>::</a></li>
		</ul>
		<div id=editor class='tabspace'>
			<form>
				<textarea id=hookeditor style='width:100%;height:100%;resize:none;'></textarea>
			</form>
		</div>
	</div>
</div>
<div style='display:none;'>
	<div id=infoBox title='About Hooks' >
		<b>Hooks</b> are small pieces of codes that get hooked up at runtime depending of the state of request being processed.
		This would ean you can change practially any part of your response to request with small pieces of codes.
	</div>
</div>
<script language=javascript>
linkURL="services/?scmd=cmshooks&site=<?=SITENAME?>&forsite=<?=$_REQUEST["forsite"]?>";
loadedCode="";
loadedHook="";
$(function() {
	$("#toolbar").detach();
	$("#list").css("height",($(".page").height())+"px");
	$(".tabspace").css("height",($(".page").height()-40)+"px");
	$("#hookeditor").css("height",($(".tabspace").height()-2)+"px");
	$("#hooklist").css("height",($(".list").height()-30)+"px");
	
	loadEditor("hookeditor");
	fixEditorSize("hookeditor");
	readOnly();
	
	$(".editprops").hide();
	
	$(".hooklist").delegate("ul li h3","click",function() {
			$(this).parents("li").find("ul").slideToggle();
		});
	$(".hooklist").delegate("ul>li>ul>li","click",function() {
			loadCode($(this).attr("fl"));
		});
		
	loadHookList();
});
function loadHookList() {
	l=linkURL+"&action=listhooks";
	$("#hooklist").html("<div class='ajaxloading3'></div>");
	
	$("#hooklist").load(l);
}
function readOnly() {
	editor.setOption("readOnly", true);
}
function loadCode(idh, func) {
	$("*").css("cursor","wait");
	editor.setValue("");
	lnk=linkURL+"&action=fetch&fetch="+idh;
	$.ajax({
			url:lnk,			
			success:function(data, textStatus, jqXHR) {
				loadedCode=data;
				loadedHook=idh;
				editor.setValue(data);
				
				$("*").css("cursor","auto");
				$(".hooklist ul li").css("cursor","pointer");
				$(".hooklist ul li h3").css("cursor","pointer");
				$(".editprops").css("cursor","pointer");
				
				$(".editprops").show();
				
				readOnly();
				
				$("#loadedFile").html("::"+loadedHook);
				
				if(loadedHook.indexOf("~")>2) {
					$(".editprops.block").html("Unblock");
				} else {
					$(".editprops.block").html("Block");
				}
				
				if(func!=null) func();
			},
		});
}
function editCode() {
	editor.setOption("readOnly", false);
}
function saveCode() {
	if(loadedHook.length>0 && loadedCode!=editor.getValue()) {
		lnk=linkURL+"&action=save&save="+loadedHook;
		q="&code="+encodeURIComponent(editor.getValue());
		$.ajax({
				type: 'POST',
				url: lnk,
				data: q,
				success:function(data, textStatus, jqXHR) {
					if(data.length>0) lgksAlert(data);
				},			  
			});
	}	
}
function deleteHook() {
	if(loadedHook.length>0) {
		lgksConfirm("Sure About Deleting Hook ::"+loadedHook,"Delete Hook",function() {
				lnk=linkURL+"&action=delete&delete="+loadedHook;
				$.ajax({
						type: 'POST',
						url: lnk,
						success:function(data, textStatus, jqXHR) {
							if(data.length>0) lgksAlert(data);
							loadHookList();
							closeHook();
						},			  
					});
			});
	}
}
function blockHook(btn) {
	if(loadedHook.length>0) {
		msg="Sure About Blocking Hook ::"+loadedHook;
		title="Block Hook";
		
		if($(btn).text().toLowerCase()=="unblock") {
			msg="Sure About Unblocking Hook ::"+loadedHook;
			title="Unblock Hook";
		}
		
		lgksConfirm(msg,title,function() {
				lnk=linkURL+"&action=block&block="+loadedHook;
				$.ajax({
						type: 'POST',
						url: lnk,
						success:function(data, textStatus, jqXHR) {
							if(data.length>0) lgksAlert(data);
							loadHookList();
							closeHook();
						},			  
					});
			});
	}
}
function createHook() {
	closeHook();
	msg="To create a new hook, please give a <b>state/new-name</b> for the hook.<br/>Then you can edit the newly created hook.";
	lgksPrompt(msg,"Create New Hook",null,function(txt) {
			if(txt.length>0) {
				if(txt.indexOf("/")>1) {
					txt=txt.split(" ").join("_")+".php";
					lnk=linkURL+"&action=create&create="+txt;
					idh=txt;
					$.ajax({
						type: 'POST',
						url: lnk,
						success:function(data, textStatus, jqXHR) {
							if(data.length>0) lgksAlert(data);
							loadHookList();
							loadCode(idh, function() { editCode() });
						},			  
					});
				} else {
					lgksAlert("New Name Must Be Like :: <b>state/new-name</b>");
				}
			}
		});
	//$(".editprops").show();
}
function closeHook() {
	loadedCode="";
	loadedHook="";
	$("#loadedFile").html("::");
	editor.setValue("");
	readOnly();
	$(".editprops").hide();
}
</script>
<?php } ?>
