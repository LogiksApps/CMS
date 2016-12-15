<?php
if(!defined('ROOT')) exit('No direct script access allowed');

$siteCfg=ROOT.APPS_FOLDER.$_REQUEST['forSite']."/apps.cfg";

if(!file_exists($siteCfg) || !array_key_exists($_REQUEST['forSite'], $_SESSION['siteList'])) {
	$lx=SiteLocation."?site=cms";
	echo "<h1 align=center>Sorry, the requested site is not available or does not exist.</h1>";
	echo "<h2 align=center><a href='{$lx}'>Goto Default CMS Home Page</a></h2>";
	exit();
}
_pageConfig("forSite",$_REQUEST['forSite']);
_pageConfig("siteList",_session("siteList"));
_pageVar("SESS_USER_NAME",_session("SESS_USER_NAME"));
_pageVar("SESS_USER_ID",_session("SESS_USER_ID"));
?>
<script>
CMS_FOR_SITE="<?=$_REQUEST["forsite"]?>";
</script>