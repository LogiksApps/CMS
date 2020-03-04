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

function saveEditorSettings(key,value) {
	editorConfig[key]=value;
	localStorage.setItem('logikscms.editorconfig',JSON.stringify(editorConfig));
}
function loadEditorSettings() {
	config=localStorage.getItem('logikscms.editorconfig');
	if(config==null || config.length<=2) {
		editorConfig=defaultEditorConfig;
		localStorage.setItem('logikscms.editorconfig',JSON.stringify(editorConfig));
	} else {
		config=$.parseJSON(config);
		editorConfig=$.extend(defaultEditorConfig,config)
	}
}
function setupEditorConfig(aceEditor,ext) {
	aceEditor.session.setMode("ace/mode/"+ext);

	aceEditor.setTheme("ace/theme/"+editorConfig.theme);
	aceEditor.setFontSize(editorConfig.fontsize);
	aceEditor.setShowPrintMargin(editorConfig.showPrintMargin);
	aceEditor.setHighlightActiveLine(editorConfig.highlightActiveLine);
	aceEditor.setDisplayIndentGuides(editorConfig.displayIndentGuides);
	aceEditor.setShowInvisibles(editorConfig.showInvisibles);
	
	aceEditor.getSession().setUseWrapMode(editorConfig.useWrapMode);
	aceEditor.getSession().setTabSize(editorConfig.tabsize);

	aceEditor.renderer.setShowGutter(editorConfig.showGutter);
	
	aceEditor.setOptions({
	        enableBasicAutocompletion: true,
	        enableSnippets: true,
	        enableLiveAutocompletion: false
	    });

	aceEditor.setReadOnly(true);
}

function initAutocompletion(aceEditor) {
	aceEditor.completers.push({
		    getCompletions: function(editor, session, pos, prefix, callback) {
		      	var wordList = ["foo", "bar", "baz"];
		        callback(null, wordList.map(function(word) {
		            return {
		                caption: word,
		                value: word,
		                meta: "static"
		            };
		        }));
		    }
		  });

	// rhymeCompleter = {
 //        getCompletions: function(editor, session, pos, prefix, callback) {
 //            if (prefix.length === 0) { callback(null, []); return }
 //            // $.getJSON(
 //            //     "http://rhymebrain.com/talk?function=getRhymes&word=" + prefix,
 //            //     function(wordList) {
 //            //         // wordList like [{"word":"flow","freq":24,"score":300,"flags":"bc","syllables":"1"}]
 //            //         callback(null, wordList.map(function(ea) {
 //            //             return {name: ea.word, value: ea.word, score: ea.score, meta: "rhyme"}
 //            //         }));
 //            //     });
 //        }
 //    }
 //    langTools.addCompleter(rhymeCompleter);
}

function addCustomCommands(aceEditor) {
	// add command to lazy-load keybinding_menu extension
    aceEditor.commands.addCommand({
        name: "showKeyboardShortcuts",
        bindKey: {win: "Ctrl-.", mac: "Command-."},
        exec: function(editor) {
            ace.config.loadModule("ace/ext/keybinding_menu", function(module) {
                module.init(editor);
                aceEditor.showKeyboardShortcuts()
            })
        }
    });
    aceEditor.commands.addCommand({
    	name: "saveSource",
    	bindKey: {win: "Ctrl-s", mac: "Command-s"},
    	exec: function(editor) {
    		saveFile();
    	}
    });
    aceEditor.execCommand("showKeyboardShortcuts");
}


function loadContent(aceEditor, txt) {
	aceEditor.setValue(txt);
	setTimeout(function() {
		aceEditor.selection.clearSelection()
		aceEditor.setReadOnly(false);
		aceEditor.session.getUndoManager().reset();
	},100);
}
</script>