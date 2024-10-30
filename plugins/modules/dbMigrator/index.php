<?php
if(!defined('ROOT')) exit('No direct script access allowed');

if(!checkRootAccess()) {
    return;
}
$moduleList = ["application"];

$fs = ["applications"];
if(file_exists(CMS_APPROOT."plugins/modules/")) {
    $fs1 = scandir(CMS_APPROOT."plugins/modules/");
    $fs1 = array_slice($fs1, 2);
}
else $fs1 = [];

if(file_exists(CMS_APPROOT."pluginsDev/modules/")) {
    $fs2 = scandir(CMS_APPROOT."pluginsDev/modules/");
    $fs2 = array_slice($fs2, 2);
}
else $fs2 = [];

foreach($fs1 as $f) {
    $f1 = CMS_APPROOT."plugins/modules/{$f}/.install/sql/schema.json";
    if(file_exists($f1)) $moduleList[] = $f;
    
}
foreach($fs2 as $f) {
    $f1 = CMS_APPROOT."pluginsDev/modules/{$f}/.install/sql/schema.json";
    if(file_exists($f1)) $moduleList[] = $f;
}
//$finalTableFilterList = ["asd"=>2];
?>
<div class='container'>
    <h3 align=center class=''>Migrate Database to latest updates</h3>
    <div class=''>
        <div class='col-xs-12' style='margin-top: 20px;'>
    		<div class='col-md-3'>
    		    <label>Select Core/Module To Migrate</label>
    		    <select class='form-control select' id='target' multiple style="height: 70%;">
    		        <?php
    		            foreach($moduleList as $module) {
    		              //  if($tbl=="do" || $tbl=="data")
    		              //      echo "<option value='{$tbl}' selected>{$tbl} ({$count})</option>";
    		              //  else
		                  //  echo "<option value='{$tbl}'>{$tbl} ({$count})</option>";
		                  echo "<option value='{$module}'>{$module}</option>";
    		            }
    		            
    		        ?>
    		    </select>
    		</div>
    		<div class='col-md-3'>
    		    <label>Select Target Version</label>
    		    <select class='form-control select' id='version' multiple style="height: 70%;">
    		        <option value=''>Select Application/Module</option>
    		    </select>
    		</div>
    		<div class='col-md-6'>
    		    <div class='row'>
    		        <p class='alert alert-danger' style='padding: 10px 30px;'>This process will add/remove columns from the existing system based on selected migration rules. <b>This is not reversable.<br><br>Data migration is not supported yet.</b></p>
    		    </div>
    		    <br><br><br><br>
    		    <div class='text-center'>
    		        <button class='btn btn-default reload_page'><i class='fa fa-refresh'></i> Reload</button>
    		        <button class='btn btn-danger start_migration'><i class='fa fa-file-import'></i> Migrate</button>
    		    </div>
    		</div>
    	</div>
    </div>
</div>
<script>
$(function() {
    $(".reload_page").click(function() {
        window.location.reload();
    });
    $(".start_migration").click(startMigration);
    
    $("#target").change(loadAppVersions);
});
function loadAppVersions() {
    $("#version").html("<option value=''>Loading ...</option>");
    processAJAXPostQuery(_service("dbMigrator", "list_versions"),"mod="+$("#target").val(), function(data) {
        $("#version").html("");
        $.each(data.Data, function(k,v) {
            $("#version").append(`<option value='${v}'>${v}</option>`);
        });
    }, "json");
}
function startMigration() {
    if($("#target").val()==null) {
        lgksAlert("Please select which module/application do you want to migrate?");
        return;
    }
    if($("#version").val()==null) {
        lgksAlert("Target Version to Migrate is required to proceed, if not found, Migration is not supported for selected module/application");
        return;
    }
    lgksConfirm("Are you sure about starting Migration Process?<br><b>This is not reversible</b>", "Start Migration", function(ans) {
        if(ans) {
            lgksLoader("Running Migration, This may take some time");
            processAJAXPostQuery(_service("dbMigrator", "migrate"),"mod="+$("#target").val()+"&vers="+$("#version").val(), function(data) {
                lgksLoaderHide();
                lgksAlert(data.Data);
            }, "json");
        }
    });
}
</script>