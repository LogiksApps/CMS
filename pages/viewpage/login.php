<?php
if(!defined('ROOT')) exit('No direct script access allowed');

if(isset($_SESSION['SESS_ERROR_MSG'])) {
	_pageVar('ERROR_MSG',$_SESSION['SESS_ERROR_MSG']);
	unset($_SESSION['SESS_ERROR_MSG']);
}
?>