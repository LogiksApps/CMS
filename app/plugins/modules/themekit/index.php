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
	$themeFolder=ROOT.THEME_FOLDER;
	$themeSpec="";
	$subskinSpec="";
	
	$uiconf=parseConfigFile(ROOT.APPS_FOLDER.$_REQUEST['forsite']."/config/uiconf.cfg");
	if(isset($uiconf['THEME_SPECS']['value']))
		$themeSpec=strtolower($uiconf['THEME_SPECS']['value']);
	if(isset($uiconf['SUBSKIN_SPECS']['value']))
		$subskinSpec=strtolower($uiconf['SUBSKIN_SPECS']['value']);
	
	_js("jquery.lightbox");
	_css("jquery.lightbox");
?>
<style>
<?php
include "style.css";
?>
#pgworkspace {
	overflow-x:hidden;
}
</style>
<div id=themePage class=tabs style='width:100%;height:100%;overflow:none;'>
	<ul>
		<li><a href='#a'>Installed Themes</a></li>
	</ul>
	<div id=a class='localThemePage'>
	<?php
		if(strlen($themeSpec)<=0 || $themeSpec=="custom") {
			echo "<br/><br/><br/><h3 class='clr_pink' style='width:400px;margin:auto;padding:10px;'>This AppSite uses Open Theme Engine.<br/>So No Matching Theme Found</h3>";
		} else {
			$themes=scandir($themeFolder);
			unset($themes[0]);unset($themes[1]);
			foreach($themes as $n=>$t) {
				$ft=$themeFolder.$t."/";
				$th="";
				if(file_exists($ft.$themeSpec.".thm")) {
					$th=$ft.$themeSpec.".thm";
				} elseif(file_exists($ft.strtoupper($themeSpec.".thm"))) {
					$th=$ft.strtoupper($themeSpec.".thm");
				}
				if(strlen($th)>0) {
					$themes[$t]=$th;
				}
				unset($themes[$n]);
			}
			$subThemePath=ROOT.SKINS_FOLDER.$subskinSpec."/";
			if($subskinSpec=="jquery") $subskinSpec="jquery.ui";
			$jqThemes="";			
			$jqThemeArr=scandir($subThemePath);
			unset($jqThemeArr[0]);unset($jqThemeArr[1]);
			//$jqThemes.="<option value=''>Theme Skin</option>";
			foreach($jqThemeArr as $n=>$t) {
				if(is_dir($subThemePath.$t)) continue;
				if($t=="{$subskinSpec}.css") continue;
				$t=str_replace(".css","",$t);
				$t=str_replace("{$subskinSpec}.","",$t);
				$nm=ucwords($t);
				if($t==$uiconf['APPS_SUBSKIN']['value']) {
					$jqThemes.="<option value='$t' selected>$nm</option>";
				} else {
					$jqThemes.="<option value='$t'>$nm</option>";
				}
			}
			if(count($themes)<=0) {
				echo "<br/><br/><br/><h3 class='clr_pink' style='width:400px;margin:auto;padding:10px;'>No Matching Themes Installed.</h3>";
			} else {
				foreach($themes as $theme=>$thm) {
					$screenshot=dirname($thm)."/screenshot.png";
					if(file_exists($screenshot)) {
						$screenshot=substr($screenshot,strlen(ROOT));
					} else {
						$screenshot="media/images/nopic.jpg";
					}
					
					$data=file_get_contents($thm);
					$data=explode("\n",$data);
					foreach($data as $a=>$b) {
						if(strlen($b)>0) {
							$b=explode(":",$b);
							$data[strtolower($b[0])]=$b[1];
						}
						unset($data[$a]);
					}
					
					if(!isset($data['author'])) $data['author']="";
					if(!isset($data['about theme'])) $data['about theme']="";
					if(!isset($data['date'])) $data['date']="";
					if(!isset($data['jqtheme'])) $data['jqtheme']="";
					
					$html="";
					if($uiconf['APPS_THEME']['value']==$theme) {
						$html.="<div id='theme_{$theme}' class='theme ui-corner-all active' theme='{$theme}' jqtheme='{$data['jqtheme']}'>";
					} else {
						$html.="<div id='theme_{$theme}' class='theme ui-corner-all' theme='{$theme}' subtheme='{$data['jqtheme']}'>";
					}
					$html.="<a href='{$screenshot}' title='".toTitle($theme)." Theme'><img src='{$screenshot}' width=200px height=150px alt='' /></a>";
					
					$html.="<div class='buttons'>";
					$html.="<div id=useTheme class='buttonDiv clr_darkmaroon ui-corner-all'><div class='okicon' style='width:80%;'>Use Theme</div></div>";
					$html.="<select id=jqthemeSelector class='clr_darkblue ui-corner-all'>{$jqThemes}</select>";
					$html.="</div>";
					
					$html.="<h2 title='Double Click To Use Theme'>".toTitle($theme)." Theme</h2>";
					$html.="<p><b>Author</b> : {$data['author']}</p>";
					$html.="<p><b>Description</b> : {$data['about theme']}</p>";
					$html.="<p><b>Published</b> : {$data['date']}</p>";
					
					unset($data['author']);unset($data['about theme']);unset($data['date']);
					$data['specs']=implode(",",$data);
					$html.="<p><b>Specifications</b> : {$data['specs']}</p>";
					
					$html.="</div>";
					echo $html;
				}
			}
		}
	?>
	</div>
</div>
<script language=javascript>
$(function() {
	$("#themePage .theme a").lightBox({
			fixedNavigation:true,
			txtImage:"Theme",
		});
	$("#themePage").delegate(".theme img","click",function() {
			src=$(this).attr('src');
			//viewScreenshot(src);
		});
	$("#themePage").delegate("#useTheme","click",function() {
			themeDiv=$(this).parents("div.theme");
			theme=themeDiv.attr("theme");
			subtheme=themeDiv.attr("subtheme");
			UseTheme(theme,subtheme);
		});
	$("#themePage").delegate(".theme h2","dblclick",function() {
			themeDiv=$(this).parents("div.theme");
			theme=themeDiv.attr("theme");
			subtheme=themeDiv.attr("subtheme");
			UseTheme(theme,subtheme);
		});
	$("#themePage").delegate("#jqthemeSelector","change",function() {
			UpdateSubTheme(this.value);
		});
});
function viewScreenshot(ref) {
	lgksOverlayFrame(ref,"Theme ScreenShot");
}
function UseTheme(theme,jqtheme) {
	l="services/?scmd=cfgedit&site=<?=SITENAME?>&forsite=<?=$_REQUEST['forsite']?>&cfgfile=uiconf&action=save";
	q="APPS_THEME="+theme+"&APPS_SUBSKIN="+jqtheme;
	processAJAXPostQuery(l,q,function(data) {
			$(".localThemePage .theme.active").removeClass("active");
			$(".localThemePage .theme#theme_"+theme).addClass("active");
			$(".localThemePage .theme#theme_"+theme).find("select#jqthemeSelector").val(jqtheme);
			if(data.length>0) {
				data=data+"<br/><br/><br/><h4 align=center class='clr_green ui-corner-all' style='width:150px;padding:5px;margin:auto;'><a href='index.php?site=<?=$_REQUEST['forsite']?>' target=_blank>PREVIEW AppSite</a></h4><br/>"
				lgksAlert(data);
			}
		});
}
function UpdateSubTheme(jqtheme) {
	l="services/?scmd=cfgedit&site=<?=SITENAME?>&forsite=<?=$_REQUEST['forsite']?>&cfgfile=uiconf&action=save";
	q="APPS_SUBSKIN="+jqtheme;
	processAJAXPostQuery(l,q,function(data) {
			if(data.length>0) {
				data=data+"<br/><br/><br/><h4 align=center class='clr_green ui-corner-all' style='width:150px;padding:5px;margin:auto;'><a href='index.php?site=<?=$_REQUEST['forsite']?>' target=_blank>PREVIEW AppSite</a></h4><br/>"
				lgksAlert(data);
			}
		});
}
</script>
<?php
}
?>
