<?php
if(isset($_SESSION["SITEPARAMS"]) && isset($_SESSION["SITEPARAMS"]['APPS_STATUS']) && strtolower($_SESSION["SITEPARAMS"]['APPS_STATUS'])=="production") {
    _pageVar("SIDEBAR_FILES",false);
} else {
    _pageVar("SIDEBAR_FILES",true);
}

if($_SESSION['SESS_PRIVILEGE_ID']>2) {
    _pageVar("SIDEBAR_OPEN_APPS",true);
} else {
    _pageVar("SIDEBAR_OPEN_APPS",false);
}
?>