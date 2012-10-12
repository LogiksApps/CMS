<?php
if(!defined('ROOT')) exit('No direct script access allowed');

_js("jquery.cookie");
loadHelpers("cookies");

$editor=checkModule("editor");
$webPath=getWebPath($editor);

$extArr=array();
$hint="";
$hintsDir=dirname($editor)."/codemirror/hints/";
$fsExt=scandir($hintsDir);
unset($fsExt[0]);unset($fsExt[1]);
foreach($fsExt as $a) {
	array_push($extArr,str_replace("-hint.js","",$a));
	$hint.="<script src='{$webPath}codemirror/hints/{$a}' type='text/javascript' language='javascript'></script>";
}

$themeArr=array("ambiance","blackboard","cobalt","default","eclipse","elegant","erlang-dark",
	"lesser-dark","monokai","neat","night","rubyblue","vibrant-ink","xq-dark");
$theme="";
$themeFile="default";
$themeDir=dirname($editor)."/codemirror/theme/";
foreach($themeArr as $n=>$a) {
	if(file_exists($themeDir.$a.".css")) {
		$theme.="<link href='{$webPath}codemirror/theme/{$a}.css' rel='stylesheet' type='text/css' media='all' />";
	} else {
		unset($themeArr[$n]);
	}
}
if(isset($_COOKIE['CODE-EDITOR-THEME'])) {
	if(in_array($_COOKIE['CODE-EDITOR-THEME'],$themeArr)) {
		$themeFile=$_COOKIE['CODE-EDITOR-THEME'];
	} else {
		createCookie("CODE-EDITOR-THEME",$themeFile);
	}
} else {
	createCookie("CODE-EDITOR-THEME",$themeFile);
}
?>
<?=$theme?>


<link href='<?=$webPath?>codemirror/theme/simple-hint.css' rel='stylesheet' type='text/css' media='all' />
<link href='<?=$webPath?>codemirror/theme/dialog.css' rel='stylesheet' type='text/css' media='all' />

<script src='<?=$webPath?>codemirror/js/simple-hint.js' type='text/javascript' language='javascript'></script>
<?=$hint?>


<script src='<?=$webPath?>codemirror/js/dialog.js' type='text/javascript' language='javascript'></script>
<script src='<?=$webPath?>codemirror/js/search.js' type='text/javascript' language='javascript'></script>
<script src='<?=$webPath?>codemirror/js/searchcursor.js' type='text/javascript' language='javascript'></script>
<script src='<?=$webPath?>codemirror/js/foldcode.js' type='text/javascript' language='javascript'></script>

<script src='<?=$webPath?>codemirror/js/match-highlighter.js' type='text/javascript' language='javascript'></script>
<script src='<?=$webPath?>codemirror/js/formatting.js' type='text/javascript' language='javascript'></script>
<style>
.activeline {
	
}
</style>
<script language=javascript>
var foldFunc=null;
var useActiveLine=false;
hlLine = null;
function loadDevEditor(divID,ext) {
	$("#codearea").css("height",($(window).height()-$("#toolbar").height()-$("#statusbar").height()-5));
	height1=$("#codearea").height()
	
	if(ext==null) ext="htmlmixed";
	
	editor = CodeMirror.fromTextArea(document.getElementById(divID), {
				mode: ext,
				lineNumbers: true,
				lineWrapping: false,
				tabMode: 'shift',
				enterMode: 'keep',
				matchBrackets: true,
				indentWithTabs: true,
				indentUnit:4,
				height:height1,
				onCursorActivity: function() {
					if(hlLine!=null) {
						editor.setLineClass(hlLine, null);
					}
					if(useActiveLine) {
						hlLine = editor.setLineClass(editor.getCursor().line, 'activeline');
					}
					
					editor.matchHighlight("CodeMirror-matchhighlight");
				},
				onGutterClick: function(cm) {foldFunc(cm, cm.getCursor().line);},
				extraKeys: {
							"Ctrl-Space": "autocomplete",
							"Ctrl-Q": function(cm){console.log(editor);foldFunc(cm, cm.getCursor().line);},
						}
	});
	
	changeTheme("<?=$themeFile?>");
	changeExtension(ext);
	
	$(".CodeMirror-scroll").css("height",height1);
}
function getData() {
	return editor.getValue();
}
function setData(txt) {
	editor.setValue(txt);
}
function readonly() {
	editor.setOption("readOnly",true);
}
function setFontSize(size) {
	size=parseInt(size);
	$('.CodeMirror').css('font-size',size+"pt");
	$('.CodeMirror').css('line-height',(size+5)+"pt");
}
function changeTheme(theme) {
	if(theme!=null && theme.length>0) {
		editor.setOption("theme",theme);
		$.cookie("CODE-EDITOR-THEME",theme);
	}
}
function changeExtension(ext) {
	if(ext==null) ext="htmlmixed";
	else if(ext=='js') ext="javascript";
	
	editor.setOption("mode",ext);
	
	if(ext=="htmlmixed" || ext=="xml" || ext=="xmlpure") {
		foldFunc = CodeMirror.newFoldFunction(CodeMirror.tagRangeFinder);
	} else {
		foldFunc = CodeMirror.newFoldFunction(CodeMirror.braceRangeFinder);
	}
	
	if(ext=="javascript") {
		CodeMirror.commands.autocomplete = function(cm) {
			CodeMirror.simpleHint(cm, CodeMirror.javascriptHint);
		}
	} else if(ext=="xml") {
		CodeMirror.commands.autocomplete = function(cm) {
			CodeMirror.simpleHint(cm, CodeMirror.xmlHints);
		}
	} else if(ext=="pig") {
		CodeMirror.commands.autocomplete = function(cm) {
			CodeMirror.simpleHint(cm, CodeMirror.pigHints);
		}
	} else {
		CodeMirror.commands.autocomplete = null;
	}
}
function getSelectedRange() {
	return { from: editor.getCursor(true), to: editor.getCursor(false) };
}
function getSelectedText() {
	return editor.getSelection();
}
//Other Functions
function autoFormatSelection() {
	var range = getSelectedRange();
	editor.autoFormatRange(range.from, range.to);
}

function commentSelection(isComment) {
	var range = getSelectedRange();
	editor.commentRange(isComment, range.from, range.to);
}
</script>
