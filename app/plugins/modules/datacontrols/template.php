<?php
if(!defined('ROOT')) exit('No direct script access allowed');
session_check(true);

if(!isset($_REQUEST["forsite"])) {
	dispErrMessage("HostSite Is Required But Not Found","CMS Error",400);
	exit();
}

loadModule("dbcon");$dbCon=getDBControls();

loadModule("page");

loadModule("editor");
loadEditor("ckeditor");

_js("jquery.editinplace");

$btns=array();
$btns[sizeOf($btns)]=array("title"=>"Reload","icon"=>"reloadicon","tips"=>"Reload Template","onclick"=>"loadTemplate()");
$btns[sizeOf($btns)]=array("title"=>"Save","icon"=>"saveicon","tips"=>"Save Template","onclick"=>"saveTemplate()");
$btns[sizeOf($btns)]=array("title"=>"Preview","icon"=>"viewicon","tips"=>"Show Preview","onclick"=>"showPreview()");

$layout="apppage";
$params=array("toolbar"=>$btns,"contentarea"=>"printContent");

printPageContent($layout,$params);

function printContent() {
	$vcss="";
	
	loadModuleLib("views","editprops");
	
	$f=checkModule("views");
	if(strlen($f)>0) {
		//$f=dirname($f);
		//$vcss=$f."/style.css";
		//$vcss=file_get_contents($vcss);
		$webPath=getWebPath($f);
		$vcss=$webPath."style.css";
	}
	$dbGroupsTag="";
	$sql="SELECT groupid FROM "._dbtable("lists")." group by groupid";
	$r=_dbQuery($sql);
	if($r) {
		$data=_dbData($r);
		_db()->freeResult($r);
		foreach($data as $a) {
			$dbGroupsTag.="<option value='<!--?=createDataSelector(_db(),\"{$a['groupid']}\");?-->'>{$a['groupid']}</option>";
		}
	}
	
	/*$bgClasses="clr_pink,clr_green,clr_blue,clr_yellow,clr_red,clr_orange,clr_white,clr_white_inverted,clr_darkblue,clr_darkmaroon,clr_skyblue";
	$data=explode(",",$bgClasses);
	$bgClasses="<option value=''>None</option>";
	foreach($data as $a) {
		$bgClasses.="<option>$a</option>";
	}*/
?>
<link href='<?=$vcss?>' rel='stylesheet' type='text/css' media='all' />
<style>
#toolbar button {
	width:120px;
}
#pgworkspace {
	height:100%;
	overflow:hidden;
}
.pageArea {
	width:100%;height:100%;
	overflow:auto;
	padding:0px !important;margin:0px !important;
}
.tabPage {
	width:100%;height:100%;
	overflow:auto;
	padding:0px !important;margin:0px !important;
}
textarea.inplace_field {
	width:100%;
}
input[type=text] {
	border:1px solid #aaa;
	width:95%;
}
select {
	width:95%;
	height:22px;
}
select.chainSelector {
	margin-top:7px;
	display:none;
}
#formEditor {
	width:400px;height:80%;overflow:auto;margin-top:30px;padding:3px;float:left;
}
#fieldEditor {
	width:400px;height:400px;overflow:hidden;margin-left:100px;margin-top:30px;float:left;
}
#formEditor .formTools {
	display:none;float:right;
}
#formEditor:hover .formTools {
	display:block;
}
#formEditor .formHolder {
	margin-top:15px;
}
#formEditor .minibtn {
	margin-left:-5px;margin-top:3px;
	cursor:pointer;
}
#formEditor .minibtn:hover {
	opacity:0.5;
}
</style>
<div id=mainSpace style='width:100%;height:100%;'>
	<div id=pageArea class=tabs style='width:100%;height:100%;float:right;overflow:hidden;'>
			<ul>
				<li><a href='#tplSrc' onclick=''>TEMPLATE</a></li>
				<li><a href='#sqlSrc' onclick=''>SQL</a></li>
				<li><a href='#formSrc' onclick=''>FORM</a></li>
			</ul>
			<div id=tplSrc class='tabPage' style='overflow:hidden;'>
				<div id=codeEditor title='Code Editor' style='width:100%;height:95%;overflow:hidden;'>
					<textarea name=codearea id=codearea style='width:100%;height:500px;' readonly></textarea>
				</div>
			</div>
			<div id=sqlSrc class='tabPage' style='overflow:hidden;'>
				<button style="width:100px;" onclick="deleteSQLRow();"><div class='deleteicon'>Delete</div></button>
				<div style='width:100%;height:90%;overflow:auto;'>
					<table id=sqlEditor class='datatable nostyle' title='SQL Editor' width=100% cellpadding=1 cellspacing=0 border=0>
						<thead>
							<tr class='clr_darkmaroon'><th width=50px>SL.</th><th>SQL</th><th width=35px>A</th></tr>
						</thead>
						<tbody>
							<tr><th colspan=10><h3>No Template Loaded ...</h3></th></tr>
						</tbody>
					</table>
				</div>
			</div>
			<div id=formSrc class='tabPage' style='overflow:hidden;'>
				<div id=fieldEditor class='templateForm ui-widget-content clr_blue ui-corner-tl ui-corner-bl'>
					<ul style='list-style:none;'>
						<li class=titleCol>Field Title *</li>
						<li class=inputCol><input name=title type=text onchange="$('#fieldEditor input[name=name]').val(this.value.replace(' ',''));" /></li>
						<li class=titleCol>Field Name *</li>
						<li class=inputCol><input name=name type=text /></li>
						<li class=titleCol>Field Properties</li>
						<li class=inputCol>
							<select name=class>
								<option value=''>None</option>
								<?php
									$fieldProperties=getFieldProperties();
									foreach($fieldProperties as $a=>$b) {
										echo "<option value=\"$b\">$a</option>";
									}
								?>
							</select>
						</li>
						<li class=titleCol>Field Type</li>
						<li class=inputCol>
							<select name=type onchange='showChainSelector(this);'>
								<option value='text'>Text Field</option>
								<option value='autocomplete'>Autocomplete Field</option>
								<option value='date'>Date Field</option>
								<!--<option value='datetime'>DateTime Field</option>-->
								<option value='dbselect'>DBSelector</option>
							</select>
							<select name=dbselect_values class='chainSelector'>
								<?=$dbGroupsTag?>
							</select>
						</li>
						<li class=titleCol>Field SRC</li>
						<li class=inputCol><input name=src type=text /></li>
						<li class=titleCol>Extra Style</li>
						<li class=inputCol><input name=style type=text /></li>
					</ul>
					<hr/>
					<div align=center>
						<button onclick="$('#fieldEditor input').val('');">Reset</button>
						<button onclick='addNewField();'>Add</button>
					</div>
				</div>
				<div id=formEditor class='templateForm ui-widget-content clr_blue ui-corner-tr ui-corner-br ui-corner-bl'>
					<div class='formTools'>
						<button onclick="$('#formEditor ul').html('');">Clear</button>
					</div>
					<ul class='formHolder'>
						
					</ul>
				</div>
			</div>
		</div>
</div>
<div style='display:none'>

</div>
<script language=javascript>
$(function() {
	$("#mainSpace").css("height",($("#pgworkspace").height()-35)+"px;");
	$("#pageArea").css("height",($("#pgworkspace").height()-35)+"px;");
	$(".tabPage").css("height",($("#pageArea").height())+"px;");
	
	$("#codearea").css("height",$(window).height()-200);
	$("#codearea").css("width","98%");
	
	baseDir="media/";
	CKEDITOR.config.filebrowserBrowseUrl='plugins/modules/fileselectors/index.php?popup=direct&site=<?=$_REQUEST["forsite"]?>&baseDir='+baseDir;
	CKEDITOR.config.filebrowserImageBrowseUrl='plugins/modules/fileselectors/index.php?popup=direct&type=Others&site=<?=$_REQUEST["forsite"]?>&baseDir='+baseDir;
	CKEDITOR.config.filebrowserFlashBrowseUrl='plugins/modules/fileselectors/index.php?popup=direct&type=Others&site=<?=$_REQUEST["forsite"]?>&baseDir='+baseDir;
	CKEDITOR.config.filebrowserUploadUrl='plugins/modules/fileselectors/index.php?popup=direct&command=QuickUpload&type=Others&site=<?=$_REQUEST["forsite"]?>&baseDir='+baseDir;
	CKEDITOR.config.filebrowserImageUploadUrl='plugins/modules/fileselectors/index.php?popup=direct&command=QuickUpload&type=Others&site=<?=$_REQUEST["forsite"]?>&baseDir='+baseDir;
	CKEDITOR.config.filebrowserFlashUploadUrl='plugins/modules/fileselectors/index.php?popup=direct&command=QuickUpload&type=Others&site=<?=$_REQUEST["forsite"]?>&baseDir='+baseDir;
	
	CKEDITOR.config.toolbar = 'WYSIWYG_PHP';
	CKEDITOR.config.uiColor = '#DDD';
	
	CKEDITOR.config.startupMode="source";
	CKEDITOR.config.toolbarLocation="bottom";
	
	loadEditor("codearea");
	fixEditorSize("codearea");
	
	loadTemplate();
});
function getCMD(cmdMode) {
	if(cmdMode==null) cmdMode="templates";
	return "services/?scmd=datacontrols."+cmdMode+"&site=<?=SITENAME?>&forsite=<?=$_REQUEST["forsite"]?>&tmpl=<?=$_REQUEST['id']?>&mode=<?=$_REQUEST['mode']?>";
}
function loadTemplate() {
	$("#pageArea").hide();
	$("#mainSpace").addClass("ajaxloading6");
	
	$("#sqlEditor tbody").html("");
	//$("#formEditor tbody").html("");
	
	l=getCMD()+"&action=fetch";
	processAJAXQuery(l,function(txt) {
			try {
				json=$.parseJSON(txt);
				if(json!=null) {
					if(json.error!=null && json.error.length>0) {
						$("#mainSpacer").addClass("ajaxerror");
						lgksAlert(json.error);
					} else {
						editor.setData(json.template);
						$.each(json.sql,function(k,v) {
								if(v!="(Type New SQL Here)" && v.length>0) {
									html="<tr><th class='serial'>0</th><td class='sql editable'>"+v+"</td><th><input type=checkbox name=toDelete /></th></tr>";
									$("#sqlEditor tbody").append(html);
								}
							});
						updateSQLTable();
						
						$('#formEditor ul.formHolder').html(json.form);
						updateFormView();
						
						$("#pageArea").show();
					}
				} else {
					$("#mainSpace").addClass("ajaxerror");
					lgksAlert("Error Loading Template 1");
				}
			} catch(e) {
				$("#mainSpace").addClass("ajaxerror");
				lgksAlert("Error Loading Template 2");
			} finally {
				$("#mainSpace").removeClass("ajaxloading6");
			}
		});
	$("#pageArea").tabs("select",0);
}
function saveTemplate() {
	sql="";form="";
	$("#sqlEditor tbody tr td.sql").each(function() {
			if($(this).text()!="(Type New SQL Here)" && $(this).text().length>0)
				sql+=$(this).text().trim().replace('\n','')+"\n";
		});
	
	clone=$('#formEditor ul.formHolder').clone();
	clone.find(".minibtn").detach();
	form=clone.html();
	
	l=getCMD()+"&action=save";
	q="&template="+encodeURIComponent(editor.getData());
	q+="&sql="+encodeURIComponent(sql);
	q+="&form="+encodeURIComponent(form);
	
	processAJAXPostQuery(l,q,function(txt) {
			if(txt.length>0) lgksAlert(txt);
		});
}
/*SQL Editor Functions*/
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
/*Form Editor Functions*/
function addNewField() {
	titl=$('#fieldEditor input[name=title]').val();
	type=$('#fieldEditor select[name=type]').val();
	name=$('#fieldEditor input[name=name]').val().trim();
	clas=$('#fieldEditor select[name=class]').val();
	srcs=$('#fieldEditor input[name=src]').val();
	styl=$('#fieldEditor input[name=style]').val();
	
	if(titl.length<=0) {
		lgksAlert("Field Title Is Required.");
		return;
	}
	if(name.length<=0) {
		lgksAlert("Field Name Is Required.");
		return;
	}
	if(name.indexOf(' ')>0) {
		lgksAlert("Field Name Can Not Contain Space.");
		return;
	}
	
	html="<li class=titleCol><span>"+titl+"</span></li>";
	html+="<div class='minibtn deleteicon left' onclick='removeField(this)'></div>";
	html+="<li class=inputCol>";
	html+=getField(type,name,clas,srcs,styl);
	html+="</li>";
	
	$('#formEditor ul.formHolder').append(html);
	
	$('#fieldEditor input').val("");
	$('#fieldEditor input[name=title]').focus();
}
function removeField(selector) {
	p=$(selector).prev();
	n=$(selector).next();
	$(selector).detach();
	p.detach();
	n.detach();
}
function showChainSelector(selector) {
	$('#fieldEditor .chainSelector').hide();
	nm=selector.value+"_values";
	if($('#fieldEditor select[name='+nm+']').length>0) {
		$('#fieldEditor select[name='+nm+']').show();
	} else if($('#fieldEditor input[name='+nm+']').length>0) {
		$('#fieldEditor input[name='+nm+']').show();
	} else if($('#fieldEditor textarea[name='+nm+']').length>0) {
		$('#fieldEditor input[name='+nm+']').show();
	}
}
function getField(type,name,clas,srcs,styl) {
	html="";
	if(type=="text") {
		html+="<input name="+name+" type=text";
		if(clas.length>0)
			html+=" class='"+clas+"'";
		if(srcs.length>0)
			html+=" src='"+srcs+"'";
		if(styl.length>0)
			html+=" style='"+styl+"'";
		html+=" />";
	} else if(type=="autocomplete") {
		html+="<input name="+name+" type=text";
		if(clas.length>0)
			html+=" class='autocomplete "+clas+"'";
		else
			html+=" class='autocomplete'";
		if(srcs.length>0)
			html+=" src='"+srcs+"'";
		if(styl.length>0)
			html+=" style='"+styl+"'";
		html+=" />";
	} else if(type=="date") {
		html+="<input name="+name+" type=text";
		if(clas.length>0)
			html+=" class='datefield "+clas+"'";
		else
			html+=" class='datefield'";
		if(srcs.length>0)
			html+=" src='"+srcs+"'";
		if(styl.length>0)
			html+=" style='"+styl+"'";
		html+=" />";
	} else if(type=="datetime") {
		html+="<input name="+name+" type=text";
		if(clas.length>0)
			html+=" class='datetimefield "+clas+"'";
		else
			html+=" class='datetimefield'";
		if(srcs.length>0)
			html+=" src='"+srcs+"'";
		if(styl.length>0)
			html+=" style='"+styl+"'";
		html+=" />";
	} else if(type=="dbselect") {
		html+="<select name="+name;
		if(clas.length>0)
			html+=" class='datetimefield "+clas+"'";
		else
			html+=" class='datetimefield'";
		if(srcs.length>0)
			html+=" src='"+srcs+"'";
		if(styl.length>0)
			html+=" style='"+styl+"'";
		html+=" >";
		
		if($('#fieldEditor .chainSelector:visible').length>0) {
			v=$('#fieldEditor .chainSelector:visible').val();
			html+=v;
		}
		
		html+="</select>";
	}
	
	return html;
}
function updateFormView() {
	$("<div class='minibtn deleteicon left' onclick='removeField(this)'></div>").insertAfter('#formEditor ul.formHolder li.titleCol');
}
function showPreview() {
	l=getCMD("lists")+"&action=preview";
	parent.lgksOverlayFrame(l,"Preview !");
}
</script>
<?php
}
?>			
