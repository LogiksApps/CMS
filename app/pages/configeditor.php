<?php
if(!defined('ROOT')) exit('No direct script access allowed');

if(isset($_REQUEST["cfg"])) {
	$cfg=$_REQUEST["cfg"];
	if(isset($_REQUEST["schema"])) $schema=$_REQUEST["schema"]; else $schema=$cfg;
	loadModule("cfgeditor");
	
	$schema=explode(",",$schema);
	
	if(isset($_REQUEST["forsite"])) {
		$cfgFile=findAppCfgFile($cfg);
		if(strlen($cfgFile)>0) {
			loadCfgFile($cfgFile,$schema);
		} else {
			echo "<style>body {overflow:hidden;}</style>";
			dispErrMessage("Config Request Not Found.","404:Not Found",404);
		}
	} else {
		echo "<style>body {overflow:hidden;}</style>";
		dispErrMessage("Requested Site Not Found.","404:Not Found",404);
	}
} else {
	loadModuleLib("cfgeditor","manager");
}
?>
