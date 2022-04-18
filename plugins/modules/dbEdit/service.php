<?php
if(!defined('ROOT')) exit('No direct script access allowed');
checkServiceAccess();

include_once __DIR__."/commons.php";

$dbKey="app";
if(isset($_GET["dkey"])) {
	$dbKey=$_GET["dkey"];
}

$dbList=Database::getConnectionList();

switch ($_REQUEST['action']) {
	case "listDatabase":
		printServiceMsg($dbList);
	break;
	case "dbList":
		if(count($dbList)<=0) {
			$db=[];
		} else {
			$db=_db($dbKey)->get_dbObjects();
      
     		$db['functions'] = [];
      		$db['procedures'] = [];
      
      		foreach ($db['routines'] as $key=>$dat) {
        		if(isset($dat['ROUTINE_TYPE'])) {
          			if($dat['ROUTINE_TYPE']=="FUNCTION") {
            			$db['functions'][$key] = $dat;
            			unset($db['routines'][$key]);
          			} elseif($dat['ROUTINE_TYPE']=="PROCEDURE") {
            			$db['procedures'][$key] = $dat;
            			unset($db['routines'][$key]);
          			}
        		}
      		}
			foreach ($db as $key => $obj) {
				$db[$key]=array_keys($obj);
			}
			foreach ($db['tables'] as $key=>$tbl) {
				if(in_array($tbl, $db['views'])) unset($db['tables'][$key]);
				elseif(in_array($tbl, $db['triggers'])) unset($db['tables'][$key]);
				elseif(in_array($tbl, $db['events'])) unset($db['tables'][$key]);
				elseif(in_array($tbl, $db['routines'])) unset($db['tables'][$key]);
			}
			$db['tables']=array_values($db['tables']);
		}
		
		printServiceMsg($db);
	break;
	case "panel":
		if(count($dbList)<=0) {
			echo "<h2 align=center>This app does not have any database configured.</h2>";
		} else if(isset($_REQUEST['panel'])) {
			$panel=strtolower($_REQUEST['panel']);
			$panelFile=__DIR__."/panels/$panel.php";
			if(file_exists($panelFile)) {
				include_once $panelFile;
			} else {
				echo "<h2 align=center>Please load something to view its information.</h2>";
			}
		} else {
			echo "<h2 align=center>Selected Panel Not Supported Yet</h2>";
		}
	break;
	case "dbTablePanel":
		if(count($dbList)<=0) {
			echo "<h2 align=center>This app does not have any database configured.</h2>";
		} else if(isset($_REQUEST['panel'])) {
			$panel=strtolower($_REQUEST['panel']);
			$panelFile=__DIR__."/dbTablePanels/$panel.php";
			if(file_exists($panelFile)) {
				include_once $panelFile;
			} else {
				echo "<h2 align=center>Selected Panel Not Supported Yet</h2>";
			}
		} else {
			echo "<h2 align=center>Selected Panel Not Supported Yet</h2>";
		}
	break;


	case "query":
		if(isset($_POST['q'])) {
			if(strlen($_POST['q'])<=1) {
				echo "<h5>Empty query is not allowed. <br>You can try SQL92 or LogiksDB JSON format to query your database</h5>";
				return;
			}
			if(!isset($_REQUEST['type'])) $_REQUEST['type']="SQL";
			if(substr(trim($_POST['q']), 0,1)=="{") $_REQUEST['type']="JSON";
			else $_REQUEST['type']="SQL";

			switch (strtolower($_REQUEST['type'])) {
				case 'sql':
					$qType=strtoupper(current(explode(" ",$_POST['q'])));
					$sql=_db($dbKey)->_raw($_POST['q']);
					$data=$sql->_get();
					if(isset($_REQUEST['showSQL']) && $_REQUEST['showSQL']=="true") {
						echo "<citie>".$sql->_SQL()."</citie>";
					}
					if($qType=="SELECT" || $qType=="SHOW") {
						if($data) {
							printDataInTable($data);
						} else {
							echo "<h4 class='errorMsg'>"._db($dbKey)->get_error()."</h4>";
						}
					} else {
						if($data) {
							echo "<h4 class='successMsg'>Succesfully excuted <u>{$qType}</u> query.</h4>";
						} else {
							echo "<h4 class='errorMsg'>"._db($dbKey)->get_error()."</h4>";
						}
					}
					break;
				
				case "json":
					$sql=AbstractQueryBuilder::fromJSON($_POST['q'],_db($dbKey));
					$data=$sql->_get();
					if(isset($_REQUEST['showSQL']) && $_REQUEST['showSQL']=="true") {
						echo "<citie>".$sql->_SQL()."</citie>";
					}
					printDataInTable($data);
					break;

				default:
					echo "<h5>Query Type not supported</h5>";
					break;
			}
			//echo "QUERY : {$_POST['q']}";
		} else {
			echo "<h5>Query not defined <br>You can try SQL92 or LogiksDB JSON format to query your database</h5>";
		}
	break;


	case "deleteTable":
		if(isset($_POST['src'])) {
			$src=explode(",", $_POST['src']);

			foreach ($src as $s) {
				$s=explode("/", $s);
				switch ($s[0]) {
					case 'tables':
						$sql=_db($dbKey)->_raw("DROP TABLE {$s[1]}");
						$res=$sql->_run();
						break;
					case 'views':
						$sql=_db($dbKey)->_raw("DROP VIEW {$s[1]}");
						$res=$sql->_run();
						break;
					
				}
			}
		} else {
			echo "<h5>Source table not defined</h5>";
		}
	break;

	case "insertRecord":
		if(isset($_GET['src'])) {
			$src=explode("/", $_GET['src']);
			if(count($src)==0) {
				$src[1]=$src[0];
				$src[0]="tables";
			}
			if($src[0]=="tables") {
				$cols=_db($dbKey)->get_columnlist($src[1]);
				foreach ($cols as $c) {
					if(array_key_exists($c, $autoFillColumns)) {
						$_POST[$c]=$autoFillColumns[$c];
					}
					if(array_key_exists($c, $insertFillColumns)) {
						$_POST[$c]=$insertFillColumns[$c];
					}
				}
				$sql=_db($dbKey)->_insertQ1($src[1],$_POST);
				$res=$sql->_run();

				if($res) echo "success";
				else echo "Sorry, failed to create new record.";
			} else {
				echo "<h5>Source format '{$src[0]}' not supported</h5>";
			}
		} else {
			echo "<h5>Source table not defined</h5>";
		}
	break;

	case "updateRecord":
		if(isset($_GET['src']) && isset($_GET['refid']) && $_GET['refid']>0) {
			$src=explode("/", $_GET['src']);
			if(count($src)==0) {
				$src[1]=$src[0];
				$src[0]="tables";
			}
			if($src[0]=="tables") {
				$cols=_db($dbKey)->get_columnlist($src[1]);
				foreach ($cols as $c) {
					if(array_key_exists($c, $autoFillColumns)) {
						$_POST[$c]=$autoFillColumns[$c];
					}
				}
				$sql=_db($dbKey)->_updateQ($src[1],$_POST,["id"=>$_GET['refid']]);
				$res=$sql->_run();

				if($res) echo "success";
				else echo "Sorry, failed to update new record."._db($dbKey)->get_error();//$sql->_SQL();
			} else {
				echo "<h5>Source format '{$src[0]}' not supported</h5>";
			}
		} else {
			echo "<h5>Source table not defined</h5>";
		}
	break;

	case "deleteRecord":
		if(isset($_GET['src'])) {
			$src=explode("/", $_GET['src']);
			if(count($src)==0) {
				$src[1]=$src[0];
				$src[0]="tables";
			}
			if($src[0]=="tables") {
				$sql=_db($dbKey)->_deleteQ($src[1],$_POST);
				$res=$sql->_run();

				if($res) echo "success";
				else echo "Sorry, failed to delete the record.";
			} else {
				echo "<h5>Source format '{$src[0]}' not supported</h5>";
			}
		} else {
			echo "<h5>Source table not defined</h5>";
		}
	break;	

	case "addField":
		if(isset($_GET['src']) && isset($_POST['field']) && strlen($_POST['field'])>0) {
			$src=explode("/", $_GET['src']);
			if(count($src)==0) {
				$src[1]=$src[0];
				$src[0]="tables";
			}
			if($src[0]=="tables") {
				$sql=_db($dbKey)->_raw("ALTER TABLE {$src[1]} ADD COLUMN {$_POST['field']}");
				//echo $sql->_SQL();exit();
				$res=$sql->_run();

				if($res) echo "success";
				else echo "Sorry, failed to add new Column. : ";
			} else {
				echo "<h5>Source format '{$src[0]}' not supported</h5>";
			}
		} else {
			echo "<h5>Source table not defined</h5>";
		}
	break;	

	case "updateField":
		if(isset($_GET['src']) && isset($_POST['field']) && strlen($_POST['field'])>0  && isset($_POST['field_new']) && strlen($_POST['field_new'])>0) {
			$src=explode("/", $_GET['src']);
			if(count($src)==0) {
				$src[1]=$src[0];
				$src[0]="tables";
			}
			if($src[0]=="tables") {
				$sql=_db($dbKey)->_raw("ALTER TABLE {$src[1]} CHANGE COLUMN {$_POST['field']} {$_POST['field_new']}");
				//echo $sql->_SQL();exit();
				$res=$sql->_run();

				if($res) echo "success";
				else echo "Sorry, failed to update the selected Column. : ";
			} else {
				echo "<h5>Source format '{$src[0]}' not supported</h5>";
			}
		} else {
			echo "<h5>Source table not defined</h5>";
		}
	break;

	case "deleteField":
		if(isset($_GET['src']) && isset($_POST['field']) && strlen($_POST['field'])>0) {
			$src=explode("/", $_GET['src']);
			if(count($src)==0) {
				$src[1]=$src[0];
				$src[0]="tables";
			}
			if($src[0]=="tables") {
				$sql=_db($dbKey)->_raw("ALTER TABLE {$src[1]} DROP COLUMN {$_POST['field']}");
				$res=$sql->_run();

				if($res) echo "success";
				else echo "Sorry, failed to delete the selected Column.";
			} else {
				echo "<h5>Source format '{$src[0]}' not supported</h5>";
			}
		} else {
			echo "<h5>Source table not defined</h5>";
		}
	break;	
    
    case "cmd":
        if(isset($_REQUEST['src'])) {
          $cmd=strtolower($_REQUEST['src']);
    			$cmdFile=__DIR__."/cmds/{$cmd}.php";
    			if(file_exists($cmdFile)) {
    				include_once $cmdFile;
    			} else {
    				echo "Command not found";
    			}
        } else {
          echo "Command not defined";
        }
    break;
    case "data":
        if(isset($_REQUEST['src'])) {
            $cmd=strtolower($_REQUEST['src']);
			$cmdFile=__DIR__."/data/{$cmd}.dat";
			if(file_exists($cmdFile)) {
				echo file_get_contents($cmdFile);
			} else {
				echo "";
			}
        } else {
          echo "";
        }
    break;
    case "createView":
        if(isset($_POST['query']) && isset($_POST['title'])) {
            $_POST['title'] = preg_replace("/[^A-Za-z0-9]/","_",$_POST['title']);
            
            if(!isset($_POST['algorithm']) || strlen($_POST['algorithm'])<=0) {
                $_POST['algorithm'] = "UNDEFINED";
            }
            
            if(!isset($_POST['sql_security']) || strlen($_POST['sql_security'])<=0) {
                $_POST['sql_security'] = "";
            } else {
                $_POST['sql_security'] = "SQL SECURITY {$_POST['sql_security']}";
            }
            
            if(!isset($_POST['columns']) || strlen($_POST['columns'])<=0) {
                $_POST['columns'] = "";
            } else {
                $_POST['columns'] = "({$_POST['columns']})";
            }
            
            if(!isset($_POST['with_options']) || strlen($_POST['with_options'])<=0) {
                $_POST['with_options'] = "";
            } else {
                $_POST['with_options'] = "WITH {$_POST['columns']}  CHECK OPTION";
            }
            
            //CREATE ALGORITHM = MERGE SQL SECURITY DEFINER VIEW `view_test124` (a,b,c,d,e) AS SELECT * FROM `accounts_banks` WITH CASCADED CHECK OPTION
            $sqlView = "CREATE ALGORITHM = {$_POST['algorithm']} {$_POST['sql_security']} VIEW {$_POST['title']} {$_POST['columns']} AS {$_POST['query']}";
            
            exit($sqlView);
            
            $a = _db($dbKey)->queryBuilder()->fromSQL($sqlView,_db($dbKey)->queryBuilder()->getInstance())->_RUN();
                
            if($a) {
                echo "success";
            } else {
                echo _db($dbKey)->get_error();
            }
        } else {
            echo "View Code Not Found";
        }
    break;
    case "createTable":
        if(isset($_POST['tbl_name']) && strlen($_POST['tbl_name'])>0 && is_array($_POST['name']) && count($_POST['name'])>0) {
            $_POST['tbl_name'] = preg_replace("/[^A-Za-z0-9]/","_",$_POST['tbl_name']);
            $sqlTable = "CREATE TABLE {$_POST['tbl_name']}";
            $sqlCols = [];
            if(count($sqlCols)>=0) {
                
                foreach($_POST['name'] as $n => $name) {
                    $name = preg_replace("/[^A-Za-z0-9]/","_",$name);
                    $sqlCol = "{$name} ";
                    
                    switch(strtoupper($_POST['type'][$n])) {
                        case "DATE":
                        case "DATETIME":
                        case "TIMESTAMP":
                        case "TIME":
                        case "YEAR":
                        case "TINYTEXT":
                        case "TEXT":
                        case "MEDIUMTEXT":
                        case "LONGTEXT":
                        case "TINYBLOB":
                        case "MEDIUMBLOB":
                        case "BLOB":
                        case "LONGBLOB":
                            $sqlCol .= "{$_POST['type'][$n]}";
                            break;
                        default:
                            if($_POST['length'][$n]) {
                                $sqlCol .= "{$_POST['type'][$n]}({$_POST['length'][$n]}) ";
                            } else {
                                $sqlCol .= "{$_POST['type'][$n]} ";
                            }
                    }
                    
                    if($_POST['attributes'][$n]) {
                        $sqlCol .= "{$_POST['attributes'][$n]} ";
                    }
                    
                    if($_POST['default'][$n]) {
                        if($_POST['default'][$n]=="NULL" || $_POST['default'][$n]=="CURRENT_TIMESTAMP") {
                            $sqlCol .= "DEFAULT {$_POST['default'][$n]} ";
                        } else {
                            $sqlCol .= "DEFAULT '{$_POST['default'][$n]}' ";
                        }
                    }
                    
                    if($_POST['null'][$n] && $_POST['null'][$n]=="no") {
                        $sqlCol .= "NOT NULL ";
                    }
                    if($_POST['ai'][$n] && $_POST['ai'][$n]=="yes") {
                        $sqlCol .= "AUTO_INCREMENT ";
                    }
                    
                    if($_POST['collation'][$n]) {
                        $charSet1 = current(explode("_",$_POST['collation'][$n]));
                        $sqlTable .= " CHARACTER SET {$charSet1} COLLATE {$_POST['collation'][$n]}";
                    }
                    
                    if($_POST['comments'][$n]) {
                        $sqlCol .= "COMMENT '{$_POST['comments'][$n]}' ";
                    }
                    
                    $sqlCols[] = $sqlCol;
                }
                
                if(isset($_POST['index'])) {
                    $a = array_search("primary_0",$_POST['index'],true);
                    if($a!==false) {
                        $sqlCols[] = "PRIMARY KEY ({$_POST['name'][$a]}) ";
                    }
                    
                    $a = array_search("unique_0",$_POST['index'],true);
                    if($a!==false) {
                        $sqlCols[] = "INDEX 'index_{$_POST['name'][$a]}_0' ({$_POST['name'][$a]}) ";
                    }
                    
                    $a = array_search("index_0",$_POST['index'],true);
                    if($a!==false) {
                        $sqlCols[] = "UNIQUE 'unique_{$_POST['name'][$a]}_0' ({$_POST['name'][$a]}) ";
                    }
                    
                    $a = array_search("fulltext_0",$_POST['index'],true);
                    if($a!==false) {
                        $sqlCols[] = "FULLTEXT 'fulltext_{$_POST['name'][$a]}_0' ({$_POST['name'][$a]}) ";
                    }
                    
                    $a = array_search("spatial_0",$_POST['index'],true);
                    if($a!==false) {
                        $sqlCols[] = "SPATIAL 'spatial_{$_POST['name'][$a]}_0' ({$_POST['name'][$a]}) ";
                    }
                }
                
                $sqlTable .= " ( " . implode(", ", $sqlCols) . " ) ";
                
                if(isset($_POST['tbl_storage_engine']) && strlen($_POST['tbl_storage_engine'])>0) {
                    $sqlTable .= " ENGINE = {$_POST['tbl_storage_engine']}";
                }
                if(isset($_POST['tbl_collation']) && strlen($_POST['tbl_collation'])>0) {
                    $charSet = current(explode("_",$_POST['tbl_collation']));
                    $sqlTable .= " CHARSET = {$charSet} COLLATE {$_POST['tbl_collation']}";
                }
                
                if(isset($_POST['tbl_comments']) && strlen($_POST['tbl_comments'])>0) {
                    $sqlTable .= " COMMENT = `{$_POST['tbl_comments']}`";
                }
                
                $a = _db($dbKey)->queryBuilder()->fromSQL($sqlTable,_db($dbKey)->queryBuilder()->getInstance())->_RUN();
                
                if($a) {
                    echo "success";
                } else {
                    echo _db($dbKey)->get_error();
                }
            } else {
                echo "Fields are missing error";
            }
        } else {
            echo "Table Name or Fields are missing";
        }
        
        //printArray($_POST);
        break;
    case "dumpSchema":
        $fileJSON_SCHEMA = CMS_APPROOT."SQL/db_schema-".getApp_VERSCODE().".json";
        $fileJSON_DATA = CMS_APPROOT."SQL/db_data-".getApp_VERSCODE().".json";
        
        if(file_exists($fileJSON_SCHEMA) || file_exists($fileJSON_DATA)) {
            echo "<b style='color:red;'>Schema files for this version <u>".getApp_VERSCODE()."</u> exists, please increase the version no of the application to continue</b>";
            exit();
        }
        
        //$fields = ["Field","Type","NULL","KEY","DEFAULT","EXTRA"];
        $dbStatus=_db($dbKey)->get_dbObjects();
        $tables = _db($dbKey)->get_tablelist();
        $dbData = [];
        $finalDBConfig = [];
        foreach($tables as $tbl) {
            if(!isset($dbStatus['tables'][$tbl])) continue;
            $finalDBConfig[$tbl] = ["info"=>[], "columns"=>[]];
            
            //$cols=_db($dbKey)->get_columnlist($tbl, false);
            $cols=_db($dbKey)->get_defination($tbl);
            $info=$dbStatus['tables'][$tbl];
            
            $finalDBConfig[$tbl]['info'] = $info;
            
            foreach($cols as $col) {
                $finalDBConfig[$tbl]['columns'][$col[0]] = $col;
            }
            
            $tblArr = explode("_", $tbl);
            if(in_array($tblArr[0], ["do"])) {//, "data", "sys"
                $tblData = _db($dbKey)->_selectQ($tbl, "*")->_GET();
                $dbData[$tbl] = $tblData;
            }
        }
        
        //printArray($finalDBConfig);
        
        if(!is_dir(dirname($fileJSON_SCHEMA))) {
            mkdir(dirname($fileJSON_SCHEMA), 0777, true);
        }
        file_put_contents($fileJSON_SCHEMA, json_encode($finalDBConfig, JSON_PRETTY_PRINT));
        file_put_contents($fileJSON_DATA, json_encode($dbData, JSON_PRETTY_PRINT));
        
        echo "Successfully Saved Database Schema and Important Table Data to SQL folder";
        break;
}
?>