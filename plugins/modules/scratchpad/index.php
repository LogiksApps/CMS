<?php
if(!defined('ROOT')) exit('No direct script access allowed');

//include __DIR__."/api.php";
$slug = _slug("a/b/c/d");
// printArray($slug);

loadModule("pages");
loadVendor("ace");

function pageSidebar() {
    return false;
}

function pageContentArea() {
    return "<div id='scratchpadContainer' class='scratchpadContainer'>
    <div id='codeEditor' class='col-md-6'>
		<div id='editorScript' class='editorArea' ext='php'></div>
    </div>
    <div id='codeOutput'  class='col-md-6'>
        <h5>Output <citie>(Press CTRL+S/CMD+S to run the code)</citie></h5>
        <pre id='resultArea'>...</pre>
    </div>
</div>";
}

printPageComponent(false,[
		"toolbar"=>[
// 			"loggers"=>["title"=>"Loggers","align"=>"right","type"=>"dropdown","options"=>$loggers],
			// "pages"=>["title"=>"Pages","align"=>"right"],
			// "comps"=>["title"=>"Components","align"=>"right"],
			// "layouts"=>["title"=>"Layouts","align"=>"right"],
			// ['type'=>"bar"],

			// ["title"=>"Search Site","type"=>"search","align"=>"left"]
			"refreshPage"=>["icon"=>"<i class='fa fa-refresh'></i>"],
			['type'=>"bar"],
			"runCode"=>["icon"=>"<i class='fa fa-play'></i>","title"=>"Run"],
			
			"clearOutput"=>["icon"=>"<i class='fa fa-trash'></i>","title"=>"Clear", "align"=>"right"],
		],
		"sidebar"=>"pageSidebar",
		"contentArea"=>"pageContentArea"
	]);
	
// echo _css("logBook");
// echo _js("logBook");
?>
<style>
.pageComp {
    overflow: hidden;
}
.scratchpadContainer {
    width: 100%;
    height: 95%;
    height: calc(100% - 43px);
}
.scratchpadContainer>* {
    height: 100%;
    border-right:1px solid #000;
    padding: 1px;
    overflow: hidden;
}
.editorArea {
    position: absolute;
    top: 0px;
    bottom: 0px;
    right: 0;left: 0;
}
#resultArea {
    width: 100%;
    height: 95%;
    height: calc(100% - 22px);
}
h5 {
    font-weight: bold;
    font-size: 20px;
    height: 22px;
}
citie {
    font-size: 12px;
    color: #999;
}
</style>

<script>
var editor = null;
var lang = "php";
$(function() {
    editor=ace.edit("editorScript");
    loadEditorSettings();
    setupEditorConfig(editor, lang);
    
    editor.commands.addCommand({
    	name: "saveSource",
    	bindKey: {win: "Ctrl-r", mac: "Command-r"},
    	exec: function(editor) {
    		runCode();
    	}
    });
    editor.commands.addCommand({
    	name: "saveSource",
    	bindKey: {win: "Ctrl-s", mac: "Command-s"},
    	exec: function(editor) {
    		runCode();
    	}
    });
    
    clearOutput();
    
    editor.setReadOnly(false);
    var code = localStorage.getItem("SCRATCHPAD_CODE");
    if(code!=null && code.length>2) {
        editor.setValue(decodeURIComponent(code));
        editor.selection.clearSelection()
		editor.session.getUndoManager().reset();
    }
});
function refreshPage() {
    window.location.reload();
}
function runCode() {
    if(editor.getValue()==null || editor.getValue().length<2) {
        lgksToast("No Code found for running...");
        return;
    }
    var code = encodeURIComponent(editor.getValue());
    localStorage.setItem("SCRATCHPAD_CODE", code);
    
    $("#resultArea .loading").detach();
    $("#resultArea").append("<div class='loading'>Loading</div>");
    
    processAJAXPostQuery(_service("scratchpad", "runcode"), `code=${code}&lang=php`, function(data) {
        $("#resultArea .loading").detach();
        $("#resultArea").append(data);
        $("#resultArea").append("<hr/>");
        
        $("#resultArea").scrollTop($("#resultArea").height()+1000);
    }, "raw");
}
function clearOutput() {
    $("#resultArea").html("<span class='loading'>Output will be displayed here. Press CTRL+S/CMD+S to run the code</span>");
}
</script>