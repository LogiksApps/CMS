<?php
if(!defined('ROOT')) exit('No direct script access allowed');

checkServiceAccess();
if(!isset($_REQUEST["action"])) {
	printServiceErrorMsg("NotAcceptable","Action Not Defined.");
}

//echo $_REQUEST["forsite"];
$site=$_REQUEST["forsite"];

$compileSource=[];

switch (strtoupper($_REQUEST['action'])) {
	case 'PAGES':
	break;
	case 'CSS':
	break;
	case 'JS':
	break;
}
?>