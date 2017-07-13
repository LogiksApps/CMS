var editorList={};

var curHistoryFile="";
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
    		case "history":
    			loadHistory();
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

function loadHistory() {
	type=$("#pageEditor .tab-pane.active").attr("id");
	src=$("#pageEditor").attr("src");
	curHistoryFile="";
	
	$("#editorAsidebar .historyContainer").html("<div class='ajaxloading ajaxloading5'></div>");
	processAJAXPostQuery(_service("pageEditor","getFile"),"&type="+type+"&src="+src,function(ans) {
		if(ans.Data.file!=null && ans.Data.file.length>0) {
			curHistoryFile=ans.Data.file;
			processAJAXPostQuery(_service("cmsEditor","gethistory"),"src="+ans.Data.file,function(ans) {
					html=[];
					try {
						$.each(ans.Data.history,function(k,v) {
							html.push("<li class='list-group-item' data-refid='"+v.id+
												"'><i class='fa fa-calendar'></i> <a class='btn btn-default btn-xs pull-right' onclick='checkoutHistory("+v.id+")'><i class='fa fa-plus'></i></a>"+
												v.created_on+" <br><small>"+v.created_by+"</small></li>");
						});
					} catch(e) {
						console.log(e);
					}
					$("#editorAsidebar .historyContainer").html("<ul class='list-group'>"+html.join("")+"</ul>");
				},"json");
		} else {
			$("#editorAsidebar .historyContainer").html("Not Supported Yet");
		}
	},"json");
	
	$("#editorAsidebar").show();
}
function checkoutHistory(refid) {
	if(curHistoryFile==null || curHistoryFile.length<=0) return;
	processAJAXPostQuery(_service("cmsEditor","gethistoryContent"),"src="+curHistoryFile+"&refid="+refid,function(ans) {
				lgksOverlay("<textarea style='width:100%;height:70%;border:1px solid #AAA;' readonly>"+ans+"</textarea>");
			});
}