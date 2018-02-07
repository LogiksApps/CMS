<?php
if(!defined('ROOT')) exit('No direct script access allowed');
checkServiceAccess();

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
			->_whereOR("guid",[$_SESSION['SESS_GUID'],'global']);

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
						->_whereOR("site",['*',CMS_SITENAME]);
			
			$sqlData=$sqlData->_GET();
			foreach($sqlData as $a=>$b) {
				$sqlData[$a]['module_title']=toTitle(_ling($b['module']));
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
	case "generate":
		break;
	case "generate-save":
		break;
}

function roleSortModule($a,$b) {
	return strcasecmp(strtolower($a), strtolower($b));
}
?>