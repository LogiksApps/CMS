<?php
if(!defined('ROOT')) exit('No direct script access allowed');

loadModuleLib("logiksIDE", "api");

$moduleList = [
        "modules-app"=>[
            "aasdsad"=>"xasdasda"
        ],
        "modules"=>[
            "aasdsad"=>"xasdasda"
        ],
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
</style>
<div id='searchField' class="searchField d-none hidden">
    <input type='text' placeholder='Search tables' />
</div>
<div id="sidebarSourceTree" class='panel-group sidebarMenu' style="top: 100px !important;">
    <?php
        foreach ($moduleList as $category=>$moduleInfo) {
            $categoryTitle = toTitle($category);
            $hash=md5("sidebarMenu".$category);
            echo "<div class='panel panel-default'>";
            echo "  <div class='panel-heading' role='tab' id='$hash'>";
            echo "    <h4 class='panel-title'>";
            echo "      <a role='button' data-toggle='collapse' data-parent='#sidebarMenu' href='#collapse{$hash}' aria-expanded='true' aria-controls='collapseOne'>";
            echo "        $categoryTitle";
            echo "      </a>";
            echo "      <i class='fa fa-angle-right pull-right'></i>";
            echo "    </h4>";
            echo "  </div>";
            
            echo "  <div id='collapse{$hash}' class='panel-collapse collapse ".($category=="modules"?"in":"")."' role='tabpanel' aria-labelledby='$hash'>";
            echo "    <div class='panel-body'>";
            
            foreach($moduleInfo as $a=>$b) {
                $icon = "fa fa-cube";
                echo "<a class='db_item' href='#' data-type='{$category}' data-refkey='{$b}'><i class='menuIcon {$icon}'></i>&nbsp; {$b}</a>";
            }
            
            echo "    </div>";
            echo "  </div>";
            echo "</div>";
        }
    ?>
</div>
<script>
$(function() {
    $("#sidebarSourceTree .db_item").click(function() {
        var type = $(this).data("type");
        var refkey = $(this).data("refkey");
        //alert();
        // var lx = _link("modules/dbEdit")+"&srctype="+type+"&src="+refkey;
        // parent.openLinkFrame("DB-"+toTitle(type), lx, true);
    });
});
</script>