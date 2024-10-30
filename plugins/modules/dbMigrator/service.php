<?php
if(!defined('ROOT')) exit('No direct script access allowed');
//checkServiceAccess();

handleActionMethodCalls();
//pluginsDev
function _service_list_versions() {
    if(!isset($_POST['mod'])) return [];
    
    switch($_POST['mod']) {
        case "application":
            $f = CMS_APPROOT.".install/sql/";
            if(!file_exists($f)) return [];
            
            $result = glob(CMS_APPROOT.".install/sql/db_schema-*.json");
            foreach($result as $k=>$b) {
                $result[$k] = str_replace(CMS_APPROOT.".install/sql/db_schema-", "", str_replace(".json", "", $b));
            }
            //return [end($result)];
            return $result;
            break;
        default:
            $fArr = [CMS_APPROOT."plugins/modules/{$_POST['mod']}/", CMS_APPROOT."pluginsDev/modules/{$_POST['mod']}/"];
            foreach($fArr as $f) {
                if(file_exists($f)) {
                    return ["current"];
                }
            }
            return [];
    }
}

function _service_migrate() {
    if(!isset($_POST['mod'])) return "Error finding Mode Of Migration";
    if(!isset($_POST['vers'])) return "Error finding Version To Migrate";
    ini_set ( 'max_execution_time', 0); 
    
    $dbKey = "app";
    
    //Find DB Files
    switch($_POST['mod']) {
        case "application":
            $f = CMS_APPROOT.".install/sql/";
            if(!file_exists($f)) return "Application does not support SQL Migration";
            
            //$migrationFiles = glob(CMS_APPROOT.".install/sql/*-{$_POST['vers']}.json");
            // printArray($migrationFiles);
            // foreach($migrationFiles as $f) {
            //    println(current(explode("-", basename($f))));
            //}
            
            $schemaFile = CMS_APPROOT.".install/sql/db_schema-{$_POST['vers']}.json";
            $dataFile = CMS_APPROOT.".install/sql/db_data-{$_POST['vers']}.json";
            $schemaSQL = file_get_contents(CMS_APPROOT.".install/sql/db_schema-{$_POST['vers']}.sql");
            $schemaSQL = explode(";\n", $schemaSQL);
            
            $re = '/CREATE TABLE [a-zA-Z0-9_-`]+ \(/m';
            foreach($schemaSQL as $k=>$sql) {
                preg_match_all($re, $sql, $matches, PREG_SET_ORDER, 0);
                $tblName = $matches[0][0];
                $tblName = str_replace("`", "", $tblName);
                $tblName = str_replace("CREATE TABLE ", "", $tblName);
                $tblName = str_replace("(", "", $tblName);
                $tblName = trim($tblName);
                
                unset($schemaSQL[$k]);
                $schemaSQL[$tblName] = $sql;
            }
            
            $response = [];
            if(file_exists($schemaFile)) {
                $schema = json_decode(file_get_contents($schemaFile), true);
                $response['schema_migration_status'] = migrateSchema($dbKey, $schema, $schemaSQL);
            }
            
            if(file_exists($dataFile)) {
                $data = json_decode(file_get_contents($dataFile), true);
                $response['data_addition_status'] = migrateData($dbKey, $data);
            }
            
            
            return (implode(", ", array_values($response)));
        break;
        default:
            $fArr = [CMS_APPROOT."plugins/modules/{$_POST['mod']}/", CMS_APPROOT."pluginsDev/modules/{$_POST['mod']}/"];
            $finalPath = false;
            foreach($fArr as $f) {
                if(file_exists($f)) {
                    $finalPath = $f;
                }
            }
            if(!$finalPath) return "Module not found";
            
            $schemaFile = $finalPath.".install/sql/schema.json";
            $dataFile = $finalPath.".install/sql/data.json";
            $schemaSQL = file_get_contents($finalPath.".install/sql/schema.sql");
            $schemaSQL = explode(";\n", $schemaSQL);
            // printArray([$schemaFile, $dataFile]);exit();
            
            $re = '/CREATE TABLE [a-zA-Z0-9_-`]+ \(/m';
            foreach($schemaSQL as $k=>$sql) {
                preg_match_all($re, $sql, $matches, PREG_SET_ORDER, 0);
                $tblName = $matches[0][0];
                $tblName = str_replace("`", "", $tblName);
                $tblName = str_replace("CREATE TABLE ", "", $tblName);
                $tblName = str_replace("(", "", $tblName);
                $tblName = trim($tblName);
                
                unset($schemaSQL[$k]);
                $schemaSQL[$tblName] = $sql;
            }
            
            $response = [];
            if(file_exists($schemaFile)) {
                $schema = json_decode(file_get_contents($schemaFile), true);
                $response['schema_migration_status'] = migrateSchema($dbKey, $schema, $schemaSQL);
            }
            
            if(file_exists($dataFile)) {
                $data = json_decode(file_get_contents($dataFile), true);
                $response['data_addition_status'] = migrateData($dbKey, $data);
            }
            
            
            return (implode(", ", array_values($response)));
            
        break;
    }
    
    // return "Failed to migrate";
}
function migrateSchema($dbKey, $schema, $schemaSQL) {
    //Read Existing DB
    //Find Difference
        //New Table to Create
        //Existing Table, New Columns to Add
        //Existing Table, existing column, to alter
    
    // printArray([$dbKey, $schema, $schemaSQL]);exit();
    $actionData = [];    
    
    $dbStatus=_db($dbKey)->get_dbObjects();
    $tables = _db($dbKey)->get_tablelist();
    //$info=$dbStatus['tables'][$tbl];
    //printArray($dbStatus);
    $actionData["table_to_create"] = array_diff(array_keys($schema),$tables);
    
    $actionData["table_cols_to_add"] = [];
    $actionData["table_cols_to_change"] = [];
    
    foreach($schema as $tbl=>$info) {
        if(in_array($tbl, $actionData["table_to_create"])) continue;
        
        $dbTableCols=_db($dbKey)->get_defination($tbl);
        $dbTableColList = [];
        foreach($dbTableCols as $k=>$col) {
            $dbTableColList[] = $col[0];
            $dbTableCols[$col[0]] = $col;
            unset($dbTableCols[$k]);
        }
        
        $tblEngine = $info['info']['ENGINE'];
        $tblCollation = $info['info']['TABLE_COLLATION'];
        $tblCols = $info['columns'];
        
        $colDiff = array_diff(array_keys($tblCols), $dbTableColList);
        if($colDiff) {
            if(!isset($actionData["table_cols_to_add"][$tbl])) $actionData["table_cols_to_add"][$tbl] = [];
            
            foreach($colDiff as $col) {
                $actionData["table_cols_to_add"][$tbl][$col] = $tblCols[$col];
            }
        }
        foreach($tblCols as $col=>$colConfig) {
            $colDiff = array_diff($colConfig,$dbTableCols[$col]);
            if($colDiff) {
                if(!isset($actionData["table_cols_to_change"][$tbl])) $actionData["table_cols_to_change"][$tbl] = [];
                
                $actionData["table_cols_to_change"][$tbl][$col] = $tblCols[$col];
            }
        }
    }
    
    $sqlQueries = [];
    foreach($actionData as $action=>$configObj) {
        switch($action) {
            case "table_to_create":
                foreach($configObj as $tbl) {
                    if(isset($schemaSQL[$tbl])) {
                        $sqlQueries[] = trim($schemaSQL[$tbl]);
                    }
                }
                break;
            case "table_cols_to_add":
                foreach($configObj as $tbl=>$cols) {
                    foreach($cols as $col=>$colConfig) {
                        //CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci 
                        $sqlQueries[] = "ALTER TABLE {$tbl} ADD {$col} {$colConfig[1]} ".($colConfig[2]=="YES"?"NULL":"NOT NULL")." ".(strlen($colConfig[4])>0?"DEFAULT {$colConfig[4]}":"");
                    }
                }
                break;
            case "table_cols_to_change":
                foreach($configObj as $tbl=>$cols) {
                    foreach($cols as $col=>$colConfig) {
                        //CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci 
                        $sqlQueries[] = "ALTER TABLE {$tbl} CHANGE {$col} {$col} {$colConfig[1]} ".($colConfig[2]=="YES"?"NULL":"NOT NULL")." ".(strlen($colConfig[4])>0?"DEFAULT {$colConfig[4]}":"");
                    }
                }
                break;
            default:
        }
    }
    
    // printArray([$actionData, $sqlQueries]);exit();
    $response = [];
    if($sqlQueries) {
        foreach($sqlQueries as $sql) {
            $a = _db($dbKey)->_RAW($sql)->_RUN();
            if(!$a) {
                $response[] = "Error - $sql, "._db($dbKey)->get_error();
            }
        }
    } else {
        return "Nothing to Migrate";
    }
    // printArray([$actionData, $sqlQueries, $response]);exit();
    if(!$response) return "Schema Migration is successfull";
    return implode(", ", $response);
}
function migrateData($dbKey, $data) {
    
}
?>