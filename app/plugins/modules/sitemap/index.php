<?php
if(!defined('ROOT')) exit('No direct script access allowed');
session_check(true);

loadModule("page");

$btns=array();
$btns[sizeOf($btns)]=array("title"=>"Reload","icon"=>"reloadicon","tips"=>"Load Page List","onclick"=>"reloadPageList()");
$btns[sizeOf($btns)]=array("bar"=>"||");
$btns[sizeOf($btns)]=array("title"=>"Help","icon"=>"helpicon","tips"=>"Help Contents","onclick"=>"showHelp()");

$layout="apppage";
$params=array("toolbar"=>null,"contentarea"=>"printContent");

printPageContent($layout,$params);

function printContent() {
		$webPath=getWebPath(__FILE__);
?>
<script src='<?=$webPath?>script.js' type='text/javascript' language='javascript'></script>
<link href='<?=$webPath?>style.css' rel='stylesheet' type='text/css' media='all' />
<div id=contentArea style='width:100%;height:100%;display:none;'>
	<div style='width:25%;height:100%;float:left;overflow:auto;border-right:1px solid #CCC;'>
		<h3 class='clr_darkmaroon' align=center style='padding:5px;'>
			<div class='sitemapIcon'>SiteMap
				<div onclick="reloadSiteMap('ul#siteTree');"  class='reloadicon right' style='padding:0px;' title='Reload Site Tree'></div>
			</div>
		</h3>
		<ul id=siteTree></ul>
	</div>
	<div style='width:74%;height:100%;float:left;overflow:auto;'>
		<div id='pageEditor' class=tabs>
			<ul>
				<li><a href='#properties'>Properties</a></li>
				<li><a href='#metatags'>Metatags</a></li>
				<!--<li onclick="doOps('version')" class='ui-state-default ui-corner-top'><a href='#c'>Versions</a></li>-->
				<li onclick="doOps('preview','overlayurl')" class='clr_blue ui-corner-top'><a>Preview</a></li>
				<li onclick="doOps('editor','newtab')" class='clr_blue ui-corner-top'><a>Editor/Designer</a></li>
				<li onclick="doOps('manage','newtab')" class='clr_blue ui-corner-top'><a>Manage</a></li>
				
				<li id=deleteBtn onclick="deletePage()" class='clr_orange ui-corner-top' style='float:right;width:90px;'  title='Delete Page/Link'><a><div class='deleteicon' style='color:white;'>Delete</div></a></li>
				
				<?php if(strlen(checkModule("pageCreate"))>0) { ?>
					<li id=createBtn onclick="createPage()" class='clr_darkgreen ui-corner-top' style='float:right;width:90px;'  title='Create New Page/Link'><a><div class='addicon'>Create</div></a></li>
				<?php } ?>
			</ul>
			<div id=properties style='border:0px !important;'>
			</div>
			<div id=metatags style='border:0px !important;'>
				<div style='float:right;width:100px;'>
					<button onclick='saveMeta()' style='width:100%;'><div class=saveicon>Save</div></button>
					<br/><br/>
					<button onclick='loadMeta()' style='width:100%;'><div class=reloadicon>Reset</div></button>
				</div>
				<table id=metaEditor width=70% border=0 cellspacing=0 cellpadding=0 style='border:0px;margin-left:30px;margin-top:30px;' class='nostyle'>
					<tr><th align=left width=150px>Title</th><td><input name=title type=text /></td></tr>
					<tr><th align=left width=150px>Description</th><td><input name=description type=text /></td></tr>
					<tr><th align=left width=150px>Robots</th><td><input name=robots type=text /></td></tr>
					<tr><th align=left valign=top width=150px>Keywords</th><td><textarea name=keywords style='height:50px;resize:none;'></textarea></td></tr>
					<tr><th align=left valign=top width=150px>Xtra Metatags</th><td><textarea name=metatags style='height:150px;resize:none;'></textarea></td></tr>
				</table>
				<p style='margin-left:50px;'>If left blank, corrosponding default(Global) values from AppSite's Configurations will be loaded.</p>
			</div>
		</div>
	</div>
</div>
<script language=javascript>
$(function() {
	$("#contentArea").show();
	$("ul#siteTree").delegate("li>a,h2>a","click",function(e) {
			e.preventDefault();
			
			$("ul#siteTree .activelink").removeClass("activelink");
			$(this).addClass("activelink");
			
			openRefers(this);
			return false;
		});
	reloadSiteMap();
});
function getCMD(cmd) {
	if(cmd==null) cmd="sitemap";
	return getServiceCMD(cmd)+"&forsite=<?=$_REQUEST['forsite']?>";
}
function reloadSiteMap(barID) {
	if(barID==null) barID='ul#siteTree';
	l=getCMD()+"&action=sitemaptree";
	$("ul#siteTree").html("<div class=ajaxloading5>Loading SiteMap...</div>");
	$(barID).load(l,function() {
			$("a,h2",barID).each(function() {
					if($(this).parent("li").children().length>1) {
						if($(this).find("img").length<=0) {
							$(this).addClass("folder");
						}
						$(this).click(function() {
								$(this).toggleClass("active");
								$(this).next().toggleClass("current");
								$(this).next().toggle("blind","fast");
							});
					}
				});
			$("li>a",barID).each(function() {
					if($(this).find("img").length<=0 && !$(this).hasClass("folder")) {
							$(this).addClass("leaf");
						}
				});
			$("img",barID).attr("width","16px");
			$("img",barID).attr("height","16px");
			$("#pageEditor").tabs("disable",1);
		});
	printWarning("#pageEditor #properties","","No Link Loaded");
	$("#pageEditor").tabs("disable",1);
}
function openRefers(a1) {
	if(a1==null) {
		a1=$("#siteTree a.activelink");
	}
	if(a1.length<1) {
		lgksAlert("No Link Selected").dialog({
				beforeClose: function(event, ui) {
					$("#pageEditor").tabs("select",0);
				}
			});
		return;
	}
	href=$(a1).attr("href");
	rel=$(a1).attr("rel");
	src=$(a1).attr("src");
	
	if(rel==null) {
		rel=0;
	}
	if(src==undefined) {
		src='links';
	}
	
	$("#pageEditor").tabs("disable",1);
	$("input[name],select[name],textarea[name]","#metaEditor").val("");
	if(href!=null) {
		$("#loadingmsg").show();
		$("#pageEditor #properties").html("<div class='ajaxloading'>Loading Properties ...</div>'");
		
		l=getCMD()+"&action=properties&src="+src+"&rel="+rel+"&href="+encodeURIComponent(href);
		$("#pageEditor #properties").load(l,function(data) {
				if(data.length<=0) {
					printWarning("#pageEditor #properties","","Link Source Type Mismatch.");
				} else {
					html="<div style='float:right;width:100px;'>";
					html+="<button onclick='openRefers()' style='width:100%;'><div class=reloadicon>Reload</div></button>";
					html+="<br/><br/>";
					html+="<button onclick='saveProperties()' style='width:100%;'><div class=saveicon>Save</div></button>";
					html+="</div>";
					$("#pageEditor #properties").prepend(html);
					
					$("#pageEditor #properties button").button();
				}
				$("#loadingmsg").hide();
				$("#pageEditor").tabs("enable",1);
			});
		
		l1="services/?scmd=blocks.meta&site=<?=SITENAME?>&forsite=<?=$_REQUEST['forsite']?>&action=fetchmeta&forpage="+encodeURIComponent(href);
		//l1=getCMD()+"&action=metatags&href="+encodeURIComponent(href);
		processAJAXQuery(l1,function(txt) {
				json=$.parseJSON(txt);
				if(json!=null) {
					$.each(json,function(k,v) {
							$("input[name="+k+"],select[name="+k+"],textarea[name="+k+"]","#metaEditor").val(v);
						});
				}
				$("#loadingmsg").hide();
			});
	} else {
		printWarning("#pageEditor #properties","","No Link Loaded");
	}
}
function createPage() {
	l=getCMD('pageCreate')+"&action=showDialog&ptype=all&callback=reloadSiteMap";
	lgksPopup(l,null,{
			width:800,
			height:500,
			modal:true,
			stack:true,
			resizable:false,
			draggable:false,
			closeOnEscape:true,
			dialogClass:"warn",
			show:"fade",
			hide:"fade",
		},"url","Create New Page");
}
function deletePage() {
	a1=$("#siteTree a.activelink");
	if(a1.length==1) {
		href=$(a1).attr("href");
		rel=$(a1).attr("rel");
		src=$(a1).attr("src");
		txt=$(a1).text();
		
		if(rel==null) {
			rel=0;
		}
		if(src==null) {
			src='links';
		}
		if(href!=null) {
			lgksConfirm("Do you really want to delete PageLink <br/> <b><a href='#' onclick=\"doOps('preview','overlayurl')\">"+txt+"</a></b>?",
				"Delete Page Link!",function() {
					/*l=getCMD()+"&action=menudelete&menuid="+menu;
					processAJAXQuery(l,function(data) {
							if(data.length>0) lgksAlert(data);
							else {
								$("#menuselector option[value="+menu+"]").detach();
								loadMenuGroup($("#menuselector").val());
							}
						});*/
				});
		}
	}
}
function doOps(act,target) {
	a1=$("#siteTree a.activelink");
	if(a1.length==1) {
		href=$(a1).attr("href");
		rel=$(a1).attr("rel");
		src=$(a1).attr("src");
		txt=$(a1).text();
		
		if(rel==null) {
			rel=0;
		}
		if(src==null) {
			src='links';
		}
		if(href!=null) {
			l=getCMD()+"&action="+act+"&src="+src+"&rel="+rel+"&href="+encodeURIComponent(href);
			if(target=="overlayurl") {
				parent.lgksOverlayFrame(l,txt);
			} else if(target=="newtab") {
				parent.openInNewTab(txt,l);
			} else if(target=="popup") {
				jqPopupURL(txt,l);
			}
		}
	}
}
function saveMeta() {
	a1=$("#siteTree a.activelink");
	if(a1.length==1) {
		href=$(a1).attr("href");
		rel=$(a1).attr("rel");
		src=$(a1).attr("src");
		txt=$(a1).text();
		
		if(rel==null) {
			rel=0;
		}
		if(src==null) {
			src='links';
		}
		
		if(href!=null) {
			$("#loadingmsg").show();
			l="services/?scmd=blocks.meta&site=<?=SITENAME?>&forsite=<?=$_REQUEST['forsite']?>&action=savemeta&forpage="+encodeURIComponent(href);
			q=[];
			$("input[name],select[name],textarea[name]","#metaEditor").each(function() {
					nm=$(this).attr("name");
					v=$(this).val();
					q.push(nm+"="+encodeURIComponent(v));
				});
			q=q.join("&");
			processAJAXPostQuery(l,q,function(txt) {
					if(txt.trim().length>0) {
						lgksAlert(txt);
					}
					$("#loadingmsg").hide();
				});
		} else {
			lgksAlert("Does Not Have Any MetaSupport.").dialog({
					beforeClose: function(event, ui) {
						$("#pageEditor").tabs("select",0);
					}
				});
		}
	} else {
		lgksAlert("No Link Selected").dialog({
				beforeClose: function(event, ui) {
					$("#pageEditor").tabs("select",0);
				}
			});
	}
}
function saveProperties() {
	alert("Saving");
}
function checkLoaded() {
	a1=$("#siteTree a.activelink");
	if(a1.length<1) {
		lgksAlert("No Link Selected").dialog({
				beforeClose: function(event, ui) {
					$("#pageEditor").tabs("select",0);
				}
			});
		return false;
	} else {
		href=$(a1).attr("href");
		if(href==null) {
			lgksAlert("Does Not Have Any MetaSupport.").dialog({
					beforeClose: function(event, ui) {
						$("#pageEditor").tabs("select",0);
					}
				});
		}
		return false;
	}
	return true;
}
</script>
<?php
}
?>

