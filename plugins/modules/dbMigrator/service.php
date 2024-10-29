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
    //Find DB Files
    //Read Existing DB
    //Find Difference
        //New Table to Create
        //Existing Table, New Columns to Add
        //Existing Table, existing column, to alter
    
    return "Failed to migrate";
}
?>