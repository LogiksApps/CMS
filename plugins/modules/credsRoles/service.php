<?php
if(!defined('ROOT')) exit('No direct script access allowed');

checkServiceAccess();

if(!isset($_REQUEST["action"])) {
	printServiceErrorMsg("NotAcceptable","Action Not Defined.");
}

switch($_REQUEST["action"]) {
	case "save":
		$keys=array_keys($_POST);

		$data=_db(true)->_selectQ(_dbTable("rolemodel",true),"count(*) as max",["MD5( CONCAT( id,  privilegehash ) )"=>$keys[0],
				"site"=>$_REQUEST['forsite']])->_get();
		if($data[0]['max']>0) {
			$sql=_db(true)->_updateQ(_dbTable("rolemodel",true),["allow"=>$_POST[$keys[0]],"dtoe"=>date("Y-m-d H:i:s")],[
					"MD5( CONCAT( id,  privilegehash ) )"=>$keys[0],
					"site"=>$_REQUEST['forsite']
				]);
			//echo $sql->_SQL();
			$a=_dbQuery($sql);
			if($a) {
				printServiceMsg("success");
			} else {
				printServiceMsg("error");
			}
		} else {
			printServiceMsg("error2");
		}
		break;
}
?>