<?php
if(!defined('ROOT')) exit('No direct script access allowed');

$perspectiveActive = perspectives_active();
$perspectiveList = perspectives_list();
?>
<ul id='perspectiveMenu' class="nav navbar-top-links navbar-left" data-toggle="tooltip" title='Perspective View - <?=toTitle($perspectiveActive)?>'>
	<li class="dropdown">
        <a class="dropdown-toggle" data-toggle="dropdown" href="#">
            <i class="fas fa-object-group"></i> <i class="fa fa-caret-down"></i>
        </a>
        <ul class="dropdown-menu">
            <?php
                foreach($perspectiveList as $key=>$config) {
                    if($key==$perspectiveActive)
                        echo "<li class='active'><a href='#' data-key='{$key}'>{$config['title']}</a></li>";
                    else
                        echo "<li><a href='#' data-key='{$key}'>{$config['title']}</a></li>";
                }
            ?>
        </ul>
    </li>
</ul>
<script>
$(function() {
    $("#perspectiveMenu .dropdown-menu a").click(function() {
        $.cookie("LOGIKCMS-PERSPECTIVE-ACTIVE", $(this).data("key"));
        window.location.reload();
    });
});
</script>