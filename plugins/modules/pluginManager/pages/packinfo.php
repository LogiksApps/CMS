<?php
if(!defined('ROOT')) exit('No direct script access allowed');

$pluginList=fetch_package_list($_REQUEST['type']);

if(!isset($pluginList[$_POST['packid']])) {
    echo "<h2 align=center>Package/Plugin Not Found</h2>";
    return;
}

$package=$pluginList[$_POST['packid']];
unset($package['packid']);

foreach($package as $a=>$p) {
    if((substr($a,0,3)=="is_" || substr($a,0,4)=="has_")) {
        unset($package[$a]);
    }
}
$logiksPackageInfo = $package['logiksinfo'];
unset($package['logiksinfo']);

$repoInfo = [];
foreach($logiksPackageInfo as $a=>$b) {
    if(!is_array($b)) {
        $repoInfo[$a]=$b;
    }
}

$packageMoreInfo = package_more_info($package);
$widgets = $packageMoreInfo['widgets'];
$dashlets = $packageMoreInfo['dashlets'];
$tables = $packageMoreInfo['tables'];

$packageData = $package;
unset($packageData['fullpath']);

if(isset($packageData['error_msg'])) {
    $packageData['error_msg'] = "<p style='font-weight:bold;color:red;'>{$packageData['error_msg']}</p>";
}

// printArray($package);
?>
<div class='container-fluid'>
    <ul class="nav nav-tabs">
      <li class="active"><a data-toggle="tab" href="#menu0">Info</a></li>
      <li><a data-toggle="tab" href="#menu1">Repo</a></li>
      <li><a data-toggle="tab" href="#menu2">Dependencies</a></li>
      <li><a data-toggle="tab" href="#menu3">Widgets</a></li>
      <li><a data-toggle="tab" href="#menu4">Dashlets</a></li>
      <li><a data-toggle="tab" href="#menu5">Tables</a></li>
    </ul>
    
    <div class="tab-content">
        <div id="menu0" class="tab-pane fade in active">
            <div class='table-responsive'>
                <?=arrayToHTML($packageData,"table","table table-striped table-bordered");?>
            </div>
            <div class='text-center'>
                <?php
                    if($package['status']!="ARCHIVE") {
                        echo "<button class='btn btn-info pull-right packageActionButton' cmd='reinstallPackage' data-packid='{$_POST['packid']}'>Reinstall</button>";
                    }
                ?>
                <!--<button class='btn btn-danger pull-left' >Redownload</button>-->
            </div>
        </div>
        <div id="menu1" class="tab-pane fade">
            <div class='table-responsive'>
                <?=arrayToHTML($repoInfo,"table","table table-striped table-bordered");?>
            </div>
            <?php
                if(isset($logiksPackageInfo['authors'])) {
                    echo "<h4>Authors</h4><div class='table-responsive'>";
                    echo arrayToHTML($logiksPackageInfo['authors'],"table","table table-striped table-bordered");
                    echo "</div>";
                }
            ?>
            <?php
                if(isset($logiksPackageInfo['repository'])) {
                    echo "<h4>Repository</h4><div class='table-responsive'>";
                    echo arrayToHTML($logiksPackageInfo['repository'],"table","table table-striped table-bordered");
                    echo "</div>";
                }
            ?>
        </div>
        <div id="menu2" class="tab-pane fade">
            <?php
                if(isset($logiksPackageInfo['dependencies'])) {
                    $dependencies = $logiksPackageInfo['dependencies'];
                    echo "<div class='table-responsive'>";
                    echo arrayToHTML($dependencies,"table","table table-striped table-bordered");
                    echo "</div>";
                }
            ?>
        </div>
        <div id="menu3" class="tab-pane fade">
            <div class='table-responsive'>
                <?=arrayToHTML($widgets,"table","table table-striped table-bordered");?>
            </div>
        </div>
        <div id="menu4" class="tab-pane fade">
            <div class='table-responsive'>
                <?=arrayToHTML($dashlets,"table","table table-striped table-bordered");?>
            </div>
        </div>
        <div id="menu5" class="tab-pane fade">
            <div class='table-responsive'>
                <table class="table table-striped table-bordered" width="100%">
                    <thead>
                        <tr>
                            <th>Table</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            foreach($tables as $a=>$b) {
                                echo "<tr><td>{$a}</td><td>{$b}</td></tr>";
                            }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>