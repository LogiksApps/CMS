<?php
if(!defined('ROOT')) exit('No direct script access allowed');

$defnFile=getAppFile("pages/defn/".$_REQUEST["src"]);
if(!file_exists($defnFile)) {
	$_REQUEST["src"]=basename($_REQUEST["src"]);
	echo "<h1 align=center>Sorry, Source {$_REQUEST["src"]} not found.</h1>";
	return;
}

$srcName=basename($defnFile);
$srcName=str_replace(".json", "", $srcName);
//echo $defnFile;

include __DIR__."/config.php";

$title='Info';
$title=toTitle($srcName)." Page";

$jsonPage=json_decode(file_get_contents($defnFile),true);
$jsonPage=array_merge($defaultPage,$jsonPage);

echo _css("pageEditor");
echo _js("pageEditor");

include __DIR__."/editor.php";
?>
