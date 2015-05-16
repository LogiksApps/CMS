<?php
if (!defined('ROOT')) exit('No direct script access allowed');
checkServiceSession();
checkUserSiteAccess($_REQUEST['forsite'],true);
user_admin_check(true);

if(isset($_REQUEST["action"]) && isset($_REQUEST["mode"])) {
	loadModule("dbcon");$dbCon=getDBControls();
	$tbl="";
	if($_REQUEST["mode"]=="forms") $tbl=_dbtable("forms");
	elseif($_REQUEST["mode"]=="reports") $tbl=_dbtable("reports");
	//elseif($_REQUEST["mode"]=="search") $tbl=_dbtable("search");
	else {
		printErr("WrongFormat");
		exit();
	}
	
	$cntrls=array();
	$cntrls["forms"]=array(
			"cols1"=>"id,title,engine,datatable_table,datatable_cols,datatable_where,datatable_colnames,datatable_hiddenCols,datatable_model,submit_table",
			"cols2"=>"id,title,datatable_table,datatable_params,submit_table",
		);
	$cntrls["reports"]=array(
			"cols1"=>"id,title,engine,datatable_table,datatable_cols,datatable_colnames,datatable_hiddenCols,datatable_model,datatable_where",
			"cols2"=>"id,title,datatable_table,datatable_params",
		);
	$cntrls["search"]=array(
			"cols1"=>"id,title,engine,datatable_table,datatable_cols,datatable_colnames,datatable_hiddenCols,datatable_model,datatable_where",
			"cols2"=>"id,title,datatable_table,datatable_params",
		);
	
	if(isset($_REQUEST['id'])) {
		$_REQUEST['id']=str_replace("--","",$_REQUEST['id']);
		$_REQUEST['id']=str_replace(" ","",$_REQUEST['id']);
		$_REQUEST['id']=str_replace("or","",$_REQUEST['id']);
		$_REQUEST['id']=str_replace("OR","",$_REQUEST['id']);
		$_REQUEST['id']=mysql_real_escape_string($_REQUEST['id'],_db()->getLink());
	} else {
		printErr("WrongFormat");
		exit();
	}
	if($_REQUEST['action']=="fetchdatatable") {
		$sql="SELECT {$cntrls[$_REQUEST['mode']]['cols1']} FROM $tbl WHERE id={$_REQUEST['id']} AND (site='{$_REQUEST['forsite']}' OR site='*')";
		
		$r=_dbQuery($sql);
		if($r) {
			$a=_dbData($r);			
			_db()->freeResult($r);
			if(isset($a[0])) {
				$a=$a[0];				
				
				$a["datatable_table"]=$a["datatable_table"].",";
				$a["datatable_table"]=str_replace(",,",",",$a["datatable_table"]);
				
				if($a["datatable_colnames"]==null || strlen($a["datatable_colnames"])==0) {
					$a["datatable_colnames"]=toTextTitle($a["datatable_cols"]);
				}
				if($a["datatable_hiddenCols"]==null || strlen($a["datatable_hiddenCols"])==0) {
					$a["datatable_hiddenCols"]="";
				}
				if($a["datatable_where"]==null || strlen($a["datatable_where"])==0) {
					$a["datatable_where"]=array();
				} else {
					$x=$a["datatable_where"];
					$x=preg_split("/( AND | and | OR | or )/",$x,-1,PREG_SPLIT_DELIM_CAPTURE);
					$a["datatable_where"]=$x;
				}
				if(strlen($a["datatable_model"])>2) {
					$modelArr=json_decode($a["datatable_model"],true);
					unset($a["datatable_model"]);
					
					$a["datatable_hiddenCols"]=$modelArr["modelData"]["hiddenCols"];
					$a["datatable_searchCols"]=$modelArr["modelData"]["searchCols"];
					$a["datatable_sortCols"]=$modelArr["modelData"]["sortCols"];
					$a["datatable_classes"]=$modelArr["modelData"]["classes"];
				} else {
					$a["datatable_searchCols"]=$a["datatable_cols"];
					$a["datatable_sortCols"]=$a["datatable_cols"];
					$a["datatable_classes"]="";
				}
				
				$a["datatable_colnames"]=split_by_commas($a["datatable_colnames"]);
				$a["datatable_cols"]=split_by_commas($a["datatable_cols"]);
				
				echo json_encode($a);
			}			
			exit();
		}
		exit();
	} elseif($_REQUEST['action']=="fetchparams") {
		$sql="SELECT {$cntrls[$_REQUEST['mode']]['cols2']} FROM $tbl WHERE id={$_REQUEST['id']} AND (site='{$_REQUEST['forsite']}' OR site='*')";
		
		$r=_dbQuery($sql);
		if($r) {
			$a=_dbData($r);			
			_db()->freeResult($r);
			if(isset($a[0])) {
				$a=$a[0];
				
				if($a["datatable_params"]==null || strlen($a["datatable_params"])==0) {
					$a["datatable_params"]=array();
				} else {
					$x=$a["datatable_params"];
					
					$xArr=array();
					preg_match_all("/[a-zA-Z0-9._-]+=\[(.*?)\]/",$x,$xArr);
					
					$s1=$x;
					foreach($xArr[0] as $n=>$m) {
						$s1=str_replace($m,"",$s1);
					}
					$x=explode(";",$s1);
					
					$out=array();
					foreach($x as $t) {
						if(strlen($t)>0) {
							$t=explode("=",$t);
							$dt=trim($t[1]);
							if(strtolower($dt)=="true" || strtolower($dt)=="false") $dt=strtolower($dt);
							$out[trim(str_replace(".","_",$t[0]))]=$dt;
						}
					}
					foreach($xArr[0] as $t) {
						if(strlen($t)>0) {
							$t=explode("=",$t);
							if(count($t)>2) {
								$a1=$t[0];
								unset($t[0]);
								$a2=implode($t);
								
								$t=array($a1,$a2);
							}							
							$dt=trim($t[1]);
							$out[trim(str_replace(".","_",$t[0]))]=$dt;
						}
					}
					$a["datatable_params"]=$out;
				}
				echo json_encode($a);
			}			
			exit();
		}
		exit();
	} elseif($_REQUEST['action']=="savedatatable") {
		$data=$_POST;
		//printArray($_POST);exit();
		$tbls=explode(",",$data['datatable_table']);
		if(strlen($data['datatable_table'])>0) {
			if(strlen($tbls[count($tbls)-1])==0) unset($tbls[count($tbls)-1]);
			if(strlen($tbls[0])<=0) unset($tbls[0]);
			$tbls=trim(implode(",",$tbls));
		}
		
		$data['datatable_cols']=trim($data['datatable_cols']);
		$data['datatable_colnames']=trim($data['datatable_colnames']);
		$data['datatable_hiddenCols']=trim($data['datatable_hiddenCols']);
		$data['datatable_searchCols']=trim($data['datatable_searchCols']);
		$data['datatable_sortCols']=trim($data['datatable_sortCols']);
		$data['datatable_classes']=trim($data['datatable_classes']);
		$data['datatable_where']=trim($data['datatable_where']);
				
		$modelArr=array();
		$modelArr["modelEngine"]="DataControls1";
		$modelArr["modelData"]=array();
		$modelArr["modelData"]["hiddenCols"]=$data['datatable_hiddenCols'];
		$modelArr["modelData"]["searchCols"]=$data['datatable_searchCols'];
		$modelArr["modelData"]["sortCols"]=$data['datatable_sortCols'];
		$modelArr["modelData"]["classes"]=$data['datatable_classes'];
		
		$data['datatable_model']=json_encode($modelArr);
		
		$cols="datatable_table=\"{$tbls}\",datatable_cols=\"{$data['datatable_cols']}\",datatable_colnames=\"{$data['datatable_colnames']}\",datatable_hiddenCols=\"{$data['datatable_hiddenCols']}\",datatable_where=\"{$data['datatable_where']}\",datatable_model='{$data['datatable_model']}'";
		$sql="UPDATE $tbl SET $cols WHERE id={$_REQUEST['id']} AND (site='{$_REQUEST['forsite']}' OR site='*')";
		//exit($tbls);
		if(strlen($sql)>0) {
			$r=_dbQuery($sql);
			if(!$r) {
				echo "Error Saving The Design. Try Again.";
			} else {
				echo "Successfully Updated Datatable.";
			}
			exit();
		}
		exit();
	} elseif($_REQUEST['action']=="saveparams") {
		$data=$_POST['datatable_params'];
		$data=str_replace("\"","\\\"",$data);
		$sql="UPDATE $tbl SET datatable_params=\"{$data}\" WHERE id={$_REQUEST['id']} AND (site='{$_REQUEST['forsite']}' OR site='*')";
		if(strlen($sql)>0) {
			$r=_dbQuery($sql);
			if(!$r) {
				echo "Error Saving The Design. Try Again.";
			} else {
				echo "Successfully Updated Datatable Param/Configs.";
			}
			exit();
		}
		exit();
	}
}

function toTextTitle($t) {
	$t=str_replace("_"," ",$t);
	$t=str_replace(","," , ",$t);
	$t=ucwords($t);
	$t=str_replace(" , ",",",$t);
	$t=str_replace(", ",",",$t);
	$t=str_replace(" ,",",",$t);
	return $t;
}
?>
