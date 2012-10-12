<?php
if(!defined('ROOT')) exit('No direct script access allowed');
session_check(true);
user_admin_check();

loadModule("page");

$params["toolbar"]="printToolbar";
$params["contentarea"]="printContent";

$layout="apppage";

_js(array("jquery.multiselect"));
_css(array("jquery.multiselect"));

printPageContent("apppage",$params);

function printToolbar() { ?>
<button onclick="reloadList()" style='width:100px;' ><div class='reloadicon'>Reload</div></button>
<button onclick="createRSS()" title="Create New Feed" style="width:100px;" ><div class='addicon'>Create</div></button>
||
<button onclick="$('#infoBox').dialog({resizable:false,width:600,height:'auto',modal:true,});" style='width:100px;' ><div class='infoicon'>About</div></button>
<?php
}
function printContent() { 
	$webPath=getWebPath(__FILE__);
?>
<style>
.minibtn {
	padding-left:0px;
}
h5,h6 {
	margin:0px;
	padding:0px;
	color:#888;
}
input {
	border:1px solid #aaa;
	width:95%;height:22px;
}
select {
	width:95%;height:24px;
}
textarea {
	border:1px solid #aaa;
	width:94%;
	resize:none;
}
input[type=checkbox] {
	width:25px;
}
table tr, table td {
	border:0px;
}
table th {
	text-align:center;
}
button.ui-multiselect {
	width:285px !important;
	height:27px !important;
}
.ui-multiselect.ui-button {
	width:285px !important;
	height:27px !important;
}
.ui-multiselect.ui-button .ui-button-text {
	margin:0px;padding:0px;
}
button.ui-multiselect span {
	font-weight:bold !important;
}
</style>
<table id=feedTable class='datatable' width=99% cellpadding=2 cellspacing=0 border=0 style='margin:5px;border:2px solid #aaa;'>
	<thead>
		<tr class='ui-widget-header'>
			<th width=100px>RSSID</th>
			<th>Title</th>
			<th>Category</th>
			<th width=50px>Language</th>
			<th>Author</th>
			<th width=70px>Till Date</th>
			<th width=25px>Blocked</th>
			<th width=70px>--A--</th>
			<th width=30px>&nbsp;</th>
		</tr>
	</thead>
	<tbody>
	
	</tbody>
</table>
<div style='display:none;'>
	<div id=infoBox title='About Galleria' >
		<b>RSS Generator</b> is a central place to manage all the generated RSS Feeds for site <b><?=strtoupper($_REQUEST["forsite"])?></b>.
		<p>
			<b>What is RSS?</b><br/>
			RSS (Rich Site Summary) is a format for delivering regularly changing web content. Many news-related sites, 
			weblogs and other online publishers syndicate their content as an RSS Feed to whoever wants it.
		</p>
		<p>
			<b>Why RSS? Benefits and Reasons for using RSS.</b><br/>
			RSS solves a problem for people who regularly use the web. It allows you to easily stay informed by retrieving 
			the latest content from the sites you are interested in. You save time by not needing to visit each site individually. 
			You ensure your privacy, by not needing to join each site's email newsletter. The number of sites offering RSS feeds is	
			growing rapidly and includes big names like Yahoo News.
		</p>
		<p>
			<b>What do I need to do to read an RSS Feed? RSS Feed Readers and News Aggregators.</b><br/>
			Feed Reader or News Aggregator software allow you to grab the RSS feeds from various sites and display them for you to read and use.
			A variety of RSS Readers are available for different platforms. 
			<ul>
				<li>Amphetadesk (Windows, Linux, Mac).</li>
				<li>My Yahoo, Bloglines, and Google Reader are popular Web-Based feed readers.</li>
				<li>XPRSS on BlackBerry</li>
				<li>Aggregator,JustReader,Pulse on Android</li>
				<li>NetNewsWire, Reeder, Flipboard,Pulse on iPhone and iPad</li>
			</ul>
		</p>
		<p>
			Please visit <a style='color:blue;font-weight:bold;' href='http://en.wikipedia.org/wiki/RSS' target=_blank>Wikipedia</a> for futhuer information about RSS.
		</p>
	</div>
	<div id=editor title='RSS Editor' style='width:800px;height:500px;'>
		<?php
			include "editor.php";
		?>
	</div>
</div>
<script language=javascript>
lnk="services/?scmd=rssedit&site=<?=SITENAME?>&forsite=<?=$_REQUEST["forsite"]?>";
$(function() {
	$("#feedTable tbody").delegate(".minibtn","click",function() {
			rel=$(this).parents("tr").attr("rel");
			rssid=$(this).parents("tr").attr("rssid");
			if($(this).hasClass('editicon')) {
				editRSS(rel,rssid);
			} else if($(this).hasClass('deleteicon')) {
				deleteRSS(rel,rssid);
			} else if($(this).hasClass('linkicon')) {
				l="<?=SiteLocation?>services/?scmd=rss&site=<?=$_REQUEST["forsite"]?>&rss="+rssid;
				lgksAlert("<a href='"+l+"' target=_blank>"+l+"</a>");
			}
		});
	$("#feedTable tbody").delegate("input[type=checkbox]","change",function() {
			rssid=$(this).parents("tr").attr("rel");
			name=$(this).attr("name");
			
			if(name=="blocked") {
				l=lnk+"&action=blocked";
				q="&id="+rssid+"&v="+this.checked;
				processAJAXPostQuery(l,q,function(txt) {
						if(txt.length>0) lgksAlert(txt);
					});
			}
		});
	$("select[multiple]").multiselect({
				minWidth:290,
				header:"Choose Data Tables",
			});
	$(".datefield").datepicker();
	reloadList();
});
function reloadList() {
	l=lnk+"&format=table&action=rsslist";
	$("#feedTable tbody").load(l);
}
function reloadEditor(updateSelector) {
	$("#rssEditor").tabs("select",0);
	
	$("#editor *[name]").val("");
	$("#editor *[name=id]").val(0);
	$("#editor *[name=language]").val('en-US');
	$("#editor *[name=attributes_template]").val(0);
	$("#editor *[name=attributes_limit]").val(10);
	$("#editor *[name=image_link]").val('images/rss.png');
	$("#editor *[name=blocked]").val('false');
	$("#editor *[name=secure]").val('false');
	
	if(updateSelector) {
		loadEditorSelectors(null,null);
	}
}
function deleteRSS(id,rssid) {
	lgksConfirm("Are you about deleting selected RSS?","Delete RSS "+rssid,function() {
			l=lnk+"&action=delete";
			q="&id="+id;
			processAJAXPostQuery(l,q,function(txt) {
					if(txt.length>0) {
						lgksAlert(txt);
						reloadList();
					} else {
						$("#feedTable tbody tr[rel="+id+"]").detach();
					}
				});
		});
}
function editRSS(id,rssid) {
	reloadEditor(false);
	l=lnk+"&action=view&format=json";
	q="&id="+id;
	processAJAXPostQuery(l,q,function(txt) {
			json=$.parseJSON(txt);
			if(json!=null) {
				dt="";
				dc="";
				$.each(json,function(k,v) {
						if(k=="datatable_table") {
							dt=v;
						} else if(k=="datatable_cols") {
							dc=v;
						} else if(k=="datatable_orderby") {
							dr=v;
						} else if(k=="attributes_itemImageCol") {
							di=v;
						}
						$("#editor *[name="+k+"]").val(v);
					});
				loadEditorSelectors(dt,dc,dr,di);
				$("#editor").dialog({
						width:900,
						height:460,
						resizable:false,
						buttons:{
							Cancel:function() {
								$(this).dialog("close");
							},
							Save:function() {
								dlg=$(this);
								if(!checkSubmit()) {
									return;
								}
								l=lnk+"&action=save&format=json";
								q="";
								$("#editor *[name]").each(function() {
										nm=$(this).attr("name");
										if(nm.length>0) {
											q+="&"+nm+"="+$(this).val();
										}
									});
								processAJAXPostQuery(l,q,function(txt) {
										if(txt.length>0) {
											lgksAlert(txt);
										} else {
											reloadList();
											dlg.dialog("close");
										}
									});
							}
						}
					});
			}
		});
}
function createRSS() {
	reloadEditor(true);
	$("#editor").dialog({
			width:900,
			height:460,
			resizable:false,
			buttons:{
				Cancel:function() {
					$(this).dialog("close");
				},
				Save:function() {
					dlg=$(this);
					if(!checkSubmit()) {
						return;
					}
					l=lnk+"&action=save&format=json";
					q="";
					$("#editor *[name]").each(function() {
							nm=$(this).attr("name");
							if(nm.length>0) {
								q+="&"+nm+"="+$(this).val();
							}
						});
					processAJAXPostQuery(l,q,function(txt) {
							if(txt.length>0) {
								lgksAlert(txt);
							} else {
								reloadList();
								dlg.dialog("close");
							}
						});
				}
			}
		});
}
function checkSubmit() {
	if($("#editor *[name=rssid]").val().length<=0) {
		lgksAlert("RSSID Is Must");
		return false;
	}
	if($("#editor *[name=title]").val().length<=0) {
		lgksAlert("Title Is Must");
		return false;
	}
	if($("#editor *[name=language]").val().length<=0) {
		lgksAlert("Language Is Must");
		return false;
	}
	return true;
}
function loadEditorSelectors(tables,cols,orderBy,imageCol) {
	$("#editor select[name=datatable_table]").multiselect("destroy");
	$("#editor select[name=datatable_cols]").multiselect("destroy");
	
	$("#editor select[name=datatable_table]").css("display","none");
	$("#editor select[name=datatable_cols]").css("display","none");
	
	l=getServiceCMD("dbedit")+"&forsite=<?=$_REQUEST['forsite']?>&format=select&action=tablelist";
	$("#editor select[name=datatable_table]").load(l,function(data) {
			if(tables!=null && tables.length>0) {
				$("#editor select[name=datatable_table]").val(tables.split(","));
			}
			oldValue="";
			$("#editor select[name=datatable_table]").multiselect({
					minWidth:290,
					header:false,
					nonSelectedText:"Choose Data Tables",
					beforeopen:function() {
						oldValue=$("#editor select[name=datatable_table]").val();
						if(oldValue==null) oldValue="";
					},
					close:function(event,ui) {
						if(oldValue.toString()!=$("#editor select[name=datatable_table]").val().toString()) {
							l=getServiceCMD("dbedit")+"&forsite=<?=$_REQUEST['forsite']?>&format=select&action=columnlist&tbl="+$("#editor select[name=datatable_table]").val();
							$("#editor select[name=datatable_cols]").load(l,function(data) {
									$("#editor select[name=datatable_orderby]").html(data);
									$("#editor select[name=attributes_itemImageCol]").html(data);
									$("#editor select[name=attributes_itemImageCol]").prepend("<option value=''>None</option>");
									$("#editor select[name=datatable_cols]").multiselect("destroy");
									$("#editor select[name=datatable_cols]").multiselect({
											minWidth:290,
											header:true,
											nonSelectedText:"Choose Columns",
										});
								});
						}
					}
				});
		});
	if(tables!=null && tables.length>0) {
		l=getServiceCMD("dbedit")+"&forsite=<?=$_REQUEST['forsite']?>&format=select&action=columnlist&tbl="+tables;
		$("#editor select[name=datatable_cols]").load(l,function(data) {
				if(cols!=null && cols.length>0) {
					$("#editor select[name=datatable_cols]").val(cols.split(","));
				}
				$("#editor select[name=datatable_orderby]").html(data);
				$("#editor select[name=attributes_itemImageCol]").html(data);
				
				$("#editor select[name=datatable_orderby]").val(orderBy);
				$("#editor select[name=attributes_itemImageCol]").val(imageCol);
				
				$("#editor select[name=datatable_cols]").multiselect({
						minWidth:290,
						header:true,
						nonSelectedText:"Choose Columns",
					});
			});
	} else {
		$("#editor select[name=datatable_cols]").html("");
		$("#editor select[name=datatable_orderby]").html("");
		$("#editor select[name=attributes_itemImageCol]").html("");
		
		$("#editor select[name=datatable_cols]").multiselect({
				minWidth:290,
				header:true,
				nonSelectedText:"Choose Columns",
			});
	}
}
</script>
<?php } ?>
