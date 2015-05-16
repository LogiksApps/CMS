<?php
if (!defined('ROOT')) exit('No direct script access allowed');

if(!isset($_REQUEST["forsite"])) {
	dispErrMessage("HostSite Is Required But Not Found","CMS Error",400);
	exit();
}

$supportedModes=array("forms","reports","search","analyze");
if(!in_array($_REQUEST["mode"],$supportedModes)) {
	dispErrMessage("Selected Mode Not Supported","CMS Error",400);
	exit();
}
loadModule("page");

$btns=array();
$btns[sizeOf($btns)]=array("title"=>"Clear","icon"=>"clearicon","tips"=>"Clear Form","onclick"=>"clearEditor()");
$btns[sizeOf($btns)]=array("title"=>"Reload","icon"=>"reloadicon","tips"=>"Reload Form","onclick"=>"reloadEditor()");
$btns[sizeOf($btns)]=array("bar"=>"||");
$btns[sizeOf($btns)]=array("title"=>"Editor","icon"=>"editicon","tips"=>"Editor Window","onclick"=>"showEditor();");
$btns[sizeOf($btns)]=array("title"=>"Properties","icon"=>"gearicon","tips"=>"Properties Window","onclick"=>"showProperties();");
$btns[sizeOf($btns)]=array("title"=>"Preview","icon"=>"viewicon","tips"=>"Preview DataTable","onclick"=>"showPreview();");
$btns[sizeOf($btns)]=array("bar"=>"||");
$btns[sizeOf($btns)]=array("title"=>"Check","icon"=>"okicon","tips"=>"Check And Rectify Errors.","onclick"=>"rectifyError();");
$btns[sizeOf($btns)]=array("title"=>"Save","icon"=>"saveicon","tips"=>"Save Form","onclick"=>"saveControl()");
//$btns[sizeOf($btns)]=array("title"=>"Info","icon"=>"infoicon","tips"=>"Show Info","onclick"=>"showInfo()");

$layout="apppage";
$params=array("toolbar"=>$btns,"contentarea"=>"printContent");

include "props/datatable.php";

printPageContent($layout,$params);

_js(array("jquery.multiselect","jquery.listAttributes","jquery.editinplace","jquery.tablednd"));
_css(array("jquery.multiselect","colors","styletags","formfields"));

?>
<script src='<?=$webPath?>js/common.js' type='text/javascript' language='javascript'></script>
<script src='<?=$webPath?>js/datatables.js' type='text/javascript' language='javascript'></script>
<link href='<?=$webPath?>css/datatables.css' rel='stylesheet' type='text/css' media='all' />
<?php
function printContent() {
	$data=file_get_contents(dirname(__FILE__)."/resources/datatables/sqlfunc.dat");
	$data=explode("\n",$data);
	$funcArr="";
	foreach($data as $a) {
		if(strlen($a)>0) {
			$r=explode("=",$a);
			if(count($r)>1) {
				$t=$r[0];
				unset($r[0]);
				if(count($r)>1) {
					$funcArr.="<li value=\"".implode("=",$r)."\">$t</li>";
				} else {
					$funcArr.="<li value=\"{$r[1]}\">$t</li>";
				}
			}
		}
	}

	$data=file_get_contents(dirname(__FILE__)."/resources/datatables/sqlopts.dat");
	$data=explode("\n",$data);
	$sqlOpts="";
	foreach($data as $a) {
		if(strlen($a)>0) {
			$r=explode("#",$a);
			if(count($r)>1) {
				$sqlOpts.="<option value=\"{$r[1]}\">{$r[0]}</option>";
			}
		}
	}

	$data=file_get_contents(dirname(__FILE__)."/resources/datatables/collimits.json");
	$data=explode("\n",$data);
	$colLimits=array();
	foreach($data as $a) {
		if(strlen($a)>0) {
			$r=explode("=",$a);
			$colLimits[$r[0]]=$r[1];
		}
	}
	$colLimits=json_encode($colLimits);
?>
<style>
#toolbar button { width:120px; }
#datatablediv {display:none;}
#datatablediv .ui-tabs-nav.ui-helper-reset.ui-helper-clearfix.ui-widget-header {
	display:none;
}
#datatablediv .ui-multiselect.ui-widget {
	width:300px !important;
}
#colClasses .ui-multiselect.ui-widget {
	height:30px !important;
}
</style>
<script language=javascript>
site="<?=$_REQUEST["forsite"]?>";
curSite="<?=SITENAME?>";
lnkLst=getServiceCMD("datacontrols.lists") + "&mode=<?=$_REQUEST["mode"]?>";
engine="";
colLimits=$.parseJSON('<?=$colLimits?>');
$(function() {
	sqlOpts='<?=$sqlOpts?>';

	$("select").addClass("ui-widget-header ui-corner-all");
	$("select").css("opacity","0.6");

	$("#datatablediv select[multiple]").multiselect({
			selectedList:3,
			minWidth:300,
		});

	$("#datatablediv .datatablepage").css("height",($(window).height()-50)+"px");
	$("#datatablediv").tabs();

	$("#sidebar").css("height",($(window).height()-$("#toolbar").height()-25)+"px");
	$("#sidebar").css("width","200px");
	$("#designer").css("height",($("#sidebar").height())+"px");
	$("#designer").css("width",($(window).width()-$("#sidebar").width()-40)+"px");

	$("#datatablediv").delegate("tr.helptip","click",function() {
			$(this).hide("slow").detach();
		});
	$("#helptip3").show("slow").delay(2500).fadeOut("slow");

	$("#propertiesSearch").keypress(function(key) {
			v=$("#propertiesSearch").val().toUpperCase();
			$("#settings tbody th.title.searchFound").removeClass("searchFound");
			if(v.length>0) {
				//$("#settings tbody th.title:contains("+v+")").addClass("searchFound");
				$("#settings tbody th.title[name*="+v+"]").addClass("searchFound");
			}
		});
	$("#propertiesSearch").change(function(key) {
			v=$("#propertiesSearch").val().toUpperCase();
			$("#settings tbody th.title.searchFound").removeClass("searchFound");
			if(v.length>0) {
				//$("#settings tbody th.title:contains("+v+")").addClass("searchFound");
				$("#settings tbody th.title[name*="+v+"]").addClass("searchFound");
			}
		});
	//showProperties();

	reloadEditor();
});
function getCMD(cmdMode) {
	if(cmdMode==null) cmdMode="datatables";
	return "services/?scmd=datacontrols."+cmdMode+"&site="+curSite+"&mode=<?=$_REQUEST["mode"]?>&forsite=<?=$_REQUEST["forsite"]?>&id=<?=$_REQUEST['id']?>";
}
function setupColumnLimit() {
	if(colLimits[engine]==null) ColLimit=-1;
	else ColLimit=colLimits[engine];
}
function showLoading(b) {
	if(b) {
		$("#loadingmsg").show();
		$("#datatablediv").hide();
		$("#loadingdiv").show();
	} else {
		$("#loadingmsg").hide();
		$("#loadingdiv").hide();
		$("#datatablediv").show();
	}
}
function clearEditor(silent) {
	if(silent==null) silent=false;
	if(silent) {
		$("#designer #datatable_tables").val("");
		$("#designer #datatable_col_details tbody").html("");
		$("#designer #datatable_where_details tbody").html("");

		$("input, select","#settings tbody").each(function() {
				if($(this).attr('ori').length>0)
					$(this).val($(this).attr('ori'));
			});
		$("#settings tbody select[multiple]").multiselect("destroy");
		$("#settings tbody select[multiple]").multiselect({
				selectedList:3,
				minWidth:300,
			});
	} else {
		lgksConfirm("Are you sure to clear all the changes you made ?","Clear Editors",function() {
				$("#designer #datatable_tables").val("");
				$("#designer #datatable_col_details tbody").html("");
				$("#designer #datatable_where_details tbody").html("");

				$("#settings input, #settings select").each(function() {
						if($(this).attr('ori').length>0)
							$(this).val($(this).attr('ori'));
					});
				$("#settings tbody select[multiple]").multiselect("destroy");
				$("#settings tbody select[multiple]").multiselect({
						selectedList:3,
						minWidth:300,
					});
			});
	}
}
function reloadEditor() {
	n=$('#datatablediv').tabs("option",'selected');
	clearEditor(true);

	if(n==0) {
		l=getCMD()+"&action=fetchdatatable";
		showLoading(true);

		loadTableList('#sidebar #dbtables',function() {
				loadColumnListMore('#sidebar #form_TableColumns',$(this).val(),'ul',activateDnD);
			});
		processAJAXQuery(l,function(txt) {
				json=$.parseJSON(txt);

				s="<?=strtoupper($_REQUEST["mode"])?> ID:- "+json.id+" ["+json.title+"]";
				$("#designer #dtcontrol").val(s);
				$("#designer #datatable_tables").val(json.datatable_table);

				engine=json.engine;
				setupColumnLimit();

				if(json.datatable_cols==null) json.datatable_cols="";
				if(json.datatable_colnames==null) json.datatable_colnames="";
				if(json.datatable_hiddenCols==null) json.datatable_hiddenCols="";

				if(json.datatable_searchCols==null) json.datatable_searchCols="";
				if(json.datatable_sortCols==null) json.datatable_sortCols="";
				if(json.datatable_classes==null) json.datatable_classes="";

				tbls=json.datatable_table;
				colHidden=json.datatable_hiddenCols.split(",");

				cols=json.datatable_cols;
				colNames=json.datatable_colnames;

				colSearch=json.datatable_searchCols.split(",");
				colSort=json.datatable_sortCols.split(",");
				colClasses=json.datatable_classes.split(",");

				if(colNames.length!=cols.length) {
					showLoading(false);
					lgksAlert("The Datatable Is InConsistant.Aborting.");
					return;
				}
				$("#designer #datatable_tables").val(tbls);

				if(cols.length>0 && cols[0].length>0) {
					addColumns(cols,colNames,colHidden,colSearch,colSort,colClasses);
				}
				$("#designer #datatable_tables").val(tbls);
				$("#datatable_where_details tbody .wtables").html(getActiveTablesList());

				addWColumns(json.datatable_where);

				showLoading(false);
			});
	} else if(n==1) {
		l=getCMD()+"&action=fetchparams";
		showLoading(true);
		processAJAXQuery(l,function(txt) {
				json=$.parseJSON(txt);
				$.each(json.datatable_params,function(key,value) {
						if($("#settings tbody select[name="+key+"][multiple]").length>0) {
							value=(""+value).substring(1,value.length-1);
							$("#settings tbody select[name="+key+"][multiple]").val(value.split(","));
						} else {
							if($("#settings tbody input[name="+key+"]").hasClass("json_array")) {
								value=(""+value).substring(1,value.length-1);
							}
							$("#settings tbody input[name="+key+"]").val(value);
							$("#settings tbody select[name="+key+"]").val(value);
						}
					});
				$("#settings tbody select[multiple]").multiselect("destroy");
				$("#settings tbody select[multiple]").multiselect({
						selectedList:3,
						minWidth:300,
					});
				showLoading(false);
			});
	} else if(n==2) {
	}
}
function saveControl() {
	n=$('#datatablediv').tabs("option",'selected');
	if(n==0) {
		l=getCMD()+"&action=savedatatable";
		$("#loadingmsg").show();
		q="";

		cols=[];
		colNames=[];
		colHidden=[];
		colSearch=[];
		colSort=[];
		colClasses=[];
		colWhere="";
		$("#datatable_col_details tbody tr").each(function() {
				s1=$(this).attr("colpath");
				if(s1.length>0) {
					s2=$(this).find("td.title").text();
					if(s2.trim().length<=0) s2=s1;
					if($(this).find("td.hide input[type=checkbox]").is(":checked")) {
						colHidden.push(s1.trim());
					}
					if($(this).find("td.search input[type=checkbox]").is(":checked")) {
						colSearch.push(s1.trim());
					}
					if($(this).find("td.sort input[type=checkbox]").is(":checked")) {
						colSort.push(s1.trim());
					}
					s3=$(this).find("td.class").text().replace("--","");

					cols.push(s1.trim());
					colNames.push(s2.trim());
					colClasses.push(s3.trim());
				}
			});
		allCheck=true;
		$("#datatable_where_details tbody tr").each(function() {
				if($(this).find("td.multiq select").length>0) {
					colWhere+=" "+$(this).find("td.multiq select").val()+" ";
				}
				if($(this).find("td:not(.multiq):not(.dsbtn)").length==5) {//FULL Selectors
					eq=""+$(this).find("td.equals select").val();
					colWhere+=" "+ $(this).find("td.table1 select").val()+"."+$(this).find("td.col1 select").val()+" "+
									eq+" "+$(this).find("td.table2 select").val()+"."+$(this).find("td.col2 select").val()+" ";
				} else if($(this).find("td:not(.multiq):not(.dsbtn)").length==4) {//Half Selectors
					eq=""+$(this).find("td.equals select").val();
					colWhere+=" "+ $(this).find("td.table1 select").val()+"."+$(this).find("td.col1 select").val()+" "+
									eq+" "+$(this).find("td.dvalue input").val()+" ";

					ine=$(this).find("td.dvalue input");
					if(ine.val().toLowerCase().indexOf(' and ')>0 || ine.val().toLowerCase().indexOf(' or ')>0) {
						ine.focus();
						ine.css("background",'#FFE6EA');
						$(this).find("td.dvalue input").change(function() {
								ine.css("background",'#FFFFFF');
							});
						allCheck=false;
						lgksAlert("Please, Do not use <b><i><u>and</u></i></b>, <b><i><u>or</u></i></b>.");
						return;
					}
				} else if($(this).find("td:not(.multiq):not(.dsbtn)").length==1) {//SQL Field
					colWhere+=" ("+$(this).find("td.dvalue input").val()+") ";

					ine=$(this).find("td.dvalue input");
					if(ine.val().toLowerCase().indexOf(' and ')>0 || ine.val().toLowerCase().indexOf(' or ')>0) {
						ine.focus();
						ine.css("background",'#FFE6EA');
						$(this).find("td.dvalue input").change(function() {
								ine.css("background",'#FFFFFF');
							});
						allCheck=false;
						lgksAlert("Please, Do not use <b><i><u>and</u></i></b>, <b><i><u>or</u></i></b>.");
						return;
					}
				}
			});
		colWhere=colWhere.trim();

		if(cols.length!=colNames.length) {
			lgksAlert("DataTable Design Is Inconsistant. Please Check Again.");
			return;
		}
		if(allCheck) {
			q+="&datatable_table="+encodeURIComponent($("#datatable_tables").val());
			q+="&datatable_cols="+encodeURIComponent(cols.join(","));
			q+="&datatable_colnames="+encodeURIComponent(colNames.join(","));
			q+="&datatable_hiddenCols="+encodeURIComponent(colHidden.join(","));
			q+="&datatable_searchCols="+encodeURIComponent(colSearch.join(","));
			q+="&datatable_sortCols="+encodeURIComponent(colSort.join(","));
			q+="&datatable_classes="+encodeURIComponent(colClasses.join(","));
			q+="&datatable_where="+encodeURIComponent(colWhere);

			processAJAXPostQuery(l,q,function(txt) {
					if(txt.length>0) lgksAlert(txt);
					$("#loadingmsg").hide();
				});
		}
	} else if(n==1) {
		l=getCMD()+"&action=saveparams";
		$("#loadingmsg").show();
		q="";

		$("#settings tbody input, #settings tbody select").each(function() {
				v=$(this).val();
				ori=$(this).attr("ori");
				if(v==ori) return;
				if(this.tagName=="INPUT") {
					//v="'"+v+"'";
				}
				if($(this).hasClass("json_array")) {
					if(v==null) v="";
					if(v=="true" || v=="false") v="["+v+"]";
					else if(isNaN(v)) v="['"+v+"']";
					else v="["+v+"]";
					v=v.replace("''","'");
					v=v.replace("''","'");
					v=v.replace("''","'");
					v=v.replace("''","'");
				} else if($(this).hasClass("texttype")) {
					v="'"+v+"'";
					v=v.replace("\"","`");
					v=v.replace("\"","`");
					v=v.replace("\"","`");
					v=v.replace("\"","`");
					v=v.replace("''","'");
					v=v.replace("''","'");
					v=v.replace("''","'");
					v=v.replace("''","'");
					v=v.replace("'`","'");
					v=v.replace("`'","'");
				}

				q+=$(this).attr("name").replace("_",".")+"="+v+";";
			});
		q="datatable_params="+encodeURIComponent(q);
		processAJAXPostQuery(l,q,function(txt) {
				if(txt.length>0) lgksAlert(txt);
				$("#loadingmsg").hide();
			});
	}
}
function activateDnD() {
	dParams={
				appendTo:'#pgworkspace',
				//connectToSortable:'#editor',
				snap:true,
				cursor:'move',
				revert:'invalid',
				//helper: "clone",
				helper: function(event, ui) {
					return $('<div class="elementDrag clr_blue" />').text($(this).text());
				},
			};
	$("#form_TableColumns li" ).draggable(dParams);
	$("#form_Functions li" ).draggable(dParams);

	$("#datatable_col_details_droptarget").droppable({
			activeClass: "ui-state-default",
			hoverClass: "ui-state-hover",
			drop: function(event, ui) {
				$(this).find(".placeholder").remove();
				if(ui.draggable.parents("ul").attr("id")=="form_TableColumns") {
					addTableColumn(ui.draggable.get(0));
				} if(ui.draggable.parents("ul").attr("id")=="form_Functions") {
					addSQLFunction(ui.draggable.get(0));
				}
				return false;
			}
		});
}
function showPreview() {
	l=getCMD("lists")+"&action=preview";
	parent.lgksOverlayFrame(l,"Preview !");
}
function showProperties() {
	$("#helptip2").show("slow").delay(2500).fadeOut("slow");
	$('#datatablediv').tabs('select',1);
	reloadEditor();
}
function showEditor() {
	$("#helptip3").show("slow").delay(2500).fadeOut("slow");
	$('#datatablediv').tabs('select',0);
	reloadEditor();
}
</script>
<div id=loadingdiv class='ui-widget-content' style='width:100%;height:99%;border:0px;' align=center>
	<div class=ajaxloading6>Loading ...</div>
</div>
<div id=datatablediv class='ui-widget-content' style='width:100%;height:99%;border:0px;'>
	<ul>
		<li><a href='#editor'>Editor</a></li>
		<li><a href='#settings'>Settings</a></li>
		<li><a href='#preview'>Preview</a></li>
	</ul>
	<div id=editor class='datatablepage' style='padding:0px;'>
		<div id=sidebar class='' style=''>
			<select id=dbtables class="" style='width:100% !important;height:25px;' onchange="loadColumnListMore('#sidebar #form_TableColumns',$(this).val(),'ul',activateDnD);">
				<option value="*">Select One Table</option>
			</select>
			<div class='dbcolumns' style='width:100%;height:55%;overflow-x:hidden;overflow-y:auto;border:1px solid #aaa;'>
				<ul id=form_TableColumns class='table_columns' style='width:100%;'>
				</ul>
			</div>
			<div class='dbfunctions' style='width:100%;height:40%;overflow-x:hidden;overflow-y:auto;border:1px solid #aaa;'>
				<ul id=form_Functions style='width:100%;'>
					<?=$funcArr?>
				</ul>
			</div>
		</div>
		<div id=designer style="padding:4px;">
			<table width=100% cellpadding=2 cellspacing=0 border=0 class='nostyle'>
				<tr>
					<th width=120px align=left>For Control</th><td><input id=dtcontrol type=text readonly style='font-weight:bold;color:#777;text-transform:uppercase;' value='<?="{$_REQUEST['mode']} ID:- {$_REQUEST['id']}"?>' /></td>
				</tr>
				<tr><th colspan=20><hr/></th></tr>
				<tr>
					<th width=120px align=left>Tables</th><td><input id=datatable_tables name=datatable_tables type=text readonly style='font-weight:bold;' /></td>
				</tr>
				<tr>
					<th valign=top align=left>
						<br/>Columns
					</th>
					<td>
						<div id=datatable_col_details_droptarget class='ui-corner-all' style='width:100%;height:280px;overflow:auto;border:1px solid #aaa;'>
							<table id=datatable_col_details class='editortable' width=100% cellpadding=3 cellspacing=0 border=1 style=''>
								<thead>
									<tr align=center class='ui-widget-header clr_green' style='height:20px;'>
										<th width=50px>Sl.</th>
										<th width=150px>Table</th>
										<th>Column</th>
										<th width=170px>Title</th>
										<th width=40px>Hidden</th>
										<th width=40px>Search</th>
										<th width=40px>Sort</th>
										<th width=100px>Class</th>
										<th width=75px>--</th>
									</tr>
								</thead>
								<tbody>
								</tbody>
							</table>
						</div>
					</td>
				</tr>
				<tr><td colspan=10 style="height:5px"></td></tr>
				<tr>
					<th valign=top align=left>
						Where Conditions
						<br/><br/><br/><br/>
						<div align=center>
							<button onclick="addBlankWhereRow(1)"> Full </button>
							<button onclick="addBlankWhereRow(2)"> Half </button>
							<button onclick="addBlankWhereRow(3)">Blank</button>
						</div>
					</th>
					<td>
						<div id=datatable_where_details_droptarget class='ui-corner-all' style='width:100%;height:180px;overflow:auto;border:1px solid #aaa;'>
							<table id=datatable_where_details class='editortable nostyle' width=100% cellpadding=3 cellspacing=0 border=1 style=''>
								<thead>
									<tr align=center class='ui-widget-header clr_darkmaroon' style='height:20px;'>
										<th width=9%>--</th>
										<th width=19%>Table 1</th>
										<th width=19%>Column 1</th>
										<th width=10%><b>EQ</b></th>
										<th width=19%>Table 2</th>
										<th width=19%>Column 2</th>
										<th width=5%>--</th>
									</tr>
								</thead>
								<tbody>
								</tbody>
							</table>
						</div>
					</td>
				</tr>
				<tr><td colspan=10 style="height:5px"></td></tr>
				<tr id='helptip3' class='helptip'>
					<th>&nbsp;</th>
					<th class='clr_blue' colspan=20>Please remember to save before you switch back to <i>Properties</i> or <i>Preview</i> mode.</th>
				</tr>
				<tr id='helptip1' class='helptip'>
					<th>You Can Use ::</th>
					<td colspan=10 class='clr_yellow' align=center>
						$date,$dateFrm,$dateTo and <i>#args#</i> In the textfields. Do not use <b><i><u>and</u></i></b>, <b><i><u>or</u></i></b>.
					</td>
				</tr>
			</table>
		</div>
	</div>
	<div id=settings class='datatablepage'>
		<table class='formTable' width=99% cellpadding=0 cellspacing=10 border=0 style='margin:auto;'>
			<thead>
				<tr id='helptip2' class='helptip'>
					<th class='clr_green' colspan=20>Please remember to save before you switch back to <i>Editor</i> or <i>Preview</i> mode.</th>
				</tr>
				<tr>
					<td colspan=100 align=right>
						<input id=propertiesSearch type=text title='Search ...' class='searchfield' style='border:1px solid #aaa;width:200px;height:20px;margin-right:70px;' />
					</td>
				</tr>
			</thead>
			<tbody>
				<?=printProperties();?>
			</tbody>
		</table><br/><br/>
	</div>
	<div id=preview class='datatablepage' style='padding:5px;'>
		<table id=previewtable class='editortable ui-corner-all' width=100% cellpadding=3 cellspacing=0 border=1 style=''>
			<thead class='ui-widget-header' >
				<tr align=center style='height:22px;'>
				</tr>
			</thead>
			<tbody></tbody>
		</table>
	</div>
</div>
<div id=holder style='display:none;'>
	<div id=mysqlFuncEditor title='SQL Function' align=center>
		<input name=sqlfunc type=hidden />
		<select class='data' style="width:100%;height:25px;font-size:13px;font-weight:bold;" multiple>
		</select><br/><br/>
		<button onclick="$('#mysqlFuncEditor').dialog('close');">Cancel</button>
		<button onclick="selectMysqlValue('#mysqlFuncEditor');">Select</button>
	</div>
	<select id=sqlopts>
		<?=$sqlOpts?>
	</select>
	<div id=colClasses title='Select Column Classes' align=center>
		<?php
			include "resources/datatables/colclasses.php";
		?>
	</div>
</div>
<?php
}
unset($_SESSION['frmData']);
?>
