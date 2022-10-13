<?php
if(!defined('ROOT')) exit('No direct script access allowed');

function _service_createTable() {
    $dbKey = $_ENV['DBKEY'];
    $dbList = $_ENV['DBLIST'];
    
    // printArray($_POST);exit();
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
                $a = array_search("primary",$_POST['index'],true);
                if($a!==false) {
                    $sqlCols[] = "PRIMARY KEY ({$_POST['name'][$a]}) ";
                }
                
                $a = array_search("unique",$_POST['index'],true);
                if($a!==false) {
                    $sqlCols[] = "INDEX `index_{$_POST['name'][$a]}_0` ({$_POST['name'][$a]}) ";
                }
                
                $a = array_search("index",$_POST['index'],true);
                if($a!==false) {
                    $sqlCols[] = "UNIQUE `unique_{$_POST['name'][$a]}_0` ({$_POST['name'][$a]}) ";
                }
                
                $a = array_search("fulltext",$_POST['index'],true);
                if($a!==false) {
                    $sqlCols[] = "FULLTEXT `fulltext_{$_POST['name'][$a]}_0` ({$_POST['name'][$a]}) ";
                }
                
                $a = array_search("spatial",$_POST['index'],true);
                if($a!==false) {
                    $sqlCols[] = "SPATIAL `spatial_{$_POST['name'][$a]}_0` ({$_POST['name'][$a]}) ";
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
            
            if(isset($_GET['preview']) && $_GET['preview']=="true") {
                echo $sqlTable;
            } else {
                $a = _db($dbKey)->queryBuilder()->fromSQL($sqlTable,_db($dbKey)->queryBuilder()->getInstance())->_RUN();
            
                if($a) {
                    echo "success";
                } else {
                    echo _db($dbKey)->get_error();
                }
            }
        } else {
            echo "Fields are missing error";
        }
    } else {
        echo "Table Name or Fields are missing";
    }
    exit();
}

function _service_createView() {
    $dbKey = $_ENV['DBKEY'];
    $dbList = $_ENV['DBLIST'];
    
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
        
        if(isset($_GET['preview']) && $_GET['preview']=="true") {
            echo $sqlView;
        } else {
            $a = _db($dbKey)->queryBuilder()->fromSQL($sqlView,_db($dbKey)->queryBuilder()->getInstance())->_RUN();
            
            if($a) {
                echo "success";
            } else {
                echo _db($dbKey)->get_error();
            }
        }
    } else {
        echo "View Code Not Found";
    }
}

function _service_createProcedure() {
    $dbKey = $_ENV['DBKEY'];
    $dbList = $_ENV['DBLIST'];
    
    $_POST['parameters'] = explode("\n", $_POST['parameters']);
    $_POST['parameters'] = implode(",", $_POST['parameters']);
    
    // printArray($_POST);exit();
    $sqlOutput = "CREATE PROCEDURE `{$_POST['title']}` ({$_POST['parameters']})";
    
    if(isset($_POST['comment']) && strlen($_POST['comment'])>0) {
        $sqlOutput .= " COMMENT '{$_POST['comment']}'";
    }
    
    $sqlOutput .= " {$_POST['is_deterministic']} {$_POST['sqldataaccess']} SQL SECURITY {$_POST['security_type']} {$_POST['definition']}";
    //exit($sqlOutput);
    
    if(isset($_GET['preview']) && $_GET['preview']=="true") {
        echo $sqlOutput;
    } else {
        $a = _db($dbKey)->queryBuilder()->fromSQL($sqlOutput,_db($dbKey)->queryBuilder()->getInstance())->_RUN();
    
        if($a) {
            echo "success";
        } else {
            echo _db($dbKey)->get_error();
        }
    }
}

function _service_createFunction() {
    $dbKey = $_ENV['DBKEY'];
    $dbList = $_ENV['DBLIST'];
    
    $_POST['parameters'] = explode("\n", $_POST['parameters']);
    $_POST['parameters'] = implode(",", $_POST['parameters']);
    
    // printArray($_POST);exit();
    $sqlOutput = "CREATE FUNCTION `{$_POST['title']}` ({$_POST['parameters']}) RETURNS {$_POST['return_type']}";
    
    if(isset($_POST['comment']) && strlen($_POST['comment'])>0) {
        $sqlOutput .= " COMMENT '{$_POST['comment']}'";
    }
    
    $sqlOutput .= " {$_POST['is_deterministic']} {$_POST['sqldataaccess']} SQL SECURITY {$_POST['security_type']} {$_POST['definition']}";
    //exit($sqlOutput);
    
    if(isset($_GET['preview']) && $_GET['preview']=="true") {
        echo $sqlOutput;
    } else {
        $a = _db($dbKey)->queryBuilder()->fromSQL($sqlOutput,_db($dbKey)->queryBuilder()->getInstance())->_RUN();
    
        if($a) {
            echo "success";
        } else {
            echo _db($dbKey)->get_error();
        }
    }
}
function _service_createTrigger() {
    $dbKey = $_ENV['DBKEY'];
    $dbList = $_ENV['DBLIST'];
    
    // printArray($_POST);exit();
    
    $sqlOutput = "CREATE TRIGGER `{$_POST['title']}` {$_POST['trigger_time']} {$_POST['trigger_event']} ON {$_POST['trigger_table']} FOR EACH ROW {$_POST['definition']}";
    
    if(isset($_GET['preview']) && $_GET['preview']=="true") {
        echo $sqlOutput;
    } else {
        $a = _db($dbKey)->queryBuilder()->fromSQL($sqlOutput,_db($dbKey)->queryBuilder()->getInstance())->_RUN();
    
        if($a) {
            echo "success";
        } else {
            echo _db($dbKey)->get_error();
        }
    }
}
function _service_createEvent() {
    $dbKey = $_ENV['DBKEY'];
    $dbList = $_ENV['DBLIST'];
    
    // $_GET['preview'] = "true";
    // printArray($_POST);
    
    $sqlOutput = false;
    switch($_POST['event_type']) {
        case "ONE TIME":
            if(!isset($_POST['event_time']) || strlen($_POST['event_time'])<=0) {
                echo "Event Time Missing or incorrect";
                exit();
            }
            $_POST['event_time'] = str_replace("T", " ", $_POST['event_time']);//:00.000000
            //CREATE EVENT `ex1` ON SCHEDULE AT '2022-05-30 00:00:00.000000' ON COMPLETION PRESERVE DISABLE COMMENT 'sde' DO select id FROM docs_tbl
            $sqlOutput = "CREATE EVENT `{$_POST['title']}` ON SCHEDULE AT '{$_POST['event_time']}' ON COMPLETION {$_POST['event_preserve']} {$_POST['event_status']}";
            
            if(isset($_POST['comment']) && strlen($_POST['comment'])>0) {
                $sqlOutput .= " COMMENT '{$_POST['comment']}'";
            }
            
            $sqlOutput .= " DO {$_POST['definition']}";
            break;
        case "RECURRING":
            $sqlOutput = "CREATE EVENT `{$_POST['title']}`";
            
            if(!isset($_POST['event_end']) || strlen($_POST['event_end'])<=0) {
                //Without End
                
                $_POST['event_start'] = str_replace("T", " ", $_POST['event_start']);//:00.000000
                
                //CREATE EVENT `e2` ON SCHEDULE EVERY 1 HOUR STARTS '2022-05-27 00:00:00.000000' ON COMPLETION PRESERVE ENABLE COMMENT 'qwe' DO select id from docs_tbl
                $sqlOutput = "CREATE EVENT `{$_POST['title']}` ON SCHEDULE EVERY {$_POST['event_period_value']} {$_POST['event_period']} STARTS '{$_POST['event_start']}' ON COMPLETION {$_POST['event_preserve']} {$_POST['event_status']}";
                
                if(isset($_POST['comment']) && strlen($_POST['comment'])>0) {
                    $sqlOutput .= " COMMENT '{$_POST['comment']}'";
                }
                
                $sqlOutput .= " DO {$_POST['definition']}";
            } else {
                //With End
                $_POST['event_start'] = str_replace("T", " ", $_POST['event_start']);//:00.000000
                $_POST['event_end'] = str_replace("T", " ", $_POST['event_end']);//:00.000000
                
                //CREATE EVENT `e2` ON SCHEDULE EVERY 1 HOUR STARTS '2022-05-27 00:00:00' ENDS '2022-05-31 00:00:00.000000' ON COMPLETION PRESERVE ENABLE COMMENT 'qwe' DO select id from docs_tbl
                $sqlOutput = "CREATE EVENT `{$_POST['title']}` ON SCHEDULE EVERY {$_POST['event_period_value']} {$_POST['event_period']} STARTS '{$_POST['event_start']}' ENDS '{$_POST['event_end']}' ON COMPLETION {$_POST['event_preserve']} {$_POST['event_status']}";
                
                if(isset($_POST['comment']) && strlen($_POST['comment'])>0) {
                    $sqlOutput .= " COMMENT '{$_POST['comment']}'";
                }
                
                $sqlOutput .= " DO {$_POST['definition']}";
            }
            break;
    }
    
    if($sqlOutput) {
        if(isset($_GET['preview']) && $_GET['preview']=="true") {
            echo $sqlOutput;
        } else {
            $a = _db($dbKey)->queryBuilder()->fromSQL($sqlOutput,_db($dbKey)->queryBuilder()->getInstance())->_RUN();
        
            if($a) {
                echo "success";
            } else {
                echo _db($dbKey)->get_error();
            }
        }
    } else {
        echo "Event Type Not Supported";
    }
    //DROP EVENT `e2`; 
}
?>