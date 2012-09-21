<?php
if(!defined('ROOT')) exit('No direct script access allowed');
session_check(true);

if(!isset($_REQUEST["forsite"])) {
	dispErrMessage("HostSite Is Required But Not Found","CMS Error",400);
	exit();
}

loadModule("codeEdit");
?>
