<?php
if(!defined('ROOT')) exit('No direct script access allowed');

echo _css("jquery.contextMenu");
echo _js(["jquery.contextMenu","sidebarFiles"]);
?>
<style>
#sidebarFileTree {
    width: 100%;
    /*height: 93%;*/
    overflow-x: auto;
    position: absolute;
    bottom: 0px;
    top: 60px;
    left: 0px;
    right: 0px;
}
</style>
<div class="searchField">
    <input type='text' placeholder='Search files' />
</div>
<ul id="sidebarFileTree" class='sidebarTree'></ul>
