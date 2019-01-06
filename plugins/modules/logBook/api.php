<?php
if(!defined('ROOT')) exit('No direct script access allowed');

if(!function_exists("getLoggerList")) {
	function getAllLogFolder() {
		return ROOT.LOG_FOLDER.$_REQUEST['forsite']."/";
	}
	function getLoggerList() {
		$logDir=getAllLogFolder();
		if(!is_dir($logDir)) return [];
		$fs=scandir($logDir);
		$fs=array_splice($fs, 2);
		foreach ($fs as $key => $fname) {
			$fp=$logDir.$fname;
			$fpp=scandir($fp);
			if(count($fpp)<=2) {
				unset($fs[$key]);
			}
		}
		return $fs;
	}
}
?>