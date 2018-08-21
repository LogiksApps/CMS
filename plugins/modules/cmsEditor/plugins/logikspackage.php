<?php
if(!defined('ROOT')) exit('No direct script access allowed');

$srcFile=getAppFile($_REQUEST['src']);
$fileContent = file_get_contents($srcFile);

$jsonData = json_decode($fileContent,true);
if(!isset($jsonData['type'])) {
    $jsonData['type']="package";
}

switch(strtolower($jsonData['type'])) {
    case "app":
        break;
    case "plugin":
        break;
}
//printArray($jsonData);
?>
<style>
table th,table td {
    vertical-align:middle;
}
</style>
<table class='table table-hover table-stripped'>
    <thead>
        
    </thead>
    <tbody>
        <tr><th>Name</th><td><?=detectField('name',fieldValue('name',$jsonData))?></td></tr>
    </tbody>
</table>