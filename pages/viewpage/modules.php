<?php
if(!defined('ROOT')) exit('No direct script access allowed');

$slug=_slug();

if(isset($slug["module"])) {
	if(checkModule($slug["module"])) {
		loadModule($slug["module"]);
	} else {
		trigger_logikserror("Sorry, Module not found.");
	}
} else {
	trigger_logikserror("Sorry, Module not defined.");
}
?>
