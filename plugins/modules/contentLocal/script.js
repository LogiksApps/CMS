currentContent=null;
var editArea = null;
$(function() {
	$("#pgtoolbar .nav.navbar-nav.navbar-left").css("width",$(".pageCompContainer.withSidebar .pageCompSidebar").width());
	$("<a id='toolbtn_editContent' title='Edit Content' onclick='editContentEnable()' href='#' class='onOpenEditor hidden'><i class='fa fa-pencil'></i> </a>"+
		"<a id='toolbtn_saveContent' title='Save Content' onclick='saveContent()' href='#' class='onOpenEditor hidden'><i class='fa fa-save'></i> </a>"+
			"<label id='titleContent' class='titleContent'></label>").
			insertAfter($("#pgtoolbar .nav.navbar-nav.navbar-left"));
	
	$('#componentTree').delegate(".list-group-item.list-file a","click",function() {
		file=$(this).closest(".list-group-item");
		
		title=$(file).attr("title");
		slug=$(file).data("slug");
		vers=$(file).data("vers");
		
		openContent(title,slug);
	});
	
	$("#localeList").load(_service("contentLocal","locales","select"),function() {
		$("#localeList").val('gb');
	});
	
	loadEditorSettings();
  listContent();
});
function listContent() {
	$("#componentSpace").html('<h2 align="center">Please load a content.</h2>');
	
  $("#componentTree").html("<div class='ajaxloading5'></div>");

  processAJAXQuery(_service("contentLocal","list"),function(jsonList) {
    fs=txt.Data;
		if(fs==null || fs.length<=0) {
			$("#componentTree").html("<p align=center><br>No Content Found.</p>");
			return;
		}
		html="";html1="";
		$.each(fs,function(k,v) {
			if(v.length<=0) return;
			kx=md5(k);

			html1+="<div class='list-group-item list-folder'><a href='#item-"+kx+"' data-toggle='collapse'><i class='glyphicon glyphicon-folder-close'></i>"+toTitle(k)+"</a></div>";
			html1+="<div class='list-group-folder collapse' id='item-"+kx+"'>";
			$.each(v,function(m,n) {
				//data-schema='"+k+"/"+n+"'
				html1+="<div class='list-group-item list-file' title='"+n.category+" : "+n.title+" ("+n.type+")' data-id='"+n.id+"' data-vers='"+n.vers+"' data-slug='"+n.slug+"' data-type='"+n.type+"'>";
				html1+="<a href='#'><i class='fa fa-file'></i><span class='text'>"+n.title+"</span></a>";
				html1+="<input type='checkbox' name='selectFile' class='pull-right' data-slug='"+n.slug+"' data-title='"+n.title+"' /></div>";
			});
			html1+="</div>";
		});
		$("#componentTree").html(html+html1);

		if($('#componentTree .list-group-item[data-slug="'+currentContent+'"]').length>0) {
			$('#componentTree .list-group-item[data-slug="'+currentContent+'"]').closest(".list-group-folder.collapse").addClass("in");
			$('#componentTree .list-group-item[data-slug="'+currentContent+'"]').addClass("active");

			tag=$('#componentTree .list-group-item[data-slug="'+currentContent+'"]');
			title=$(tag).text();
			vers=$(tag).data("vers");

			$("#pgtoolbar .titleContent").html(title+" [v"+vers+"]");
		} else {
			$("#pgtoolbar .nav.navbar-right li.active").removeClass('active');
		}
  },"json");
}
function openContent(title,slug) {
	currentContent=slug;
	$("#pgtoolbar .titleContent").html(title);
	$("#pgtoolbar .onOpenEditor").removeClass("hidden");
	
	$('#componentTree .list-group-item.active').removeClass("active");
	$('#componentTree .list-group-item[data-slug="'+currentContent+'"]').addClass("active");
	
	loadTextEditor();
}
function openCreateModal() {
	$("#createContentModal").modal("show");
}
function createContent(btn) {
	frm=$(btn).closest(".modal-content").find("form");
	v1=frm.find("input[name=fname]").val();
	if(v1==null || v1.length<=0) {
	lgksToast("Content name is must");
	return;
	}
	frm.find("input[name=fname]").val(v1.replace(/[^A-Z0-9]+/ig, "_"));
	processAJAXPostQuery(_service("contentLocal","create"),frm.serialize(),function(ans) {
			err=ans.split(":");
			if(err[0]=="error") {
				lgksToast(err[1]);
			} else {
				openContent($(btn).closest(".modal-content").find("form").find("input[name=fname]").val(),ans)
				listContent();
				$("#createContentModal").modal("hide");
			}
		},"RAW");
}
function saveContent() {
	if(currentContent==null) {
		lgksToast("Please load a template to edit.");
		return;
	}
	
	processAJAXPostQuery(_service("contentLocal","save"),"slug="+currentContent+"&txt="+encodeURIComponent(editArea.getValue()),function(ans) {
		err=ans.split(":");
		if(err[0]=="error") {
			lgksToast(err[1]);
		} else {
			lgksToast(ans);
		}
	},"RAW");
}
function deleteContent(src) {
	q=[];
	$("input[name=selectFile]:checked").each(function() {
		q.push($(this).data("slug"));
	});
	if(q.length<=0) {
		return;
	}
	lgksConfirm("Are you sure about deleting the below files?<br><br><ol><li>"+q.join("</li><li>")+"</li></ol>","Delete Files",function(ans) {
		if(ans) {
			processAJAXPostQuery(_service("contentLocal","delete"),"slug="+q.join(","),function(ans) {
					err=ans.split(":");
					if(err[0]=="error") {
						lgksToast(err[1]);
					} else {
						lgksToast(ans);
						listContent();
					}
				},"RAW");
		}
	});
}
function editContentEnable() {
	$("#toolbtn_editContent").removeClass("highlight");
	editArea.setReadOnly(false);
}
	
function loadTextEditor() {
	if(currentContent==null) {
		lgksToast("Please load an article to edit its content");
		return;
	}
	
	srcType="html";
	
	$("#pgtoolbar .nav.navbar-right li.active").removeClass('active');
	$("#toolbtn_loadTextEditor").closest("li").addClass("active");
	
	$("#componentSpace").html("<h2 class='ajaxloading5'></h2>");
	processAJAXPostQuery(_service("contentLocal","fetchTXT"),"slug="+currentContent,function(txt) {
		err=txt.split(":");
		if(err[0]=="error") {
			$("#componentSpace").html("<h2 class='errorMsg'>"+err[1]+"</h2>");
		} else {
			rid="templateManager"+Math.ceil(Math.random()*1000000);
			$("#componentSpace").html("<div id='"+rid+"' style='width:100%;height:100%;border:0px;'></div>");
			//$("#"+rid).html(txt);
			
			ext=currentContent.split(".");
			ext=ext[ext.length-1].toLowerCase();
			
			switch(ext) {
				case "htm":case "html":case "tpl":
					loadEditor(rid,"html");
				break;
				case "md":
					loadEditor(rid,"markdown");
				break;
			}

			editArea.setValue(txt);
		}
	},"RAW");
}
function loadEditor(rid,ext) {
	if(ext==null || ext=="undefined") ext="html";
	
	editArea=ace.edit(rid);
	
	setupEditorConfig(editArea,ext);
	addCustomCommands(editArea);
	initAutocompletion(editArea);
	
	$("#toolbtn_editContent").addClass("highlight");
	editArea.session.selection.clearSelection();
	editArea.session.getUndoManager().reset();
// 	editArea.setReadOnly(false);
}