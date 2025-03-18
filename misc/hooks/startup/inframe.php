<?php
if(strpos(strtolower($_SERVER['HTTP_USER_AGENT']), "electron")) return;

$noFix1=explode(",", getConfig("LOGIN_EXEMPT"));
$noFix2=explode(",", getConfig("INFRAME_EXEMPT"));
$noFix1[]="login";
$noFix1[]="home";

$page = current(explode("/",PAGE));

if(!in_array(PAGE, $noFix1) && !in_array(PAGE, $noFix2)) {
	if(_server('HTTP_REFERER')==null || strlen(_server('HTTP_REFERER'))<=1) {
		header("Location:"._link(""));
		exit("This page is allowed within Studio only.");
	} elseif(current(explode("/",PAGE))=="modules") {
	?>
	<script>if(top==window) window.location = "<?=_link("")?>";</script>
	<?php
	}
}
?>