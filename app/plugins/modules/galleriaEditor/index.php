<?php
if(!defined('ROOT')) exit('No direct script access allowed');
session_check(true);
user_admin_check();

loadModule("page");

$params["toolbar"]="printToolbar";
$params["contentarea"]="printContent";

$layout="apppage";

printPageContent("apppage",$params);

function printToolbar() { ?>
<button onclick="reloadList()" style='width:100px;' ><div class='reloadicon'>Reload</div></button>
<select id=bannerList class='ui-widget-header' style='width:200px;height:27px;margin-top:4px;' onchange="loadPhotoset($('#bannerList').val());">
</select>
||
<button onclick="createPhotoSet()" title="Create New Banner/Gallery" style="width:100px;" ><div class='addicon'>Create</div></button>
<button onclick="deletePhotoSet()" title="Delete Banner/Gallery" style="width:100px;" ><div class='deleteicon'>Delete</div></button>
||
<button onclick="$('#infoBox').dialog({resizable:false,width:500,height:'auto',modal:true,});" style='width:100px;' ><div class='infoicon'>About</div></button>
<?php
}
function printContent() { 
	$webPath=getWebPath(__FILE__);
?>
<link href='<?=$webPath?>style.css' rel='stylesheet' type='text/css' /> 
<style>
#toolbar .left {
	padding-top:0px !important;
}
.viewer img {margin:auto;}
</style>
<div class='photoList ui-widget-header' style='width:100%;height:100%;background:white;'>
	<div id=photoViewer style='width:52%;height:100%;float:left;overflow:hidden;'>
		<div class='viewer nophoto'></div>
		<div class='tools ui-widget-content ui-corner-all'>
			<button class='left' title='Cycle Through Zoom Options' onclick="showOrginalZoom=!showOrginalZoom;viewPhoto(lastPhoto);"><div class='bigcycleicon'>&nbsp;</div></button>
			<button class='left' title='Delete This Media' onclick="deleteMedia()"><div class='bigdeleteicon'>&nbsp;</div></button>
			
			<button class='right' title='Save' onclick="saveDescription()" style='height: 70px;'><div class='bigsaveicon'>&nbsp;</div></button>
			<textarea id=photoDescription class='photoDescription' style='width:50%;height:50px;border:1px solid #aaa;margin:4px;resize:none;float:right;' readonly></textarea>
			<input id=photoLink class='photoLink' type=text style='width: 50%; height: 20px; border: 1px solid rgb(170, 170, 170); margin: 4px; resize: none; float: right;' readonly />
		</div>
	</div>
	<div style='width:45%;height:100%;float:right;overflow:hidden;'>
		<div id=gallery class='gallery' style='width:99%;height:70%;overflow:auto;' align=center></div>
		<div id=uploadForm style='width:99%;height:30%;' align=center>
			<hr/><br/>
			<form method=POST onsubmit="return checkFile();" enctype="multipart/form-data" target="upload_frame" 
				action="services/?scmd=galleriaEditor&site=<?=SITENAME?>&forsite=<?=$_REQUEST["forsite"]?>&action=upload&js=clearUploadForm">
				
				<input type="hidden" name="photoset" value="" />
				<table class='uploadTable' width=100% cellspacing=0 cellpadding=2 border=0 style='display:none;'>
					<tr>
						<th width=100px>Photo File</th>
						<td><input type=file id=photofile name=photofile  style='width:90%;border:1px solid #aaa;background:white;' onchange="return checkFile();" /></td>
					</tr>
					<tr>
						<th width=100px>Description</th>
						<td><textarea id=photofile name=photofile  style='width:89%;height:35px;border:1px solid #aaa;resize:none;' ></textarea></td>
					</tr>
					<tr><td colspan=10 align=center>
						<button type=reset>Clear</button>
						<button type=submit>Upload</button>						
					</td></tr>
				</table>
			</form>
			<iframe id=upload_frame name=upload_frame style='display:none'></iframe>
		</div>
	</div>
</div>
<div style='display:none;'>
	<div id=infoBox title='About Galleria' >
		<b>Gallery n Banners</b> is a central place to manage all the PhotoSets that are being used across
		all the banners, galleries, slideshows, etc ... that are using photoset service to create banner 
		and galleries. You can also setup the attributes and properties that are used by the banners that 
		use these central attributes via photoset service.
	</div>
</div>
<script language=javascript>
lnk=getServiceCMD("galleriaEditor");
var photoSet="";
var lastPhoto="";
showOrginalZoom=false;

$(function() {
	$("#gallery").delegate("div.thumbnail","click",function() {
			$("#gallery div.thumbnail.activeThumbnail").removeClass("activeThumbnail");
			$(this).addClass("activeThumbnail");
			viewPhoto($(this).attr('rel'));
		});
	reloadList();
});
function reloadList(v) {
	photoSet=null;
	lastPhoto=null;
	q=lnk+"&action=listsets&format=select";
	$("#bannerList").html("<option vaue=''>Loading ...</option>");
	
	$("#photoViewer .viewer").html("");
	$("#photoViewer .viewer").removeClass("nophoto");
	$("#photoViewer .viewer").addClass("nophoto");
	$("#photoViewer .tools>*").hide();
	$("#uploadForm .uploadTable").hide();
	$("#photoViewer .photoDescription").val("");
	$("#uploadForm form input[name=photofile]").val("");
	
	$("#bannerList").load(q, function() {
			if(v!=null && v.length>0) $("#bannerList").val(v);
			loadPhotoset($("#bannerList").val());
		});
}
function loadPhotoset(setid) {
	if(setid==null || setid.length<=0) return;
	photoSet=setid;
	lastPhoto=null;
	q=lnk+"&action=listphotos&format=list&photoset="+photoSet;
	
	resetViewer();
	
	$("#gallery").html("<div class='ajaxloading7'>Loading Photos</div>");
	$("#gallery").load(q, function () {
			$("#uploadForm input[name=photoset]").val(photoSet);
			$("#uploadForm .uploadTable").show();
		});
}
function viewPhoto(photo) {
	if(photo==null || photo.length<=0) return;
	lastPhoto=photo;
	q=lnk+"&action=viewphoto&format=json"+"&photoset="+photoSet+"&photo="+photo;
	if(showOrginalZoom) {
		q+="&orginal=true";
	}
	$("#photoViewer .viewer").removeClass("nophoto");
	$("#photoViewer .tools>*").hide();
	$("#photoViewer .photoDescription").val("");
	$("#photoViewer .photoLink").val("");
	$("#photoViewer .viewer").html("<div class='ajaxloading8' style='margin-top:45%;'></div>");
	processAJAXQuery(q,function(data) {
			if(data.length>3) {
				json=$.parseJSON(data);
				if(json==null) {
					$("#photoViewer .viewer").html("");
				} else {
					$("#photoViewer .viewer").html(json.html);
					$("#photoViewer .photoDescription").val(json.description);
					$("#photoViewer .photoLink").val(json.lnk);
				}
			} else {
				$("#photoViewer .viewer").html("");
			}
			if($("#photoViewer .viewer").html().length>0) {
				$("#photoViewer .tools>*").show();
				$("#photoViewer .photoDescription").removeAttr("readonly");
				$("#photoViewer .photoLink").removeAttr("readonly");
			} else {
				$("#photoViewer .viewer").addClass("nophoto");
			}
		});
}
function saveDescription() {
	if(lastPhoto==null || lastPhoto.length<=0) {
		lgksAlert("No Media Selected At All.");
		return;
	}
	v=$('#photoDescription').val();
	ls=$('#photoLink').val();
	l=lnk+"&action=setDescription"+"&photoset="+photoSet+"&photo="+lastPhoto;
	l+="&data="+encodeURIComponent(v)+"&lnk="+encodeURIComponent(ls);
	$("#loadingmsg").show();
	$('#photoDescription').val("");
	$('#photoLink').val("");
	$('#photoDescription').addClass("ajaxloading");
	$('#photoLink').hide();
	processAJAXQuery(l,function(txt) {
			if(txt.length>0) lgksAlert(txt);
			$("#loadingmsg").hide();
			$('#photoDescription').val(v);
			$('#photoLink').val(ls);
			$('#photoDescription').removeClass("ajaxloading");
			$('#photoLink').show();
		});
}
function deleteMedia() {
	if(lastPhoto==null || lastPhoto.length<=0) {
		lgksAlert("No Media Selected At All.");
		return;
	}
	lgksConfirm("Do you really want to delete this photo?","Delete Media ?",function() {
				$("#loadingmsg").show("fast");
				$("#photoViewer .viewer").html("<div class='ajaxloading8' style='margin-top:45%;'></div>");
				l=lnk+"&action=deletePhoto&format=json"+"&photoset="+photoSet+"&photo="+lastPhoto;
				processAJAXQuery(l,function(txt) {
							json=$.parseJSON(txt);
							if(json.status=="ok") {
								resetViewer();
								$("#gallery div.thumbnail.activeThumbnail").detach();
							} else {
								viewPhoto(lastPhoto);
								lgksAlert(json.msg);
							}
							$("#loadingmsg").hide();
						});
			});
	
}
function createPhotoSet() {
	lgksPrompt("Please give a new name for the Set.<br/> Space or special characters are not allowed.<br/>The name must also be unique.","Create PhotoSet !",null,function(set) {
			if(set.length>0) {
				$("#loadingmsg").show("fast");
				l=lnk+"&action=createSet&format=json&photoset="+set.split(" ").join("_");
				processAJAXQuery(l,function(txt) {
							json=$.parseJSON(txt);
							if(json.status=="ok") {
								reloadList(set);
							} else {
								lgksAlert(json.msg);
							}
							$("#loadingmsg").hide();
						});
			}
		});
}
function deletePhotoSet() {
	set=$("#bannerList").val();
	lgksConfirm("Do you really want to delete photo set <b>"+set+"</b>?","Delete PhotoSet ?",function() {
				$("#loadingmsg").show("fast");
				l=lnk+"&action=deleteSet&format=json&photoset="+set;
				processAJAXQuery(l,function(txt) {
							json=$.parseJSON(txt);
							if(json.status=="ok") {
								reloadList();
							} else {
								lgksAlert(json.msg);
							}
							$("#loadingmsg").hide();
						});
			});
}
function resetViewer() {
	$("#photoViewer .viewer").html("");
	$("#photoViewer .viewer").removeClass("nophoto");
	$("#photoViewer .viewer").addClass("nophoto");
	$("#photoViewer .tools>*").hide();
	$("#photoViewer .photoDescription").val("");
	
	$("#uploadForm form input[name=photofile]").val("");
}
function checkFile() {
	f=$("#uploadForm form input[name=photofile]").val();
	f1=f.toLowerCase();
	if(f1.lastIndexOf(".png")==-1 && f1.lastIndexOf(".gif")==-1 &&
			f1.lastIndexOf(".jpg")==-1 && f1.lastIndexOf(".jpeg")==-1) {
		$("#uploadForm form input[name=photofile]").val("");
	    lgksAlert("Please upload Image Files (png/gif/jpg/jpeg)");
	    return false;
	}	
	return true;
}
function clearUploadForm(txt) {
	$("#uploadForm form input[name=photofile]").val("");
	loadPhotoset(photoSet);
}
</script>
<?php } ?>
