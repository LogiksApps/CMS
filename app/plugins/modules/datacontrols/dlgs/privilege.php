<?php
if(!defined('ROOT')) exit('No direct script access allowed');

if(!isset($_REQUEST["forsite"])) {
	dispErrMessage("HostSite Is Required But Not Found","CMS Error",400);
	exit();
}

if(!isset($_REQUEST['closeHandle'])) $_REQUEST['closeHandle']="";
else $_REQUEST['closeHandle']="#".$_REQUEST['closeHandle'];

loadModule("dbcon");$dbCon=getDBControls();

$toolbtns=array();
$a=loadModuleLib($_REQUEST['mode'],"editprops");
if($a) {
	$toolbtns=getToolButtons();
} else {
	echo "<h3 align=center>Privilege Editor Support Not Found</h3>";
	exit();
}
$options="<option value='*'>All Options</option>";
foreach($toolbtns as $a=>$b) {
	$options.="<option value='$a'>$b</option>";
}

$privilegeData=array();
$sql="SELECT id,name FROM "._dbtable("privileges",true)." where blocked='false' and (site='*' OR site='{$_REQUEST['forsite']}') and id>2";
$result=_dbQuery($sql,true);
if($result) {
	$privilegeData=$dbCon->fetchAllData($result);
	$dbCon->freeResult($result);
}
?>
<style>
#privilegeEditor .ui-multiselect.ui-widget {
	width:350px !important;
	height:25px;
	font-weight:bold;
	padding:3px;
}
#privilegeEditor select {
	width:350px !important;
	height:25px;
	font-size:12px !important;
}
#privilegeEditor td.supportcol select {
	width:250px !important;
}
#privilegeEditor input[type=text] {
	width:350px !important;
	height:23px;
	border:1px solid #aaa;
	font-weight:bold;
}
</style>
<div id=privilegeEditor style='width:620px;padding:5px;'>
	<table width=100% border=0 cellpadding=0 cellspacing=10>
	<?php
		echo "<input name=id type=hidden value='{$_REQUEST['id']}' />";
		foreach($privilegeData as $q) {
			$nm=$q['name'];
			$nm=str_replace("_"," ",$nm);
			$nm=strtoupper($nm);
			$sf="<tr><td class=titlecol width=200px><b>{$nm}</b></td><td class=valuecol><select name={$q['name']} multiple>$options</select></td></tr>";
			echo $sf;
		}
	?>
	</table>
	<br/>
	<div align=center>
		<button onclick="closeMe()">Cancel</button>
		<button onclick="savePrivileges()">Save</button>
	</div>
</div>
<script language=javascript>
site="<?=$_REQUEST["forsite"]?>";
mode="<?=$_REQUEST["mode"]?>";
lnkLst="services/?scmd=datacontrols.lists&mode="+mode+"&forsite="+site;
lnkPrivileges="services/?scmd=datacontrols.privilege&mode="+mode+"&forsite="+site;
$(function() {
	$("#privilegeEditor select:not(.nostyle):not(.multiselect)").addClass("ui-widget-header ui-corner-all");
	$("#privilegeEditor button").button();
	$("#privilegeEditor select[multiple]").multiselect({
				minWidth:300,
				selectedList:4,
				noneSelectedText:"Select Actions",
				selectedList:3,
			});
	fetchForm();
});
function fetchForm() {
	l=lnkPrivileges+"&action=fetch";
	q="&id="+$("#privilegeEditor input[name=id]").val();
	processAJAXPostQuery(l,q,function(txt) {
			json=$.parseJSON(txt);
			updateFormData(json);
		});
}
function updateFormData(json) {
	for(var key in json) {
		if($("#privilegeEditor select[name="+key+"][multiple]").length>0) {
			sele=$("#privilegeEditor select[name="+key+"][multiple]");
			sele.multiselect("destroy");
			sele.val(json[key].split(","));
			sele.multiselect({
					minWidth:400,
					selectedList:4,
				});
		} else {
			 $("#privilegeEditor input[name="+key+"]").val(json[key]);
			 $("#privilegeEditor textarea[name="+key+"]").val(json[key]);
			 $("#privilegeEditor select[name="+key+"]").val(json[key]);
		}
	}
}
function savePrivileges() {
	if($("#privilegeEditor").find("input,textarea,select").length<=1) {closePropsDialog(); return;}
	l=lnkPrivileges+"&action=save";
	q="";	
	$("#privilegeEditor").find("input,textarea,select").each(function() {
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
