<?php
if(!defined('ROOT')) exit('No direct script access allowed');
checkServiceAccess();

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