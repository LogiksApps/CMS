<?php
if(!defined('ROOT')) exit('No direct script access allowed');

$webpath=$webpath."ace/";

$toolbarTools=array(
		// "save"=>[
		// 		"icon"=>"<i class='icon fa fa-save'></i>",
		// 		"title"=>"Save",
		// 	]
	);

$fss=scandir(dirname(__DIR__)."/ace/");
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

$favLang=[
		"javascript",
		"php",
		"html"
	];

?>
<script src="<?=$webpath?>ace.js" type="text/javascript" charset="utf-8"></script>
<script src="<?=$webpath?>ext-language_tools.js" type="text/javascript" charset="utf-8"></script>
<script src="<?=$webpath?>ext-beautify.js" type="text/javascript" charset="utf-8"></script>
<?=_css("cmsEditor")?>
<?=_js("cmsEditor")?>
<?=_js("md5")?>

<div id='editorToolbar'>
	<a href="#" class="btn" cmd="trash" title="Delete"><i class="icon fa fa-trash"></i></a>
	<a href="#" class="btn" cmd="save" title="Save"><i class="icon fa fa-save"></i></a>
	<input name='fname' style='width:40%;' value='<?=$_REQUEST['src']?>' data-original='<?=$_REQUEST['src']?>' />
	<?php
		foreach ($toolbarTools as $key => $value) {
			if(!isset($value['title'])) $value['title']="";
			$value['title']=_ling($value['title']);
			echo "<a href='#' class='btn' cmd='{$key}' title='{$value['title']}'>{$value['icon']}</a>";
		}
	?>
	<div class='pull-right'>
		<!-- <a href='#' class='btn' cmd='settings'><i class='icon fa fa-cog'></i></a> -->
		<div class="btn-group">
		  <button type="button" class="btn btn-default dropdown-toggle"
					data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
		    Language <span class="caret"></span>
		  </button>
		  <ul class="dropdown-menu pull-right langlist">
		  	<?php
		  		foreach ($favLang as $fx) {
		  			$fx=str_replace(".js","",$fx);
		  			if(strlen($fx)<=3) $ttl=strtoupper($fx);
		  			else $ttl=toTitle($fx);
		  			if(strtoupper($_REQUEST['ext'])==strtoupper($fx)) {
		  				echo "<li cmd='language' rel='{$fx}' class='active'><a href='#'>{$ttl}</a></li>";
		  			} else {
		  				echo "<li cmd='language' rel='{$fx}' ><a href='#'>{$ttl}</a></li>";
		  			}
		  		}
		  		echo "<hr>";
		  		foreach ($fsLang as $fx) {
		  			$fx=str_replace(".js","",$fx);
		  			if(in_array($fx, $favLang)) continue;
		  			if(strlen($fx)<=3) $ttl=strtoupper($fx);
		  			else $ttl=toTitle($fx);
		  			if(strtoupper($_REQUEST['ext'])==strtoupper($fx)) {
		  				echo "<li cmd='language' rel='{$fx}' class='active'><a href='#'>{$ttl}</a></li>";
		  			} else {
		  				echo "<li cmd='language' rel='{$fx}' ><a href='#'>{$ttl}</a></li>";
		  			}
		  		}
		  	?>
		  </ul>
		</div>
		<div class="btn-group">
		  <button type="button" class="btn btn-default dropdown-toggle"
					data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
		    Theme <span class="caret"></span>
		  </button>
		  <ul class="dropdown-menu pull-right themelist">
		    <?php
		  		foreach ($fsTheme as $fx) {
		  			$fx=str_replace(".js","",$fx);
		  			$ttl=toTitle($fx);
		  			if(strtoupper($_REQUEST['theme'])==strtoupper($fx)) {
		  				echo "<li cmd='theme' rel='{$fx}'><a href='#'>{$ttl}</a></li>";
		  			} else {
		  				echo "<li cmd='theme' rel='{$fx}'><a href='#'>{$ttl}</a></li>";
		  			}
		  		}
		  	?>
		  </ul>
		</div>
	</div>
</div>
<div id="editor"></div>

<script>
var langTools = ace.require("ace/ext-language_tools");
//var beautify = ace.require("ace/ext/beautify");
var editor = ace.edit("editor");
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

$(function() {
	$("#editorToolbar").delegate(".btn[cmd],li[cmd]","click",function(e) {
		cmd=$(this).attr('cmd');
		doEditorAction(cmd,this);
	});

	loadEditorSettings();

	setupEditorConfig(editor,"<?=$_REQUEST['ext']?>");

	$("#editorToolbar .themelist li[rel='"+editorConfig.theme+"']").addClass("active");

	initAutocompletion(editor);
	addCustomCommands(editor);

	lx=_service("cmsEditor")+"&action=getsrc&src=<?=$_REQUEST['src']?>";
	processAJAXQuery(lx,function(txt) {
		editor.setValue(txt);
		setTimeout(function() {
			editor.selection.clearSelection()
			editor.setReadOnly(false);
			editor.session.getUndoManager().reset();
		},100);
	});
});
function doEditorAction(cmd,src) {
	switch(cmd) {
		case "language":
			lang=$(src).attr("rel");
			editor.session.setMode("ace/mode/"+lang);

			$("#editorToolbar .langlist .active").removeClass("active");
			$(src).addClass("active");
		break;
		case "theme":
			theme=$(src).attr("rel");
			editor.setTheme("ace/theme/"+theme);

			$("#editorToolbar .themelist .active").removeClass("active");
			$(src).addClass("active");

			saveEditorSettings('theme',theme);
		break;

		case "trash":
			deleteFile();
		break;
		case "save":
			saveFile();
		break;
		case "reveal":

		break;
		case "beautify":

		break;
		case "checkerror":

		break;
		case "history":

		break;
		case "settings":
			editor.showSettingsMenu();
		break;
	}
}

function saveFile() {
	value=editor.getValue();
	value1=encodeURIComponent(value);
	
	q=[];
	q.push("src="+$("#editorToolbar input[name=fname]").val());
	q.push("fname="+$("#editorToolbar input[name=fname]").data("original"));
	q.push("text="+value1);
	q.push("hash="+md5(value));
	q.push("hash1="+md5(value1));

	processAJAXPostQuery(_service("cmsEditor","save"),q.join("&"),function(txt) {
		try {
			json=$.parseJSON(txt);
			if(json.Data=="saved") {
				lgksToast("<i class='fa fa-check-circle successToast'></i>Save successfull");
				if($("#editorToolbar input[name=fname]").val()!=$("#editorToolbar input[name=fname]").data("original") ||
						$("#editorToolbar input[name=fname]").data("original").length<=0) {
					parent.loadFileTree($("#editorToolbar input[name=fname]").val());
				}
				$("#editorToolbar input[name=fname]").data("original",$("#editorToolbar input[name=fname]").val());
			} else if(json.error!=null && json.error.msg!=null && json.error.msg.indexOf("failed to open stream: Permission denied")>0) {
				lgksToast("<i class='fa fa-check-circle errorToast'></i>Path is readonly, could not write to the file.");
			} else {
				lgksToast("<i class='fa fa-check-circle errorToast'></i>"+json.Data);
			}
		} catch(e) {
			console.error(e);
		}
	});
}
function deleteFile() {
	if($("#editorToolbar input[name=fname]").data("original")==null || $("#editorToolbar input[name=fname]").data("original").length<=0) {
		lgksToast("<i class='fa fa-check-circle warnToast'></i>File is already deleted");
		return;
	}
	lgksConfirm("You are about to delete : "+$("#editorToolbar input[name=fname]").val()+"<br> Sure?","Delete File!",function(e) {
		q=[];
		q.push("src="+$("#editorToolbar input[name=fname]").val());
		q.push("fname="+$("#editorToolbar input[name=fname]").data("original"));

		processAJAXPostQuery(_service("cmsEditor","delete"),q.join("&"),function(txt) {
			try {
				json=$.parseJSON(txt);
				if(json.Data=="deleted") {
					lgksToast("<i class='fa fa-check-circle successToast'></i>Deleted successfully");
					$("#editorToolbar input[name=fname]").data("original","");
					parent.loadFileTree();
				} else if(json.Data=="done") {
					parent.loadFileTree();
				} else {
					lgksToast("<i class='fa fa-check-circle errorToast'></i>"+json.Data);
				}
			} catch(e) {
				console.error(e);
			}
		});
	})
}
</script>
