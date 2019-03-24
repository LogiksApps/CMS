<?php
if(!defined('ROOT')) exit('No direct script access allowed');

$slug = _slug("a/b/c");
if(!isset($slug['b']) || strlen($slug['b'])<=0) $slug['b']= "plugins";

if(file_exists(__DIR__."/pages/{$slug['b']}.php")) {
    include_once __DIR__."/pages/{$slug['b']}.php";
} else {
    echo "<h2 align=center>Subpage not supported yet</h2>";
}
?>
