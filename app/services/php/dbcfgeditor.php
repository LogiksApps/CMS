<?php
if(!defined('ROOT')) exit('No direct script access allowed');
checkServiceSession();
checkUserSiteAccess($_REQUEST['forsite'],true);
user_admin_check(true);

if(isset($_REQUEST["action"])) {
	$tbl=_dbtable("config_sites",true);
	
	if($_REQUEST["action"]=="settingslist") {
		$sql="SELECT scope FROM $tbl WHERE site='{$_REQUEST['forsite']}' group by scope";
		$result=_dbQuery($sql,true);
		if($result) {
			$data=_dbData($result);
			_dbFree($result,true);
			foreach($data as $a) {
				echo "<option value='{$a['scope']}'>".toTitle($a['scope'])."</option>";
			}
		}
		exit();
	} elseif($_REQUEST["action"]=="save" && isset($_REQUEST['scope'])) {
		$x=true;
		foreach($_POST as $a=>$b) {
			$sql="UPDATE $tbl SET value='$b' WHERE id=$a";
			if(!_dbQuery($sql,true)) {
				$x=false;
			}
		}
		if($x) {
			echo "Success";
		}
		exit();
	}
}
?>
