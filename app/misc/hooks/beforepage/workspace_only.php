<?php
if(!$_REQUEST['popup']) {
	if(isset($_REQUEST["page"]) && strlen($_REQUEST["page"])>0 && $_REQUEST["page"]!=getConfig("LANDING_PAGE")) {
		//$_SESSION["LGKS_MSG"]="Sorry, The Page You Requested Cannot Be Opened Outof Workspace";
		//$_SESSION["LGKS_MSG_SHOWN"]=true;
		echo "\t<script>
			if(typeof parent.SESS_KEY!='string') {
				sessKey=parent.SESS_KEY;
				//document.location='error.php?code=405';
				if(sessKey==null || sessKey.length<0 || sessKey!='".session_id()."')
					document.location='index.php?site=".SITENAME."';
			}
		</script>";
	} else {
		//echo "<script>SESS_KEY='lgkscms';</script>";
		echo "<script>SESS_KEY='".SITENAME.session_id()."';</script>";
	}
}
?>
