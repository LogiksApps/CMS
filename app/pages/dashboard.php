<?php
user_admin_check(true);
checkUserSiteAccess($_REQUEST['forsite'],true);

loadModule("dashboard")
?>
<style>
.portlet-header {
	height:20px;
}
</style>
