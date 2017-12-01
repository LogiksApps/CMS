<?php
if(!defined('ROOT')) exit('No direct script access allowed');
checkServiceAccess();

switch($_REQUEST["action"]) {
	case "save":
		$keys=array_keys($_POST);

		$data=_db(true)->_selectQ(_dbTable("rolemodel",true),"count(*) as max",["MD5( CONCAT( id,  privilegehash ) )"=>$keys[0],
				"site"=>$_REQUEST['forsite']])->_get();
		if($data[0]['max']>0) {
			$sql=_db(true)->_updateQ(_dbTable("rolemodel",true),["allow"=>$_POST[$keys[0]],"edited_on"=>date("Y-m-d H:i:s"),"edited_by"=>$_SESSION['SESS_USER_ID']],[
					"MD5( CONCAT( id,  privilegehash ) )"=>$keys[0],
					"site"=>$_REQUEST['forsite']
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
}
?>