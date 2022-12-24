<?php
if(!defined('ROOT')) exit('No direct script access allowed');
//Main Sidebar

$sidebars = [
			    "studio"=> [
			        "title"=> "CMS",
			        "icon"=> "fa fa-cubes fa-fw",
			        "src"=> "sidebarMenu"
			    ],
			    "apps"=> [
			        "title"=> "APPS",
			        "icon"=> "fa fa-object-group fa-fw",
			        "src"=> "sidebarApps"    
			    ],
			    "files"=> [
			        "title"=> "FILES",
			        "icon"=> "fa fa-folder fa-fw",
			        "src"=> "sidebarFiles"
			    ]
			];
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
}

// printArray($sidebars);
$firstOne = array_keys($sidebars)[0];
?>
<ul id="sidebarTab" class="nav nav-tabs nav-justified <?=(count($sidebars)==1)?"hidden d-none":""?>" data-tabs="tabs">
    <?php
        foreach($sidebars as $src=>$srcConfig) {
            $icon = $srcConfig['icon'];
            if($src==$firstOne)
                echo "<li class='active' role='presentation'><a href='#sidebarTab-{$src}' data-toggle='tab'><i class='{$icon}'></i>&nbsp;"._ling($srcConfig['title'])."</a></li>";
            else
                echo "<li role='presentation'><a href='#sidebarTab-{$src}' data-toggle='tab'><i class='{$icon}'></i>&nbsp;"._ling($srcConfig['title'])."</a></li>";
        }
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
