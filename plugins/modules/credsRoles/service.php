<?php
if(!defined('ROOT')) exit('No direct script access allowed');

include_once __DIR__."/api.php";

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
	case "list-roles-main":
		$sql=_db(true)->_selectQ(_dbTable("roles",true),"id,guid,site,name,blocked,remarks,md5(concat(id,name)) as hash, (".
		        _db(true)->_selectQ(_dbTable("users",true), "count(*)", ["blocked"=>"false"])->_whereRAW("FIND_IN_SET(lgks_roles.id ,roles)")->_SQL()
		        .") as users")
			->_where(array("blocked"=>"false"))
			->_whereOR("site",['*',CMS_SITENAME])
			->_whereOR("guid",[$_SESSION['SESS_GUID'],'global'])
            ->_orderBy("name asc");

		$dataRoles=$sql->_GET();
		foreach($dataRoles as $a=>$b) {
		    $dataRoles[$a]['privilegehash']=_slugify("{$b['id']}_{$b['name']}");
			$dataRoles[$a]['title']=toTitle(_ling($b['name']));
		}
		printServiceMsg([
		        "ROLES"=>$dataRoles
		    ]);
		break;
	case "list-roles-permissions":
	    $sql=_db(true)->_selectQ(_dbTable("rolemodel",true),"id,guid,category,module,activity,action,remarks,privilegehash,allow", [])//"blocked"=>"false"
            ->_whereOR("site",['*',CMS_SITENAME])
            ->_orderBy("activity asc");
		
		if($_SESSION['SESS_PRIVILEGE_ID']<=ADMIN_PRIVILEGE_ID) {
		    $sql->_whereOR("guid",[$_SESSION['SESS_GUID'],'global']);
		} else {
		    $sql->_whereOR("guid",[$_SESSION['SESS_GUID']]);
		}
		
		//For Limited Roles
		if(isset($_POST['roleids']) && strlen($_POST['roleids'])>0) {
		    $sqlRoles = _db(true)->_selectQ(_dbTable("roles",true),"id,name", ["id"=>[$_POST['roleids'], "IN"]])->_get();
		    if(!$sqlRoles) $sqlRoles = [];
		    
		    $privliegeList = [];
		    foreach($sqlRoles as $row) $privliegeList[] = _slugify("{$row['id']}_{$row['name']}");
		    $sql->_whereIN("privilegehash", $privliegeList);
		}
        // echo $sql->_SQL();exit();
		$data=$sql->_GET();
		if(!$data) {
		    $data = [];
		}
		
		$finalData = [];
		foreach($data as $a=>$b) {
		    $moduleName = $b['module'];//preg_replace("([A-Z])", " $0", str_replace("_", " ", $b['module']));
		    //$activityName = preg_replace("([A-Z])", " $0", str_replace("_", " ", $b['activity']));
		    $activityName = $b['activity'];
		    if(!isset($finalData[_ling($moduleName)])) $finalData[_ling($moduleName)] = [];
		    if(!isset($finalData[_ling($moduleName)][_ling($activityName)])) $finalData[_ling($moduleName)][_ling($activityName)] = [];
		    if(!isset($finalData[_ling($moduleName)][_ling($activityName)][$b['action']])) $finalData[_ling($moduleName)][_ling($activityName)][$b['action']] = [];
		    
		    
			$b['title']=toTitle(_ling($b['action']));
			
			$finalData[_ling($moduleName)][_ling($activityName)][$b['action']][$b['privilegehash']] = $b;
		}
		printServiceMsg([
		        "PERMISSIONS"=> $finalData
		    ]);
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
  
    case "update-role-module":
        if(isset($_POST['module']) && isset($_POST['allow']) && isset($_POST['privilegehash'])) {
            $where = [
					"module"=>$_POST['module'],
					"privilegehash"=>$_POST['privilegehash']
				];
			if(defined(CMS_SITENAME)) {
			    $where['site'] = CMS_SITENAME;
			}
			if($_SESSION['SESS_GUID']!="global") {
			    $where['guid'] = $_SESSION['SESS_GUID'];
			}
            $sql=_db(true)->_updateQ(_dbTable("rolemodel",true),[
                    "allow"=>(($_POST['allow']=="true")?"true":"false"),
                    "edited_on"=>date("Y-m-d H:i:s"),
                    "edited_by"=>$_SESSION['SESS_USER_ID']
                ],$where);

			$a=$sql->_RUN();
			if($a) {
				printServiceMsg("Successfully Updated");
			} else {
				printServiceMsg("error :"._db(true)->get_error());
			}
        } else {
            printServiceMsg("Error updating permission");
        }
        break;
  
    case "update-role":
        if(isset($_POST['refid']) && isset($_POST['allow'])) {
            $where = [
					"id"=>$_POST['refid'],
				];
			if(defined(CMS_SITENAME)) {
			    $where['site'] = CMS_SITENAME;
			}
			if($_SESSION['SESS_GUID']!="global") {
			    $where['guid'] = $_SESSION['SESS_GUID'];
			}
            $sql=_db(true)->_updateQ(_dbTable("rolemodel",true),[
                    "allow"=>(($_POST['allow']=="true")?"true":"false"),
                    "edited_on"=>date("Y-m-d H:i:s"),
                    "edited_by"=>$_SESSION['SESS_USER_ID']
                ],$where);

			$a=$sql->_RUN();
			if($a) {
				printServiceMsg("Successfully Updated");
			} else {
				printServiceMsg("error :"._db(true)->get_error());
			}
        } else {
            printServiceMsg("Error updating permission");
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
	case "role-users":
	    if(isset($_POST['roleids']) && strlen($_POST['roleids'])>0) {
	        $sql=_db(true)->_selectQ(_dbTable("users",true), "id,name,userid", ["blocked"=>"false"])->_whereIn("roles", $_POST['roleids']);
	        if($_SESSION['SESS_PRIVILEGE_ID']<=ADMIN_PRIVILEGE_ID) {
    		    $sql->_whereOR("guid",[$_SESSION['SESS_GUID'],'global']);
    		} else {
    		    $sql->_whereOR("guid",[$_SESSION['SESS_GUID']]);
    		}
	        printServiceMsg($sql->_GET());
	    } else {
	        printServiceMsg([]);
	    }
	    break;
    case "role-create-new":
        if(isset($_POST['role_name']) && strlen($_POST['role_name'])>0) {
            $sqlData = [
                    "guid"=>$_SESSION['SESS_GUID'],
                    "site"=>CMS_SITENAME,
                    "name"=>$_POST['role_name'],
                    "remarks"=>"",
                    "created_by"=>$_SESSION['SESS_USER_ID'],
    				"created_on"=>date("Y-m-d H:i:s"),
    				"edited_by"=>$_SESSION['SESS_USER_ID'],
    				"edited_on"=>date("Y-m-d H:i:s"),
                ];
            if($_SESSION['SESS_PRIVILEGE_ID']<=ADMIN_PRIVILEGE_ID) {
    		    $sqlData["guid"] = $_SESSION['SESS_GUID'];
    		} else {
    		    $sqlData["guid"] = $_SESSION['SESS_GUID'];
    		}
    		
	        $sql=_db(true)->_insertQ1(_dbTable("roles",true), $sqlData)->_RUN();
	        
	        if($sql) {
	            $roleIdNew = _db(true)->get_insertID();
                $privilegeHashNew = _slugify("{$roleIdNew}_{$_POST['role_name']}");
                
	            $roleData = _db(true)->_selectQ(_dbTable("rolemodel",true), "*")
                    ->_whereOR("site",['*',CMS_SITENAME])
                    ->_whereOR("guid",[$_SESSION['SESS_GUID'],'global'])
                    ->_groupBy("policystr")
                    ->_GET();
            
                if(!$roleData) $roleData = [];
                
                foreach($roleData as $k=>$role) {
                    unset($roleData[$k]['id']);
                    $roleData[$k]['allow'] = "false";
                    $roleData[$k]['privilegehash'] = $privilegeHashNew;
                    $roleData[$k]['remarks'] = $_POST['role_name'];
                    $roleData[$k]['rolehash'] = md5($role['policystr'].$privilegeHashNew.$role['site'].$role['guid']);
                    
                    $roleData[$k]["created_by"] = $_SESSION['SESS_USER_ID'];
    				$roleData[$k]["created_on"] = date("Y-m-d H:i:s");
    				$roleData[$k]["edited_by"] = $_SESSION['SESS_USER_ID'];
    				$roleData[$k]["edited_on"] = date("Y-m-d H:i:s");
                }
                
                // printArray([$privilegeHashOld, $roleInfo, $roleData]);exit();
                $a = _db(true)->_insert_batchQ(_dbTable("rolemodel",true), $roleData)->_RUN();
                
                if($a) {
    	            printServiceMsg("success");
    	        } else printServiceMsg("Error cloning Role "._db(true)->get_error());
	        }
	        else printServiceMsg("Error creating new Role");
	    } else {
	        printServiceMsg("Role Name Not Defined");
	    }
        break;
    case "role-create-clone":
        if(isset($_POST['role_name']) && strlen($_POST['role_name'])>0 && isset($_POST['src_role']) && strlen($_POST['src_role'])>0) {
            $roleInfo = _db(true)->_selectQ(_dbTable("roles",true), "*", [
                    "id"=>$_POST["src_role"],
                ])
                ->_whereOR("site",['*',CMS_SITENAME])
                ->_whereOR("guid",[$_SESSION['SESS_GUID'],'global'])
                ->_GET();
            if(!$roleInfo) {
                printServiceMsg("Role Could Not Be Found");
                exit();
            }
            $roleInfo = $roleInfo[0];
            $privilegeHashOld = _slugify("{$roleInfo['id']}_{$roleInfo['name']}");
            
            $roleData = _db(true)->_selectQ(_dbTable("rolemodel",true), "*", [
                    "privilegehash"=>$privilegeHashOld,
                ])
                ->_whereOR("site",['*',CMS_SITENAME])
                ->_whereOR("guid",[$_SESSION['SESS_GUID'],'global'])
                ->_GET();
            
            if(!$roleData) $roleData = [];
            
            $sqlData = [
                    "guid"=>$_SESSION['SESS_GUID'],
                    "site"=>CMS_SITENAME,
                    "name"=>$_POST['role_name'],
                    "remarks"=>"",
                    "created_by"=>$_SESSION['SESS_USER_ID'],
    				"created_on"=>date("Y-m-d H:i:s"),
    				"edited_by"=>$_SESSION['SESS_USER_ID'],
    				"edited_on"=>date("Y-m-d H:i:s"),
                ];
            if($_SESSION['SESS_PRIVILEGE_ID']<=ADMIN_PRIVILEGE_ID) {
    		    $sqlData["guid"] = isset($_POST['SESS_GUID'])?$_POST['SESS_GUID']:$_SESSION['SESS_GUID'];
    		} else {
    		    $sqlData["guid"] = $_SESSION['SESS_GUID'];
    		}
    		
	        $sql=_db(true)->_insertQ1(_dbTable("roles",true), $sqlData)->_RUN();
	        
	        if($sql) {
	            $roleIdNew = _db(true)->get_insertID();
                $privilegeHashNew = _slugify("{$roleIdNew}_{$_POST['role_name']}");
                foreach($roleData as $k=>$role) {
                    unset($roleData[$k]['id']);
                    $roleData[$k]['privilegehash'] = $privilegeHashNew;
                    $roleData[$k]['remarks'] = $_POST['role_name'];
                    $roleData[$k]['rolehash'] = md5($role['policystr'].$privilegeHashNew.$role['site'].$role['guid']);
                    
                    $roleData[$k]["created_by"] = $_SESSION['SESS_USER_ID'];
    				$roleData[$k]["created_on"] = date("Y-m-d H:i:s");
    				$roleData[$k]["edited_by"] = $_SESSION['SESS_USER_ID'];
    				$roleData[$k]["edited_on"] = date("Y-m-d H:i:s");
                }
                
                // printArray([$privilegeHashOld, $roleInfo, $roleData]);exit();
                $a = _db(true)->_insert_batchQ(_dbTable("rolemodel",true), $roleData)->_RUN();
                
                if($a) {
    	            printServiceMsg("success");
    	        } else printServiceMsg("Error cloning Role "._db(true)->get_error());
	            
	            
	        } else printServiceMsg("Error creating new Role");
	    } else {
	        printServiceMsg("Role Name Not Defined");
	    }
        break;
    case "role-delete":
        if(isset($_POST['roleid']) && strlen($_POST['roleid'])>0) {
            $sql=_db(true)->_updateQ(_dbTable("roles",true), [
                    "blocked"=>"true",
                    "edited_by"=>$_SESSION['SESS_USER_ID'],
    				"edited_on"=>date("Y-m-d H:i:s"),
                ], [
                    "id"=>$_POST["roleid"],
                ])
                ->_whereOR("site",['*',CMS_SITENAME])
			    ->_whereOR("guid",[$_SESSION['SESS_GUID'],'global'])
                ->_RUN();
	        
	        if($sql) {
	            printServiceMsg("success");
	        } else printServiceMsg("Error creating new Role");
        } else {
            printServiceMsg("Role Not Defined");
        }
        break;
    case "generate":
        set_time_limit(0);
	    generateRoleModel();
	    
	    printServiceMsg("RoleModel generated successfully");
		break;
	case "role-stats":
	    $roleStats = [
	            "TOTAL ROLES"=>1,
	            "TOTAL RULES"=>100
	        ];
	    //Total Role Count
	    $sql1 = _db(true)->_selectQ(_dbTable("roles",true), "count(*)", ["blocked"=>"false"]);
        if($_SESSION['SESS_PRIVILEGE_ID']<=ADMIN_PRIVILEGE_ID) {
		    $sql1->_whereOR("guid",[$_SESSION['SESS_GUID'],'global']);
		} else {
		    $sql1->_whereOR("guid",[$_SESSION['SESS_GUID']]);
		}
		$sql1 = $sql1->_GET();
		$roleStats["TOTAL ROLES"] = $sql1[0]['count(*)'];
		
		//Total Role Rules Count
		$sql2 = _db(true)->_selectQ(_dbTable("rolemodel",true), "count(*)", []);//"blocked"=>"false"
        if($_SESSION['SESS_PRIVILEGE_ID']<=ADMIN_PRIVILEGE_ID) {
		    $sql2->_whereOR("guid",[$_SESSION['SESS_GUID'],'global']);
		} else {
		    $sql2->_whereOR("guid",[$_SESSION['SESS_GUID']]);
		}
		$sql2 = $sql2->_GET();
		$roleStats["TOTAL RULES"] = $sql2[0]['count(*)'];
	    
	    printServiceMsg($roleStats);
		break;
}

function roleSortModule($a,$b) {
	return strcasecmp(strtolower($a), strtolower($b));
}
?>