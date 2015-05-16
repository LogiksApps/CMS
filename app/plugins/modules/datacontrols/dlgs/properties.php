<?php
if(!defined('ROOT')) exit('No direct script access allowed');

if(!isset($_REQUEST["forsite"])) {
	dispErrMessage("HostSite Is Required But Not Found","CMS Error",400);
	exit();
}

if(!isset($_REQUEST['closeHandle'])) $_REQUEST['closeHandle']="";
else $_REQUEST['closeHandle']="#".$_REQUEST['closeHandle'];

loadModule("dbcon");$dbCon=getDBControls();

$propsFile=dirname(dirname(__FILE__))."/props/{$_REQUEST['mode']}.php";
?>
<style>
#propseditor .ui-multiselect.ui-widget {
	width:350px !important;
	height:25px;
	font-weight:bold;
	padding:3px;
}
#propseditor select {
	width:350px !important;
	height:25px;
	font-size:12px !important;
}
#propseditor td.supportcol select {
	width:250px !important;
}
#propseditor input[type=text] {
	width:350px !important;
	height:23px;
	border:1px solid #aaa;
	font-weight:bold;
}
</style>
<div id=propseditor style='width:800px;padding:5px;'>
	<?php
		echo "<input name=id type=hidden value='{$_REQUEST['id']}' />";
		include $propsFile;
	?>
	<br/>
	<div align=center>
		<button onclick="closeMe()">Cancel</button>
		<button onclick="saveProperties()">Save</button>
	</div>
	<br/>
</div>
<script language=javascript>
site="<?=$_REQUEST["forsite"]?>";
mode="<?=$_REQUEST["mode"]?>";
lnkLst=getServiceCMD("datacontrols.lists")+"&mode="+mode;
lnkProps=getServiceCMD("datacontrols.properties")+"&mode="+mode;
$(function() {
	$("#propseditor select:not(.nostyle):not(.multiselect)").addClass("ui-widget-header ui-corner-all");
	$("#propseditor button").button();
	$("#propseditor select[multiple]").multiselect({
				minWidth:300,
				selectedList:4,
			});
	fetchForm();
});
function fetchForm() {
	l=lnkProps+"&action=fetch";
	q="&id="+$("#propseditor input[name=id]").val();
	processAJAXPostQuery(l,q,function(txt) {
			json=$.parseJSON(txt);
			if($("#propseditor select.dbtable").length>0) {
				$("#propseditor select.dbtable").each(function() {
						nm=$(this).attr("name");
						$(this).val(json[nm]);
						fr=$(this).attr("for");
						if($("#propseditor select.columnlist[name="+fr+"]").length>0) {
							loadColumnList("#propseditor select.columnlist[name="+fr+"]",json[nm],"select", function() {
									$("#propseditor select.columnlist[name="+fr+"]").val(json[fr]);
								});
						}
					});
			}
			updateFormData(json);
		});
}
function updateFormData(json) {
	for(var key in json) {
		if($("#propseditor select[name="+key+"]").hasClass("columnlist")) continue;

		if($("#propseditor select[name="+key+"][multiple]").length>0) {
			sele=$("#propseditor select[name="+key+"][multiple]");
			sele.multiselect("destroy");
			sele.val(json[key].split(","));
			sele.multiselect({
					minWidth:400,
					selectedList:4,
				});
		} else {
			 $("#propseditor input[name="+key+"]").val(json[key]);
			 $("#propseditor textarea[name="+key+"]").val(json[key]);
			 $("#propseditor select[name="+key+"]").val(json[key]);
		}
	}
}
function saveProperties() {
	if($("#propseditor").find("input,textarea,select").length<=1) {closePropsDialog(); return;}
	l=lnkProps+"&action=save";
	q="";
	$("#propseditor").find("input,textarea,select").each(function() {
			name=$(this).attr("name");
			value=$(this).val();
			if(name!=null && value!=null && name.length>0 && name!="undefined") {
				q+="&"+name+"="+encodeURIComponent(value);
			}
		});
	processAJAXPostQuery(l,q,function(txt) {
				if(txt.length>0) {
					lgksAlert(txt);
					return;
				}
				closeMe(true);
			});
}
function closeMe(reload) {
	closeDialog('<?=$_REQUEST['closeHandle']?>',reload);
}
</script>