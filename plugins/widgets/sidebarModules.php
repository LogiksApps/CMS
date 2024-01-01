<?php
if(!defined('ROOT')) exit('No direct script access allowed');

loadModuleLib("logiksIDE", "api");

$fs1 = [];
$fs2 = [];

if(is_dir(CMS_APPROOT."plugins/modules/")) {
    $fs1 = scandir(CMS_APPROOT."plugins/modules/");
    array_shift($fs1);array_shift($fs1);
}
if(is_dir(CMS_APPROOT."pluginsDev/modules/")) {
    $fs2 = scandir(CMS_APPROOT."pluginsDev/modules/");
    array_shift($fs2);array_shift($fs2);
}

$moduleList = [
    "dev"=>$fs2,
    "app"=>$fs1,
];
// printArray($db);

echo _css("jquery.contextMenu");
echo _js(["jquery.contextMenu"]);
?>
<style>
#sidebarSourceTree a {
    color: white !important;
    text-decoration: none;
}
#sidebarSourceTree .panel-developer {
    margin-left: -10px;
}
</style>
<div id='searchField' class="searchField d-none hidden">
    <input type='text' placeholder='Search tables' />
</div>
<div id="sidebarSourceTree" class='panel-group sidebarMenu' style="top: 100px !important;">
    <?php
        foreach ($moduleList as $category=>$moduleInfo) {
            $categoryTitle = toTitle($category);
            $hash=md5("sidebarMenu".$category);
            $cat = ($category=="modules-app" || $category=="app")?"app":"";
            
            echo "<div class='panel panel-default'>";
            if($cat=="app") {
                echo "  <div class='panel-heading' role='tab' id='$hash'>";
                echo "    <h4 class='panel-title'>";
                echo "      <a role='button' data-toggle='collapse' data-parent='#sidebarMenu' href='#collapse{$hash}' aria-expanded='true' aria-controls='collapseOne'>";
                echo "        App Core";
                echo "      </a>";
                echo "      <i class='fa fa-angle-right pull-right'></i>";
                echo "    </h4>";
                echo "  </div>";
                
                echo "  <div id='collapse{$hash}' class='panel-collapse collapse ".($category=="modules"?"in":"")."' role='tabpanel' aria-labelledby='$hash'>";
                echo "    <div class='panel-body'>";
                
                foreach($moduleInfo as $a=>$b) {
                    $icon = "fa fa-cube";
                    echo "<a class='module_item' href='#' data-type='{$category}' data-refkey='{$b}'><i class='menuIcon {$icon}'></i>&nbsp; {$b} <label class='label label-danger pull-right'>{$cat}</label></a>";
                }
                
                echo "    </div>";
                echo "  </div>";
            } else {
                echo "    <div class='panel-body panel-developer'>";
            
                foreach($moduleInfo as $a=>$b) {
                    $icon = "fa fa-cube";
                    echo "<a class='module_item' href='#' data-type='{$category}' data-refkey='{$b}'><i class='menuIcon {$icon}'></i>&nbsp; {$b} <label class='label label-danger pull-right'>{$cat}</label></a>";
                }
                
                echo "    </div>";
            }
            echo "</div>";
        }
    ?>
<br><br><br>
</div>
<script>
$(function() {
    $("#sidebarSourceTree .module_item").click(function() {
        var type = $(this).data("type");
        var refkey = $(this).data("refkey");
        var title = $(this).text();
        // alert(refkey+" "+type);
        var lx = _link(`modules/moduleEditor/${refkey}/${type}`)+"&srctype="+type+"&src="+refkey;
        parent.openLinkFrame("M:"+title, lx, true);
    });
});
</script>