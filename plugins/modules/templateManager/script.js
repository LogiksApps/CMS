var editArea = null;
var currentTemplate = null;
var srcType="html";
$(function() {
	$("#componentSpace").css("height","100%");
	$("#pgtoolbar .nav.navbar-nav.navbar-left").css("width",$(".pageCompContainer.withSidebar .pageCompSidebar").width());
	$("<a id='toolbtn_editTemplate' title='Edit Template' data-cmd='editTemplate' href='#' class='onOpenEditor hidden'><i class='fa fa-pencil'></i> </a>"+
		"<a id='toolbtn_saveTemplate' title='Save Template' data-cmd='saveTemplate' href='#' class='onOpenEditor hidden'><i class='fa fa-save'></i> </a>"+
			"<label id='titleContent' class='titleContent'></label><label id='wordCount' class='label label-success'></label>").
			insertAfter($("#pgtoolbar .nav.navbar-nav.navbar-left"));
	
	$('#componentTree').delegate(".list-group-item.list-file a","click",function() {
		file=$(this).closest(".list-group-item");
		
		title=$(this).text();
		slug=$(file).data("slug");
		vers=$(file).data("vers");
		group=$(file).data("group");
		
		openTemplate(title,slug,group);
	});
	
	loadEditorSettings();
	listTemplates();
});

function listTemplates() {
	//closeContentFile();
	$("#componentTree").html("<div class='ajaxloading5'></div>");
	
	processAJAXQuery(_service("templateManager","list"),function(txt) {
		fs=txt.Data;
		if(fs==null || fs.length<=0) {
			$("#componentTree").html("<p align=center><br>No Templates Found.</p>");
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
				html1+="<div class='list-group-item list-file' title='"+n.title+"' data-id='"+n.id+"' data-vers='"+n.vers+"' data-slug='"+n.slug+"' data-group='"+k+"'>";
				html1+="<a href='#'><i class='fa fa-file'></i><span class='text'>"+n.title+"</span></a>";
				html1+="<input type='checkbox' name='selectFile' class='pull-right' data-slug='"+n.slug+"' data-title='"+n.title+"' /></div>";
			});
			html1+="</div>";
		});
		$("#componentTree").html(html+html1);

		if($('#componentTree .list-group-item[data-slug="'+currentTemplate+'"]').length>0) {
			$('#componentTree .list-group-item[data-slug="'+currentTemplate+'"]').closest(".list-group-folder.collapse").addClass("in");
			$('#componentTree .list-group-item[data-slug="'+currentTemplate+'"]').addClass("active");
			
			tag=$('#componentTree .list-group-item[data-slug="'+currentTemplate+'"]');
			title=$(tag).text();
			vers=$(tag).data("vers");
			group=$(file).data("group");
			
			if(group!==null)
        	    $("#pgtoolbar .titleContent").html(group+"/"+title);
        	else
        	    $("#pgtoolbar .titleContent").html(title);
		} else {
			$("#pgtoolbar .nav.navbar-right li.active").removeClass('active');
		}
	},"json");
}

function openTemplate(title,slug,group) {
	currentTemplate=slug;
	if(group!==null)
	    $("#pgtoolbar .titleContent").html(group+"/"+title);
	else
	    $("#pgtoolbar .titleContent").html(title);
	$("#pgtoolbar .onOpenEditor").removeClass("hidden");
	
	$('#componentTree .list-group-item.active').removeClass("active");
	$('#componentTree .list-group-item[data-slug="'+currentTemplate+'"]').addClass("active");
	
	loadTextEditor();
}

function loadTextEditor() {
	if(currentTemplate===null) {
		lgksToast("Please load an article to edit its content");
		return;
	}
	
	srcType="tpl";
	
	$("#pgtoolbar .nav.navbar-right li.active").removeClass('active');
	$("#toolbtn_loadTextEditor").closest("li").addClass("active");
	
	$("#componentSpace").html("<h2 class='ajaxloading5'></h2>");
	processAJAXPostQuery(_service("templateManager","fetchTXT"),"slug="+currentTemplate,function(txt) {
		err=txt.split(":");
		if(err[0]=="error") {
			$("#componentSpace").html("<h2 class='errorMsg'>"+err[1]+"</h2>");
		} else {
			rid="templateManager"+Math.ceil(Math.random()*1000000);
			$("#componentSpace").html("<div id='"+rid+"' style='width:100%;height:100%;border:0px;'></div>");
			//$("#"+rid).html(txt);
			
			loadEditor(rid,"php");

			editArea.setValue(txt);
			
			editArea.getSession().on('change', function() {
              $("#wordCount").html(editArea.getValue().length);
            });
            $("#wordCount").html(editArea.getValue().length);
		}
	},"RAW");
}

function loadSQLEditor() {
	if(currentTemplate==null) {
		lgksToast("Please load an article to edit its content");
		return;
	}
	
	srcType="sql";
	
	$("#pgtoolbar .nav.navbar-right li.active").removeClass('active');
	$("#toolbtn_loadSQLEditor").closest("li").addClass("active");
	
	$("#componentSpace").html("<h2 class='ajaxloading5'></h2>");
	processAJAXPostQuery(_service("templateManager","fetchSQL"),"slug="+currentTemplate,function(txt) {
		err=txt.split(":");
		if(err[0]=="error") {
			$("#componentSpace").html("<h2 class='errorMsg'>"+err[1]+"</h2>");
		} else {
			rid="templateManager"+Math.ceil(Math.random()*1000000);
			$("#componentSpace").html("<div id='"+rid+"' style='width:100%;height:100%;border:0px;'></div>");
			//$("#"+rid).html(txt);
			
			loadEditor(rid,"sql");

			editArea.setValue(txt);
		}
	},"RAW");
}

function createTemplate() {
	lgksPrompt("New Template ! (No Space or special characters allowed)","New Template",function(newName) {
			if(newName!=null && newName.length>0) {
				processAJAXPostQuery(_service("templateManager","create"),"slug="+newName,function(ans) {
						err=ans.split(":");
						if(err[0]=="error") {
							lgksToast(err[1]);
						} else {
							openTemplate(newName,ans,"NEW")
							listTemplates();
						}
					},"RAW");
			}
		});
}
function deleteTemplate() {
	q=[];q1=[];
	$("#componentTree input[type=checkbox]:checked").each(function() {
		q.push($(this).data("slug"));
		q1.push("<li>"+$(this).data("title")+"</li>");
	});
	htmlMsg="Are you sure about deleting the following templates?<br><ul style='margin-top: 10px;list-style-type: decimal;'>";
	htmlMsg+=q1.join("");
	htmlMsg+="</ul>";
	lgksConfirm(htmlMsg,"Delete Template !",function(ans) {
		if(ans) {
			processAJAXPostQuery(_service("templateManager","delete"),"slug="+q.join(","),function(ans) {
						err=ans.split(":");
						if(err[0]=="error") {
							lgksToast(err[1]);
						} else {
							lgksToast(ans);
							listTemplates();
						}
					},"RAW");
		}
	});
}
function loadEditor(rid,ext) {
	if(ext==null || ext=="undefined") ext="html";
	
	editArea=ace.edit(rid);
	
	setupEditorConfig(editArea,ext);
	addCustomCommands(editArea);
	initAutocompletion(editArea);
	
	$("#toolbtn_editTemplate").addClass("highlight");
	editArea.session.selection.clearSelection();
	editArea.session.getUndoManager().reset();
	//editArea.setReadOnly(false);
}
function editTemplate() {
	$("#toolbtn_editTemplate").removeClass("highlight");
	editArea.setReadOnly(false);
}
function saveTemplate() {
	if(currentTemplate==null) {
		lgksToast("Please load a template to edit.");
		return;
	}
	
	processAJAXPostQuery(_service("templateManager","save"),"slug="+currentTemplate+"&srctype="+srcType+"&text="+encodeURIComponent(editArea.getValue()),function(ans) {
		err=ans.split(":");
		if(err[0]=="error") {
			lgksToast(err[1]);
		} else {
			lgksToast(ans);
		}
	},"RAW");
}
function saveFile() {
	saveTemplate();
}
function closeTemplate() {
	currentTemplate=null;
	$("#pgtoolbar .titleContent").html("");
	$("#componentSpace").html("<h2 align=center>Please load an article to edit its content</h2>");
}