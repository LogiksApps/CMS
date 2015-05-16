<?php
if(!defined('ROOT')) exit('No direct script access allowed');
session_check(true);

if(!isset($_REQUEST["forsite"])) {
	dispErrMessage("HostSite Is Required But Not Found","CMS Error",400);
	exit();
}
loadModule("page");

loadModule("editor");
loadEditor("ckeditor");

_js("jquery.editinplace");

$btns=array();
$btns[sizeOf($btns)]=array("title"=>"Reload","icon"=>"reloadicon","tips"=>"Load Template List","onclick"=>"reloadTemplateList()");
$btns[sizeOf($btns)]=array("title"=>"Create","icon"=>"addicon","tips"=>"Create New Template","onclick"=>"createTemplate()");
$btns[sizeOf($btns)]=array("title"=>"Clone","icon"=>"cloneicon","tips"=>"Clone Selected Template","onclick"=>"cloneTemplate()");
$btns[sizeOf($btns)]=array("title"=>"Rename","icon"=>"renameicon","tips"=>"Rename Selected Template","onclick"=>"renameTemplate()");
$btns[sizeOf($btns)]=array("title"=>"Delete","icon"=>"deleteicon","tips"=>"Delete Selected Template","onclick"=>"deleteTemplate()");
//$btns[sizeOf($btns)]=array("title"=>"Upload","icon"=>"uploadicon","tips"=>"Upload New Templates","onclick"=>"uploadTemplate()");
//$btns[sizeOf($btns)]=array("title"=>"Download","icon"=>"downloadicon","tips"=>"Download Template","onclick"=>"downloadTemplate()");

$layout="apppage";
$params=array("toolbar"=>$btns,"contentarea"=>"printContent");

printPageContent($layout,$params);

function printContent() {
		$lf=$_SESSION["APP_FOLDER"]["APPROOT"].$_SESSION["APP_FOLDER"]["APPS_TEMPLATE_FOLDER"];
?>
<style>
#toolbar .right {
	width:180px !important;
}
#toolbar .right #loadingmsg {
	float:right;
	padding-right:10px;
}
#toolbar button {
	width:120px;
}
#toolbar select {
	margin-top:-13px;
	height:25px;
}
.tabPage {
	width:100%;height:500px;
	overflow:auto;
	padding:0px !important;margin:0px !important;
}
#pageArea .src,#pageArea .layout {
	display:none;
}
input[type=text] {
	border:1px solid #aaa;
	width:95%;
}
select {
	width:95%;
	height:22px;
}
textarea.inplace_field {
	width:100%;
}
table.datatable tbody tr.active td {
	background-color:#C9FFDE !important;
	padding:2px;
}
</style>
<div style='width:100%;height:100%;'>
	<div style='width:20%;height:100%;float:left;overflow:auto;'>
		<table class='datatable' width=100% border=0 cellpadding=3 cellspacing=0 style='margin:0px;'>
			<thead>
				<tr class='subheader clr_darkblue'><td colspan=10 style='padding-left:10px;'>All Templates</td></tr>
			</thead>
			<tbody id=allpages>
			</tbody>
		</table>
	</div>
	<div style='width:80%;height:100%;float:right;overflow:hidden;'>
		<div id=pageArea class=tabs>
			<ul>
				<li><a href='#tplSrc' onclick=''>TEMPLATE</a></li>
				<li><a href='#sqlSrc' onclick=''>SQL</a></li>
				<li><a onclick='saveTemplate();'>Save</a></li>
				<li><a onclick='loadTemplate();'>Reset</a></li>
			</ul>
			<div id=tplSrc class='tabPage' style='overflow:hidden;'>
				<div id=codeEditor title='Code Editor' style='width:100%;height:95%;overflow:hidden;'>
					<textarea name=codearea id=codearea style='width:100%;height:500px;' readonly></textarea>
				</div>
			</div>
			<div id=sqlSrc class='tabPage' style='height:100%;overflow:none;'>
				<button style="width:100px;" onclick="deleteSQLRow();"><div class='deleteicon'>Delete</div></button>
				<div style='width:100%;height:80%;overflow:auto;'>
					<table id=sqlEditor class='datatable' title='SQL Editor' width=100% cellpadding=1 cellspacing=0 border=0>
						<thead>
							<tr class='clr_darkmaroon'>
								<th width=50px style='color:white;' align=center>SL.</th>
								<th style='color:white;' align=center>SQL</th>
								<th style='color:white;'  align=center width=35px>A</th>
							</tr>
						</thead>
						<tbody>
							<tr><th colspan=10><h3>No Template Loaded ...</h3></th></tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
<div style='display:none'>
	<select id=typeSelector class='ui-widget-header ui-corner-all' onchange="reloadTemplateList()">
		<option value=''>All</option>
	</select>
</div>
<script language=javascript>
var tmpl="";
$(function() {
	$(".tabPage").css("height",($("#pgworkspace").height()-35)+"px");
	$("#toolbar .right").prepend($("#typeSelector"));
	
	/*loadCodeEditor("codearea","php");
	editor.setOption("readOnly", true);*/
	
	$("#codearea").css("height",$(window).height()-230);
	$("#codearea").css("width","98%");
	
	CKEDITOR.config.toolbar = 'WYSIWYG_PHP';
	CKEDITOR.config.uiColor = '#DDD';
	
	//CKEDITOR.config.startupMode="source";
	//CKEDITOR.config.toolbarLocation="bottom";
	
	loadEditor("codearea");
	fixEditorSize("codearea");
	
	$("#allpages").delegate("tr","click",function() {
			$(this).parents("table").find("tr.active").removeClass("active");
			$(this).addClass("active");
			
			loadTemplate($(this).attr("rel"));
		});
	$("#typeSelector").html("<option>Loading ...</option>");
	$("#typeSelector").load(getCMD()+"&action=viewtypelist",function(txt) {
			reloadTemplateList();
		});
});
function getCMD() {
	return "services/?scmd=tmplEditor&site=<?=SITENAME?>&forsite=<?=$_REQUEST["forsite"]?>";
}
function reloadTemplateList() {
	$("#loadingmsg").show();
	l=getCMD()+"&action=viewtable&type="+$("#typeSelector").val();
	$("#allpages").html("<tr><td colspan=20 class='ajaxloading6'><br/><br/><br/>Loading ...</td></tr>");
	$("#allpages").load(l,function(txt) {
			$("#loadingmsg").hide();
		});
}
function updateSQLTable() {
	if($("#sqlEditor tbody tr td.sql:contains((Type New SQL Here))").length==0) {
		addSQLRow();
	}
	cnt=0;
	$("#sqlEditor tbody tr th.serial").each(function() {
			$(this).html(cnt);
			$(this).attr("title","'$SQL["+(cnt++)+"]'");
		});
	$("#sqlEditor tbody .editable").editInPlace({
			callback: function(original_element, html, original) { updateSQLTable(); return html;},
			show_buttons:false,
			field_type:"textarea",
			default_text:"(Type New SQL Here)",
		});
}
function addSQLRow() {
	n=$("#sqlEditor tbody tr").length+1;
	html="<tr><th class='serial'>"+n+"</th><td class='sql editable'></td><th><input type=checkbox name=toDelete /></th></tr>";
	$("#sqlEditor tbody").append(html);
}
function deleteSQLRow() {
	$("#sqlEditor tbody tr input[name=toDelete]:checked").each(function() {
			$(this).parents("tr").detach();
		});
	updateSQLTable()
}
function loadTemplate() {
	tmpl=$("#allpages tr.active").attr("rel");
	if(tmpl==null || tmpl.length<=0) {
		lgksAlert("No Template Selected.");
		return;
	}
	$("#loadingmsg").show();
	$("#sqlEditor tbody").html("<tr><td colspan=10 class='ajaxloading6'></td></tr>");
	l=getCMD()+"&action=fetch&tmpl="+tmpl;
	processAJAXQuery(l,function(txt) {
			json=$.parseJSON(txt);
			if(json!=null) {
				$("#sqlEditor tbody").html("");
				editor.setData(json.template);
				$.each(json.sql,function(k,v) {
						if(v!="(Type New SQL Here)" && v.length>0) {
							html="<tr><th class='serial'>0</th><td class='sql editable'>"+v+"</td><th><input type=checkbox name=toDelete /></th></tr>";
							$("#sqlEditor tbody").append(html);
						}
					});
				updateSQLTable();
			} else {
				lgksAlert("Error Loading Template");
			}
			$("#loadingmsg").hide();
		});
	$("#pageArea").tabs("select",0);
}
function saveTemplate() {
	if(tmpl==null) {
		lgksAlert("Nothing To Edit Or Save.");
		return;
	}
	sql="";
	$("#sqlEditor tbody tr td.sql").each(function() {
			if($(this).text()!="(Type New SQL Here)" && $(this).text().length>0)
				sql+=$(this).text()+"\n";
		});
	
	l=getCMD()+"&action=save&tmpl="+tmpl;
	q="&template="+encodeURIComponent(editor.getData());
	q+="&sql="+sql;
	
	processAJAXPostQuery(l,q,function(txt) {
			if(txt.length>0) {
				if(typeof lgksToast=="function") lgksToast(txt,{position: "top-right"});
				else lgksAlert(txt);
			}
		});
}

function createTemplate() {
	lgksPrompt("Please give a new name for the Template.<br/>No Space is allowed in name.<br/>A template with the name should not exist.<br/>","Create Template",
		null,function(txt) {
				if(txt.length>0) {
					txt=txt.replace(".tpl","");
					l=getCMD()+"&action=create&tmpl="+txt.split(" ").join("_").toLowerCase();
					processAJAXQuery(l,function(txt) {
							if(txt.length>0) lgksAlert(txt);
							else reloadTemplateList();
						});
				}
			});
}
function deleteTemplate() {
	toDelete=[];
	$("#allpages tr input[name=select]:checked").each(function() {
			toDelete.push($(this).attr("rel"));
		});
	if(toDelete.length<=0) return;
	lgksConfirm("Are you sure about deleting these templates?<br/>This is irrversible process.<br/><br/><div style='width:300px;height:150px;overflow:auto;'><b>"+toDelete.join(', ')+"</b></div>.",
			"Delete Templates !",function() {
					l=getCMD()+"&action=delete&tmpl="+toDelete.join(',');
					processAJAXQuery(l,function(txt) {
							if(txt.length>0) lgksAlert(txt);
							else reloadTemplateList();
						});
				});
	
}
function cloneTemplate() {
	toClone=[];
	$("#allpages tr input[name=select]:checked").each(function() {
			toClone.push($(this).attr("rel"));
		});
	if(toClone.length<=0) return;
	l=getCMD()+"&action=clone&tmpl="+toClone.join(',');
	processAJAXQuery(l,function(txt) {
			if(txt.length>0) lgksAlert(txt);
			else reloadTemplateList();
		});
}
function renameTemplate() {
	tmpl=$("#allpages tr.active").attr("rel");
	if(tmpl!=null && tmpl.length>0) {
		lgksPrompt("Please give a new name for the Template.<br/>No Space is allowed in name.<br/>A template with the name should not exist.<br/>For <b>"+tmpl+"</b>","Rename Template",
			null,function(txt) {
					if(txt.length>0) {
						if(txt.indexOf(".tpl")<0 || txt.indexOf(".tpl")!=txt.length-4) txt+=".tpl";
						l=getCMD()+"&action=rename&tmpl="+tmpl+"&totmpl="+txt.replace(" ","_").toLowerCase();
						processAJAXQuery(l,function(txt) {
								if(txt.length>0) lgksAlert(txt);
								else reloadTemplateList();
							});
					}
				});
	}
}
function closeTemplate() {
	
}
function uploadTemplate() {
	
}
function downloadTemplate() {
}
function loadWYSIWYG() {
	tmpl=$("#allpages tr.active").attr("rel");
	if(tmpl==null || tmpl.length<=0) {
		lgksAlert("No Template Selected.");
		return;
	}
	l=getCMD()+"&action=editurl&tmpl="+tmpl;
	processAJAXQuery(l,function(txt) {
			url="index.php?site=<?=SITENAME?>&forsite=<?=$_REQUEST['forsite']?>&page=wysiwygedit&file="+txt;
			if(typeof parent.openInNewTab=="function")
				parent.openInNewTab("WYSIWYG",url);
			else
				lgksOverlayURL(url,"WYSIWYG");
		});
}
</script>
<?php
}
?>
