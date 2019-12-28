<?php
if(isset($_SESSION["SITEPARAMS"]) && isset($_SESSION["SITEPARAMS"]['APPS_STATUS']) && strtolower($_SESSION["SITEPARAMS"]['APPS_STATUS'])=="production") {
    _pageVar("SIDEBAR_FILES",false);
} else {
    _pageVar("SIDEBAR_FILES",true);
}
?>