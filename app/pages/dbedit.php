<?php
if (!defined('ROOT')) exit('No direct script access allowed');

if(isset($_REQUEST["forsite"])) {
	loadModule("dbedit");
	loadDbConsole($_REQUEST["forsite"]);
} else {
	dispErrMessage("No Site Mentioned...","CMS Error",400);
}
?>
