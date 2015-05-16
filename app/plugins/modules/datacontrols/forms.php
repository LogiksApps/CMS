<?php
if(!defined('ROOT')) exit('No direct script access allowed');

if(!isset($_REQUEST["forsite"])) {
	dispErrMessage("HostSite Is Required But Not Found","CMS Error",400);
	exit();
}

loadModule("dbcon");
$dbCon=getDBControls();

$_SESSION['frmData']=array();
if($_REQUEST["mode"]=="forms") {
	$sql="SELECT id,title,header,footer,frmdata as data,engine,layout,submit_table,submit_wherecol FROM "._dbtable("forms")." WHERE id={$_REQUEST['id']}";
	$r=_dbQuery($sql);
	if($r) {
		$a=_dbData($r);
		_db()->freeResult($r);
		if(isset($a[0])) $_SESSION['frmData']=$a[0];
	}
} elseif($_REQUEST["mode"]=="search") {
	$sql="SELECT id,title,header,footer,search_form as data,engine,datatable_table as submit_table FROM "._dbtable("search")." WHERE id={$_REQUEST['id']}";
	$r=_dbQuery($sql);
	if($r) {
		$a=_dbData($r);
		_db()->freeResult($r);
		if(isset($a[0])) $_SESSION['frmData']=$a[0];
	}
} else {
	dispErrMessage("Selected Mode Not Supported","CMS Error",400);
	exit();
}

loadModule("page");

loadModule("editor");
loadEditor("codemirror");

$btns=array();
$btns[sizeOf($btns)]=array("title"=>"Clear","icon"=>"clearicon","tips"=>"Clear Form","onclick"=>"clearEditor()");
$btns[sizeOf($btns)]=array("title"=>"Reload","icon"=>"reloadicon","tips"=>"Reload Form","onclick"=>"reloadEditor()");
$btns[sizeOf($btns)]=array("bar"=>"||");
$btns[sizeOf($btns)]=array("title"=>"Properties","icon"=>"gearicon","tips"=>"Properties Window","onclick"=>"closePropertiesWindow('toggle')");
$btns[sizeOf($btns)]=array("title"=>"Script","icon"=>"codeicon","tips"=>"Javascript","onclick"=>"showJSEditor()");
$btns[sizeOf($btns)]=array("title"=>"Preview","icon"=>"viewicon","tips"=>"Preview Form","onclick"=>"showPreview('toggle')");
$btns[sizeOf($btns)]=array("bar"=>"||");
$btns[sizeOf($btns)]=array("title"=>"Save","icon"=>"saveicon","tips"=>"Save Form","onclick"=>"saveControl()");
$btns[sizeOf($btns)]=array("title"=>"Info","icon"=>"infoicon","tips"=>"Show Info","onclick"=>"showInfo()");

$layout="apppage";
$params=array("toolbar"=>$btns,"contentarea"=>"printContent");

printPageContent($layout,$params);

_js(array("jquery.multiselect","jquery.listAttributes","jquery.ui-timepicker","jquery.editinplace","jquery.tablednd"));//"jquery.tagit",
_css(array("jquery.multiselect","jquery.tagit","colors","styletags","formtable","formfields"));

?>
<script src='<?=$webPath?>js/common.js' type='text/javascript' language='javascript'></script>
<script src='<?=$webPath?>js/codeeditor.js' type='text/javascript' language='javascript'></script>
<script src='<?=$webPath?>js/frmeditor.js' type='text/javascript' language='javascript'></script>
<script src='<?=$webPath?>js/shortcuts.js' type='text/javascript' language='javascript'></script>
<link href='<?=$webPath?>css/frmeditor.css' rel='stylesheet' type='text/css' media='all' />
<?php
function printContent() {
	//Load Plugins
	$fs=scandir(dirname(__FILE__)."/resources/formElements/");
	$pluginArr=array();
	foreach($fs as $a) {
		if(strtolower(substr($a,strlen($a)-3))=="frm") {
			$t=substr($a,0,strlen($a)-4);
			$t=ucwords($t);
			$pluginArr[$t]=trim(file_get_contents(dirname(__FILE__)."/resources/formElements/$a"));
		}
	}
?>
<style>
#toolbar button { width:120px; }
#designer,#sidebar,#editor .formTable {display:none;}
</style>
<script language=javascript>
site="<?=$_REQUEST["forsite"]?>";
curSite="<?=SITENAME?>";
frmTable="<?=$_SESSION['frmData']['submit_table']?>";
lnkLst=getServiceCMD("datacontrols.lists") + "&mode=<?=$_REQUEST["mode"]?>";
$(function() {
	$("#sidebar").css("height",($(window).height()-$("#toolbar").height()-7)+"px");
	$("#designer").css("height",($("#sidebar").height())+"px");
	$("#sidebar").css("width","180px");
	$("#designer").css("width",($("#pgworkspace").width()-$("#sidebar").width()-22)+"px");
	$("#designer").css("width",($("#page").width()-$("#sidebar").width()-22)+"px");

	$("#properties select,#sidebar select").addClass("ui-state-default ui-corner-all");

	closePropertiesWindow(true);
	updateFormUI("#editor");
	showPreview(false);

	loadTableList('#sidebar #dbtables',function() {
			$("#sidebar select#dbtables").val(frmTable);
			loadColumnList('#sidebar #form_TableColumns',$("#sidebar select#dbtables").val(),'ul',activateDnD);

			$("#loadingdiv").hide();
			$("#designer,#sidebar").fadeIn('fast',function() {
					$("#editor .formTable").fadeIn();
				});
		});
	reloadEditor();
});
function getCMD(cmdMode) {
	if(cmdMode==null) cmdMode="forms";
	return "services/?scmd=datacontrols."+cmdMode+"&site="+curSite+"&mode=<?=$_REQUEST["mode"]?>&forsite=<?=$_REQUEST["forsite"]?>&id=<?=$_REQUEST['id']?>";
}
function clearEditor() {
	$("#editor .formTable tbody").html("");
	activateDnD();
}
function clearFullEditor() {
	$("#editor .formTable thead td.formheader strong").html("");
	$("#editor .formTable tbody").html("");
	$("#editor .formTable tfoot td.formfooter strong").html("");
	activateDnD();
}
function reloadEditor() {
	l=getCMD()+"&action=fetch";
	$("#loadingmsg").show();
	$("#editor .formTable tbody").html("<tr><td colspan=20 class=ajaxloading6><br/><br/><br/>Loading ...</td></tr>");
	processAJAXQuery(l,function(txt) {
			if(txt.length>0) {
				json=$.parseJSON(txt);
				s="<thead><tr><td class='formheader' colspan='100'><strong>"+json.header+"</strong></td></tr></thead>";
				s+="<tbody>"+json.data+"</tbody>";
				s+="<tfoot><tr><td class='formfooter' colspan='100'><strong>"+json.footer+"</strong></td></tr></tfoot>";
				$("#editor .formTable").html(s);
				$("#loadingmsg").hide();

				updatePropBox(null);
				closePropertiesWindow(true);
				updateFormUI("#editor");
				activateDnD();
				showPreview(false);
			}
		});
}
function activateDnD() {
	dParams={
				appendTo:'#pgworkspace',
				connectToSortable:'#editor',
				snap:true,
				cursor:'move',
				revert:'invalid',
				//helper: "clone",
				helper: function(event, ui) {
					return $('<div class="elementDrag clr_green" />').text($(this).text());
				},
			};
	$("#form_Elements li" ).draggable(dParams);
	$("#form_TableColumns li").draggable(dParams);

	$("#editor").droppable({
			activeClass: "ui-state-active",
			hoverClass: "ui-state-hover",
			drop: function(event, ui) {
				isPreview=!$(frmid+" .formTable").hasClass("debug");
				if(isPreview) {
					return false;
				}

				$(this).find(".placeholder").remove();
				if(ui.draggable.parents("ul").attr("id")=="form_Elements") {
					$(createFormToolRow(ui.draggable.clone())).appendTo("#editor .formTable tbody");
				} else {
					$(createFormDataRow(ui.draggable)).appendTo("#editor .formTable tbody");
				}

				updateFormUI("#editor");
				return true;
			}
		});
}
function cleanFormForSaving() {
	setupRowTools("#editor",false);
	try {
		$("#editor .datefield,#editor .timefield,#editor .datetimefield").datepicker("destroy");
	} catch(e) {
	}
	$("#editor td.columnInput div").detach();
	$("#editor .formTable tbody tr.active").removeClass("active");
}
function saveControl() {
	error=checkFormForErrors();
	if(error.length>0) {
		lgksAlert(error);
		return;
	}
	cleanFormForSaving();
	trs=[];
	l=getCMD()+"&action=save";
	q="&stable="+$("#dbtables").val();
	q+="&header="+encodeURIComponent($("#editor .formTable thead td.formheader strong").html());
	q+="&footer="+encodeURIComponent($("#editor .formTable  tfoot td.formfooter strong").html());
	q+="&data="+encodeURIComponent($("#editor .formTable tbody").html());

	processAJAXPostQuery(l,q,function(txt) {
			if(txt.length>0) lgksAlert(txt);
			else reloadEditor();
		});
}
function closePropertiesWindow(close) {
	$("#designer .propertiesarea").css("width","100%");
	if(close=="toggle") {
		if($("#designer .propertiesarea").is(":visible")) {
			closePropertiesWindow(true)
		} else {
			closePropertiesWindow(false)
		}
	} else if(close) {
		$("#designer .propertiesarea").hide();
		$("#designer .formarea").css("height","100%");
	} else {
		h1=$("#designer").height();
		hp=140;
		//$("#designer .formarea").css("height","77%");
		//$("#designer .propertiesarea").css("height","23%");

		$("#designer .formarea").css("height",(h1-hp)+"px");
		$("#designer .propertiesarea").css("height",(hp)+"px");
		$("#designer .propertiesarea").show();

		updatePropBox(selectedTr);
	}
}
function activateColumnsList(table) {
	if(table==frmTable) {
		loadColumnList('#sidebar #form_TableColumns',table,'ul',reloadEditor);
	} else {
		loadColumnList('#sidebar #form_TableColumns',table,'ul',clearEditor);
	}
}
function showInfo() {
	msg="<table border=0>";
	msg+="<tr><th width=85px>CTRL + S</th><td>Save Form Design</td></tr>";
	msg+="<tr><th width=85px>ALT + P</th><td>Toggle Properties Window</td></tr>";
	msg+="<tr><th width=85px>ALT + O</th><td>Toggle Form Preview</td></tr>";
	msg+="<tr><th width=85px>ALT + R</th><td>Reload Form</td></tr>";
	msg+="<tr><th width=85px>ALT + C</th><td>Clear Form</td></tr>";
	msg+="<tr><th width=85px>ESC</th><td>Close Properties Window</td></tr>";
	msg+="</table>";
	lgksAlert(msg,"Shortcuts");
}
function showJSEditor() {
	editCode("<?=$_REQUEST['id']?>",'script','js');
}
function createFormDataRow(ele) {
	if(ele==null) return "";

	name=ele.text();
	title=ele.attr('title');
	type=ele.attr('type').toLowerCase();
	val=ele.attr('default');
	nullable=ele.attr('nullable').toLowerCase();
	btype=ele.attr('btype').toLowerCase();

	s="<tr nm='"+name+"' type=\""+type+"\" nullable='"+nullable+"' default='"+val+"' btype='"+btype+"' title='"+title+"'>";
	s+="<td class='dbtns' width=80px valign=top>"+getRowToolButtons()+"</td>";
	s+="<td class='columnName'>"+title+"</td>";
	s+="<td class='columnEqual'>:</td>";
	s+="<td class='columnInput'>"+createInputForType(ele,val)+"</td>";
	s+="</tr>";

	return s;
}
function createFormToolRow(cmdText) {
	if(cmdText==null) return "";
	cmdText=cmdText.text().toLowerCase();

	name="Test";
	title=name;
	type="string";
	val="";
	nullable="";
	btype="";

	if(cmdText=="XXX") return "";
	<?php
		foreach($pluginArr as $a=>$b) {
			$a=strtolower($a);
			echo "else if(cmdText=='$a') { return \"$b\"; }\n";
		}
	?>
	else if(cmdText=="datarow") {
		s="<tr nm='"+name+"' type=\""+type+"\" nullable='"+nullable+"' default='"+val+"' btype='"+btype+"' title='"+title+"'>";
		s+="<td class='columnName'></td>";
		s+="<td class='columnEqual'>:</td>";
		s+="<td class='columnInput'>"+"<input name='"+name+"' class='textfield' type='text' id='"+name+"' value='"+val+ "' />"+"</td>";
		s+="</tr>";
		return s;
	} else {
		return "";
	}
}
</script>
<div id=loadingdiv class='ui-widget-content' style='width:100%;height:99%;border:0px;' align=center>
	<div class=ajaxloading6>Loading ...</div>
</div>
<div id=sidebar class='ui-widget-header' style=''>
	<select id=dbtables class="" style='width:100% !important;height:25px;' onchange="activateColumnsList($(this).val());">
		<option value="*">Select One Table</option>
	</select>
	<div class='' style='width:100%;height:60%;overflow-x:hidden;overflow-y:auto;border:1px solid #aaa;'>
		<ul id=form_TableColumns class='table_columns' style='width:100%;'></ul>
	</div>
	<div class='' style='width:100%;height:40%;overflow-x:hidden;overflow-y:auto;border:1px solid #aaa;'>
		<ul id=form_Elements style='width:100%;'>
			<?php
				foreach($pluginArr as $a=>$b) {
					echo "<li>$a</li>";
				}
			?>
			<li>DataRow</li>
		</ul>
	</div>
</div>
<div id=designer class='ui-widget-content' style='background:transparent;border:0px;'>
	<div id=editor class='formarea' style='background:white;'>
		<table class='formTable nostyle' align=center width=100%>
			<thead><tr><td class='formheader' colspan='100'><strong><?=$_SESSION['frmData']['header']?></strong></td></tr></thead>
			<tbody>
			<?=$_SESSION['frmData']['data']?>
			</tbody>
			<tfoot><tr><td class='formfooter' colspan='100'><strong><?=$_SESSION['frmData']['footer']?></strong></td></tr></tfoot>
		</table>
		<br/>
	</div>
	<div id=properties class='propertiesarea ui-widget-content'>
		<?php
			include "props/htmltags.php";
		?>
	</div>
</div>
<div style='display:none;'>
<div id=codeEditor title='Code Editor' style='overflow:hidden;'>
	<textarea name=codearea id=codearea></textarea>
</div>
</div>
<?php
}
unset($_SESSION['frmData']);
?>
