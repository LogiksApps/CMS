<?php
if(!defined('ROOT')) exit('No direct script access allowed');

if(!isset($_REQUEST["forsite"])) {
	dispErrMessage("HostSite Is Required But Not Found","CMS Error",400);
	exit();
}
user_admin_check(true);
checkUserSiteAccess($_REQUEST['forsite'],true);

loadModule("page");

$layout="apppage";
$params=array("toolbar"=>"printToolbar","contentarea"=>"printContent");

printPageContent($layout,$params);
?>
<style>
#toolbar select {
	margin-top:0px;
	width:250px;
	height:25px;
}
#toolbar button {
	width:130px;
}
#toolbar button div {
	width:100%;
	padding-left:10px;
}
#selectorData tr:hover {
	background:#F8FFE0;
	cursor:pointer;
}
#selectorData tr.blocked {
	background:#F2DFED;
}
a.toolbtns {
	cursor:pointer;
	color:#555;
	padding:5px;
	margin-left:7px;
	font:bold 14px Georgia;
}
a.toolbtns:hover {
	opacity:0.5;
	color:blue;
}
#legend {
	width:600px;height:23px;
	margin:auto;margin-top:40px;padding-top:7px;
}
#legend>div {
	width:150px;height:25px;
	display:inline;padding:4px;
}
.pointedto {
	background:#aaa;
}
</style>
<script>
gid="";
$(function() {
	$("select:not(multiple)").addClass("ui-state-active ui-corner-all");
	reloadSelectorList();
	
	$("#selectorData").delegate("tr","dblclick",function() {
			a1=$(this).find("th.title").text();
			b1=$(this).find("td.value").text();
			$("#infields input#a1").val(a1);
			$("#infields input#b1").val(b1);
		});
	
	$("#toolbtns a").click(function() {
			a=$(this).attr("class").replace("toolbtns","").trim();
			r=$("#selectorData input[type=checkbox]:checked");
			if(r.length>0) {
				s="";
				st="";
				r.each(function() {
						s+=$(this).attr("rel")+",";
						st+=$(this).attr("title")+", ";
					});
				s1="services/?scmd=blocks.selectors&forsite=<?=$_REQUEST["forsite"]?>&for="+s+"&action=";
				if(a=="delete") {
					lgksConfirm("Do you really want to delete these selectors ?<br/><div style='width:500px;height:100px;overflow:auto;border:1px solid #aaa;'>"+st+"</div>"+
								"<br/><br/><b>Please note: Deleting all items from a group will result in deleteting of group itself.</b>",
									"Delete Selectors ?",function() {
											s1+="delete";
											$.ajax({
												  url:s1,
												  complete: function(txt){
														loadListData();
												  }
												});
										});
				} else if(a=="block") {
					s1+="block";
					$.ajax({
						  url:s1,
						  complete: function(txt){
								loadListData();
						  }
						});					
				} else if(a=="unblock") {
					s1+="unblock";
					$.ajax({
						  url:s1,
						  complete:function(){
								loadListData();
						  },
						});
				} else if(a=="edit") {
					id=r.attr("rel");
					st=r.attr("title");
					sv=r.attr("v");
					
					lgksPrompt("Please give the new value for item <b>"+st+"</b>?","Edit Item",null,function(txt) {
								if(txt!=sv) {
									l="services/?scmd=blocks.selectors&forsite=<?=$_REQUEST["forsite"]?>&for="+s+"&action=edititem";
									qp="&id="+id+"&b1="+txt+"&gid="+gid;
									processAJAXPostQuery(l,qp,function(txt) {
											if(txt=="success") {
												$("#infields input#a1").val("");
												$("#infields input#b1").val("");
												s1="services/?scmd=blocks.selectors&forsite=<?=$_REQUEST["forsite"]?>&action=selectordata&format=table&gid="+gid;
												$("#selectorData").load(s1,function() {
															$("#infields input#a1").focus();
														});
											} else {
												lgksAlert("Failed To Update The Items Value.<br/>Please try again.");
											}
										});
								}
						});
				}
			}
		});
	$("#toolbtns #checkall").change(function() {
			b=this.checked;
			$('#selectorData input[type=checkbox]').each(function() {
					this.checked=b;
				});
		});
	$("#infields input").keydown(function(key) {
			if(key.keyCode==13) {
				if($(this).attr("id")=="a1") {
					$("#infields input#b1").val($(this).val());
					$("#infields input#b1").focus();
					$("#infields input#b1").select();
				} else if($(this).attr("id")=="b1") {
					insertItems();
				}
			}
		});
});
function reloadSelectorList(x) {
	closeList();
	s1="services/?scmd=blocks.selectors&forsite=<?=$_REQUEST["forsite"]?>&action=selectorlist";
	$("#selector").html("Loading ...");
	$("#loadingmsg").show();
	$("#selector").load(s1,function() {
			$("#selector").val(x);
			$("#loadingmsg").hide();
			loadListData();
		});
}
function loadListData() {
	gid=$('#selector').val();
	s1="services/?scmd=blocks.selectors&forsite=<?=$_REQUEST["forsite"]?>&action=selectordata&format=table&gid="+gid;
	$('#selectorData').html("<tr><td class=ajaxloading6>Loading List</td></tr>");
	$("#loadingmsg").show();
	$("#selectorData").load(s1,function(txt) {
			$("#infields").slideDown("slow");
			$("#toolbtns").slideDown("slow");
			$("#loadingmsg").hide();
		});
}
function closeList() {
	$('#selectorData').html("");
	$("#infields").slideUp("slow");
	$("#toolbtns").slideUp("slow");
}
function addNewList() {
	lgksPrompt("Please give name for the List/Selector. You will be able to request a list on this <br/>name."+
		" Should be a unique name for you to refer. >We will insert a blank record. <br/>Please delete it when you start entering data."+
		"<br/><br/><b>Please note: Deleting all items from a group will result in deleteting of group itself.</b>",
		"New List Name",null,function(txt) {
			if(txt.length>0) {
				s1="services/?scmd=blocks.selectors&forsite=<?=$_REQUEST["forsite"]?>&action=newlist&gid="+txt.split(" ").join("_");
				processAJAXQuery(s1,function(x) {
						reloadSelectorList(x);
					});
			}
		}).dialog("option","closeOnEscape","true");
}
function deleteList() {
	t=$('#selector').val();
	n=$('#selector option:selected').attr("cnt");
	lgksConfirm("Do you really want to delete a complete list of :: <b>"+t+"</b><br/>This will delete a complete set of <b>"+n+"</b> items in the list."+
			"<br/><br/><b>Please note: Deleting all items from a group will result in deleteting of group itself.</b>",
			"Delete Complete List ?",function() {
					s1="services/?scmd=blocks.selectors&forsite=<?=$_REQUEST["forsite"]?>&for="+t+"&action=deletelist";
					closeList();
					$("#loadingmsg").show();
					$.ajax({
						  url:s1,
						  complete: function(txt){
								reloadSelectorList();
						  }
						});
				});
}
function insertItems() {
	$("#selectorData tr.pointedto").removeClass("pointedto");
	a1=$("#infields input#a1").val();
	b1=$("#infields input#b1").val();
	if(a1.length>0) {
		rt=$("#selectorData th.title:contains('"+a1+"')");
		n1=rt.length;
		if(rt.length>0 && rt.text().indexOf(a1)<=0) {
			rt.parents("tr").addClass("pointedto");
			$("#infields input#a1").select();
			$("#infields input#a1").focus();
			return;
		}
		l="services/?scmd=blocks.selectors&forsite=<?=$_REQUEST["forsite"]?>&action=additem";
		q="&a1="+a1+"&b1="+b1+"&gid="+gid;
		processAJAXPostQuery(l,q,function(txt) {
				if(txt=="success") {
					$("#infields input#a1").val("");
					$("#infields input#b1").val("");
					s1="services/?scmd=blocks.selectors&forsite=<?=$_REQUEST["forsite"]?>&action=selectordata&format=table&gid="+gid;
					$("#selectorData").load(s1,function() {
								$("#infields input#a1").focus();
							});
				} else {
					lgksAlert("Failed To Insert The Items Into List.<br/>Please try again.");
				}
			});
	} else {
		$("#infields input#a1").focus();
		$("#infields input#a1").select();
	}
}
</script>
<?php
function printContent() { ?>
<p id=msgboxp style='font-size:15px; width:80%;margin:auto;margin-top:10px;'>
	Selector's are Lists,autocompletes,select html tags, etc. They can be real handy in forms and pages. Here You can create as many 
	lists/selectors as you want. Yes OfCourse, the item names have to be unique as user sees the name and same names will look lot confusing. 
	So <b>Unique Names</b> Please.
</p><br/>
<div style='width:600px;margin:auto;'>
	<div class='ui-widget-header ui-corner-top' style='width:100%;height:20px;margin:auto;font-size:15px;padding-top:5px;' align=center>
		<div style='width:50%;float:left;'>Title</div><div style='width:50%;float:right;'>Value</div>
	</div>
	<div id=infields class='ui-widget-content' style='width:100%;height:20px;margin:auto;padding:0px;font-size:15px;background:#aaa;display:none;' align=center>
		<input id=a1 type=text style='width:49%;height:100%;float:left;border:0px;' title='Name' />
		<input id=b1 type=text style='width:50%;height:100%;float:right;border:0px;' title='Value' />
	</div>
	<div class='ui-widget-content' style='width:100%;height:345px;margin:auto;overflow:auto;'>
		<table id=selectorData width=100% border=0 cellpadding=3px class='nostyle'></table>
	</div>
	<div id=toolbtns class='toolbtns ui-state-active ui-corner-bottom' style='width:100%;height:23px;margin:auto;padding-top:5px;display:none;' align=right>
		<div style='float:right'>
			<input id=checkall type=checkbox style='margin-right:7px;'/>
		</div>
		<div style='float:left'>
			<a class='toolbtns delete'>Delete</a><a class='toolbtns block'>Block</a><a class='toolbtns unblock'>Unblock</a><a class='toolbtns edit'>Edit</a>
		</div>
	</div>
</div>
<?php
} 
function printToolbar() { ?>
<button onclick="reloadSelectorList()" style='width:100px;' title='Reload List' ><div class='reloadicon'>Reload</div></button>
<select id=selector onchange='loadListData()'></select>
::
<button onclick="addNewList()" title="Create New Empty List" ><div class='addicon'>New List</div></button>
<button onclick="deleteList()" title="Delete Entire List" ><div class='deleteicon'>Delete List</div></button>
<?php } ?>
