<?php
if(!defined('ROOT')) exit('No direct script access allowed');

_js(array("jquery.ghosttext"));

loadModule("page");

$btns=array();
$btns[sizeOf($btns)]=array("title"=>"Reload","icon"=>"reloadicon","tips"=>"Reload Editor","onclick"=>"reloadEditor()");
$btns[sizeOf($btns)]=array("title"=>"Save","icon"=>"saveicon","tips"=>"Save Page","onclick"=>"saveEditor()");

//$btns[sizeOf($btns)]=array("bar"=>"<span class='divider'>&nbsp;&nbsp;</span>");

$btns[sizeOf($btns)]=array("title"=>"Format","icon"=>"formatIcon","tips"=>"Format Source Code","onclick"=>"autoFormatSelection()");

$btns[sizeOf($btns)]=array("title"=>"Help","icon"=>"helpicon","tips"=>"Help !","onclick"=>"jqPopupDiv('#helpbox')");

$layout="apppage";
$params=array("toolbar"=>$btns,"contentarea"=>"printContent");

printPageContent($layout,$params);

function printContent() {
	$file="";
	if(isset($_REQUEST["file"])) {
		$file=$_REQUEST["file"];
	}
	$ext=explode(".",$file);
	$ext=strtolower($ext[count($ext)-1]);

	if($ext=="js" || $ext=="json") {
		$ext="javascript";
	}
	loadModule("editor");

	$editorType="codemirror";//codemirror,editarea
	loadEditor($editorType);
	include "{$editorType}.php";

	$webPath=getWebPath(__FILE__);
?>
<link href='<?=$webPath?>style.css' rel='stylesheet' type='text/css' media='all' />
<style>
#toolbar .left button {
	width:45px !important;
}
</style>
<script>
forSite="<?=$_REQUEST["forsite"]?>";
forFile="<?=$file?>";
$(function() {
	$("#themeselector").val("<?=$themeFile?>");
	$("#extselector").val("<?=$ext?>");
	$("#fontsize").val("11");

	loadDevEditor("codearea","<?=$ext?>");
	reloadEditor();
});
function getCMD() {
	return getServiceCMD("editor");
}
function reloadEditor() {
	lnk=getCMD()+"&action=fetch&file="+forFile;
	$("#loadingmsg").show();
	processAJAXQuery(lnk,function(txt) {
			setData(txt);
			$("#loadingmsg").hide();
			lgksToast("Code Editor Reset",{position: "top-right"});
		});
}
function saveEditor() {
	lnk=getCMD()+"&action=save&file="+forFile;
	q="&data="+encodeURIComponent(getData());
	$("#loadingmsg").show();
	processAJAXPostQuery(lnk,q,function(txt) {
			if(txt.length>0) {
				if(typeof lgksToast=="function") lgksToast(txt,{position: "top-right"});
				else lgksAlert(txt);
			}
			$("#loadingmsg").hide();
		});
}
</script>
<textarea id=codearea style='font:14px bold Arial;width:100%;height:100%;'></textarea>
<div id=statusbar class='padding-left:3px;padding-right:3px;'>
	<div class='left'>
		<span class='filename'><?=$_REQUEST['file']?></span>
	</div>
	<div class='right'>
		<span>
			<input type=checkbox id=wrapOnOff name=wrapOnOff onchange="changeWordWrap(this.checked);" style="padding:4px;margin-right:-2px;" />
			<label for='wrapOnOff'>WordWrap</label>
		</span>
		<select id=fontsize style='width:70px;height:22px;' onchange="setFontSize(this.value)">
			<?php
				for($i=5;$i<25;$i++) {
					echo "<option value='{$i}'>{$i}pt</option>";
				}
			?>
		</select>
		<select id=extselector style='width:70px;height:22px;' onchange="changeExtension(this.value);">
			<option value=''>Others</option>
			<?php
				foreach($extArr as $a) {
					echo "<option value='$a'>".ucwords($a)."</option>";
				}
			?>
			<option value='php'>PHP</option>
			<option value='css'>CSS</option>
			<option value='xml'>XML</option>
			<option value='javascript'>JS</option>
			<option value='htmlmixed'>HTML</option>
			<option value='clike'>C/CPP</option>
		</select>
		<select id=themeselector style='width:100px;height:22px;' onchange="changeTheme(this.value);">
			<?php
				foreach($themeArr as $a) {
					echo "<option value='$a'>".ucwords($a)."</option>";
				}
			?>
		</select>
	</div>
</div>
<div style='display:none'>
	<div id=helpbox title='Help !'>
		<?php include_once "{$editorType}_help.php"; ?>
	</div>
</div>
<?php
}
?>
