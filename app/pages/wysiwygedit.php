<?php
if (!defined('ROOT')) exit('No direct script access allowed');
session_check(true);

if(!isset($_REQUEST["forsite"])) {
	dispErrMessage("HostSite Is Required But Not Found","CMS Error",400);
	exit();
}

loadModule("page");

loadModule("editor");
loadEditor("ckeditor");

$btns=array();
$btns[sizeOf($btns)]=array("title"=>"Reload","icon"=>"reloadicon","tips"=>"Load Page List","onclick"=>"reloadEditor()");
$btns[sizeOf($btns)]=array("title"=>"Save","icon"=>"saveicon","tips"=>"Create New Page","onclick"=>"saveEditor()");
$btns[sizeOf($btns)]=array("bar"=>" ||| ");
$btns[sizeOf($btns)]=array("label"=>"<b class=headerPageName >".basename($_REQUEST['file'])."</b>");

$layout="apppage";
$params=array("toolbar"=>$btns,"contentarea"=>"printContent");

printPageContent($layout,$params);

function printContent() {
	$file="";
	if(isset($_REQUEST["file"])) {
		$file=$_REQUEST["file"];
	}
	$ext=explode(".",$file);
	$ext=$ext[count($ext)-1];
?>
<style>
#pgworkspace {
	overflow:hidden;
}
.headerPageName {
	font-size:1.3em;
	margin-left:100px;
}
</style>
<script>
forSite="<?=$_REQUEST["forsite"]?>";
forFile="<?=$file?>";
$(function() {
	$("#codearea").css("height",$(window).height()-150);
	$("#codearea").css("width","99%");
	
	CKEDITOR.config.toolbar = 'WYSIWYG_PHP';
	CKEDITOR.config.uiColor = '#DDD';
	
	loadEditor("codearea");
	fixEditorSize("codearea");
	reloadEditor();
});
function getCMD() {
	return "services/?scmd=editor&site=<?=SITENAME?>&forsite="+forSite;
}
function reloadEditor() {
	lnk=getCMD()+"&action=fetch&file="+forFile;
	$("#loadingmsg").show();
	processAJAXQuery(lnk,function(txt) {
			editor.setData(txt);
			$("#loadingmsg").hide();
		});
}
function saveEditor() {
	lnk=getCMD()+"&action=save&file="+forFile;
	q="&data="+encodeURIComponent(editor.getData());
	$("#loadingmsg").show();
	processAJAXPostQuery(lnk,q,function(txt) {
			if(txt.length>0) lgksAlert(txt);
			$("#loadingmsg").hide();
		});
}
</script>
<textarea id=codearea style='font:14px bold Arial;'></textarea>
<?php
}
?>
