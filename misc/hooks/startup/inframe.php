<?php
$noFix=explode(",", getConfig("LOGIN_EXEMPT"));
$noFix[]="login";
$noFix[]="home";

if(!in_array(PAGE, $noFix)) {
	if(_server('HTTP_REFERER')==null || strlen(_server('HTTP_REFERER'))<=1) {
		header("Location:"._link(""));
		exit("This page is allowed within CMS only.");
	}
}
?>