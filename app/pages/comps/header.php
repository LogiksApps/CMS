<?php
if(!defined('ROOT')) exit('No direct script access allowed');

_js(array("jquery.ghosttext","jquery.extras"));

loadModule("accessibility");
$forSite="*";
if(isset($_REQUEST["forsite"])) {
	$forSite=$_REQUEST["forsite"];
}
//updateSideBar()
?>
<div class='right'>
	<?php 
		loadAccessibilityButtons('#sidebar a');
	?>

	<a class='button clr_darkmaroon profile' onclick="showProfileEditor();">
		<img src='<?=loadMedia("icons/user.png")?>' width=22px height=22px />
	</a>
	<a class='button clr_darkblue settings' onclick="showSettingsEditor();">
		<img src='<?=loadMedia("icons/config.png")?>' width=22px height=22px />
	</a>
	<?php
		if(isset($_SESSION['SESS_USER_NAME'])) 
			echo "Hello, <a title='Edit Profile' onclick='showProfileEditor();'>".$_SESSION['SESS_USER_NAME']."</a> | <a  href='".SiteLocation."logout.php' title='Logout Session'>Logout</a>"; 
		else echo "Welcome To <a>CMS Demo</a> : <a>Login</a>";
	?>
</div>
<div class='left' style='width:520px;'>
	<div id=seachform class='search placeholder' style="float:left;margin-left:-10px;">
		<input id=searchtxt type=text class='text ghosttext' title='Looking For Something' style='font-weight:bold' autocomplete="off" />
		<div class=searchbtn></div>
	</div>
	<div style="margin-top:-2px;float:left;">
		<select id=sitemanager title='Switch Between Available Sites' onchange="gotoSiteCMS($(this).val())" style="width:200px;height:25px;margin-right:10px;margin-top:10px;">
			<?php
				if($_SESSION['SESS_PRIVILEGE_NAME']=="root") {
					$arr=scandir(ROOT.APPS_FOLDER);
					foreach($arr as $b) {
						if(file_exists(ROOT.APPS_FOLDER.$b."/apps.cfg") && file_exists(ROOT.APPS_FOLDER.$b."/cms.php")) {
							$t=ucwords($b);
							if($b==$forSite) echo "<option value='$b' selected>$t Site</option>";
							else echo "<option value='$b'>$t Site</option>";
						}
					}
				} else {
					$arr=$_SESSION['SESS_ACCESS_SITES'];
					foreach($arr as $a=>$b) {
						if(file_exists(ROOT.APPS_FOLDER.$b."/apps.cfg") && file_exists(ROOT.APPS_FOLDER.$b."/cms.php")) {
							$t=ucwords($b);
							if($b==$forSite) echo "<option value='$b' selected>$t Site</option>";
							else echo "<option value='$b'>$t Site</option>";
						}
					}
				}
			?>
		</select>		
	</div>
	<a style='' href='#' onclick="window.open('<?=SiteLocation?>'+$('#sitemanager').val())">Preview</a>
</div>
<?php
if(strlen(getConfig("CMS_TITLEBAR_TEXT"))>0) {
	echo "<div class='center' style='margin-left:300px;'>".getConfig("CMS_TITLEBAR_TEXT")."</div>";
}
?>
<div id=header_search_selector_div style='display:none;' title='Search Menu'><b>Search :: </b>
	<select id=header_search_selector style='width:200px;height:26px;font-weight:bold;' class='ui-widget-header'></select>
</div>
<script language=javascript>
$(function() {
	$(".ghosttext").ghosttext();
	
	$("#searchtxt").keypress(function (event) {
		if(event.which==13) {
			var s=$("#searchtxt").val();
			$("#searchtxt").val("");
			a="";
			$("#sidebar").find("a:containsText('"+s+"')").each(function() {
					var r=$(this).attr("href");
					var t=$(this).html();
					a+="<option value='"+r+"' title='"+t+"'>"+t+"</option>";
				});
			$("#header_search_selector").html(a);
			if($("#header_search_selector option").length>0) {
				$("#header_search_selector_div").dialog({
							width:300,height:150,
							modal:true,stack:true,
							show:"slide",hide:"slide",
							resizable:false,draggable:true,closeOnEscape:true,
							dialogClass:'alert',
							buttons: {
								Open:function() {
									r=$("#header_search_selector").val();
									t=$("#header_search_selector option:selected").attr("title");
									if(r.length>0) {
										openInNewTab(t,"index.php?"+r);
										$(this).dialog( "close" );
									}
								},
								Close: function() {
									$(this).dialog( "close" );
								},
							},
							close: function(event, ui) {
								//event.preventDefault();				
							},
						});
			} else {
				lgksAlert("No Search Item Found Matching Your Query.");
			}
		}
	});
});
</script>
