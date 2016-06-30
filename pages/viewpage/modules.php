<?php
if(!defined('ROOT')) exit('No direct script access allowed');

$slug=_slug();

if(isset($slug["module"])) {
	if(checkModule($slug["module"])) {
		//loadModule($slug["module"]);
		_pageVar("MODULE",$slug["module"]);
	} else {
		trigger_logikserror("Sorry, Module '{$slug["module"]}' not found.",E_ERROR,404);
	}
} else {
	trigger_logikserror("Sorry, Module not defined.");
}
?>
