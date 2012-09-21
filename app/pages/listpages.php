<?php
if(!defined('ROOT')) exit('No direct script access allowed');
session_check(true);

if(!isset($_REQUEST["forsite"])) {
	dispErrMessage("HostSite Is Required But Not Found","CMS Error",400);
	exit();
}
loadModule("page");

loadModule("editor");
loadEditor("codemirror");

$btns=array();
$btns[sizeOf($btns)]=array("title"=>"Reload","icon"=>"reloadicon","tips"=>"Load Page List","onclick"=>"reloadPageList()");
$btns[sizeOf($btns)]=array("bar"=>"||");
$btns[sizeOf($btns)]=array("title"=>"Create","icon"=>"addicon","tips"=>"Create New Page","onclick"=>"createPage()");
$btns[sizeOf($btns)]=array("title"=>"Clone","icon"=>"cloneicon","tips"=>"Clone Selected Page","onclick"=>"clonePages()");
$btns[sizeOf($btns)]=array("title"=>"Delete","icon"=>"deleteicon","tips"=>"Delete Selected Page","onclick"=>"deletePages()");
$btns[sizeOf($btns)]=array("bar"=>"||");
$btns[sizeOf($btns)]=array("title"=>"Upload","icon"=>"uploadicon","tips"=>"Upload New Pages","onclick"=>"uploadPages()");
$btns[sizeOf($btns)]=array("bar"=>"||");
//$btns[sizeOf($btns)]=array("title"=>"Builder","icon"=>"designicon","tips"=>"Design Full Pages","onclick"=>"openSiteBuilder()");
//$btns[sizeOf($btns)]=array("bar"=>"||");
$btns[sizeOf($btns)]=array("title"=>"Help","icon"=>"helpicon","tips"=>"Help Contents","onclick"=>"showHelp()");
$layout="apppage";
$params=array("toolbar"=>$btns,"contentarea"=>"printContent");

printPageContent($layout,$params);
?>
<script language=javascript>
forSite="<?=$_REQUEST["forsite"]?>";
site="<?=SITENAME?>";
curr_page="";
curr_ext="";
curr_meta="";
$(function() {
	$(".tabPage").css("height",($("#pgworkspace").height()-35)+"px");
	$("select").addClass("ui-state-active ui-corner-all");
	
	$("#codearea").css("height",($(window).height()-$("#toolbar").height()));
	loadCodeEditor("codearea","php");
	reloadPageList();
	
	$("#allpages").delegate("tr","click",function() {
			closePageView();
		
			$(this).parents("table").find("tr.active").removeClass("active");
			$(this).addClass("active");
			
			curr_page=$(this).attr("rel");
			curr_ext=$(this).attr("ext");
			curr_meta=$(this).attr("meta");
			$("#fileInfo .title").html($(this).attr("title"));
			$("#fileInfo .ext").html($(this).attr("ext"));
			$("#fileInfo .size").html($(this).attr("size"));
			$("#fileInfo .created").html($(this).attr("created"));
			$("#fileInfo .modified").html($(this).attr("modified"));
			$("#fileInfo .accessed").html($(this).attr("accessed"));
			
			$("#pageArea").tabs("select",0);
			$("#pageEditBar").show();
			
			$("#pageEditBar button.designer").hide();
			$("#pageEditBar button.weditor").show();
			
			if($(this).hasClass("system")) {
				$("#pageEditBar button.weditor").hide();
				$("#pageEditBar button.rename").hide();
			} else {
				$("#pageEditBar button.weditor").show();
				$("#pageEditBar button.rename").show();
			}
			if(curr_ext=="JSON") {
				$("#pageArea .src").hide();
				$("#pageArea .layout").show();
			} else if(curr_ext=="PAGE") {
				$("#pageEditBar button.designer").show();
				$("#pageEditBar button.weditor").hide();
			} else {
				$("#pageArea .layout").hide();
				$("#pageArea .src").show();
			}
			editor.setOption("readOnly", true);
		});
});
function getCMD() {
	return "services/?scmd=blocks.pages&site="+site+"&forsite="+forSite;
}
function closePageView() {
	$("#fileInfo .title").html("");
	$("#fileInfo .ext").html("");
	$("#fileInfo .size").html("");
	$("#fileInfo .created").html("");
	$("#fileInfo .modified").html("");
	$("#fileInfo .accessed").html("");
	
	$("#pageArea").tabs("select",0);
	$("#pageEditBar").hide();
	
	$("#pageArea .layout").hide();
	$("#pageArea .src").hide();
}
function reloadPageList() {
	closePageView();
	
	$("#loadingmsg").show();
	l=getCMD()+"&action=viewtable&format=table&showSysPages="+$("#showSysPages").is(":checked");
	$("#allpages").html("<tr><td colspan=20 class='ajaxloading6'><br/><br/><br/>Loading ...</td></tr>");
	$("#allpages").load(l,function(txt) {
			$("#loadingmsg").hide();
		});
	l=getCMD()+"&action=editorlist";
	$("#editorSelector select[name=editors]").load(l);
	resetEditors();
	
	l=getCMD()+"&action=componentlist";
	$("#componentSelector select[name=components]").load(l);
	
	l=getCMD()+"&action=templatelist";
	$("#templateSelector select[name=templates]").load(l);
}
linkDlg=null;
function viewLinksToMe(page,ext) {
	if(page==null || page.length<=0) {
		lgksAlert("No Page Selected");
		return false;
	}
	l=getCMD()+"&action=linkstome&ext="+ext+"&forpage="+page;
	linkDlg=jqPopupURL(l,"What Links To Me?");
}
function createNewLink(page) {
	closeLnkDialog();
	
	l=getCMD()+"&action=createLinkDlg&forpage="+page;
	linkDlg=jqPopupURL(l,"Create New Link : "+page);
}
function closeLnkDialog() {
	if(linkDlg!=null) linkDlg.dialog("close");
}
function saveNewLink(table) {
	//alert(table);
}
function viewPage(page) {
	if(page==null || page.length<=0) {
		lgksAlert("No Page Selected");
		return false;
	}
	l="index.php?site=<?=$_REQUEST["forsite"]?>&page="+page;
	
	if(typeof parent.openInNewTab=="function")
		parent.openInNewTab("Preview",l);
	else
		lgksOverlayURL(l,"Preview");
	//window.open(l);
}
function renamePage(page,ext) {
	if(page==null || page.length<=0) {
		lgksAlert("No Page Selected");
		return false;
	}
	lgksPrompt("Please give a new name for the page <b>"+page+"</b> !","Rename Page",null,function(txt) {
			if(txt.length>0 && txt!=page) {// && txt.indexOf(".")>1
				l=getCMD()+"&action=rename";
				q="&pg="+page+"&ext="+ext+"&topg="+txt;
				$("#loadingmsg").show();
				processAJAXPostQuery(l,q,function(txt) {
						if(txt.length>0) lgksAlert(txt);
						reloadPageList();
					});
			} else {
				if(txt.length>0)
					lgksAlert("The given name is not Proper.<br/>Try Again.");
			}
		});
	resetEditors();
}
function editPage(page,ext,editor) {
	if(page==null || page.length<=0) {
		lgksAlert("No Page Selected");
		return false;
	}
	ext=ext.toLowerCase();
	if(editor=="codeviewer") {
		loadCodeArea(page,ext);
	} else {
		curr_page=page;
		curr_ext=ext.toUpperCase();
		if(ext=="php" || ext=="html" || ext=="htm" || ext=="xhtml" || ext=="xhtm") {
			loadEditor("wysiwyg");
		} else if(ext=="js" || ext=="css") {
			loadEditor("codeeditor");
		} else if(ext=="tpl") {
			loadEditor("codeeditor");
		} else if(ext=="page") {
			//designPage(page,ext);
			loadEditor("pagebuilder");
		} else if(ext=="json") {
			$("#pageArea").tabs("select",3);
		} else {
			osxPopupDiv("#editorSelector");
		}
	}
}
function loadEditor(editor) {
	curr_page=$("#allpages tr.active").attr('rel');
	if(curr_page==null || curr_page.length<=0) {
		lgksAlert("No Page Selected");
		return false;
	}
	l=getCMD()+"&action=editor&type="+editor+"&editpage="+curr_page;
	
	if(typeof parent.openInNewTab=="function")
		parent.openInNewTab(editor.toUpperCase()+" Editor",l);
	else
		lgksOverlayURL(l);
	
	//curr_page="";
	//curr_ext="";
}
function clonePages() {
	r=$("#allpages input[name=pgselect][type=checkbox]:checked");
	if(r.length>0) {
		pg="";
		$(r).each(function() {
				pg+=$(this).attr("rel")+",";
			});
		l=getCMD()+"&action=clone";
		q="&toclone="+pg;
		processAJAXPostQuery(l,q,function(txt) {
				if(txt.length>0) lgksAlert(txt);
				reloadPageList();
			});		
	}
}
function deletePages() {
	r=$("#allpages input[name=pgselect][type=checkbox]:checked");
	r1=$("#allpages tr.active").attr('rel');
	if(r.length>0) {
		pg="";
		$(r).each(function() {
				pg+=$(this).attr("rel")+",";
			});
		lgksConfirm("Are you Sure about deleting the pages <br/><br/><div style='width:100%;height:70px;border:1px solid #aaa;overflow:auto;'><h3>"+pg+"</h3></div><br/>Deleting them will not remove them from Menus though.",
				"Delete Pages",function() {
					l=getCMD()+"&action=delete";
					q="&todelete="+pg;
					$("#loadingmsg").show();
					processAJAXPostQuery(l,q,function(txt) {
							if(txt.length>0) lgksAlert(txt);
							reloadPageList();
						});
				});	
	} else if(r1!=null && r1.length>0) {
		pg=r1;
		lgksConfirm("Are you Sure about deleting the pages <br/><br/><div style='width:100%;height:70px;border:1px solid #aaa;overflow:auto;'><h3>"+pg+"</h3></div><br/>Deleting them will not remove them from Menus though.",
				"Delete Page",function() {
					l=getCMD()+"&action=delete";
					q="&todelete="+pg;
					$("#loadingmsg").show();
					processAJAXPostQuery(l,q,function(txt) {
							if(txt.length>0) lgksAlert(txt);
							reloadPageList();
						});
				});	
	} else {
		lgksAlert("No Pages Selected");
	}
}
function createPage() {
	$("#createPageEditor input[name=pagename]").val("");
	$("#createPageEditor select[name=pagetmplt]").val("blank");
	osxPopupDiv("#createPageEditor",function(txt) {
			if(txt=="OK") {
				nm=$("#createPageEditor input[name=pagename]").val().split(" ").join("_");
				tmpl=$("#createPageEditor select[name=pagetmplt]").val();
				if(nm.length>0) {
					l=getCMD()+"&action=create";
					q="&nm="+nm+"&tmpl="+tmpl;
					$("#loadingmsg").show();
					processAJAXPostQuery(l,q,function(txt) {
							if(txt.length>0) lgksAlert(txt);
							reloadPageList();
						});
				}
			}
		});
}
/*New JS Codes*/
function loadSource() {
	curr_page=$("#allpages tr.active").attr('rel');
	if(curr_page.length<=0 && curr_ext.length<=0) {
		lgksAlert("No Page Selected");
		return false;
	}
	page=curr_page;
	ext=curr_ext.toLowerCase();
	if(ext=="js") ext="javascript";
	else if(ext=="html" || ext=="htm") ext="htmlmixed";
	else if(ext=="php") ext="php";
	else ext="htmlmixed";
	editor.setValue("Loading ...");
	$("#loadingmsg").show();
	l=getCMD()+"&action=fetch&forpage="+page;
	processAJAXQuery(l,function(txt) {
			editor.setValue(txt);
			editor.setOption("mode",ext);
			$("#loadingmsg").hide();
		});
}
function saveSource() {
	if(curr_page.length<=0 && curr_ext.length<=0) {
		return false;
	}
	page=curr_page;
	ext=curr_ext.toLowerCase();
	$("#loadingmsg").show();
	data=editor.getValue();
	l1=getCMD()+"&action=save&forpage="+page;
	q="&data="+encodeURIComponent(data);
	$("#loadingmsg").show();
	processAJAXPostQuery(l1,q,function(txt) {
			if(txt.trim().length>0) {
				lgksAlert(txt);
			}
			$("#loadingmsg").hide();
		});
}
function loadMeta() {
	if(curr_meta.length<=0) {
		lgksAlert("No Page Selected").dialog({
				beforeClose: function(event, ui) {
					$("#pageArea").tabs("select",0);
				}
			});
		return false;
	}
	$("input[name],select[name],textarea[name]","#metaEditor").val("");
	page=curr_meta;
	$("#loadingmsg").show();
	l="services/?scmd=blocks.meta&site="+site+"&forsite="+forSite+"&action=fetchmeta&forpage="+page;
	processAJAXQuery(l,function(txt) {
			json=$.parseJSON(txt);
			if(json!=null) {
				$.each(json,function(k,v) {
						$("input[name="+k+"],select[name="+k+"],textarea[name="+k+"]","#metaEditor").val(v);
					});
			}
			$("#loadingmsg").hide();
		});
}
function saveMeta() {
	if(curr_meta.length<=0) {
		return false;
	}
	page=curr_meta;
	$("#loadingmsg").show();
	l1="services/?scmd=blocks.meta&site="+site+"&forsite="+forSite+"&action=savemeta&forpage="+page;
	q=[];
	$("input[name],select[name],textarea[name]","#metaEditor").each(function() {
			nm=$(this).attr("name");
			v=$(this).val();
			q.push(nm+"="+encodeURIComponent(v));
		});
	q=q.join("&");
	processAJAXPostQuery(l1,q,function(txt) {
			if(txt.trim().length>0) {
				lgksAlert(txt);
			}
			$("#loadingmsg").hide();
		});
}
function loadLayout() {
	if(curr_meta.length<=0) {
		return false;
	}
	page=curr_meta;
	
	$("#layoutEditor .regions tbody").html("");
	$("#layoutEditor .properties input").val("");
	
	$("#loadingmsg").show();
	l=getCMD()+"&action=fetchlayout&forpage="+curr_meta;
	processAJAXQuery(l,function(txt) {
			json=$.parseJSON(txt);
			
			$.each(json,function(k,v) {
				if(k=="layout") {
					return;
				}
				if(k=="enabled") {
					if(v) {
						v="true";
					} else {
						v="false";
					}
				}
				$("#layoutEditor .properties tbody input[name="+k+"]").val(v);
				$("#layoutEditor .properties tbody select[name="+k+"]").val(v);
			});
			$.each(json['layout'],function(k,v) {
				html="<tr>";
				if(v['enable']=="true" || v['enable'])
					html+="<th><input type=checkbox name='layout["+k+"][enable]' class='visible' checked /></th>";
				else
					html+="<th><input type=checkbox name='layout["+k+"][enable]' class='visible' /></th>";
				html+="<th align=left style='text-transformation:capitalize;'>"+k+"</th>";
				html+="<td><input type=text name='layout["+k+"][component]' class='component' value='"+v['component']+"' /></td>";
				html+="<td>";
				if(k=="header" || k=="footer" || k=="copyright" || k=="content")
					html+="<div title='' class='minibtn blankicon right' rel='"+k+"' onclick=''></div>";
				else
					html+="<div title='Delete' class='minibtn deleteicon right' rel='"+k+"' onclick=\"$(this).parents('tr').detach();\"></div>";
				html+="<div title='Edit In Source Editor' class='minibtn editicon right' rel='"+k+"' onclick='editComponentCode(this)'></div>";
				html+="<div title='Edit In WYSIWYG Editor' class='minibtn codeicon right' rel='"+k+"' onclick='editComponent(this)'></div>";
				html+="<div title='View Suggestions' class='minibtn popupicon right' rel='"+k+"' onclick='suggestComponent(this)'></div>";
				html+="</td>";
				html+="</tr>";
				
				$("#layoutEditor .regions tbody").append(html);
			});
			$("#loadingmsg").hide();
		});
}
function saveLayout() {	
	if(curr_meta.length<=0) {
		return false;
	}
	page=curr_meta;
	
	$("#loadingmsg").show();
	l1=getCMD()+"&action=savelayout&forpage="+page;
	q="";
	$("#layoutEditor .properties tbody input[name],#layoutEditor .properties tbody select[name]").each(function() {
			q+="&"+$(this).attr("name")+"="+encodeURIComponent($(this).val());
		});
	$("#layoutEditor .regions tbody input[name]").each(function() {
			v=$(this).val();
			if($(this).attr("type")=="checkbox") {
				if($(this).is(':checked')) {
					v=true;
				} else {
					v=false;
				}
			}
			q+="&"+$(this).attr("name")+"="+v;//encodeURIComponent(v);
		});
	$("#layoutEditor").hide();
	processAJAXPostQuery(l1,q,function(txt) {
			if(txt.trim().length>0) {
				lgksAlert(txt);
			}
			$("#layoutEditor").show();
			$("#loadingmsg").hide();
		});
}
function downloadPage(page,ext) {
	if(page==null || page.length<=0) {
		lgksAlert("No Page Selected");
		return false;
	}
	l1=getCMD()+"&action=download&forpage="+page;
	window.open(l1);
}
function previewPage(curr_page,curr_ext) {
	if(curr_page==null) return;
	if(curr_ext.length>0) {
		curr_page=curr_page.substr(0,curr_page.length-curr_ext.length-1);
	}
	lgksPrompt("Please give any extra query parameters(format : a=b&c=d)?","QUERY PARAMS",null,function(txt) {
			if(txt.length>0) {
				txt="&"+txt;
				txt=txt.replace("&&","&");
			}
			txt="page="+curr_page+txt;
			l=getCMD()+"&action=preview&link="+encodeURIComponent(txt);
			parent.lgksOverlayFrame(l,"Preview");
		});
}
uploadDlg=null;
function uploadPages() {
	uploadDlg=osxPopupDiv("#uploadPages");
}
function uploadComplete(txt) {
	uploadDlg.dialog('close');
	reloadPageList();
	if(txt.length>0) lgksAlert(txt);
}
function resetEditors() {
	$("#metaarea").val();
	$("#metaarea").attr("readonly","true");
	editor.setValue("");
}
function suggestComponent(btn) {
	rel=$(btn).attr('rel');
	tr=$(btn).parents("tr");
	$('#componentSelector select[name=components]').val(tr.find("input.component").val());
	jqPopupDiv("#componentSelector").dialog({
			resizable:false,
			height:150,
			buttons:{
				"Select":function() {
					v=$('#componentSelector select[name=components]').val();
					tr.find(".component").val(v);
					tr.find(".visible").get(0).checked=true;
					$(this).dialog('close');
				},
				"Cancel":function() {
					$(this).dialog('close');
				},
			}
		});
}
function suggestTemplate(btn) {
	rel=$(btn).attr('rel');
	tr=$(btn).parents("tr");
	$('#templateSelector select[name=templates]').val(tr.find("input[name=template]").val());
	jqPopupDiv("#templateSelector").dialog({
			resizable:false,
			height:150,
			buttons:{
				"Select":function() {
					v=$('#templateSelector select[name=templates]').val();
					tr.find("input[name=template]").val(v);
					$(this).dialog('close');
				},
				"Cancel":function() {
					$(this).dialog('close');
				},
			}
		});
}
function editComponent(btn) {
	rel=$(btn).attr('rel');
	tr=$(btn).parents("tr");
	ref=tr.find("input.component").val();
	
	l=getCMD()+"&action=editurl&file="+ref;
	processAJAXQuery(l,function(txt) {
			url="index.php?site=<?=SITENAME?>&forsite=<?=$_REQUEST['forsite']?>&page=wysiwygedit&file="+txt;
			if(typeof parent.openInNewTab=="function")
				parent.openInNewTab("WYSIWYG",url);
			else
				lgksOverlayURL(url,"WYSIWYG");
		});
}
function editComponentCode(btn) {
	rel=$(btn).attr('rel');
	tr=$(btn).parents("tr");
	ref=tr.find("input.component").val();
	
	nm=ref.split("/");
	nm=nm[nm.length-1];
	
	l=getCMD()+"&action=editurl&file="+ref;
	processAJAXQuery(l,function(txt) {
			url="index.php?site=<?=SITENAME?>&forsite=<?=$_REQUEST['forsite']?>&page=codeeditor&file="+txt;
			if(typeof parent.openInNewTab=="function")
				parent.openInNewTab(nm,url);
			else
				lgksOverlayURL(url,nm);
		});
}
function openSiteBuilder(page,ext) {
	url="index.php?site=<?=SITENAME?>&forsite=<?=$_REQUEST['forsite']?>&encoded=<?=cryptURL("page=modules&mod=siteBuilder&popup=true")?>";
	if(page!=null && page.length>0) {
		url+="&design="+page;
	}
	win=window.open(url);
}
function showHelp() {
	jqPopupDiv("#helpInfo",null,true,"700","500");
}
</script>
<?php
function printContent() {
	$templates="";
	if(is_dir(APPROOT.CMS_PAGE_TEMPLATES)) {
		$fs=scandir(APPROOT.CMS_PAGE_TEMPLATES);
		unset($fs[0]);unset($fs[1]);
		foreach($fs as $a) {
			$t=explode(".",$a);
			unset($t[count($t)-1]);
			$t=implode(".",$t);
			$templates.="<option value='{$a}'>{$t} Template</option>";
		}
	}
?>
<style>
.tabPage {
	width:100%;height:500px;
	overflow:auto;
	padding:0px !important;margin:0px !important;
}
#pageArea .src,#pageArea .layout {
	display:none;
}
input[type=text],textarea {
	border:1px solid #aaa;
	width:95%;
}
select {
	width:95%;
	height:22px;
}
.minibtn {
	cursor:pointer;margin:0px !important;padding:0px;padding-left:7px;
}
</style>
<div style='width:100%;height:100%;'>
<div style='width:20%;height:100%;float:left;overflow:auto;'>
	<table class='datatable' width=100% border=0 cellpadding=3 cellspacing=0 style='margin:0px;'>
		<tbody id=allpages>
		</tbody>
	</table>
	<span style='position:fixed;top:10px;right:50px;'>
		<h3 style='color:#639480;'>
			<input onchange='reloadPageList()' type=checkbox id=showSysPages />Show System Pages
		</h3>
	</span>
</div>
<div style='width:80%;height:100%;float:right;overflow:hidden;'>
	<div id=pageArea class=tabs>
		<ul>
			<li><a class='indo' href='#fileInfo'>Info</a></li>
			<li><a class='meta' href='#fileMeta' onclick='return loadMeta();'>Meta</a></li>
			<li><a class='src' href='#fileSrc' onclick='return loadSource();'>Source</a></li>
			<li><a class='layout' href='#fileLayout' onclick='return loadLayout();'>Layout</a></li>
		</ul>
		<div id=fileInfo class='tabPage'>
			<table width=70% cellpadding=2 cellspacing=0 border=0 style='border:0px;margin:10px;' class='nostyle'>
				<tr><th width=150px align=left>Page Name</th><td class='title'></td></tr>
				<tr><th width=150px align=left>Source Type</th><td class='ext'></td></tr>
				<tr><th width=150px align=left>Size</th><td class='size'></td></tr>
				<tr><th width=150px align=left>Created</th><td class='created'></td></tr>
				<tr><th width=150px align=left>Modified</th><td class='modified'></td></tr>
				<tr><th width=150px align=left>Accessed</th><td class='accessed'></td></tr>
				<tr><td colspan=10 style='border:0px;'><hr/></td></tr>
				<tr><th colspan=10 align=center id=pageEditBar style='display:none;'>
					<button class='linkstome' style="width:120px;" class='' onclick="viewLinksToMe(curr_page,curr_ext)"><div class='searchicon'>Links</div></button>
					<button class='rename' style="width:120px;" class='' onclick="renamePage(curr_page,curr_ext)"><div class='renameicon'>Rename</div></button>
					<button class='weditor' style="width:120px;" class='' onclick="editPage(curr_page,curr_ext)"><div class='editicon' style='padding-left:30px;'>Editor</div></button>
					<button class='designer' style="width:120px;" class='' onclick="editPage(curr_page,curr_ext)"><div class='designicon' style='padding-left:30px;'>Design</div></button>
					<button class='download' style="width:120px;" class='' onclick="downloadPage(curr_page,curr_ext)"><div class='downloadicon'>Download</div></button>
					
					<button class='preview' style="width:120px;" class='' onclick="previewPage(curr_page,curr_ext)"><div class='viewicon'>Preview</div></button>
				</td></tr>
			</table>
		</div>
		<div id=fileMeta class='tabPage' style='overflow:hidden;'>
			<button style="width:90px;" class='' onclick="loadMeta();"><div class='reloadicon'>Reset</div></button>
			<button style="width:90px;" class='' onclick="saveMeta()"><div class='searchicon'>Save</div></button>
			<div id=metaEditor title='Code Editor' style='width:100%;height:95%;overflow:hidden;'>
				<table width=70% border=0 cellspacing=0 cellpadding=0 style='border:0px;margin-left:30px;margin-top:30px;' class='nostyle'>
					<tr><th align=left width=150px>Title</th><td><input name=title type=text /></td></tr>
					<tr><th align=left width=150px>Description</th><td><input name=description type=text /></td></tr>
					<tr><th align=left width=150px>Robots</th><td><input name=robots type=text /></td></tr>
					<tr><th align=left valign=top width=150px>Keywords</th><td><textarea name=keywords style='height:50px;resize:none;'></textarea></td></tr>
					<tr><th align=left valign=top width=150px>Xtra Metatags</th><td><textarea name=metatags style='height:150px;resize:none;'></textarea></td></tr>
				</table>
				<p style='margin-left:50px;'>If left blank, corrosponding default(Global) values from AppSite's Configurations will be loaded.</p>
			</div>
		</div>
		<div id=fileSrc class='tabPage' style='overflow:hidden;'>
			<button style="width:90px;" class='' onclick="editor.setOption('readOnly', false);"><div class='editicon'>Edit</div></button>
			<button style="width:90px;" class='' onclick="loadSource();"><div class='reloadicon'>Reset</div></button>
			<button style="width:90px;" class='' onclick="saveSource()"><div class='searchicon'>Save</div></button>
			<div id=codeEditor title='Code Editor' style='width:100%;height:95%;overflow:hidden;'>
				<textarea name=codearea id=codearea style='width:100%;height:500px;' readonly></textarea>
			</div>
		</div>
		<div id=fileLayout class='tabPage' style='overflow:hidden;'>
			<button style="width:90px;" class='' onclick="loadLayout();"><div class='reloadicon'>Reset</div></button>
			<button style="width:90px;" class='' onclick="saveLayout()"><div class='searchicon'>Save</div></button>
			<hr/>
			<div id=layoutEditor title='Code Editor' style='width:100%;height:90%;overflow:auto;padding-left:20px;padding-top:10px;'>
				<table class='properties nostyle noborder' width=500px border=0 cellspacing=0 cellpadding=1>
					<thead>
						<tr>
							<th width=50px>&nbsp;</th>
							<th width=120px>Property</th>
							<th>Value</th>
							<th width=50px>&nbsp;</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<th></th>
							<th align=left>Template</th>
							<td><input name=template class='template' type=text /></td>
							<td>
								<div class='minibtn popupicon right' rel='' onclick='suggestTemplate(this)'></div>
							</td>
						</tr>
						<tr>
							<th></th>
							<th align=left>CSS Libs</th>
							<td><input name=css type=text /></td>
						</tr>
						<tr>
							<th></th>
							<th align=left>JS Libs</th>
							<td><input name=js type=text /></td>
						</tr>
						<tr>
							<th></th>
							<th align=left>Modules</th>
							<td><input name=modules type=text /></td>
						</tr>
						<tr>
							<th></th>
							<th align=left>Enabled</th>
							<td>
								<select name=enabled class='ui-widget-header'>
									<option value='true'>Yes</option>
									<option value='false'>No</option>
								</select>
							</td>
						</tr>
					</tbody>
				</table><br/>
				<table class='regions nostyle noborder' width=500px border=0 cellspacing=0 cellpadding=1>
					<thead>
						<tr>
							<th width=50px>Visible</th>
							<th width=120px>Region</th>
							<th>Component</th>
						</tr>
						<tr><td colspan=10><hr/></td></tr>
					</thead>
					<tbody></tbody>
				</table><br/>
			</div>
		</div>
	</div>
</div>
</div>

<div style='display:none'>
	<div id=editorSelector title='Select Page Editor' align=center>
		<select name=editors style="width:100%;height:25px;font-size:15px;font-weight:bold;">
		</select><br/><br/><br/>
		<button onclick="$('#editorSelector').dialog('close');">Cancel</button>
		<button onclick="loadEditor($('#editorSelector select[name=editors]').val());$('#editorSelector').dialog('close');">Start</button>
	</div>
	<div id=componentSelector title='Select Page Component' align=center>
		<select name=components style="width:100%;height:25px;font-size:15px;font-weight:bold;">
		</select><br/><br/>
	</div>
	<div id=templateSelector title='Select Page Template' align=center>
		<select name=templates style="width:100%;height:25px;font-size:15px;font-weight:bold;">
		</select><br/><br/>
	</div>
	<div id=uploadPages title='Upload Pages'>
		<form enctype='multipart/form-data' method='POST' target="uptarget"
			 action='services/?scmd=blocks.pages&forsite=<?=$_REQUEST["forsite"]?>&action=upload'>
			 <table class='noborder' width=100% border=0 cellspacing=0 style='border:0px;'>
				<tr><th width=100px>Page Name</th>		
					<td><input type=file name=attachment  style="width:100%;height:20px;font-size:13px;font-weight:bold;border:1px solid #aaa;" /></td>
				</tr>
				<tr><td colspan=10><hr/></td></tr>
				<tr><td colspan=10 align=center>
					<button type=reset>Reset</button>
					<button type=submit onclick="">Submit</button>
				</td></tr>
			</table>
		</form>
		<iframe id=iptarget name=uptarget style='display:none'></iframe>
	</div>
	<div id=createPageEditor title='Create New Page' align=center>
		<p>Please create a new page. Please do not give any extensions as they are picked up automatically.
		</p>
		<table class='noborder' width=100% border=0 cellspacing=0>
			<tr><th width=100px>Page Name</th>		
				<td><input type=text name=pagename  style="width:100%;height:20px;font-size:13px;font-weight:bold;border:1px solid #aaa;" /></td>
			</tr>
			<tr><th width=100px>Template</th>
				<td>
				<select name=pagetmplt style="width:100%;height:25px;font-size:15px;font-weight:bold;">
					<?=$templates?>
					<option value='generated'>Virtual Page (Using Sub-Components)</option>
					<option value='blank'>Custom Page</option>
				</select>
				</td>
			</tr>
		</table><br/>
	</div>
	<div id=helpInfo title='Help !' class='helpInfo' style='width:100%;text-align:justify;font-size:15px;font-family:verdana;'>
		<b>Page Manager</b>, helps you manage all your pages across your selected appSite.
		<p><u><b>Various Page Categories</b></u></p>
		<ul>
			<li><b>System Pages</b>, these are system created and used pages. Changing them may cause catastrophic results. You 
			may delete such pages but one at a time.
			</li>
			<li><b>Web Pages</b>, these are your pages that you created/designed/uploaded. They are most common type of pages
			and used widely across the appSite.
			</li>
			<li><b>Virtual Pages</b>, these are more development oriented pages. Here you can connect various <b>Components</b> you 
			created under Developer Tab. They are more organized way of developing a appSite.
			</li>
		</ul>
		<p><u><b>Various Editors</b></u></p>
		<ul>
			<li><b>Source Editor</b>, this is the core developer's editor which nativly helps you to view and edit the source of the pages.
			</li>
			<li><b>WYSIWYG Editor</b>, this is a more user friendly editor with a visual HTML editor that supports various attributes simmillar
			to Dreamweaver or Kompozer desktop HTML Editors. Here you can design full pages as well use PHP and JS in Source mode of editor.
			</li>
			<li><b>Page Builder</b>, this is a user oriented page designer where you don't have to write a single line of code and simply use 
			drag and drop interface to design you Logiks Compatible Dynamic WebPages using avaiable layouts and widgets.
			</li>
		</ul>
	</div>
</div>
<?php
}
?>
