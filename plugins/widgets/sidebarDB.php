<?php
if(!defined('ROOT')) exit('No direct script access allowed');

loadModuleLib("logiksIDE", "api");
$slug = _slug();

$dbKey = "app";

if(!_db($dbKey)) {
    echo "<h3 align=center>DB not configured</h3>";
    return;
}

$db=_db($dbKey)->get_dbObjects();
if(!$db) $db = ["tables"=>[], "routines"=>[]];

$db['functions'] = [];
$db['procedures'] = [];

foreach ($db['routines'] as $key=>$dat) {
    if(isset($dat['ROUTINE_TYPE'])) {
      	if($dat['ROUTINE_TYPE']=="FUNCTION") {
    		$db['functions'][$key] = $dat;
    		unset($db['routines'][$key]);
      	} elseif($dat['ROUTINE_TYPE']=="PROCEDURE") {
    		$db['procedures'][$key] = $dat;
    		unset($db['routines'][$key]);
      	}
    }
}
foreach ($db as $key => $obj) {
    $db[$key]=array_keys($obj);
}
foreach ($db['tables'] as $key=>$tbl) {
    if(in_array($tbl, $db['views'])) unset($db['tables'][$key]);
    elseif(in_array($tbl, $db['triggers'])) unset($db['tables'][$key]);
    elseif(in_array($tbl, $db['events'])) unset($db['tables'][$key]);
    elseif(in_array($tbl, $db['routines'])) unset($db['tables'][$key]);
}
$db['tables']=array_values($db['tables']);



if(isset($slug['module']) && strlen($slug['module'])>0) {
    
    $modName = strtolower($slug['module']);
    foreach($db as $k=>$objects) {
        foreach($objects as $k1=>$obj) {
            $tblMod = strtolower(current(explode("_", $obj)));
            if($tblMod!=$modName) {
                unset($db[$k][$k1]);
            }
        }
    }
    
    // foreach($db as $k=>$objects) {
    //     if(count($objects)<=0) {
    //         if(!in_array($k, ["tables","views"])) {
    //             unset($db[$k]);
    //         }
    //     }
    // }
}

echo _css("jquery.contextMenu");
echo _js(["jquery.contextMenu"]);
?>
<h3 class='heading'>Databases</h3>
<div id="sidebarSourceTree" class='panel-group sidebarMenu'>
    <?php
        foreach ($db as $category=>$dbInfo) {
            $categoryTitle = toTitle($category);
            $hash=md5("sidebarMenu".$category);
            echo "<div class='panel panel-default'>";
            echo "  <div class='panel-heading' role='tab' id='$hash'>";
            echo "    <h4 class='panel-title'>";
            echo "      <a role='button' data-toggle='collapse' data-parent='#sidebarMenu' href='#collapse{$hash}' aria-expanded='true' aria-controls='collapseOne'>";
            echo "        $categoryTitle";
            echo "      </a>";
            echo "      <i class='fa fa-angle-right pull-right'></i>";
            echo "      <span class='label label-warning pull-right'>".count($dbInfo)."</span>";
            echo "    </h4>";
            echo "  </div>";
            echo "  <div id='collapse{$hash}' class='panel-collapse collapse' role='tabpanel' aria-labelledby='$hash'>";
            echo "    <div class='panel-body'>";
            
            foreach($dbInfo as $a=>$b) {
                $icon = "fa fa-table";
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
        
        var lx = _link("modules/dbEdit")+"&srctype="+type+"&src="+refkey;
        parent.openLinkFrame("DB-"+toTitle(type), lx, true);
    });
});
</script>