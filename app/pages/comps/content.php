<?php
if(!defined('ROOT')) exit('No direct script access allowed');
loadModule("dbcon");loadFolderConfig();//getDBControls();
$_SESSION["LGKS_EDITOR_FPATH"]=APPS_FOLDER.$_REQUEST["forsite"]."/{$_SESSION['APP_FOLDER']['APPS_MEDIA_FOLDER']}";

loadModule("tabbedspace");

_js(array("jquery.mailform"));
?>
<style>#workspace.ui-corner-all {border:0px !important;}</style>
<script language=javascript>
$(function() {
	initUI("body");
	$("body").attr("oncontextmenu","return false");
	$("body").attr("onselectstart","return false");
	$("body").attr("ondragstart","return false");
});
function initUI(ele) {
	$(ele+" button").button();
	$("select").addClass("ui-widget-header ui-corner-all");
	$(ele+" .datepicker").datepicker();
	$(ele+" .progressbar").progressbar({value:37});
	$(ele+" .slider").slider();
	$(ele+" .draggable").draggable();
	$(ele+" .accordion").accordion({
			fillSpace: true
		});
}
function showProfileEditor() {
	openInNewTab('Profile', '?site=<?=SITENAME?>&page=profile');
}
function showSettingsEditor() {
	openInNewTab('Settings', '?site=<?=SITENAME?>&page=settings');
}
function gotoSiteCMS(site) {
	document.location='index.php?site=<?=SITENAME?>&forsite='+site;
}
function openMailPad(mailto,subject,body,attach) {
	if(mailto==null) mailto="";
	if(subject==null) subject="";
	if(body==null) body="";
	
	$.mailform(mailto,subject,body);
}
</script>
