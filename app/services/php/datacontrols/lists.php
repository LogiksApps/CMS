<?php
if(!defined('ROOT')) exit('No direct script access allowed');
checkServiceSession();
checkUserSiteAccess($_REQUEST['forsite'],true);
user_admin_check(true);

if(!isset($_REQUEST['format'])) $_REQUEST['format']="table";

if(isset($_REQUEST["action"]) && isset($_REQUEST["mode"])) {
	loadModule("dbcon");
	$dbCon=getDBControls();
	$folders=loadFolderConfig();
	$tbl="";
	if(isset($_REQUEST["mode"])) $tbl=_dbtable($_REQUEST["mode"]);
	else {
		printErr("WrongFormat");
		exit();
	}
	$f=checkModule("datacontrols");
	$f=dirname($f)."/config.php";
	include $f;
	
	if(isset($_POST['id'])) {
		$_POST['id']=str_replace("--","",$_POST['id']);
		$_POST['id']=str_replace(" ","",$_POST['id']);
		$_POST['id']=str_replace("or","",$_POST['id']);
		$_POST['id']=str_replace("OR","",$_POST['id']);
		$_POST['id']=mysql_real_escape_string($_POST['id'],_db()->getLink());
	}
	
	if($_REQUEST['action']=="viewlist") {
		$sql="SELECT {$cntrls[$_REQUEST['mode']]['cols']} FROM $tbl WHERE (site='{$_REQUEST['forsite']}' OR site='*') order by category desc,weight, id desc";
		$r=_dbQuery($sql);
		if($r) {
			$a=_dbData($r);
			
			$btns1=array();$btns2=array();
			$cols=$dbCon->getColumnList($tbl);
			$cols=array_keys($cols);
			foreach($manageButtons1 as $q=>$w) {
				if(strlen($w['checkcol'])>0) {
					if(in_array($w['checkcol'],$cols)) {
						$btns1[$q]=$w["html"];
					}
				} else {
					$btns1[$q]=$w["html"];
				}
			}
			foreach($manageButtons2 as $q=>$w) {
				$btns2[$q]=$w["html"];
			}
			foreach($a as $x) {
				$mbtns1=array();$mbtns2=array();
				foreach($btns1 as $q=>$w) {
					$mbtns1[$q]=sprintf($w,$x['id']);
				}
				foreach($btns2 as $q=>$w) {
					$mbtns2[$q]=sprintf($w,$x['id']);
				}
				printRow($x,$_REQUEST['mode'],$mbtns1,$mbtns2);
			}
			_db()->freeResult($r);
			exit();
		} else {
			exit("<tr><td colspan=20><h3>No ".ucwords($_REQUEST['mode'])." Found</h3></td></tr>");
		}
	}
	elseif($_REQUEST['action']=="block" && isset($_POST['id'])) {
		$sql="UPDATE $tbl SET blocked='{$_POST['v']}' WHERE id={$_POST['id']}";
		_dbQuery($sql);
		exit();
	}
	elseif($_REQUEST['action']=="onmenu" && isset($_POST['id'])) {
		$sql="UPDATE $tbl SET onmenu='{$_POST['v']}' WHERE id={$_POST['id']}";
		_dbQuery($sql);
		exit();
	}
	elseif($_REQUEST['action']=="delete" && isset($_POST['id'])) {
		$whr="";
		$rr=explode(",",$_POST['id']);
		foreach($rr as $r) {
			if(strlen($r)>0) {
				if(strlen($whr)>0) $whr.=" OR id=$r";
				else $whr=" id=$r";
			}
		}
		$sql="DELETE FROM $tbl WHERE $whr";
		_dbQuery($sql);
		exit();
	}
	elseif($_REQUEST['action']=="clone" && isset($_POST['id'])) {
		$whr="";
		$rr=explode(",",$_POST['id']);
		foreach($rr as $r) {
			if(strlen($r)>0) {
				if(strlen($whr)>0) $whr.=" OR id=$r";
				else $whr=" id=$r";
			}
		}
		_db()->cloneRow($tbl,$whr);
		exit();
	} 
	elseif($_REQUEST['action']=="export" && isset($_POST['id'])) {
		$colStr="title,category,header,footer,datatable_table,datatable_cols,datatable_colnames,datatable_hiddenCols,datatable_where,datatable_params,datatable_model";
		
		$whr="";
		$rr=explode(",",$_POST['id']);
		foreach($rr as $r) {
			if(strlen($r)>0) {
				if(strlen($whr)>0) $whr.=" OR id=$r";
				else $whr=" id=$r";
			}
		}
		
		$sql="INSERT INTO "._dbtable("reports")." ($colStr) (SELECT $colStr FROM "._dbtable("forms")." WHERE $whr)";
		$dbCon->executeQuery($sql);
		if($dbCon->insert_id()>0) {
			$date=date('Y-m-d');
			$userid=$_SESSION['SESS_USER_ID'];
			$sql="UPDATE "._dbtable("reports")." SET userid='$userid',doc='$date',doe='$date' WHERE ID=".$dbCon->insert_id();
			if($dbCon->executeQuery($sql)) {
				exit("Successfully Exported Form To Report.");
			}
		}
		exit();
	}
	elseif($_REQUEST['action']=="saveControl") {
		$date=date("Y-m-d");
		$userid=$_SESSION["SESS_USER_ID"];
		$site=$_REQUEST['forsite'];
		
		if($_POST['id']=="0") {
			$sql="INSERT INTO $tbl (id,title,category,header,footer,blocked,privilege,userid,doc,doe,site) VALUES ";
			$sql.="(0,'{$_POST['title']}','{$_POST['category']}','{$_POST['header']}','{$_POST['footer']}','false','*','{$userid}','{$date}','{$date}','{$site}')";
		} else {
			$sql="UPDATE $tbl SET title='{$_POST['title']}',category='{$_POST['category']}',header='{$_POST['header']}',footer='{$_POST['footer']}',userid='{$userid}',doe='{$date}'  WHERE ID={$_POST['id']}";
		}
		$r=_dbQuery($sql);
		if(!$r) {
			echo "Error In Creating/Updating DataControl. Try Again";
		}
		exit();
	}
	elseif($_REQUEST['action']=="info" && isset($_REQUEST['id'])) {
		$sql="SELECT id,title,category,header,footer,blocked,onmenu,privilege FROM $tbl WHERE id={$_REQUEST['id']}";
		$r=_dbQuery($sql);
		if($r) {
			$a=_dbData($r);
			if(count($a)>0) {
				$a=$a[0];
				echo json_encode($a);
				exit();
			}
			_db()->freeResult($r);
		}
		$a=array("id"=>"","title"=>"","category"=>"","header"=>"","footer"=>"","blocked"=>"","privilege"=>"",);
		echo json_encode($a);
		exit();
	} 
	elseif($_REQUEST['action']=="fetch" && isset($_POST['id']) && isset($_POST['col'])) {
		$sql="SELECT {$_POST['col']} as code FROM $tbl WHERE id={$_POST['id']}";
		$r=_dbQuery($sql);
		if($r) {
			$a=_dbData($r);
			if(count($a)>0) {
				echo $a[0]["code"];
			}
			_db()->freeResult($r);
		}
		exit();
	} elseif($_REQUEST['action']=="save" && isset($_POST['id']) && isset($_POST['col'])  && isset($_POST['code'])) {
		$code=mysql_real_escape_string($_POST['code'],_db()->getLink());
		$sql="UPDATE $tbl SET {$_POST['col']}='$code' where id={$_POST['id']}";
		_dbQuery($sql);
		exit();
	}
		
	elseif($_REQUEST['action']=="dlgs" && isset($_REQUEST['dlg'])) {
		$dlg=$_REQUEST['dlg'];
		loadModuleLib("datacontrols","dlgs/$dlg");
		exit();
	}	
	
	//Others
	elseif($_REQUEST["action"]=="tablelist") {
		$arr=$dbCon->getTableList();
		if(isset($_REQUEST["format"]) && $_REQUEST["format"]=="select") {
			foreach($arr as $a) {
				$t=$a;
				if(!(isset($_REQUEST["system"]) && $_REQUEST["system"]=="true")) {
					if(strpos($a,$GLOBALS['DBCONFIG']["DB_APPS"]."_")===0 || strpos($a,$GLOBALS['DBCONFIG']["DB_SYSTEM"]."_")===0) {
						continue;
					}
				}			
				echo "<option value='$a'>$t</option>";
			}
		}
		exit();
	} elseif($_REQUEST["action"]=="columnlist") {
		$sysCol=array("site","userid","doc","doe","toc","toe","privilege");
		if(!isset($_REQUEST["tbl"])) {
			if($_REQUEST["format"]=="select") {
				echo "<option>Table Not Found</option>";
			} else {
				echo "<li>Table Not Found</li>";
			}
			exit();
		}
		$arr=$dbCon->getColumnList($_REQUEST["tbl"]);
		if(isset($_REQUEST["format"])) {
			if($_REQUEST["format"]=="select") {
				foreach($arr as $a=>$b) {
					if(!in_array($a,$sysCol)) {
						$t=$a;
						echo "<option value='$a'>$t</option>";
					}
						
				}
			} elseif($_REQUEST["format"]=="ul") {
				foreach($arr as $a=>$b) {
					if(!in_array($a,$sysCol)) {
						$t=$a;
						echo "<li rel='$a'>$t</li>";
					}
				}
			}
		} else {
			foreach($arr as $a=>$b) {
				if(!in_array($a,$sysCol)) {
					$t=$a;
					echo "<option value='$a'>$t</option>";
				}
			}
		}
		exit();
	} elseif($_REQUEST["action"]=="columnlistmore") {
		if(!isset($_REQUEST["tbl"])) {
			if($_REQUEST["format"]=="select") {
				echo "<option>Table Not Found</option>";
			} else {
				echo "<li>Table Not Found</li>";
			}
			exit();
		}
		$arr=$dbCon->getColumnList($_REQUEST["tbl"]);
		if(isset($_REQUEST["format"])) {
			if($_REQUEST["format"]=="select") {
				foreach($arr as $a=>$b) {
					$t=$a;
					echo "<option value='$a'>$t</option>";
				}
			} elseif($_REQUEST["format"]=="ul") {
				foreach($arr as $a=>$b) {
					$t=$a;
					echo "<li rel='$a'>$t</li>";
				}
			}
		} else {
			foreach($arr as $a=>$b) {
				$t=$a;
				echo "<option value='$a'>$t</option>";
			}
		}
		exit();
	} elseif($_REQUEST["action"]=="columninfolistmore") {
		if(!isset($_REQUEST["tbl"])) {
			echo "<option>Table Not Found</option>";
			exit();
		}
		$arr=$dbCon->getColumnList($_REQUEST["tbl"]);
		$arr1=$dbCon->getTableInfo($_REQUEST["tbl"]);
		$cnt=0;
		foreach($arr1[0] as $z) {
			$arr[$z][sizeOf($arr[$z])]=$arr1[1][$cnt];
			$cnt++;
		}
		if(isset($_REQUEST["format"])) {
			if($_REQUEST["format"]=="select") {
				foreach($arr as $a=>$b) {
					$t1=$a;	  //Name
					$t2=$b[1];//Type
					$t3=$b[2];//Is Nullable
					$t4=$b[3];//Primary Key
					$t5=$b[4];//default
					$t6=$b[6];//php type
					$title=toTitle($a);
					echo "<option title='$title' value='$t1' type=\"$t2\" nullable='$t3' default='$t5'  btype='$t6'>$t1</option>";
				}
			} elseif($_REQUEST["format"]=="ul") {
				foreach($arr as $a=>$b) {
					$t1=$a;	  //Name
					$t2=$b[1];//Type
					$t3=$b[2];//Is Nullable
					$t4=$b[3];//Primary Key
					$t5=$b[4];//default
					$t6=$b[6];//php type
					$title=toTitle($a);
					echo "<li title='$title' type=\"$t2\" nullable='$t3' default='$t5' btype='$t6'>$t1</li>";
				}
			}
		} else {
			foreach($arr as $a=>$b) {
				$t1=$a;	  //Name
				$t2=$b[1];//Type
				$t3=$b[2];//Is Nullable
				$t4=$b[3];//Primary Key
				$t5=$b[4];//default
				$t6=$b[6];//php type
				$title=toTitle($a);
				echo "<option title='$title' value=\"$t2\" type='$t2' nullable='$t3' default='$t5'  btype='$t6'>$t1</option>";
			}
		}
		exit();
	} elseif($_REQUEST["action"]=="columninfolist") {
		$sysCol=array("site","userid","doc","doe","toc","toe","privilege");
		if(!isset($_REQUEST["tbl"])) {
			echo "<option>Table Not Found</option>";
			exit();
		}
		$arr=$dbCon->getColumnList($_REQUEST["tbl"]);
		$arr1=$dbCon->getTableInfo($_REQUEST["tbl"]);
		$cnt=0;
		foreach($arr1[0] as $z) {
			$arr[$z][sizeOf($arr[$z])]=$arr1[1][$cnt];
			$cnt++;
		}
		if(isset($_REQUEST["format"])) {
			if($_REQUEST["format"]=="select") {
				foreach($arr as $a=>$b) {
					if(in_array($a,$sysCol)) continue;
					$t1=$a;	  //Name
					$t2=$b[1];//Type
					$t3=$b[2];//Is Nullable
					$t4=$b[3];//Primary Key
					$t5=$b[4];//default
					$t6=$b[6];//php type
					$title=toTitle($a);
					echo "<option title='$title' value='$t1' type=\"$t2\" nullable='$t3' default='$t5'  btype='$t6'>$t1</option>";
				}
			} elseif($_REQUEST["format"]=="ul") {
				foreach($arr as $a=>$b) {
					if(in_array($a,$sysCol)) continue;
					$t1=$a;	  //Name
					$t2=$b[1];//Type
					$t3=$b[2];//Is Nullable
					$t4=$b[3];//Primary Key
					$t5=$b[4];//default
					$t6=$b[6];//php type
					$title=toTitle($a);
					echo "<li title='$title' type=\"$t2\" nullable='$t3' default='$t5' btype='$t6'>$t1</li>";
				}
			}
		} else {
			foreach($arr as $a=>$b) {
				if(in_array($a,$sysCol)) continue;
				$t1=$a;	  //Name
				$t2=$b[1];//Type
				$t3=$b[2];//Is Nullable
				$t4=$b[3];//Primary Key
				$t5=$b[4];//default
				$t6=$b[6];//php type
				$title=toTitle($a);
				echo "<option title='$title' value=\"$t2\" type='$t2' nullable='$t3' default='$t5'  btype='$t6'>$t1</option>";
			}
		}
		exit();
	} elseif($_REQUEST["action"]=="srceditpage") {
		$f=checkModule("datacontrols");
		$f=dirname($f)."/resources/src/{$_REQUEST['srctype']}.php";
		if(file_exists($f)) {
			include $f;
		} else {
			echo "<h3 align=center>No Source Properties Found For This Type.</h3>";
		}
		exit();
	} elseif($_REQUEST['action']=="preview") {
		$page=$_REQUEST['mode'];
		$ss="";
		if($page=="forms") $ss="&fid={$_REQUEST['id']}";
		elseif($page=="reports") $ss="&rid={$_REQUEST['id']}";
		elseif($page=="views") $ss="&view={$_REQUEST['tmpl']}&vtype=db";
		elseif($page=="search") $ss="&sid={$_REQUEST['id']}";
		$url="../index.php?site={$_REQUEST['forsite']}&popup=true&page={$page}{$ss}";
		//printArray($_REQUEST);
		//echo $url;
		header("Location:$url");
		exit();
	}
}

function printRow($row, $mode, $btns1=array(), $btns2=array()) {
	$clz1="";
	
	if($row['privilege']=="*") $privilege="All Users";
	else $privilege=ucwords($row['privilege']);
	
	if($mode=="forms") {
		if($row['layout']=="plain") {
			$btns1['datatable']="<div class='blankicon colbtn'></div>";
		}
		unset($btns1['template']);
		if(!isset($row['al'])) {
			$row['al']="";
		}
	} elseif($mode=="reports") {
		if(!isset($row['al'])) {
			$row['al']="";
		}
		unset($btns1['template']);
	} elseif($mode=="search") {
		if($row['engine']=="grid") {
			$btns1['template']="<div class='blankicon colbtn'></div>";
		}
	} elseif($mode=="views") {
		unset($btns1['forms']);
	}
	
	$s="<tr rel='{$row['id']}' class='$clz1' title='{$row['category']}/{$row['title']} [{$row['id']}]'>";
	$s.="<td align=center><input type=checkbox name=rowselect rel='{$row['id']}' val='{$row['title']}' /></td>";
	$s.="<td class='category'>{$row['category']}</td>";
	$s.="<td class='title'>{$row['title']}</td>";
	$s.="<td align=center>".strtoupper($row['engine'])."</td>";
	if(isset($row['al']))
		$s.="<td align=center title='{$row['al']}'>".substr(strtolower($row['al']),0,20)."</td>";
	
	if(isset($row['datatable_table']))
		$s.="<td title='{$row['datatable_table']}'>".substr($row['datatable_table'],0,20)."</td>";
	else
		$s.="<td align=center title=''></td>";
	$s.="<td align=center>"._pDate($row['doc'])."</td>";
	$s.="<td align=center>"._pDate($row['doe'])."</td>";
	
	if($row['blocked']=="true") $s.="<td align=center><input type=checkbox name=row_block rel='{$row['id']}' checked=true /></td>";
	else $s.="<td align=center><input type=checkbox name=row_block rel='{$row['id']}' /></td>";
	
	if($row['onmenu']=="true") $s.="<td align=center><input type=checkbox name=row_onmenu rel='{$row['id']}' checked=true /></td>";
	else $s.="<td align=center><input type=checkbox name=row_onmenu rel='{$row['id']}' /></td>";
	
	$s.="<td align=center title='Current Privileges\n\n{$privilege}' ><div name='privileges' class='colbtn usericon' style='padding-left:10px;' rel='{$row['id']}'></div></td>";
	$s.="<td align=center>".implode("",$btns1)."&nbsp;&nbsp;</td>";
	$s.="<td align=center>".implode("",$btns2)."&nbsp;&nbsp;</td>";
	$s.="</tr>";
	echo $s;
}
?>
