<?php
if(!defined('ROOT')) exit('No direct script access allowed');

switch($_REQUEST["action"]) {
	case "info":
		$sql1=_db(true)->_selectQ(_dbTable("rolemodel",true),"module,count(*) as count")
				->_whereOR("site",['*',CMS_SITENAME])
				->_whereOR("guid",[$_SESSION['SESS_GUID'],'global'])->_GROUPBY("module")->_ORDERBY("module")->_GET();
		$sql2=_db(true)->_selectQ(_dbTable("rolemodel",true),"module,activity,count(*) as count")
				->_whereOR("site",['*',CMS_SITENAME])
				->_whereOR("guid",[$_SESSION['SESS_GUID'],'global'])->_GROUPBY("concat(module,activity)")->_ORDERBY("module")->_GET();
		$sql3=_db(true)->_selectQ(_dbTable("rolemodel",true),"module,activity,category,count(*) as count")
				->_whereOR("site",['*',CMS_SITENAME])
				->_whereOR("guid",[$_SESSION['SESS_GUID'],'global'])->_GROUPBY("concat(module,activity,category)")->_ORDERBY("category,module,activity")->_GET();
		
		printServiceMsg([$sql1,$sql2,$sq3]);
		break;
	case "list-privileges":
		$sql=_db(true)->_selectQ(_dbTable("privileges",true),"id,site,name,blocked,remarks,md5(concat(id,name)) as privilegehash,md5(concat(id,name)) as hash")
			//->_where(array("blocked"=>"false"))//,"length(hash)"=>[0,">"]
			->_whereOR("site",['*',CMS_SITENAME])
			->_whereOR("guid",[$_SESSION['SESS_GUID'],'global'])
      ->_orderBy("name asc");

			$data=$sql->_GET();
			foreach($data as $a=>$b) {
				if($b['id']<=ROLE_PRIME) {
					unset($data[$a]);
					continue;
				}
				$data[$a]['title']=toTitle(_ling($b['name']));
			}
			printServiceMsg($data);
		break;
	case "list-modules":
			$sql=_db(true)->_selectQ(_dbTable("rolemodel",true),"id,guid,category,module as name,count(*) as count")
            ->_whereOR("site",['*',CMS_SITENAME])
						->_groupBy("module")
            ->_orderBy("name asc");
		
			$data=$sql->_GET();
			foreach($data as $a=>$b) {
				if($b['id']<=ROLE_PRIME) {
					unset($data[$a]);
					continue;
				}
				$data[$a]['title']=toTitle(_ling($b['name']));
			}
			printServiceMsg($data);
		break;
	case "list-roles":
		if(isset($_POST['roleid'])) {
			$sqlData=_db(true)->_selectQ(_dbTable("rolemodel",true),"id,guid,category,module,md5(concat(module)) as modulehash,activity,privilegehash,allow,role_type")
            ->_where(array("privilegehash"=>$_POST['roleid']))
						->_whereOR("site",['*',CMS_SITENAME])
            ->_orderBy("remarks asc");
			
			$sqlData=$sqlData->_GET();
			foreach($sqlData as $a=>$b) {
				$sqlData[$a]['group_title']=toTitle(_ling($b['module']));
				$sqlData[$a]['role_title']=toTitle(_ling(strtolower($b['category']))." @ "._ling(strtolower($b['activity'])));
				
				$sqlData[$a]['rolehash']=md5($b['id'].$b['privilegehash']);
				$sqlData[$a]['allow']=($sqlData[$a]['allow']==="true");
			}
			
			printServiceMsg($sqlData);
		} else {
			printServiceMsg([]);
		}
		break;
	case "list-roles-modules":
		if(isset($_POST['module'])) {
			$sqlData=_db(true)->_selectQ(_dbTable("rolemodel",true),"id,guid,category,module,md5(concat(remarks)) as modulehash,activity,privilegehash,allow,role_type,remarks")
            ->_where(array("module"=>$_POST['module']))
						->_whereOR("site",['*',CMS_SITENAME]);
			
			$sqlData=$sqlData->_GET();
			foreach($sqlData as $a=>$b) {
				$sqlData[$a]['group_title']=toTitle(_ling(trim($b['remarks'])));
				$sqlData[$a]['role_title']=toTitle(_ling(strtolower($b['category']))." @ "._ling(strtolower($b['activity'])));
				
				$sqlData[$a]['rolehash']=md5($b['id'].$b['privilegehash']);
				$sqlData[$a]['allow']=($sqlData[$a]['allow']==="true");
			}
			
			printServiceMsg($sqlData);
		} else {
			printServiceMsg([]);
		}
		break;
  
  //For UI0
  case "list-modules-for-role":
    if(isset($_POST['roleid'])) {
      $sql=_db(true)->_selectQ(_dbTable("rolemodel",true),"id,guid,category,module as name,count(*) as count")
            ->_whereOR("site",['*',CMS_SITENAME])
            ->_where(array("privilegehash"=>$_POST['roleid']))
            ->_orderBy("name asc")
			->_groupBy("module");
		
			$data=$sql->_GET();
			if(!$data) {
			    $data = [];
			}
			foreach($data as $a=>$b) {
				if($b['id']<=ROLE_PRIME) {
					unset($data[$a]);
					continue;
				}
				$data[$a]['title']=toTitle(_ling($b['name']));
			}
			printServiceMsg($data);
    } else {
			printServiceMsg([]);
	}
    break;
  case "list-activity-role-module":
    if(isset($_POST['module']) && isset($_POST['roleid'])) {
			$sqlData=_db(true)->_selectQ(_dbTable("rolemodel",true),"id,guid,category,module,md5(concat(remarks)) as modulehash,activity,privilegehash,allow,role_type,remarks,count(*) as  count")
            ->_where(array("module"=>$_POST['module']))
            ->_where(array("privilegehash"=>$_POST['roleid']))
						->_whereOR("site",['*',CMS_SITENAME])
            ->_groupBy("activity")
            ->_orderBy("activity asc");
			
			$sqlData=$sqlData->_GET();
			
			$finalData = [];
			foreach($sqlData as $a=>$b) {
				// $sqlData[$a]['group_title']=toTitle(_ling(trim($b['remarks'])));
				// $sqlData[$a]['role_title']=toTitle(_ling(strtolower($b['category']))." @ "._ling(strtolower($b['activity'])));
				
				// $sqlData[$a]['rolehash']=md5($b['id'].$b['privilegehash']);
				// $sqlData[$a]['allow']=($sqlData[$a]['allow']==="true");
				$b['activity'] = current(explode(".",$b['activity']));
				if(!array_key_exists($b['activity'],$finalData)) {
				    $finalData[$b['activity']] = ["activity" => $b['activity'], "title" => toTitle($b['activity']), "count"=>$b['count']];
				} else {
				    $finalData[$b['activity']]['count']+=$b['count'];
				}
			}
			if(isset($finalData["MAIN"])) {
			    $main = $finalData["MAIN"];
			    unset($finalData["MAIN"]);
			    $finalData = array_values($finalData);
			    array_unshift($finalData,$main);
			}
			
			//printServiceMsg($sqlData);
			printServiceMsg($finalData);
		} else {
			printServiceMsg([]);
		}
    break;
  case "list-permissions-activity-role-module":
    if(isset($_POST['activity']) && isset($_POST['module']) && isset($_POST['roleid'])) {
			$sqlData=_db(true)->_selectQ(_dbTable("rolemodel",true),"id,guid,category,module,md5(concat(remarks)) as modulehash,activity,privilegehash,allow,role_type,remarks")
            ->_where(array("module"=>$_POST['module']))
            //->_where(array("activity"=>$_POST['activity']))
            ->_whereRAW("(activity='{$_POST['activity']}' OR activity LIKE '{$_POST['activity']}%')")//array("activity"=>$_POST['activity'])
            ->_where(array("privilegehash"=>$_POST['roleid']))
			->_whereOR("site",['*',CMS_SITENAME])
            ->_orderBy("category asc,activity asc");
			
			$sqlData=$sqlData->_GET();
			foreach($sqlData as $a=>$b) {
				$sqlData[$a]['group_title']=toTitle(_ling(trim($b['remarks'])));
				$sqlData[$a]['role_title']=toTitle(_ling(strtolower($b['category']))." @ "._ling(strtolower($b['activity'])));
				
				$sqlData[$a]['rolehash']=md5($b['id'].$b['privilegehash']);
				$sqlData[$a]['allow']=($sqlData[$a]['allow']==="true");
			}
			
			printServiceMsg($sqlData);
		} else {
			printServiceMsg([]);
		}
    break;
  case "list-permissions-role-module":
    if(isset($_POST['module']) && isset($_POST['roleid'])) {
			$sqlData=_db(true)->_selectQ(_dbTable("rolemodel",true),"id,guid,category,module,md5(concat(remarks)) as modulehash,activity,privilegehash,allow,role_type,remarks")
            ->_where(array("module"=>$_POST['module']))
            ->_where(array("privilegehash"=>$_POST['roleid']))
			->_whereOR("site",['*',CMS_SITENAME]);
			
			$sqlData=$sqlData->_GET();
			foreach($sqlData as $a=>$b) {
				$sqlData[$a]['group_title']=toTitle(_ling(trim($b['remarks'])));
				$sqlData[$a]['role_title']=toTitle(_ling(strtolower($b['category']))." @ "._ling(strtolower($b['activity'])));
				
				$sqlData[$a]['rolehash']=md5($b['id'].$b['privilegehash']);
				$sqlData[$a]['allow']=($sqlData[$a]['allow']==="true");
			}
			
			printServiceMsg($sqlData);
		} else {
			printServiceMsg([]);
		}
    break;
  
  
	case "save":
		$keys=array_keys($_POST);

		$data=_db(true)->_selectQ(_dbTable("rolemodel",true),"count(*) as max",["MD5( CONCAT( id,  privilegehash ) )"=>$keys[0],
				"site"=>CMS_SITENAME])->_get();
		if($data[0]['max']>0) {
			$sql=_db(true)->_updateQ(_dbTable("rolemodel",true),["allow"=>$_POST[$keys[0]],"edited_on"=>date("Y-m-d H:i:s"),"edited_by"=>$_SESSION['SESS_USER_ID']],[
					"MD5( CONCAT( id,  privilegehash ) )"=>$keys[0],
					"site"=>CMS_SITENAME
				]);
			//echo $sql->_SQL();
			$a=$sql->_RUN();
			if($a) {
				printServiceMsg("success");
			} else {
				printServiceMsg("error :"._db(true)->get_error());
			}
		} else {
			printServiceMsg("error2");
		}
		break;
	case "generate":
	    
		break;
	case "generate-save":
	    
		break;
	
	case "downloadcsv":
	    $sql=_db(true)->_selectQ(_dbTable("rolemodel",true),"*",[])
				->_whereOR("site",['*',CMS_SITENAME])
				//guid
				->_orderBy("module asc,category asc,activity asc")
				->_limit(1000000000000000);
		$sqlData=$sql->_GET();
		
		$headerData = [
		        "Module",
		        "Category",
		        "Activity"
		    ];
		$finalData = [];
		$roleList = [];
		
		$sqlRoles=_db(true)->_selectQ(_dbTable("rolemodel",true),"id,privilegehash,remarks")
            ->_whereOR("site",['*',CMS_SITENAME])
			->_groupBy("privilegehash")
			->_orderBy("remarks asc");
		
		$sqlRoles=$sqlRoles->_GET();
		foreach($sqlRoles as $row) {
		    $headerData[] = $row['remarks'];
		    $roleList[$row['remarks']] = "false";
		}
		
		foreach($sqlData as $row) {
		    $data = [
		                "module"=>str_replace('"','',$row['module']),
		                "category"=>str_replace('"','',$row['category']),
		                "activity"=>str_replace('"','',$row['activity']),
		            ];
		    
		    $dataHash = md5(implode("-",array_values($data)));
		    if(!isset($finalData[$dataHash])) {
		        $finalData[$dataHash] = array_merge($data,$roleList);
		    } else {
		        $finalData[$dataHash][$row['remarks']] = $row['allow'];
		    }
		}
// 		printArray($roleList);
// 		printArray($headerData);
// 		printArray($finalData);
// 		exit();

		$csvData = [];
		$csvData[] = '"'.implode('", "', $headerData).'"';
		foreach($finalData as $row) {
		    if(count($row)<=0) continue;
		    $csvData[] = '"'.implode('", "', $row).'"';
		}
		
		$date = date("Y-m-d-H-i-s");
		header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
        //header("Connection: close");
        header("Content-type: text/csv"); 
        header("Content-Disposition: attachment; filename=roles-{$date}.csv"); 
		ob_clean();
		echo implode("\r\n",$csvData);
	    break;
	case "uploadcsv":
	    if(!isset($_FILES["attachment"])) {
            $errMsg = "No upload file found.";
            echo $errMsg;
            echo "<script>top.lgksAlert('{$errMsg}');</script>";
            exit();
        }
        exit("Import Not Allowed Yet");
        if($_FILES["attachment"]['error']==0) {
            $fileName = $_FILES['attachment']['tmp_name'];
            
            $csvData = array_map('str_getcsv', file($fileName));
            $headers = $csvData[0];printArray($headers);
            
            $statusHash = [];
            foreach($csvData as $k=>$row) {
                if($k==0) continue;
                $statusHash[] = md5(implode("-",$row));
            }
            //$sqlFind = _db(true)->_updateQ(_dbTable("rolemodel",true),"",[])->_whereIN("md5(concat(module,category,activity,allow))",$statusHash)->_SQL();
            //echo $sqlFind;
            foreach($csvData as $k=>$row) {
                if($k==0) continue;
                
                foreach($row as $m=>$colStatus) {
                    if($m<3) continue;
                    $colStatus = trim($colStatus);
                    if(!in_array($colStatus,["true","false"])) continue;
                    
                    $dbQuery = _db(true)->_updateQ(_dbTable("rolemodel",true),[
                            "allow"=>$colStatus
                        ],[
                            "guid"=>$_SESSION['SESS_GUID'],
                            "site"=>CMS_SITENAME,
                            "module"=>$row[0],
                            "category"=>$row[1],
                            "activity"=>$row[2],
                            "remarks"=>$headers[$m],
                            // "privilegehash"=>"",
                        ]);
                    $dbQuery->_RUN();
                    //printArray([$dbQuery->_SQL(),$row]);
                }
            }
            $a = _db(true)->_RAW("OPTIMIZE TABLE "._dbTable("rolemodel",true))->_RUN();
            $errMsg = "Imported successfully";
            echo $errMsg;
            echo "<script>top.lgksAlert('{$errMsg}');</script>";
        }
	    break;
}

function roleSortModule($a,$b) {
	return strcasecmp(strtolower($a), strtolower($b));
}
?>