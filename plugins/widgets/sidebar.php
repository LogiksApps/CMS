<?php
if(!defined('ROOT')) exit('No direct script access allowed');
//Main Sidebar
$page = current(explode("/", PAGE));
// echo $page;

$show_header_title = true;
$sidebars = [];

$cmsSidebarFile = APPROOT."config/sidebar.json";

if(file_exists($cmsSidebarFile)) {
    $sidebarsContent = json_decode(file_get_contents($cmsSidebarFile), true);
    
    if($sidebarsContent && isset($sidebarsContent[$page])) {
        if(isset($sidebarsContent[$page]['show_header_title'])) $show_header_title = $sidebarsContent[$page]['show_header_title'];
        
        if(isset($sidebarsContent[$page]['sidebars'])) $sidebars = $sidebarsContent[$page]['sidebars'];
    }
}

foreach($sidebars as $a=>$b) {
    if(isset($b['enabled']) && $b['enabled']===false) unset($sidebars[$a]);
    if(isset($b['privileges'])) {
        if(!in_array($_SESSION['SESS_PRIVILEGE_NAME'], explode(",", $b['privileges']))) {
            unset($sidebars[$a]);
        }
    }
    if(isset($b['modes']) && $b['modes']!="*") {
        if(!in_array(getAppType(), $b['modes'])) {
            unset($sidebars[$a]);
        }
    }
    if(isset($b['module']) && strlen($b['module'])>0) {
        if(!checkModule($b['module'])) unset($sidebars[$a]);
    }
}

if(!$sidebars) {
    echo "<h5 align=center><br><br><br>No Sidebar Configuration Found</h1>";
    return;
}

if(count($sidebars)>3) {
    $show_header_title = false;
}
if(!$sidebars) {
?>
<style>
#sidebar, #leftMenuOpen {display: none !important;}
#header {left: 0px !important;}
#page-wrapper {margin: 0px !important;}
body.home #content>ul#myTab {left: 0px !important;}
</style>
<?php
    return;
} elseif(count($sidebars)==1) {
?>
<style>
.sidebarMenu {
    top: 47px !important;
}
</style>
<?php
} else {
?>
<style>
.sidebarbtn.button-logout {
    padding: 14px;
    font-size: 20px;
    position: absolute;
    bottom: 0px;
    cursor: pointer;
}
</style>
<?php
}

// printArray($sidebars);
$firstOne = array_keys($sidebars)[0];
$uiType = "accordion";

switch($uiType) {
    case "tabbed":
        ?>
<ul id="sidebarTab" class="nav nav-tabs nav-justified <?=(count($sidebars)==1)?"hidden d-none":""?>" data-tabs="tabs">
    <?php
        foreach($sidebars as $src=>$srcConfig) {
            $icon = $srcConfig['icon'];
            $title = $srcConfig['title'];
            if(!$show_header_title) {
                $srcConfig['title'] = "";
            }
            if(isset($srcConfig['description'])) {
                $title = $srcConfig['description'];
            }
            if($src==$firstOne)
                echo "<li class='active' role='presentation' title='{$title}'><a href='#sidebarTab-{$src}' data-toggle='tab'><i class='{$icon}'></i>&nbsp;"._ling($srcConfig['title'])."</a></li>";
            else
                echo "<li role='presentation' title='{$title}'><a href='#sidebarTab-{$src}' data-toggle='tab'><i class='{$icon}'></i>&nbsp;"._ling($srcConfig['title'])."</a></li>";
        }
        echo "<li class='sidebarbtn button-last' role='presentation' title='Logout'><a href='"._link("logout")."'><i class='fa fa-power-off'></i>L</a></li>";
    ?>
</ul>
<div id="sidebarPane" class="tab-content noselect">
    <?php
        foreach($sidebars as $src=>$srcConfig) {
            if($src==$firstOne)
                echo "<div id='sidebarTab-{$src}' class='tab-pane active'>";
            else
                echo "<div id='sidebarTab-{$src}' class='tab-pane'>";
            $srcArr = explode(".", $srcConfig['src']);
            if(count($srcArr)>1)
                loadPluginComponent($srcArr[0], $srcArr[1]);
            else
                loadWidget($srcConfig['src']);
            echo "</div>";
        }
    ?>
</div>
        <?php
        break;
    case "accordion":
        ?>
<div class="panel-group" id="sidebarAccordion">
    <?php
        foreach($sidebars as $src=>$srcConfig) {
            $icon = $srcConfig['icon'];
            
            $panelClass = "";
            $openClassHeader = "";
            $openClassBody = "";
            $areaExpand = "";
            if($src==$firstOne) {
                $openClassHeader = " active";
                // $openClassBody = " in";
                $areaExpand = "aria-expanded='true'";
            }
            if(isset($srcConfig['position']) && $srcConfig['position']=="bottom") {
                $panelClass = "panel-bottom";
            }
            ?>
            <div class="panel panel-default <?=$panelClass?>">
                  <div class="panel-heading <?=$openClassHeader?>">
                    <h4 class="panel-title <?=$openClassHeader?> mainTitle" title='<?=_ling($srcConfig['title'])?>'>
                      <a data-toggle="collapse" data-parent="#sidebarAccordion" <?=$areaExpand?> href="#collapse-<?=$src?>"><?="<i class='{$icon}'></i>&nbsp;<span>"._ling($srcConfig['title'])."</span>"?></a>
                    </h4>
                  </div>
                  <div id="collapse-<?=$src?>" class="panel-collapse collapse <?=$openClassBody?> mainCollapse">
                    <div class="panel-body noselect">
                        <?php
                            $srcArr = explode(".", $srcConfig['src']);
                            if(count($srcArr)>1)
                                loadPluginComponent($srcArr[0], $srcArr[1]);
                            else
                                loadWidget($srcConfig['src']);
                        ?>
                    </div>
                  </div>
            </div>
            <?php
        }
        echo "<div class='sidebarbtn button-logout' role='presentation' title='Logout'><i class='fa fa-power-off'></i></div>";//<a href='"._link("logout")."'></a>
    ?>
</div>
<script>
$(function() {
    $("#sidebar .panel-heading.active").parent().find(".panel-collapse.mainCollapse").addClass("in");
    $("#sidebar .sidebarbtn.button-logout").click(a=> {
        var a = confirm("Do you want to logout this session?");
        if(a) {
            window.location = _link("logout.php");
        }
    });
});
</script>
        <?php
        break;
}
?>