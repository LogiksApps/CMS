<?php
if(!defined('ROOT')) exit('No direct script access allowed');

$srcFile=getAppFile($_REQUEST['src']);
$fileContent = file_get_contents($srcFile);
$fileContent = explode("\n",$fileContent);

$defnFile = basename($_REQUEST['src']);
$defnFile = str_replace(".cfg","",str_replace(".ini","",$defnFile));
$defnFile = CMS_APPROOT."config/defn/{$defnFile}.json";
if(file_exists($defnFile)) {
    $defnParams = json_decode(file_get_contents($defnFile),true);
} else {
    $defnParams = [];
}
?>
<style>
table th,table td {
    vertical-align:middle;
}
</style>
<table class='table table-hover table-stripped'>
    <thead>
        <tr>
            <th width=35%>Name</th>
            <th>Value</th>
        </tr>
    </thead>
    <tbody>
        <?php
            $preVal = "[DEFINE]";
            foreach($fileContent as $row) {
                $rowArr = explode("=",$row);
                if(count($rowArr)>1) {
                    $rowA = $rowArr[0];
                    array_shift($rowArr);
                    $rowB = implode("=",$rowArr);
                    $rowT = toTitle($rowA);
                    echo "<tr><th width=35%>{$rowT}</th><td>".detectField($rowA,$rowB,$preVal,$defnParams)."</td></tr>";
                } else {
                    if(substr($row,0,1)=="[") {
                        $preVal= $row;
                    } else {
                        
                    }
                    
                    //echo "<tr><th colspan=10>{$row}</th></tr>";
                }
            }
        ?>
    </tbody>
</table>
