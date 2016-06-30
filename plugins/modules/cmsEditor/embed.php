<?php
if(!defined('ROOT')) exit('No direct script access allowed');

$webpath=getWebPath(__DIR__)."/";
$webpath=$webpath."ace/";


$fss=scandir(__DIR__."/ace/");
$fsLang=[];
$fsTheme=[];

foreach ($fss as $fx) {
	if(substr($fx, 0, 5)=="mode-") {
		$fsLang[]=substr($fx, 5);
	} elseif(substr($fx, 0, 6)=="theme-") {
		$fsTheme[]=substr($fx, 6);
	}
}

$_REQUEST['theme']="twilight";
?>
<script src="<?=$webpath?>ace.js" type="text/javascript" charset="utf-8"></script>
<script src="<?=$webpath?>ext-language_tools.js" type="text/javascript" charset="utf-8"></script>
<script src="<?=$webpath?>ext-beautify.js" type="text/javascript" charset="utf-8"></script>
<?=_css("cmsEditor")?>
<?=_js("cmsEditor")?>
<?=_js("md5")?>

<script>
var langTools = ace.require("ace/ext-language_tools");
//var beautify = ace.require("ace/ext/beautify");

var defaultEditorConfig={
		"theme":"<?=$_REQUEST['theme']?>",
		"fontsize":'12px',
		"tabsize":4,
		"showPrintMargin":false,
		"highlightActiveLine":true,
		"displayIndentGuides":true,
		"useWrapMode":true,
		"showInvisibles":false,
		"showGutter":true
	};
var editorConfig={};
</script>