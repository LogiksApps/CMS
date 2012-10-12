<?php
if(!defined('ROOT')) exit('No direct script access allowed');
checkServiceSession();
checkUserSiteAccess($_REQUEST['forsite'],true);
user_admin_check(true);

if(isset($_REQUEST["action"])) {
	loadModule("dbcon");
	loadFolderConfig();
	
	$lf=$_SESSION["APP_FOLDER"]["APPROOT"].$_SESSION["APP_FOLDER"]["APPS_TEMPLATE_FOLDER"];
	
	loadHelpers("files");
	
	if(!is_dir($lf)) {
		if(mkdir($lf,0777,true)) {
			chmod($lf,0777);
		}
	}
	if($_REQUEST["action"]=="viewtable") {
		$lfs=scandir($lf);
		unset($lfs[0]);unset($lfs[1]);
		foreach($lfs as $a) {
			$ext=strtolower(fileExtension($a));
			if($ext=="tpl") {
				$t=toTitle(substr($a,0,strlen($a)-4));
				echo "<tr rel='$a'><td align=center><input type=checkbox name=select rel='$a' /></td><td>$t</td></tr>";
			}
		}
		exit();
	} elseif($_REQUEST["action"]=="editurl" && isset($_REQUEST['tmpl'])) {
		$p="{$lf}{$_REQUEST['tmpl']}";
		$p=str_replace($_SESSION["APP_FOLDER"]["APPROOT"],"",$p);
		echo $p;
		exit();
	} elseif($_REQUEST["action"]=="fetch" && isset($_REQUEST['tmpl'])) {
		$tpl="{$lf}{$_REQUEST['tmpl']}";
		$pth=substr($tpl,0,strlen($tpl)-4);
		$sql=$pth.".sql";
		$css=$pth.".css";
		$js=$pth.".js";
		$data=array("path"=>$pth,"template"=>"","sql"=>"","css"=>"","js"=>"",);
		
		if(file_exists($tpl) && is_readable($tpl)) {
			$d=file_get_contents($tpl);
			$data['template']=$d;
		}
		if(file_exists($sql) && is_readable($sql)) {
			$d=file_get_contents($sql);
			$data['sql']=explode("\n",$d);
		}
		if(file_exists($css) && is_readable($css)) {
			$d=file_get_contents($css);
			$data['css']=$d;
		}
		if(file_exists($js) && is_readable($js)) {
			$d=file_get_contents($js);
			$data['js']=$d;
		}
		echo json_encode($data);
		exit();
	} elseif($_REQUEST["action"]=="save" && isset($_REQUEST['tmpl'])) {
		$tpl="{$lf}{$_REQUEST['tmpl']}";
		$pth=substr($tpl,0,strlen($tpl)-4);
		$sql=$pth.".sql";
		$css=$pth.".css";
		$js=$pth.".js";
		
		$msg=array();
		if(isset($_POST['template'])) {
			if(is_writable($tpl)) {
				$data=$_POST['template'];
				$data=cleanText($data);
				file_put_contents($tpl,$data);
			} else {
				array_push($msg,"Template File Is ReadOnly");
			}
		}
		if(isset($_POST['sql'])) {
			if(is_writable($sql)) {
				$data=$_POST['sql'];
				$data=cleanText($data);
				file_put_contents($sql,$data);
			} else {
				array_push($msg,"Template SQL Query File Is ReadOnly");
			}
		}
		if(isset($_POST['css'])) {
			if(is_writable($css)) {
				$data=$_POST['css'];
				$data=cleanText($data);
				file_put_contents($css,$data);
			} else {
				array_push($msg,"Template CSS File Is ReadOnly");
			}
		}
		if(isset($_POST['js'])) {
			if(is_writable($js)) {
				$data=$_POST['js'];
				$data=cleanText($data);
				file_put_contents($js,$data);
			} else {
				array_push($msg,"Template JS File Is ReadOnly");
			}
		}
		if(count($msg)>0) {
			echo "Error Occured During Saving Of Template <b>{$_REQUEST['tmpl']}</b><br/>";
			echo implode("<br/>",$msg);
		}
		exit();
	} elseif($_REQUEST["action"]=="create" && isset($_REQUEST['tmpl'])) {
		$tpl="{$lf}{$_REQUEST['tmpl']}";
		$pth=substr($tpl,0,strlen($tpl)-4);
		$sql=$pth.".sql";
		$css=$pth.".css";
		$js=$pth.".js";
		
		if(file_exists($tpl)) {
			exit("Template With Name <b>{$_REQUEST['tmpl']}</b> already exists.<br/>Choose some other name.");
		}
		
		file_put_contents($tpl,"");
		file_put_contents($sql,"");
		
		if(file_exists($tpl)) {
			chmod($tpl,0777);
			chmod($sql,0777);
		} else {
			echo "Error Creating Blank Template. Template Folder May Be ReadOnly";
		}
		exit();
	} elseif($_REQUEST["action"]=="delete" && isset($_REQUEST['tmpl'])) {
		$tmpl=explode(",",$_REQUEST['tmpl']);
		foreach($tmpl as $a) {
			$tpl=$lf.$a;
			$pth=substr($tpl,0,strlen($tpl)-4);
			$sql=$pth.".sql";
			$css=$pth.".css";
			$js=$pth.".js";
			if(file_exists($tpl)) unlink($tpl);
			if(file_exists($sql)) unlink($sql);
			if(file_exists($css)) unlink($css);
			if(file_exists($js)) unlink($js);
		}
		exit();
	} elseif($_REQUEST["action"]=="clone" && isset($_REQUEST['tmpl'])) {
		$tmpl=explode(",",$_REQUEST['tmpl']);
		foreach($tmpl as $a) {
			$tpl=$lf.$a;
			$pth=substr($tpl,0,strlen($tpl)-4);
			$sql=$pth.".sql";
			$css=$pth.".css";
			$js=$pth.".js";
			
			$ntpl=$lf."copy_".basename($tpl);
			$nsql=$lf."copy_".basename($sql);
			$ncss=$lf."copy_".basename($css);
			$njs=$lf."copy_".basename($js);
			
			if(file_exists($tpl)) { copy($tpl,$ntpl); chmod($ntpl,0777); }
			if(file_exists($sql)) { copy($sql,$nsql); chmod($nsql,0777); }
			if(file_exists($css)) { copy($css,$ncss); chmod($ncss,0777); }
			if(file_exists($js)) { copy($js,$njs); chmod($njs,0777); }
		}
		exit();
	} elseif($_REQUEST["action"]=="rename" && isset($_REQUEST['tmpl']) && isset($_REQUEST['totmpl'])) {
		$tpl=$lf.$_REQUEST['tmpl'];
		$pth=substr($tpl,0,strlen($tpl)-4);
		$sql=$pth.".sql";
		$css=$pth.".css";
		$js=$pth.".js";
		
		$ntpl=$lf.$_REQUEST['totmpl'];
		$pth=substr($ntpl,0,strlen($ntpl)-4);
		$nsql=$pth.".sql";
		$ncss=$pth.".css";
		$njs=$pth.".js";
		
		if(file_exists($tpl)) { copy($tpl,$ntpl); chmod($ntpl,0777); unlink($tpl); }
		if(file_exists($sql)) { copy($sql,$nsql); chmod($nsql,0777); unlink($sql); }
		if(file_exists($css)) { copy($css,$ncss); chmod($ncss,0777); unlink($css); }
		if(file_exists($js)) { copy($js,$njs); chmod($njs,0777); unlink($js); }
		
		exit();
	}
}

function printPageInfo($a,$lf,$sys=false,$btns=null) {
	if(is_dir($lf.$a)) return;
	
	$t=$a;
	$ext=fileExtension($a);
	$nm=str_replace(".{$ext}","",$a);
	
	$clz1="";
	$type="";
	$col1="<td align=center width=35px><input type=checkbox name=select rel='$a' /></td>";
	
	$pgName=str_replace(".{$ext}","",$a);
	
	$s="<tr rel='$a' class='$clz1' 
		title='$pgName'
		size='".getFileSizeInString(filesize($lf.$a))."' 
		created='".date(getConfig("TIMESTAMP_FORMAT"),filectime($lf.$a))."' 
		modified='".date(getConfig("TIMESTAMP_FORMAT"),filemtime($lf.$a))."' 
		accessed='".date(getConfig("TIMESTAMP_FORMAT"),fileatime($lf.$a))."' 
		>";
	$s.=$col1;
	$s.="<td>".toTitle($pgName)."</td>";
	$s.="</tr>";
	echo $s;
}
?>
