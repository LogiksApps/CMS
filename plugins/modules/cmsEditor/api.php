<?php
if(!defined('ROOT')) exit('No direct script access allowed');

if(!function_exists("detectField")) {
    function detectField($rowA, $rowB, $preVal='', $defnParams=[]) {
        if(strlen($preVal)>0) {
            $preVal="{$preVal}.";
        }
        $html = "<div class='input-group'><input name='{$preVal}{$rowA}' value='{$rowB}' class='form-control' /><span class='input-group-addon' title='{$rowA} {$preVal}'><i class='fa fa-question'></i></span></div>";
        
        if(isset($defnParams[$rowA]) && strtolower($defnParams[$rowA])) {
            
        } else {
            switch(strtolower($rowB)) {
                case "true":
                    $html = "<select name='{$preVal}{$rowA}' value='{$rowB}' class='form-control'><option value='true'>True</option><option value='false'>False</option></select>";
                    break;
                case "false":
                    $html = "<select name='{$preVal}{$rowA}' value='{$rowB}' class='form-control'><option value='true'>True</option><option value='false' selected>False</option></select>";
                    break;
            }
        }
        return $html;
    }
    
    function fieldValue($key, $dataArr=[]) {
        if(isset($dataArr[$key])) return $dataArr[$key];
        else return "";
    }
}
?>