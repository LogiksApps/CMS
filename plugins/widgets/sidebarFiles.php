<?php
if(!defined('ROOT')) exit('No direct script access allowed');

loadModuleLib("logiksIDE", "api");

$appProps = getStudioAppConfig();

$basePath = "/";
$slug = _slug();

echo _css("jquery.contextMenu");
echo _js(["jquery.contextMenu","sidebarFiles"]);

if(isset($slug['module']) && strlen($slug['module'])>0) {
    $fs = [
        CMS_APPROOT."plugins/modules/{$slug['module']}/",
        CMS_APPROOT."pluginsDev/modules/{$slug['module']}/",
        CMS_APPROOT."plugins/vendors/{$slug['module']}/",
        CMS_APPROOT."pluginsDev/vendors/{$slug['module']}/",
    ];
    $modCheck = false;
    
    foreach($fs as $f) {
        if(file_exists($f)) {
            $modCheck = $f;
            $basePath = str_replace(CMS_APPROOT, "/", $modCheck."/");
            $basePath = str_replace("//", "/", $basePath);
            break;
        }
    }
    if(!$modCheck) {
        echo "<h4 class='error text-center'><br><br>Sorry, module path error</h4>";
        return;
    }
}
?>
<div id='searchField' class="searchField">
    <input type='text' placeholder='Search files' />
</div>
<ul id="sidebarFileTree" class='sidebarTree noselection' basepath='<?=$basePath?>'></ul>
