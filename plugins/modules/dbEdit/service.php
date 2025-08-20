<?php
if(!defined('ROOT')) exit('No direct script access allowed');
checkServiceAccess();

$dbKey="app";
if(isset($_GET["dkey"])) {
	$dbKey=$_GET["dkey"];
}

$dbList=Database::getConnectionList();

$_ENV['DBKEY'] = $dbKey;
$_ENV['DBLIST'] = $dbList;

include_once __DIR__."/commons.php";

$fs = scandir(__DIR__."/services");
foreach($fs as $f) {
    if($f!="." && $f!="..") {
        include_once __DIR__."/services/$f";
    }
}

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
					if(isset($_REQUEST['showSQL']) && $_REQUEST['showSQL']=="true") {
						echo "<citie>".$sql->_SQL()."</citie>";
					}
					$data=$sql->_get();
				// 	$error = $sql->get_error();
				// 	if($error) {
				// 	    echo "<citie class='alert alert-danger alert-error'>{$error}</citie>";
				// 	}
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
					if(isset($_REQUEST['showSQL']) && $_REQUEST['showSQL']=="true") {
						echo "<citie>".$sql->_SQL()."</citie>";
					}
					$data=$sql->_get();
				// 	$error = $sql->get_error();
				// 	if($error) {
				// 	    echo "<citie class='alert alert-danger alert-error'>{$error}</citie>";
				// 	}
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
    case "dataTableFilter":
        $tables = _db($dbKey)->get_tablelist();
        
        $finalData = [];
        
        foreach($tables as $tbl) {
            $ext = current(explode("_",$tbl));
            if(!isset($finalData[$ext])) $finalData[$ext] = 1;
            else $finalData[$ext]++;
        }
        
        printServiceMsg($finalData);
        break;
    case "dumpSchema":
        $fileJSON_SCHEMA = CMS_APPROOT.".install/sql/db_schema-".getApp_VERSCODE().".json";
        $fileJSON_DATA = CMS_APPROOT.".install/sql/db_data-".getApp_VERSCODE().".json";
        $fileSQL_SCHEMA = CMS_APPROOT.".install/sql/db_schema-".getApp_VERSCODE().".sql";
        $fileSQL_DATA = CMS_APPROOT.".install/sql/db_data-".getApp_VERSCODE().".sql";
        
        if(file_exists($fileJSON_SCHEMA) || file_exists($fileJSON_DATA)) {
            echo "<b style='color:red;'>Schema files for this version <u>".getApp_VERSCODE()."</u> exists, please increase the version no of the application to continue</b>";
            exit();
        }
        
        $dataFromTableFilter = explode(",", "do");//data,sys,my
        if(isset($_POST['filter'])) {
            $dataFromTableFilter = explode(",", $_POST['filter']);
        }
        
        //$fields = ["Field","Type","NULL","KEY","DEFAULT","EXTRA"];
        $dbStatus=_db($dbKey)->get_dbObjects();
        $tables = _db($dbKey)->get_tablelist();
        
        $dbData = [];
        $finalDBConfig = [];
        $dbSQL = [];
        $dbSQLData = [];
        foreach($tables as $tbl) {
            if(!isset($dbStatus['tables'][$tbl])) continue;
            $finalDBConfig[$tbl] = ["info"=>[], "columns"=>[]];
            
            //$cols=_db($dbKey)->get_columnlist($tbl, false);
            $cols=_db($dbKey)->get_defination($tbl);
            $info=$dbStatus['tables'][$tbl];
            $sqlCreate = _db($dbKey)->_RAW("SHOW CREATE TABLE {$tbl}")->_GET();
            
            $finalDBConfig[$tbl]['info'] = $info;
            
            foreach($cols as $col) {
                $finalDBConfig[$tbl]['columns'][$col[0]] = $col;
            }
            
            $tblArr = explode("_", $tbl);
            if(in_array($tblArr[0], $dataFromTableFilter)) {
                $tblData = _db($dbKey)->_selectQ($tbl, "*")->_GET();
                
                if($tblData) {
                    $dbData[$tbl] = $tblData;
                    $dbSQLData[$tbl] = _db($dbKey)->_insert_batchQ($tbl, $tblData)->_SQL().";";
                }
            }
            
            if($sqlCreate) $dbSQL[$tbl] = $sqlCreate[0]['Create Table'].";";
        }
        
        //printArray($finalDBConfig);
        
        if(!is_dir(dirname($fileJSON_SCHEMA))) {
            mkdir(dirname($fileJSON_SCHEMA), 0777, true);
        }
        file_put_contents($fileJSON_SCHEMA, json_encode($finalDBConfig, JSON_PRETTY_PRINT));
        file_put_contents($fileJSON_DATA, json_encode($dbData, JSON_PRETTY_PRINT));
        file_put_contents($fileSQL_SCHEMA, implode("\n\n", $dbSQL));
        file_put_contents($fileSQL_DATA, implode("\n\n", $dbSQLData));
        
        echo "Successfully Saved Database Schema and Important Table Data to SQL folder";
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
    default:
        handleActionMethodCalls();
}
?>