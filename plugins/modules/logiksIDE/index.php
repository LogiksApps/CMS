<?php
if(!defined('ROOT')) exit('No direct script access allowed');

include __DIR__."/api.php";

$slug = _slug("a/b/c/d");
printArray($slug);
?>