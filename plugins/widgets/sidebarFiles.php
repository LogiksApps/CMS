<?php
if(!defined('ROOT')) exit('No direct script access allowed');

echo _css("jquery.contextMenu");
echo _js(["jquery.contextMenu","sidebarFiles"]);
?>
<div class="searchField">
    <input type='text' placeholder='Search files' />
</div>
<ul id="sidebarFileTree" class='sidebarTree'></ul>
