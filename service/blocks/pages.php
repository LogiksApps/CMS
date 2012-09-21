<?php
if (!defined('ROOT')) exit('No direct script access allowed');
checkServiceSession();
isAdminSite();

if(!$_REQUEST["forsite"]) {
	printErr("WrongFormat","Command Missing Argument");
	exit();
}
checkUserSiteAccess($_REQUEST['forsite'],true);

if(isset($_REQUEST["action"])) {
	loadModule("dbcon");loadFolderConfig();getDBControls();
	$lf=$_SESSION["APP_FOLDER"]["APPROOT"].$_SESSION["APP_FOLDER"]["APPS_PAGES_FOLDER"];
	
	loadHelpers("files");
	$sysPf=$_SESSION["APP_FOLDER"]["APPROOT"].$_SESSION["APP_FOLDER"]["APPS_CONFIG_FOLDER"]."lists/syspages.lst";
	$layoutDir=$_SESSION["APP_FOLDER"]["APPROOT"].$_SESSION["APP_FOLDER"]["APPS_CONFIG_FOLDER"]."layouts/";
	
	$sysPages=array();
	$autoPages=array();
	
	if(file_exists($sysPf)) {
		$spfData=file_get_contents($sysPf);
		$sysPages=explode("\n",$spfData);
	}
	if(!is_dir($layoutDir)) {
		if(mkdir($layoutDir,0777,true)) {
			chmod($layoutDir,0777);
		}
	}
	
	if($_REQUEST["action"]=="viewtable") {
		$cnt=0;
		$showSysPages=false;
		if(isset($_REQUEST["showSysPages"])) {
			$showSysPages=($_REQUEST["showSysPages"]=="true")?true:false;
		}
		if(is_dir($lf)) {
			$fs=scandir($lf);
			unset($fs[0]);unset($fs[1]);
			$cnt=1;
			$fs=array_flip($fs);
			//printArray($fs);
			if(count($sysPages)>0) {
				if($showSysPages) {
					echo "<tr class='subheader clr_darkmaroon'><th colspan=10 style='padding-left:10px;'>System Pages</th></tr>";
				}
				foreach($sysPages as $n=>$a) {
					if(strlen($a)>0 && array_key_exists($a,$fs)) {
						if($showSysPages) {
							$cnt++;
							printPageInfo($a,$lf,true);
						}
						unset($fs[$a]);
					}
				}
			}
			echo "<tr class='subheader clr_darkblue'><th colspan=10 style='padding-left:10px;'>All Pages</th></tr>";
			if(count($fs)>0) {
				foreach($fs as $a=>$b) {
					$cnt++;
					printPageInfo($a,$lf,false);
				}
			}
		} else {
			echo "<tr class='subheader clr_darkblue'><th colspan=10 style='padding-left:10px;'>All Pages</th></tr>";
		}
		if(is_dir($layoutDir)) {
			$arrFS=scandir($layoutDir);
			unset($arrFS[0]);unset($arrFS[1]);
			foreach($arrFS as $a) {
				if(strpos(strtolower($a),".json")==strlen($a)-5) {
					$autoPages[$a]=$layoutDir.$a;
				}
			}
			if(count($autoPages)>0) {
				//echo "<tr class='subheader clr_green'><th colspan=10 style='padding-left:10px;'>Virtual Pages</th></tr>";
				foreach($autoPages as $a=>$b) {
					$cnt++;
					printPageInfo($a,$layoutDir,false);
				}
			}
		}
		
		if($cnt<=0) {
			echo "<tr><th colspan=20><h3>Error Finding Pages Folder</h3></th></tr>";
		}
		exit();
	} elseif($_REQUEST["action"]=="linkstome" && isset($_REQUEST["forpage"])) {
		$pg=explode(".",$_REQUEST['forpage']);
		$pg=$pg[0];
		$sql="SELECT id,title,menugroup,link,tips,blocked,privilege FROM "._dbTable("links")." WHERE link LIKE '%page={$pg}%' AND (site='{$_REQUEST['forsite']}' OR site='*') ORDER BY id";
		$r=_dbQuery($sql);
		echo "<table width=100% border=0 cellspacing=0>";
		if($r) {
			$data=_dbData($r);
			_db()->freeResult($r);
			
			if(count($data)>0) {
				$s="";
				$s.="<tr class='ui-widget-header'><th>Title</th><th>Group</th><th>Tips</th><th>Status</th><th>Privileges</th></tr>";
				foreach($data as $row) {
					if($row['blocked']=="true" || $row['blocked']) $row['blocked']="<input type=checkbox rel='' class='blockLink' disabled=disabled checked=true />";
					else $row['blocked']="<input type=checkbox rel='' class='blockLink' disabled=disabled checked=false />";
					if($row['privilege']=="*") $row['privilege']="All";
					
					$s.="<tr rel='{$row['id']}'>";
					$s.="<td>{$row['title']}</td>";
					$s.="<td></td>";//{$row['menugroup']}
					$s.="<td>{$row['tips']}</td>";
					$s.="<td width=40px align=center>{$row['blocked']}</td>";
					$s.="<td width=40px align=center>{$row['privilege']}</td>";
					$s.="</tr>";
				}
				echo $s;
			} else {
				echo "<tr><td colspan=20><h3>No Links To This Page Found</h3></td></tr>";
			}
		} else {
			echo "<tr><td colspan=20><h3>No Links To This Page Found</h3></td></tr>";
		}
		echo "</table>";
		//echo "<br/><br/><div align=center><a class='clr_green' style='padding:5px;' href='#' onclick=\"createNewLink('{$pg}')\"><b>Create New Link</b></a></div>";
		exit();
	} elseif($_REQUEST["action"]=="fetch" && isset($_REQUEST["forpage"])) {
		$pg="$lf{$_REQUEST["forpage"]}";
		readfile($pg);
		exit();
	} elseif($_REQUEST["action"]=="save" && isset($_POST['data'])) {
		if(in_array($_REQUEST["forpage"],$sysPages)) {
			exit("Can Not Save A System File");
		}
		$pg="$lf{$_REQUEST["forpage"]}";
		if(is_writable($pg)) {
			$data=$_POST["data"];
			$data=cleanText($data);
			file_put_contents($pg,$data);
		} else {
			echo "Sorry, Source File Is ReadOnly.";
		}
		exit();
	} 
	elseif($_REQUEST["action"]=="fetchlayout" && isset($_REQUEST["forpage"])) {
		$layoutFile="{$layoutDir}{$_REQUEST["forpage"]}.json";
		$json=file_get_contents($layoutFile);
		$json=json_decode($json,true);
		if($json==null) {
			exit("Error Loading Layout Configurations");
		}
		
		if(!isset($json['css'])) $json['css']="";
		if(!isset($json['js'])) $json['js']="";
		if(!isset($json['modules'])) $json['modules']="";
		if(!isset($json['enabled'])) $json['enabled']="true";
		
		if(!isset($json['template'])) $json['template']="";
		if(!isset($json['layout'])) $json['layout']="";
		
		echo json_encode($json);
		
		exit();
	} elseif($_REQUEST["action"]=="savelayout" && isset($_REQUEST["forpage"])) {
		$layoutFile="{$layoutDir}{$_REQUEST["forpage"]}.json";
		
		if(isset($_POST['enabled'])) {
			$_POST['enabled']=($_POST['enabled']=="true")?true:false;
		}
		if(isset($_POST['layout'])) {
			foreach($_POST['layout'] as $a=>$b) {
				$_POST['layout'][$a]['enable']=($b['enable']=="true")?true:false;
			}
		}
		$data=json_encode($_POST);
		$data=str_replace("\/","/",$data);
		file_put_contents($layoutFile,$data);
		exit();
	} elseif($_REQUEST["action"]=="rename" && isset($_POST['pg']) && isset($_POST['topg'])) { 
		if(in_array($_POST['pg'],$sysPages)) {
			exit("Can Not Rename A System File");
		}
		
		$ext=explode(".",$_POST['topg']);
		$rename=false;
		$fname=$_POST['topg'];
		if(count($ext)<=1) {
			$ext=explode(".",$_POST['pg']);
			$ext=$ext[count($ext)-1];
			$fname=$_POST['topg'].".{$ext}";
		}
		$fs=$lf;
		if(strtoupper($_POST['ext'])=="JSON") {
			$fs=$layoutDir;
		}
		if(!rename($fs.$_POST['pg'],$fs.$fname)) {
			echo "Failed To Rename Page";
		} else {
			$rename=true;
		}
		//ToDo :: Rename Should Clear/Rename Meta Files
		/*$fname1=strstr($_POST['pg'],".",true);
		$fname2=strstr($fname,".",true);
		if($rename) {
			$metaFile=$_SESSION["APP_FOLDER"]["APPROOT"].$_SESSION["APP_FOLDER"]["APPS_CONFIG_FOLDER"]."meta/{$fname1}.txt";
			if(file_exists($metaFile)) {
				$metaFile1=$_SESSION["APP_FOLDER"]["APPROOT"].$_SESSION["APP_FOLDER"]["APPS_CONFIG_FOLDER"]."meta/{$fname2}.txt";
				if(!rename($metaFile,$metaFile1)) {
					echo "Failed To Rename MetaFile";
				}
			}
		}*/
		exit();
	} elseif($_REQUEST["action"]=="clone" && isset($_POST['toclone'])) {
		$fss=explode(",",$_POST['toclone']);
		$fail=array();
		foreach($fss as $n=>$a) {
			if(strlen($a)>0) {
				$ext=fileExtension($a);
				$nm=fileName($a);
				$fs=$lf;
				if(strtoupper($ext)=="JSON") {
					$fs=$layoutDir;
				}
				$pg="{$fs}{$a}";
				$pg1="{$fs}{$nm}_copy.{$ext}";
				
				if(in_array($a,$sysPages)) {
					$fail[$a]="Can Not Clone System File";
				} elseif(file_exists($pg1)) {
					$fail[$a]="Target Exists";
				} elseif(!is_readable($pg)) {
					$fail[$a]="Source UnReadable";
				} else {
					if(copy($pg,$pg1)) {
						chmod($pg1,0666);
						unset($fss[$n]);
					} else {
						$fail[$a]="Failed To Copy";
					}
				}
			} else {
				unset($fss[$n]);
			}			
		}
		if(count($fss)>0) {
			$s="<h3>Failed To Clone</h3>";
			$s.="<table width=300px border=0 cellspacing=0>";
			foreach($fail as $a=>$b) {
				$s.="<tr><th align=left width=100px>$a</th><td>$b</td></tr>";
			}
			$s.="</table>";			
			echo $s;
		}
		exit();
	} elseif($_REQUEST["action"]=="delete" && isset($_POST['todelete'])) {
		$fss=explode(",",$_POST['todelete']);
		$fail=array();
		foreach($fss as $n=>$a) {
			if(strlen($a)>0) {
				$fs=$lf;
				$ext=strtoupper(fileExtension($a));
				if($ext=="JSON") {
					$fs=$layoutDir;
				}
				$pg="{$fs}{$a}";
				if(file_exists($pg)) {
					if(in_array($a,$sysPages)) {
						$fail[$a]="Can Not Delete System File";
					} elseif(!file_exists($pg)) {
						$fail[$a]="Source Does Not Exists";
					} elseif(!is_readable($pg) || !is_writable($pg)) {
						$fail[$a]="Source Is Write Protected";
					} else {
						if(unlink($pg)) {
							unset($fss[$n]);
						} else {
							$fail[$a]="Failed To Delete";
						}
					}
				} else {
					$fail[$a]="Failed To Delete. Not Found";
				}
			} else {
				unset($fss[$n]);
			}			
		}
		if(count($fss)>0) {
			$s="<h3>Failed To Delete</h3>";
			$s.="<table width=300px border=0 cellspacing=0>";
			foreach($fail as  $a=>$b) {
				$s.="<tr><th align=left width=100px>$a</th><td>$b</td></tr>";
			}
			$s.="</table>";			
			echo $s;
		}
		exit();
	} elseif($_REQUEST["action"]=="create" && isset($_POST['nm'])) {
		$nm=$_POST['nm'];
		$tmpl=$_POST['tmpl'];
		$pg="{$lf}{$nm}";
		
		if($tmpl=="blank") {
			$a=file_put_contents($pg,"");
			if($a!==false) {
				chmod($pg,0666);
			} else {
				echo "Error Creating Page. ReadOnly Set.";
			}
		} else if($tmpl=="generated") {
			$pg="{$layoutDir}{$nm}.json";
			$pg=str_replace(".json.json",".json",$pg);
			$d=getBlankLayout("json");
			$a=file_put_contents($pg,$d);
			if($a!==false) {
				chmod($pg,0666);
			} else {
				echo "Error Creating Page. ReadOnly Set.";
			}
		} else {
			if(strlen($tmpl)>0) {
				$tmpl=APPROOT.CMS_PAGE_TEMPLATES.$tmpl;
				if(is_file($tmpl)) {
					$ext=fileExtension($tmpl);
					$d=file_get_contents($tmpl);
					$a=file_put_contents("{$pg}.{$ext}",$d);
					if($a!==false) {
						chmod("{$pg}.{$ext}",0666);
					} else {
						echo "Error Creating Page. ReadOnly Set.";
					}
				} else {
					echo "No Template Page Found";
				}
			} else {
				echo "Template Missing";
			}
		}		
		exit();
	} elseif($_REQUEST["action"]=="editor") {
		$editor=$_REQUEST['type'];
		
		$p="{$lf}{$_REQUEST['editpage']}";
		$p=str_replace($_SESSION["APP_FOLDER"]["APPROOT"],"",$p);
		
		$url="";
		if($editor=="codeeditor") {
			$url="../index.php?&site=cms&forsite={$_REQUEST['forsite']}&page=codeeditor&file=$p";
			echo $url;
		} elseif($editor=="wysiwyg") {
			$url="../index.php?&site=cms&forsite={$_REQUEST['forsite']}&page=wysiwygedit&file=$p";
			echo $url;
		} elseif($editor=="pagebuilder") {
			$url="../index.php?&site=cms&forsite={$_REQUEST['forsite']}&page=modules&mod=$editor&file=$p";
		} else {
			printErr("NotAcceptable","Requested Editor Is Not Acceptable");			
			$url="";
		}		
		if(strlen($url)>0) {
			header("Location:$url");
		}		
		exit();
	} elseif($_REQUEST["action"]=="upload") {
		$msg="";
		if(count($_FILES)>0) {
			foreach($_FILES as $a=>$b){
				$fname=$b['name'];
				$tmpPath=$b['tmp_name'];
				$ext=strstr($fname,".");
				$ext=strtolower(str_replace(".","",$ext));
				if($ext=="php" || $ext=="htm" || $ext=="html" ||
					$ext=="xhtml" || $ext=="tpl") {
					@move_uploaded_file($tmpPath,$lf.$fname);
					chmod($lf.$fname,0777);
				} elseif($ext=="zip") {
					loadHelpers("zipper");
					unzipFile($tmpPath,$lf);
				} elseif($ext=="json") {
					@move_uploaded_file($tmpPath,$layoutDir.$fname);
					chmod($layoutDir.$fname,0777);
				} else {
					$msg="File Type Not Supported";
				}
			}
		}
		exit("<script>parent.uploadComplete('$msg');</script>");
	} elseif($_REQUEST["action"]=="download") {
		$p="{$lf}{$_REQUEST['forpage']}";
		$ext=strtoupper(fileExtension($p));
		if($ext=="JSON") {
			$p="{$layoutDir}{$_REQUEST['forpage']}";
		}
		$filename=$_REQUEST['forpage'];
		$mime="text/".strtolower($ext);
		if(strpos($_SERVER['HTTP_USER_AGENT'], "MSIE") !== FALSE) {
			header("Content-type: $mime");
			header("Content-Disposition: attachment; filename=$filename");
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Content-Transfer-Encoding: binary");
			header('Pragma: public');
			//header("Content-Length: ".strlen($data));
		} else {
			header("Content-type: $mime");
			header("Content-Disposition: attachment; filename=$filename");
			header("Content-Transfer-Encoding: binary");
			header("Expires: 0");
			header('Pragma: no-cache');
			//header("Content-Length: ".strlen($data));
		}
		if(file_exists($p)) {
			readfile($p);
		}
		exit();
	} elseif($_REQUEST["action"]=="editorlist") {
		$s="<option value='codeeditor'>Code Editor (Development)</option>
			<option value='wysiwyg'>WYSIWYG Editor (HTML)</option>
			<option value='pagebuilder'>Page Designer</option>";
		exit($s);
	} elseif($_REQUEST["action"]=="componentlist") {
		$s="";
		$cmps=$lf."comps/";
		if(is_dir($cmps)) {
			$cmps=scandir($cmps);
			unset($cmps[0]);unset($cmps[1]);
			$s.="<optgroup label='Components'>";
			foreach($cmps as $a) {
				$s.="<option value='comps/$a'>".basename($a)."</option>";
			}
			$s.="</optgroup>";
		}
		$tmpls=$_SESSION["APP_FOLDER"]["APPROOT"].$_SESSION["APP_FOLDER"]["APPS_TEMPLATE_FOLDER"];
		if(is_dir($tmpls)) {
			$tmpls=scandir($tmpls);
			unset($tmpls[0]);unset($tmpls[1]);
			$s.="<optgroup label='Templates'>";
			foreach($tmpls as $a) {
				if(substr($a,strlen($a)-4)==".tpl") {
					$s.="<option value='$a'>".basename($a)."</option>";
				}
			}
			$s.="</optgroup>";
		}
		exit($s);
	} elseif($_REQUEST["action"]=="templatelist") {
		$s="";
		
		$tmpls=ROOT.TEMPLATE_LAYOUT_FOLDER;
		if(is_dir($tmpls)) {
			$tmpls=scandir($tmpls);
			unset($tmpls[0]);unset($tmpls[1]);
			foreach($tmpls as $a) {
				if(substr($a,strlen($a)-4)==".tpl") {
					$a=substr(basename($a),0,strlen($a)-4);
					$s.="<option value='$a'>".toTitle($a)."</option>";
				}
			}
		}
		exit($s);
	} elseif($_REQUEST["action"]=="editurl" && isset($_REQUEST["file"])) {
		$fname=$_REQUEST["file"];
		$ext=fileExtension($fname);
		$fpath="";
		if($ext=="tpl") {
			$fpath=$_SESSION["APP_FOLDER"]["APPS_TEMPLATE_FOLDER"].$fname;
		} else {
			$fpath=$lf.$fname;
			$fpath=substr($fpath,strlen($_SESSION["APP_FOLDER"]["APPROOT"]));
		}
		if(!file_exists($_SESSION["APP_FOLDER"]["APPROOT"].$fpath)) {
			file_put_contents($_SESSION["APP_FOLDER"]["APPROOT"].$fpath,"");
		}
		echo $fpath;
		exit();
	} elseif($_REQUEST["action"]=="preview" && isset($_REQUEST["link"])) {
		$url="../index.php?site={$_REQUEST['forsite']}&popup=true&{$_REQUEST["link"]}";
		//echo $url;
		header("Location:$url");
		exit();
	}
}
printErr("WrongFormat");
exit();

function printPageInfo($a,$lf,$sys=false,$btns=null) {
	if(is_dir($lf.$a)) return;
	
	$t=$a;
	$ext=fileExtension($a);
	$nm=str_replace(".{$ext}","",$a);
	
	$clz1="";
	$type="";
	if($sys) {
		$clz1="system";
		$col1="<td align=center width=35px class='gearicon' style='background-position:center center;'></td>";
	} else {
		$col1="<td align=center width=35px><input type=checkbox name=pgselect rel='$a' /></td>";
	}
	
	$pgName=str_replace(".{$ext}","",$a);
	
	if(strtoupper($ext)=="JSON") {
		$type="generated";
	} else {
		$type="created";
	}
	
	$metaFile=$_SESSION["APP_FOLDER"]["APPROOT"].$_SESSION["APP_FOLDER"]["APPS_CONFIG_FOLDER"]."meta/{$pgName}.json";
	
	/*if(!file_exists($metaFile)) {
		if(is_writable(dirname($metaFile))) {
			file_put_contents($metaFile,"");
			chmod($metaFile,0777);
		}
	}*/
	
	$s="<tr rel='$a' class='$clz1' 
		title='$a' 
		ext='".strtoupper($ext)."' 
		size='".getFileSizeInString(filesize($lf.$a))."' 
		created='".date(getConfig("TIMESTAMP_FORMAT"),filectime($lf.$a))."' 
		modified='".date(getConfig("TIMESTAMP_FORMAT"),filemtime($lf.$a))."' 
		accessed='".date(getConfig("TIMESTAMP_FORMAT"),fileatime($lf.$a))."' 
		meta='$pgName' 
		type='$type' 
		>";
	$s.=$col1;
	$s.="<td>$pgName</td>";
	$ext=strtolower($ext);
	if($ext=="php") $s.="<td class='codeicon' title='$ext Page'>&nbsp;</td>";
	elseif($ext=="html" || $ext=="htm") $s.="<td class='editicon' title='$ext Page'>&nbsp;</td>";
	elseif($ext=="page") $s.="<td class='designicon' title='$ext Page'>&nbsp;</td>";
	elseif($ext=="json") $s.="<td class='layouticon' title='$ext Page'>&nbsp;</td>";
	else $s.="<td class='infoicon' title='$ext Page'>&nbsp;</td>";
	$s.="</tr>";
	echo $s;
}
function getBlankLayout($frmt="json") {
	$arr=array();
	
	$arr["template"]="";
	
	include ROOT."config/layoutareas.php";
	
	$arr["layout"]=array();
	
	foreach($default_Layout_Params as $a=>$b) {
		$arr["layout"][$a]=array("component"=>$b,"enable"=>false);
	}
	
	$arr["css"]="";
	$arr["js"]="";
	$arr["modules"]="";
	$arr["enabled"]="false";
	
	if($frmt=="json") {
		return json_encode($arr);
	} else {
		return $arr;
	}
}
?>
