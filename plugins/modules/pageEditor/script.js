var editorList={};

$(function() {
    //$('#pageEditor .nav.nav-tabs a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
    $('#pageEditor .nav.nav-tabs a[data-toggle="tab"]').on('click', function (e) {
      //e.target // newly activated tab
	  //e.relatedTarget // previous active tab
      target=$(e.target);
	  if(target.hasClass("showOpen")) {
	  	$('#pageEditor .nav.nav-tabs li[cmd=open]').removeClass("hidden");
	  } else {
	  	$('#pageEditor .nav.nav-tabs  li[cmd=open]').addClass("hidden");
	  }
	});
    
    $('#pageEditor .nav.nav-tabs li[cmd]').click(function(e) {
    	cmd=$(this).attr("cmd");
    	switch(cmd) {
    		case "save":
    			saveFile();
    		break;
				case "preview":
    			type=$("#pageEditor .tab-pane.active").attr("id");
					src=$("#pageEditor").attr("src");
					lx=SiteLocation+src+"?site="+parent.CMS_FOR_SITE;
					window.open(lx);
    		break;
    	}
    });

    loadEditorSettings();

    $(".tab-pane>.editorArea[id]").each(function() {
		refid=$(this).attr("id");
		ext=$(this).attr('ext');
		edit=ace.edit(refid);

		setupEditorConfig(edit,ext);
		addCustomCommands(edit);
		initAutocompletion(edit);

		editorList[refid]=edit;
	});

	editorList['editorCode'].session.setMode({path:"ace/mode/php", inline:true})

    loadEditorContent(editorList['editorMarkup'],"markup");
    loadEditorContent(editorList['editorCode'],"code");
    loadEditorContent(editorList['editorStyle'],"style");
    loadEditorContent(editorList['editorScript'],"script");
});

function loadEditorContent(editor,type) {
	src=$("#pageEditor").attr("src");
	lx=_service("pageEditor","getsrc")+"&src="+src+"&type="+type;

	processAJAXQuery(lx,function(txt) {
		editor.setValue(txt);
		setTimeout(function() {
			editor.selection.clearSelection()
			if(!readonlyEditors) {
				editor.setReadOnly(false);
			}
			editor.session.getUndoManager().reset();
		},100);
	});
}

function saveFile() {
	type=$("#pageEditor .tab-pane.active").attr("id");
	src=$("#pageEditor").attr("src");

	q=[];
	if($("#pageEditor .tab-pane.active").find(".editorArea").length>0) {
		q.push("txt="+encodeURIComponent(editorList[$("#pageEditor .tab-pane.active .editorArea").attr("id")].getValue()));
	} else {
		$("input[name]:visible,select[name]:visible,textarea[name]:visible","#pageEditor .tab-pane.active").each(function() {
			if($(this).attr("type")=="radio") {
				if(this.checked) {
					q.push($(this).attr("name")+"="+encodeURIComponent(this.value));
				}
			} else if($(this).attr("type")=="checkbox") {
				if(this.checked) {
					q.push($(this).attr("name")+"="+true);
				} else {
					q.push($(this).attr("name")+"="+false);
				}
			} else {
				q.push($(this).attr("name")+"="+encodeURIComponent(this.value));
			}
		});
	}
	
	lx=_service("pageEditor","savePage")+"&type="+type+"&src="+src;
	processAJAXPostQuery(lx,q.join("&"),function(txt) {
		if(txt=="done") {
			processAJAXQuery(_service("cleaner","PURGE:TEMPLATES"));
			lgksToast("Save Successfull");
		} else {
			lgksToast(txt.replace("failed:",""));
		}
	});
}
