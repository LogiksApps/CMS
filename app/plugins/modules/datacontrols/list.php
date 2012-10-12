<?php
if (!defined('ROOT')) exit('No direct script access allowed');

if(!isset($_REQUEST["forsite"])) {
	dispErrMessage("HostSite Is Required But Not Found","CMS Error",400);
	exit();
}

loadModule("page");

loadModule("editor");
loadEditor("codemirror");

$btns=array();
$btns[sizeOf($btns)]=array("title"=>"Reload","icon"=>"reloadicon","tips"=>"Load Control List","onclick"=>"reloadControlList()");
if(isset($_REQUEST["list"])) {
	$btns[sizeOf($btns)]=array("title"=>"Create","icon"=>"addicon","tips"=>"Create New Control","onclick"=>"createControl()");
	$btns[sizeOf($btns)]=array("title"=>"Clone","icon"=>"cloneicon","tips"=>"Clone Selected Control","onclick"=>"cloneControl()");
	$btns[sizeOf($btns)]=array("title"=>"Delete","icon"=>"deleteicon","tips"=>"Delete Selected Control","onclick"=>"deleteControl()");
}
$btns[sizeOf($btns)]=array("bar"=>"||");
if(isset($_REQUEST["list"]) && $_REQUEST["list"]=="forms") {
	$btns[sizeOf($btns)]=array("title"=>"Export","icon"=>"icon_export","tips"=>"Export Form To Report","onclick"=>"exportToReport()");
}

$layout="apppage";
$params=array("toolbar"=>$btns,"contentarea"=>"printContent");

_js(array("jquery.multiselect"));
_css(array("jquery.multiselect"));

printPageContent($layout,$params);
?>
<script src='<?=$webPath?>js/common.js' type='text/javascript' language='javascript'></script>
<script src='<?=$webPath?>js/codeeditor.js' type='text/javascript' language='javascript'></script>
<link href='<?=$webPath?>css/style.css' rel='stylesheet' type='text/css' media='all' />
<style>
#controlEditor .ui-multiselect.ui-widget {
	width:450px !important;
}
table td {border:0px;}
table tr {border:0px;}
table {border:0px;}
</style>
<script>
mode="<?=$_REQUEST["list"]?>";
site="<?=$_REQUEST["forsite"]?>";
curSite="<?=SITENAME?>";
openEditorLink="index.php?site="+curSite+"&forsite="+site+"&page=modules&mod=datacontrols&mode=<?=$_REQUEST["list"]?>";
$(function() {
	$("select:not[multiple]").addClass("ui-state-active ui-corner-all");
	
	$("#datatable").delegate("input[type=checkbox]","change",function() {
			nm=$(this).attr("name").replace("row_","");
			if(nm=="rowselect") return;
			s=getCMD("lists")+"&action="+nm;
			q="&id="+$(this).attr('rel')+"&v="+this.checked;
			$("#loadingmsg").show();
			processAJAXPostQuery(s,q,function(txt) {
					if(txt.length>0) lgksAlert(txt);
					$("#loadingmsg").hide();
				});
		});
	$("#datatable").delegate(".colbtn","click",function() {
			nm=$(this).attr("name");
			id=$(this).attr("rel");
			if(nm.length<=0) {
				return;
			}
			if(nm=="style") {
				editCode(id,'style','css');
			} else if(nm=="js") {
				editCode(id,'script','js');
			} else if(nm=="ctrl") {
				editControl(id);
			} else if(nm=="properties") {
				editProperties(id);
			} else if(nm=="form") {
				editForm(id);
			} else if(nm=="datatable") {
				editDataTable(id);
			} else if(nm=="template") {
				editTemplate(id);
			} else if(nm=="privileges") {
				editPrivilege(id);
			}
		});
	reloadControlList();
});
function getCMD(cmdMode) {
	if(cmdMode==null) cmdMode="<?=$_REQUEST["list"]?>";
	return "services/?scmd=datacontrols."+cmdMode+"&mode=<?=$_REQUEST["list"]?>&forsite=<?=$_REQUEST["forsite"]?>&site="+curSite;
}
function showWindow(lnk,t) {
	/*$("#loadingmsg").show("fast");
	lgksOverlayURL(lnk,t, function() {
			reloadList();
		});*/
	parent.openInNewTab(t,lnk);
}
function reloadControlList() {
	$("#loadingmsg").show("fast");
	$("#datatable tbody").html("<tr class='nohover'><td colspan=20 class=ajaxloading6><br/><br/><br/>Loading ...</td></tr>");
	l=getCMD("lists")+"&action=viewlist";
	
	$("#datatable tbody").load(l,function() {
			$("#loadingmsg").hide("fast");
		});
}
function createControl() {
	$("#controlEditor input").val("");
	$("#controlEditor textarea").val("");
	$("#controlEditor input[name=id]").val("0");
	
	l1=getCMD("lists")+"&action=privileges";
	sele=$("#controlEditor select[name=privilege]").parent("td");
	processAJAXQuery(l1,function(txt) {
			sele.html("<select name=privilege class='privilege' multiple>"+txt+"</select>");
			$("#controlEditor select.privilege").multiselect({
					minWidth:450,
					selectedList:6,
				});
			osxPopupDiv("#controlEditor").dialog({
					resizable:false,
					buttons:{
						Save:function() {
							saveControl("#controlEditor");
							$(this).dialog("close");
						},
						Cancel:function() {
							$(this).dialog("close");
						}
					},
				});
		});
}
function deleteControl() {
	r=$("#datatable tbody input[name=rowselect][type=checkbox]:checked");
	if(r.length>0) {
		pg="";
		pt="";
		$(r).each(function() {
				pt+=$(this).attr("val")+" ("+$(this).attr("rel")+"), ";
				pg+=$(this).attr("rel")+",";
			});
			
		lgksConfirm("Are you Sure about deleting the Controls <br/><br/><div class='infobox'><h3 align=left>"+pt+"</h3></div><br/>Deleting them will not remove them from Menus though.",
				"Delete Controls ?",function() {
					l=getCMD("lists")+"&action=delete";
					q="&id="+pg;
					processAJAXPostQuery(l,q,function(txt) {
							if(txt.length>0) lgksAlert(txt);
							reloadControlList();
						});
				});	
	}
}
function cloneControl() {
	r=$("#datatable tbody input[name=rowselect][type=checkbox]:checked");
	if(r.length>0) {
		pg="";
		pt="";
		$(r).each(function() {
				pt+=$(this).attr("val")+" ("+$(this).attr("rel")+"), ";
				pg+=$(this).attr("rel")+",";
			});
			
		l=getCMD("lists")+"&action=clone";
		q="&id="+pg;
		processAJAXPostQuery(l,q,function(txt) {
				if(txt.length>0) lgksAlert(txt);
				reloadControlList();
			});
	}
}
function editControl(id) {
	l=getCMD("lists")+"&action=info&id="+id;
	processAJAXQuery(l,function(txt) {
				json=$.parseJSON(txt);
				for(var key in json) {
					 if($("#controlEditor select[name="+key+"]").length>0) {
						 l1=getCMD("lists")+"&action=privileges&id="+json["id"];
						 sele=$("#controlEditor select[name="+key+"]").parent("td");
						 processAJAXQuery(l1,function(txt) {
									sele.html("<select name=privilege class='privilege' multiple>"+txt+"</select>");
									$("#controlEditor select.privilege").multiselect({
											minWidth:450,
											selectedList:6,
										});
								});
					 } else {
						 $("#controlEditor input[name="+key+"]").val(json[key]);
						 $("#controlEditor textarea[name="+key+"]").val(json[key]);
					 }
				};
				qs=["<option value=''>None</option>"];
				$("#datatable td.category").each(function(k,v) {
						txt=$(v).text();
						if(txt.length>0)
							ssq="<option>"+txt+"</option>";
							if(qs.indexOf(ssq)<0)
								qs.push(ssq);
					});
				$("#controlEditor select.categorySelector").html(qs.join(""));
				osxPopupDiv("#controlEditor").dialog({
						resizable:false,
						buttons:{
							Save:function() {
								saveControl("#controlEditor");
								$(this).dialog("close");
							},
							Cancel:function() {
								$(this).dialog("close");
							}
						},
					});
			});
}
function saveControl(frmID) {
	if($(frmID+" input[name=title]").val().length<=0) {
		lgksAlert("A Title Is Must");
		return false;
	}
	l=getCMD("lists")+"&action=saveControl";
	q="";	
	$(frmID).find("input,textarea,select").each(function() {
			name=$(this).attr("name");
			value=$(this).val();
			if(name!=null && value!=null && name.length>0 ) {
				q+="&"+name+"="+encodeURIComponent(value);
			}
		});	
	processAJAXPostQuery(l,q,function(txt) {
				if(txt.length>0) {
					lgksAlert(txt);
					return;
				}
				reloadControlList();
				$('#controlEditor').dialog('close');
			});
	return true;
}
function exportToReport() {
	r=$("#datatable tbody input[name=rowselect][type=checkbox]:checked");
	if(r.length>0) {
		pg="";
		pt="";
		$(r).each(function() {
				pt+=$(this).attr("val")+" ("+$(this).attr("rel")+"), ";
				pg+=$(this).attr("rel")+",";
			});
			
		lgksConfirm("Are you Sure about Exporting the DataTables To Reports <br/><br/><div class='infobox'><h3 align=left>"+pt+"</h3></div><br/>This will create new reports.",
				"Export To Reports ?",function() {
					l=getCMD("lists")+"&action=export";
					q="&id="+pg;
					processAJAXPostQuery(l,q,function(txt) {
							if(txt.length>0) lgksAlert(txt);
						});
				});	
	}
}
function editPrivilege(id) {
	l=getCMD("lists")+"&action=dlgs&dlg=privilege&closeHandle=privilegeEditor&id="+id;
	$("#privilegeEditor").html("<div class=ajaxloading></div>");
	$("#privilegeEditor").load(l,function() {
			$("#privilegeEditor").attr("title",'Privilege Editor');
			osxPopupDiv("#privilegeEditor",null,650);
		});
}
function editProperties(id,title) {
	l=getCMD("lists")+"&action=dlgs&dlg=properties&closeHandle=propertyEditor&id="+id;
	$("#propertyEditor").html("<div class=ajaxloading></div>");
	$("#propertyEditor").load(l,function() {
			$("#propertyEditor").attr("title",'Property Editor');
			osxPopupDiv("#propertyEditor",null,900);
		});
}
function closeDialog(handle,reload) {
	if(handle==null || handle.length<=0) return;
	$(handle).dialog("close");
	if(reload) reloadControlList();
}
function checkAll(b) {
	$("#datatable input[type=checkbox][name=rowselect]").each(function() {
			this.checked=b;
		});
}
</script>
<?php
function printContent() { 
	$adCol="";
	if($_REQUEST["list"]=="forms") {
		$adCol="Adapter";
	} elseif($_REQUEST["list"]=="reports") {
		$adCol="ActionLink";
	}
?>
	<table id=datatable class='datatable' width=99% cellpadding=0 cellspacing=0 border=1 style='margin:5px;border:2px solid #aaa;width:99%;'>
		<thead>
			<tr align=center class='ui-widget-header'>
				<th width=50px><input type=checkbox onclick="checkAll(this.checked)" /></th>
				<th>Category</th>
				<th>Name</th>				
				<th width=70px>Engine</th>
				<?php
					if(strlen($adCol)>0) echo "<th width=150px>$adCol</th>";
				?>
				<th width=150px>DB</th>				
				<th width=80px>Created</th>
				<th width=80px>Edited</th>
				<th width=60px>Blocked</th>
				<th width=60px>OnMenu</th>
				<th width=60px>Privilege</th>
				<th width=100px>EDIT</th>
				<th width=100px>--</th>
			</tr>
		</thead>
		<tbody>
		</tbody>
	</table>
	
<div style='display:none'>
<div id=controlEditor title='Control Editor' style='overflow:hidden;'>
	<?php
		include "dlgs/ctrlEditor.php";
	?>
</div>
<div id=codeEditor title='Code Editor' style='overflow:hidden;'>
	<textarea name=codearea id=codearea></textarea>
</div>
<div id=privilegeEditor title='Privileges Editor' style='overflow:hidden;'></div>
<div id=propertyEditor title='Property Editor' style='overflow:hidden;'></div>
</div>
<?php
}
?>
