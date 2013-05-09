<?php
if(!defined('ROOT')) exit('No direct script access allowed');

if(!isset($_REQUEST["forsite"])) {
	dispErrMessage("HostSite Is Required But Not Found","CMS Error",400);
	exit();
}
user_admin_check(true);
checkUserSiteAccess($_REQUEST['forsite'],true);

loadModule("page");

$layout="apppage";
$params=array("toolbar"=>"printToolbar","contentarea"=>"printContent");

printPageContent($layout,$params);
?>
<style>
#toolbar select {
	margin-top:0px;
	width:250px;
	height:25px;
}
#toolbar button {
	width:130px;
}
#toolbar button div {
	width:100%;
	padding-left:10px;
}
#selectorData tr:hover {
	background:#F8FFE0;
	cursor:pointer;
}
#selectorData tr.blocked {
	background:#F2DFED;
}
a.toolbtns {
	cursor:pointer;
	color:#555;
	padding:5px;
	margin-left:7px;
	font:bold 14px Georgia;
}
a.toolbtns:hover {
	opacity:0.5;
	color:blue;
}
.msgboxd,.newlookupform {
	font-size:15px;
	width:400px;
	margin:auto;margin-top:10px;margin-right:10px;
	padding:4px;
	text-align:justify;
	float:right;
}
#newlookupform {display:none;}
.editarea {
	font-size:12px;
	width:500px;
	height:95%;
	margin:auto;
	text-align:justify;
	float:left;
	margin-top:10px;margin-left:10px;
}
#lookup_edit {
	width:100%;height:100%;resize:none;
	font-size:13px;
	font:14px verdana;
}
#lookup_edit[readonly] {
	background:#EEEEEE;
}
</style>
<script>
lid="";
mdc="";
$(function() {
	$("select:not(multiple)").addClass("ui-state-active ui-corner-all");
	$(".tabs").tabs();
	if($(window).width()<1000) {
		$(".editarea").css("width",($(window).width()-$("#msgboxdata").width()-100)+"px");
		$("#msgboxdata").css("font-size","12px");
		$("#selector").css("width","100px");
	}
	reloadList();

	$("#closebtn").hide();
	$("#savebtn").hide();
});
function reloadList(x) {
	closeEdit();
	s1=getServiceCMD("blocks.lookups")+"&action=list";
	$("#selector").html("Loading ...");
	$("#loadingmsg").show();
	$("#selector").load(s1,function() {
			$("#selector").val(x+".dat");
			$("#loadingmsg").hide();
			viewData();
		});
}
function viewData() {
	lid=$('#selector').val();
	closeEdit();

	s1=getServiceCMD("blocks.lookups")+"&action=data&lookup="+lid;
	$('#lookup_edit').html("Loading Data ...");
	$("#loadingmsg").show();

	processAJAXQuery(s1,function(txt) {
					$('#lookup_edit').val(txt);
					$("#loadingmsg").hide();
			});
}
function loadData() {
	lid=$('#selector').val();
	closeEdit();

	s1=getServiceCMD("blocks.lookups")+"&action=data&lookup="+lid;
	$('#lookup_edit').html("Loading Data ...");
	$("#loadingmsg").show();

	processAJAXQuery(s1,function(txt) {
					$('#lookup_edit').val(txt);
					$("#loadingmsg").hide();
					$('#lookup_edit').removeAttr("readonly");
					$('#lookup_edit').focus();

					$(".edit").show();
					$(".noedit").hide();
					$(".newlookupform").hide();
			});
}
function closeEdit() {
	$('#lookup_edit').val("No Lookup Loaded");
	$('#lookup_edit').attr("readonly","readonly");
	$(".edit").hide();
	$(".noedit").show();
}
function saveEdit() {
	data=encodeURIComponent($('#lookup_edit').val());
	s1=getServiceCMD("blocks.lookups")+"&action=save&lookup="+lid;
	q1="&data="+data;

	processAJAXPostQuery(s1,q1,function(txt) {
			if(txt=="success") {

			} else {
				lgksAlert(txt);
			}
		});
}
function deleteLookup() {
	lgksConfirm("Do you really want to delete the selected Lookup <b>"+$('#selector option:selected').text()+"</b> ?<br/>",
					"Delete Lookup ?",function() {
							closeEdit();
							s1=getServiceCMD("blocks.lookups")+"&action=delete&lookup="+$('#selector').val();
							$("#loadingmsg").show();
							processAJAXQuery(s1,function(txt) {
									if(txt.length>0) {
										lgksAlert(txt);
									} else {
										reloadList();
									}
								});
						});
}
function createBlankLookup(v) {
	if(v.length>0) {
		v1=v.split(" ").join("_");
		if(v!=v1) {
			lgksAlert("No Space Is Allowed In The Lookup Names");
			return;
		}
		s1=getServiceCMD("blocks.lookups")+"&action=blank&lookup="+v;
		$("#loadingmsg").show();
		processAJAXQuery(s1,function(txt) {
				if(txt=="success") {
					$('#nlf_blank_name').val('');
					reloadList(v);
				} else if(txt.length>0) {
					lgksAlert(txt);
				} else {
					reloadList(v);
				}
			});
	}
}
function checkFile() {
	f=$("#nlf_upload form #lookupfile").val();
	f1=f.toLowerCase();
	if(f1.lastIndexOf(".txt")==-1) {
		$("#nlf_upload form #lookupfile").val("");
	    lgksAlert("Please upload only .txt extention files");
	    return false;
	}
	return true;
}
function clearUploadField(f) {
	$("#nlf_upload form #lookupfile").val("");
	reloadList(f);
}
</script>
<?php
function printContent() { ?>
<div id=msgboxdata class='msgboxd ui-widget-header ui-border-all'>
	Lookups are file based data sources that can help users during form filling. They provide a central data dictionary which is used
	by the autocomplete fields to popup help support based on what User has typed eg. country names, stations, commands, ship names, etc...
	Ofcourse they are not db based, for that you can use <b>Selectors</b>. They can be real handy in forms and pages. Here You can create
	as many Lookups as you want. <br/><br/>
	<b>P.S.</b><br/>
	<ul style='font-size:12px;'>
		<li><b>Please type One Record/Data In One Line</b></li>
		<li><b>While Searching/Indexing, The System Will Search By Parts</b></li>
	</ul>
	<br/>
	You can also directly upload simple text files in single data for line format (simmillar to a list) to be used as Lookups
</div>
<div class='editarea'>
<textarea id=lookup_edit class='ui-widget-content ui-border-all' readonly>No Lookup Loaded</textarea>
</div>
<div id=newlookupform class='newlookupform ui-widget-content ui-border-all'>
	<div class="tabs">
		<ul>
			<li><a href='#nlf_blank'>Blank Lookup</a></li>
			<li><a href='#nlf_upload'>Upload Lookup</a></li>
			<li><a onclick="$('#newlookupform').hide();">Close</a></li>
		</ul>
		<div id=nlf_blank>
			<table width=100%>
				<tr><th>Lookup Name</th><td><input type=text id=nlf_blank_name  style='width:100%;border:1px solid #aaa;' /></td></tr>
				<tr><td colspan=10>&nbsp;</td></tr>
				<tr><td colspan=10 align=center>
					<button onclick="$('#nlf_blank_name').val('');">Clear</button>
					<button onclick="createBlankLookup($('#nlf_blank_name').val());">Create</button>
				</td></tr>
				<tr><td colspan=10><hr/></td></tr>
				<tr><td colspan=10 style='font-size:12px;color:maroon;' align=center>No Blanks Are Allowed In The Name</td></tr>
			</table>
		</div>
		<div id=nlf_upload style='padding:5px;'>
			<form method=POST onsubmit="return checkFile()" enctype="multipart/form-data" target="nlf_upload_frame"
				action="services/?scmd=blocks.lookups&forsite=<?=$_REQUEST["forsite"]?>&action=upload">

				<input type="hidden" name="MAX_FILE_SIZE" value="100000" />
				<table width=100%>
					<tr>
						<th width=100px>Lookup File</th>
						<td><input type=file id=lookupfile name=lookupfile  style='width:100%;border:1px solid #aaa;' onchange="return checkFile();" /></td>
					</tr>
					<tr><td colspan=10>&nbsp;</td></tr>
					<tr><td colspan=10 align=center>
						<button type=reset>Clear</button>
						<button type=submit>Upload</button>
					</td></tr>
					<tr><td colspan=10><hr/></td></tr>
					<tr><td colspan=10 style='font-size:12px;color:maroon;' align=left>
						Uploaded file must be a text file only with extension (.txt). <br/>
						All dots(.) in file name will be replaced with (_).<br/>
						<b>PS Note : </b> Upload File May Replace The Existing Lookup Files.
					</td></tr>
				</table>
			</form>
			<iframe id=nlf_upload_frame name=nlf_upload_frame style='display:none'></iframe>
		</div>
	</div>
</div>
<?php
}
function printToolbar() { ?>
<button class='noedit' onclick="reloadList()" style='width:100px;' ><div class='reloadicon'>Reload</div></button>
<select class='noedit' id=selector onchange='viewData()'></select>
::
<button class='noedit' onclick="loadData()" style='width:100px;' ><div class='editicon'>Edit</div></button>
<button id=savebtn class='edit' onclick="saveEdit()" style='width:100px;' ><div class='saveicon'>Save</div></button>
<button id=closebtn  class='edit'onclick="closeEdit();viewData();" style='width:100px;' ><div class='closeicon'>Close</div></button>
::
<button class='noedit' onclick="$('#newlookupform').show();" title="Create New Empty Lookup" style="width:150px;" ><div class='addicon'>New Lookup</div></button>
<button class='noedit' onclick="deleteLookup()" title="Delete Lookup File" style="width:150px;" ><div class='deleteicon'>Delete Lookup</div></button>
<?php } ?>
