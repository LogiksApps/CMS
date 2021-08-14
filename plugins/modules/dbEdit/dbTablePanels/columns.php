<?php
if(!defined('ROOT')) exit('No direct script access allowed');

$src=explode("/", $_GET['src']);
if(count($src)==0) {
	$src[1]=$src[0];
	$src[0]="tables";
}
$tblDefination = _db($dbKey)->get_defination($src[1]);
?>
<ul class='list-group' style='width:300px;margin:auto;margin-top:20px;'>
    <?php
        foreach($tblDefination as $col) {
            echo "<li class='list-group-item'>{$col[0]}</li>";
        }
    ?>
</ul>