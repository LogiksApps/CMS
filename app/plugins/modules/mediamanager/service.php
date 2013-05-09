<?php
if(!defined('ROOT')) exit('No direct script access allowed');
checkServiceSession();
checkUserSiteAccess($_REQUEST['forsite'],true);
user_admin_check(true);

$photoTbls=array("crew_photos","vessel_photos","do_photos","do_banners");

if(isset($_REQUEST["action"])) {
	$action=$_REQUEST["action"];
	loadModule("dbcon");
	$dbCon=null;

	if($action == 'filterlist') {
		$site=$_REQUEST["s"];
		$dbCon=getDBControls($site);
		if(isset($_REQUEST["quick"])) {
			$tbls=$dbCon->getTableList();
			foreach($photoTbls as $a) {
				if(in_array($a,$tbls)) {
					$t=str_replace($GLOBALS['DBCONFIG']["DB_APPS"]."_","",$a);
					$t=str_replace("_"," ",$t);
					$t=ucwords(trim($t));
					echo "<option value='$a'>$t</option>";
				}
			}
		} else {
			$photoTbls=getPhotoTables($dbCon);
			foreach($photoTbls as $a) {
				$t=str_replace($GLOBALS['DBCONFIG']["DB_APPS"]."_","",$a);
				$t=str_replace("_"," ",$t);
				$t=ucwords(trim($t));
				echo "<option value='$a'>$t</option>";
			}
		}
		exit();
	} elseif($action == 'count') {
		$site=$_REQUEST["s"];
		$dbCon=getDBControls($site);
		if(isset($_REQUEST["src"])) {
			$sql="SELECT count(*) as count FROM {$_REQUEST["src"]} WHERE (site='$site' OR site='*')";
			$result=$dbCon->executeQuery($sql);
			if($result) {
				$out=_dbData($result);
				$dbCon->freeResult($result);
				echo $out[0]['count'];
			}
		}
		exit();
	} elseif($action == 'viewthumbs' && isset($_REQUEST['viewtype'])) {
		$index=0;
		$limit=10;
		$txt="";
		$table=$_REQUEST["src"];
		$site=$_REQUEST["s"];
		$dbCon=getDBControls($site);

		if(isset($_REQUEST['index'])) $index=$_REQUEST['index'];
		if(isset($_REQUEST['limit'])) $limit=$_REQUEST['limit'];
		if(isset($_REQUEST['txt'])) $txt=$_REQUEST['txt'];

		if($_REQUEST['viewtype']=="thumbs") {
			printThumbsView($dbCon,$table,$site,$index,$limit,$txt);
		} elseif($_REQUEST['viewtype']=="details") {
			printDetailsView($dbCon,$table,$site,$index,$limit,$txt);
		} else {
			printErr("NotImplemented");
		}
		exit();
	} elseif($action == 'viewmedia') {
		$table=$_REQUEST["src"];
		$site=$_REQUEST["s"];
		$id=$_REQUEST["photo"];
		$sql="SELECT id,image_type,image_data,image_size FROM $table WHERE id=$id AND (site='$site' OR site='*')";
		$dbCon=getDBControls($site);
		$result=$dbCon->executeQuery($sql);
	        if($result) {
	                if($dbCon->recordCount($result)>0) {
	                       while($record=$dbCon->fetchData($result)){
								header("Content-type: ".$record["image_type"]);
	                       		echo $record["image_data"];
	                        }
	                }
	                $dbCon->freeResult($result);
	        }
		exit();
	} elseif($action == 'download') {
		$table=$_REQUEST["src"];
		$site=$_REQUEST["s"];
		$id=$_REQUEST["photo"];
		$sql="SELECT id,image_type,image_data,image_size FROM $table WHERE id=$id AND (site='$site' OR site='*')";
		$dbCon=getDBControls($site);
		$result=$dbCon->executeQuery($sql);
	        if($result) {
	                if($dbCon->recordCount($result)>0) {
	                       while($record=$dbCon->fetchData($result)){
							    $mime=$record["image_type"];
							    $ext=explode("/",$mime);
							    $ext=$ext[1];
								if(strpos($_SERVER['HTTP_USER_AGENT'], "MSIE") !== FALSE) {
									header("Content-type: $mime");
									header("Content-Disposition: attachment; filename=download.$ext");
									header("Expires: 0");
									header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
									header("Content-Transfer-Encoding: binary");
									header('Pragma: public');
									//header("Content-Length: ".strlen($data));
								} else {
									header("Content-type: $mime");
									header("Content-Disposition: attachment; filename=download.$ext");
									header("Content-Transfer-Encoding: binary");
									header("Expires: 0");
									header('Pragma: no-cache');
									//header("Content-Length: ".strlen($data));
								}
	                       		echo $record["image_data"];
	                        }
	                }
	                $dbCon->freeResult($result);
	        }
		exit();
	} elseif($action == 'delete') {
		$table=$_REQUEST["src"];
		$site=$_REQUEST["s"];
		$id=$_REQUEST["photo"];
		if(strpos($id,",")) {
			$id=explode(",",$id);
			foreach($id as $a=>$b) {
				$id[$a]="id='$b'";
			}
			$sql=implode(" OR ",$id);
			$sql="DELETE FROM $table WHERE $sql";
			$dbCon=getDBControls($site);
			$dbCon->executeQuery($sql);
			echo "ok";
		} else {
			$sql="DELETE FROM $table WHERE id='$id'";
			$dbCon=getDBControls($site);
			$dbCon->executeQuery($sql);
			echo "ok";
		}
		exit();
	} elseif($action == 'upload') {
		exit();
	} elseif($action == 'swap') {
		exit();
	}
}

function getPhotoTables($dbcon) {
	$tbls=$dbcon->getTableList();
	$pTbls=array();
	foreach($tbls as $a) {
		$cols=$dbcon->getColumnList($a);
		$colNames=array_keys($cols);
		if(in_array("image_type",$colNames) && in_array("image_size",$colNames) && in_array("image_data",$colNames)
			//&& in_array("thumbnails",$colNames) && in_array("title",$colNames) && in_array("category",$colNames)
			) {
			array_push($pTbls,$a);
		}
	}
	return $pTbls;
}
function getSQL($tbl,$site,$index,$limit,$txt="") {
	$searchCols=array("userid");
	$sql="SELECT id,image_type,image_data,image_size,userid,doc,doe FROM $tbl WHERE (site='$site' OR site='*') ";// OR site is null
	if($txt!=null && strlen($txt)>0) {
		$ss=array();
		foreach($searchCols as $a) {
			array_push($ss,"$a like '{$txt}%'");
		}
		$sql.="AND (".implode(" OR ",$ss).") ";
	}
	$sql.="limit $index,$limit";
	//echo $sql;
	return $sql;
}
//Various Views For Photo Thumbnails
function printThumbsView($dbCon,$tbl,$site,$index,$limit,$txt="") {
	if($dbCon==null) return;
	$size="100px";
	$sql=getSQL($tbl,$site,$index,$limit,$txt);
	$result=$dbCon->executeQuery($sql);
	if($result) {
			if($dbCon->recordCount($result)>0) {
				   while($record=$dbCon->fetchData($result)) {
						echo "<li class='mediaholder thumbnail' rel='{$record["id"]}' align=center >
								<img width=$size height=$size src=\"data:{$record["image_type"]};base64,".base64_encode($record["image_data"])."\" />
								</li>";
					}
			} else {
				echo "<h1 class='clr_pink errormsg' align=center>No Media Found</h1>";
			}
			$dbCon->freeResult($result);
	} else {
		echo "<h1 class='clr_pink errormsg' align=center>No Media Found</h1>";
	}
}
function printDetailsView($dbCon,$tbl,$site,$index,$limit,$txt="") {
	$size="50px";
	$sql=getSQL($tbl,$site,$index,$limit,$txt);
	$result=$dbCon->executeQuery($sql);
	if($result) {
			if($dbCon->recordCount($result)>0) {
				   while( $record=$dbCon->fetchData($result)){
						echo "<tr class='mediaholder' rel={$record["id"]} >";
						echo "<td align='center' width='$size'><img width=$size height=$size src=\"data:{$record["image_type"]};base64,".base64_encode($record["image_data"])."\" /></td>";
						echo "<td align='center'>{$record["image_type"]}</td>";
						echo "<td align='center'>".round(($record["image_size"]/1024),2)." kb</td>";
						echo "<td align='left'>{$record["userid"]}</td>";
						echo "<td align='center'><div class='infoicon infobtn' style='float:right;width:0px;padding-right:0px;'></div><div class='infomsg'>";
						echo "Created :: {$record["doc"]}<br/>";
						echo "Updated :: {$record["doe"]}<br/>";
						echo "Size :: ".round(($record["image_size"]/1024),2)." kb<br/>";
						echo "Type :: {$record["image_type"]}<br/>";
						echo "By User :: {$record["userid"]}<br/>";
						echo "</div></td>";
						echo "</tr>";
					}
					exit();
			} else {
				echo "<tr><td colspan=100><h1 class='clr_pink errormsg' align=center>No Media Found</h1><br/><br/><br/></td></tr>";
			}
			$dbCon->freeResult($result);
	} else {
		echo "<tr><td colspan=100><h1 class='clr_pink errormsg' align=center>No Media Found</h1><br/><br/><br/></td></tr>";
	}
}
?>
