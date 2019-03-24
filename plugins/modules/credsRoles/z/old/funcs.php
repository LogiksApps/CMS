<?php
if(!defined('ROOT')) exit('No direct script access allowed');

if(!function_exists("roleSortModule")) {
  function roleSortModule($a,$b) {
    return strcasecmp(strtolower($a), strtolower($b));
  }
  
  $dataPrivilegesFinal=[];
  $dataRolesFinal=[];

  $sql=_db(true)->_selectQ(_dbTable("privileges",true),"id,site,name,blocked,remarks,md5(concat(id,name)) as privilegehash,md5(concat(id,name)) as hash")
            //->_where(array("blocked"=>"false"))//,"length(hash)"=>[0,">"]
            ->_whereOR("site",[SITENAME,'*',CMS_SITENAME])
            ->_whereOR("guid",[$_SESSION['SESS_GUID'],'global']);

  $r=_dbQuery($sql,true);
  if($r) {
    $dataPrivileges=_dbData($r,true);
    _dbFree($r,true);

    foreach($dataPrivileges as $role) {
      if(!isset($dataRolesFinal[$role['privilegehash']])) {
        $dataPrivilegesFinal[$role['privilegehash']]=[];
      }

      $dataPrivilegesFinal[$role['privilegehash']]=$role;
    }


    $sql=_db(true)->_selectQ(_dbTable("rolemodel",true),"id,guid,category,module,activity,privilegehash,allow,role_type")
            ->_where(array("site"=>$_GET['forsite']));
    //echo $sql->_SQL();

    $r=_dbQuery($sql,true);
    if($r) {
      $dataRoles=_dbData($r,true);
      _dbFree($r,true);

      foreach($dataRoles as $role) {
        if(!isset($dataRolesFinal[$role['privilegehash']])) {
          $dataRolesFinal[$role['privilegehash']]=[];
        }

        $dataRolesFinal[$role['privilegehash']][strtolower($role['module'])][]=$role;//[$role['category']][$role['activity']]
      }

      //printArray($dataRolesFinal);
    }
  } else {
    $dataPrivilegesFinal=[];
    $dataRolesFinal=[];
  }
  
//     printArray($dataPrivilegesFinal);exit();
  // printArray($dataRolesFinal);exit();
  
//   if(count($dataPrivilegesFinal)<=0) {
//     print_error("Privleges Not Found for the site ".CMS_SITENAME);
//     return;
//   }
//   if(count($dataRolesFinal)<=0) {
//     print_error("Permissions Not Found for the site ".CMS_SITENAME);
//     return;
//   }
  
  $_GET['DX']=$dataPrivilegesFinal;
  $_GET['DY']=$dataRolesFinal;
}
?>