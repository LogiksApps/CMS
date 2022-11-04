<?php
if(!defined('ROOT')) exit('No direct script access allowed');

//$dbProcedures = _db($dbKey)->_raw("SHOW PROCEDURE STATUS")->_GET();

$dbInfo=_db($dbKey)->get_dbinfo();
$dbStatus=_db($dbKey)->get_dbstatus();
$dbVariables = _db($dbKey)->_raw("SHOW variables")->_GET();
//var_dump(_db($dbKey)->dbParams("hooks"));

$dbParams = CMS_APPROOT."config/db_params.json";
if(file_exists($dbParams)) {
    $dbParams = json_decode(file_get_contents($dbParams), true);
    if(!$dbParams || !isset($dbParams["app"])) $dbParams = [];
    else {
        $dbParams = $dbParams["app"];
    }
} else {
    $dbParams = [];
}

$miscInfo = [
        "event_scheduler"=>"SHOW variables WHERE variable_name ='event_scheduler'"
    ];
foreach($miscInfo as $a=>$b) {
    $temp = _db($dbKey)->_raw($b)->_GET();
    
    if($temp && isset($temp[0]) && isset($temp[0]['Value'])) {
        $miscInfo[$a] = $temp[0]['Value'];
    } else {
        $miscInfo[$a] = "";
    }
}

$tables = _db($dbKey)->get_tablelist();
$finalTableFilterList = [];

foreach($tables as $tbl) {
    $ext = current(explode("_",$tbl));
    if(!isset($finalTableFilterList[$ext])) $finalTableFilterList[$ext] = 1;
    else $finalTableFilterList[$ext]++;
}
?>
<style>
.table hr {
    margin-top: 2px;
    margin-bottom: 2px;
}
</style>
<div class=''>
  <ul class="nav nav-tabs" role="tablist">
    <li role="presentation" class="active"><a href="#informations" aria-controls="informations" role="tab" data-toggle="tab">Information</a></li>
    <li role="presentation"><a href="#stats" aria-controls="stats" role="tab" data-toggle="tab">DB Status</a></li>
    <li role="presentation"><a href="#dbStatus" aria-controls="dbStatus" role="tab" data-toggle="tab">Table Status</a></li>
    <li role="presentation"><a href="#dbVariables" aria-controls="dbVariables" role="tab" data-toggle="tab">DB Variables</a></li>
    <li role="presentation"><a href="#dbMisc" aria-controls="dbMisc" role="tab" data-toggle="tab">Misc Info</a></li>
    
    <li role="presentation"><a href="#dbHooks" aria-controls="dbHooks" role="tab" data-toggle="tab">DBHooks</a></li>
    <li role="presentation"><a href="#dbMetaQuery" aria-controls="dbMetaQuery" role="tab" data-toggle="tab">Meta Query</a></li>
    
    <li role="presentation" style="float: right;"><a href="#dbSaveSchema" aria-controls="dbSaveSchema" role="tab" data-toggle="tab">Save Schema</a></li>
  </ul>
  <div class="tab-content">
    <div role="tabpanel" class="tab-pane active" id="informations">
    	<div class='col-xs-12' style='margin-top: 20px;'>
    		<table class="table table-bordered table-hover table-condensed">
				<tbody>
				<?php
					foreach ($dbInfo as $key => $value) {
						printf("<tr><td>%s</td><td>%s</td></tr>",toTitle(_ling($key)),$value);
					}
				?>
				</tbody>
			</table>
    	</div>
    </div>
    <div role="tabpanel" class="tab-pane" id="stats">
    	<div class='col-xs-12' style='margin-top: 20px;'>
    		<table class="table table-bordered table-hover table-condensed">
				<tbody>
				<?php
					$nx=1;
					foreach ($dbStatus as $key => $row) {
						if($row['Value']<=0) continue;
						printf("<tr><th>".($nx++)."</th><td>%s</td><td>%s</td></tr>",toTitle(_ling($row['Variable_name'])),$row['Value']);
					}
				?>
				</tbody>
				<tbody id='statusMore' class='collapse'>
				<tr><td colspan="10" style='background: #EDEDED;'></td></tr>
				<?php
					foreach ($dbStatus as $key => $row) {
						if($row['Value']>0) continue;
						printf("<tr><th>".($nx++)."</th><td>%s</td><td>%s</td></tr>",toTitle(_ling($row['Variable_name'])),$row['Value']);
					}
				?>
				</tbody>
				<tfoot>
					<tr>
						<th colspan=10 style="text-align: center;">
							<a href='#' data-toggle="collapse" data-target="#statusMore">TOGGLE MORE</a>
						</th>
					</tr>
				</tfoot>
			</table>
    	</div>
    </div>
    <div role="tabpanel" class="tab-pane" id="dbStatus">
    	
    </div>
    <div role="tabpanel" class="tab-pane" id="dbVariables">
        <div class='col-xs-12' style='margin-top: 20px;'>
    		<table class="table table-bordered table-hover table-condensed">
				<tbody>
				<?php
				    if($dbVariables) {
				        foreach ($dbVariables as $key => $row) {
    					    printf("<tr><td>%s</td><td>%s</td></tr>",$row['Variable_name'], $row['Value']);
    					}
				    }
				?>
				</tbody>
			</table>
    	</div>
    </div>
    <div role="tabpanel" class="tab-pane" id="dbMisc">
        <div class='col-xs-12' style='margin-top: 20px;'>
    		<table class="table table-bordered table-hover table-condensed">
				<tbody>
				<?php
					foreach ($miscInfo as $key => $value) {
						printf("<tr><td>%s</td><td>%s</td></tr>",toTitle(_ling($key)),$value);
					}
				?>
				</tbody>
			</table>
    	</div>
    </div>
    <div role="tabpanel" class="tab-pane" id="dbSaveSchema">
        <div class='col-xs-12' style='margin-top: 20px;'>
    		<div class='col-md-6'>
    		    <label>Select Table Keys that needs to have its data saved?</label>
    		    <select class='form-control select' id='save_schema_db_filter' multiple>
    		        <?php
    		            foreach($finalTableFilterList as $tbl=>$count) {
    		              //  if($tbl=="do" || $tbl=="data")
    		              //      echo "<option value='{$tbl}' selected>{$tbl} ({$count})</option>";
    		              //  else
		                    echo "<option value='{$tbl}'>{$tbl} ({$count})</option>";
    		            }
    		            
    		        ?>
    		    </select>
    		    <hr>
    		    <div class='text-center'>
    		        <button class='btn btn-default save_schema2' title='Save Schema to SQL Folder usefull for migration scripts'><i class='fa fa-file-export'></i> Save Schema</button>
    		    </div>
    		</div>
    	</div>
    </div>
    <div role="tabpanel" class="tab-pane" id="dbHooks">
        <div class='col-xs-12' style='margin-top: 20px;'>
            <label>DBHooks - Special Functions, Methods, Modules that get loaded or called when a query of type INSERT, UPDATE, SELECT, etc are executed</label>
            <table class="table table-bordered table-hover table-condensed">
    		    <thead>
    		        <tr>
    		            <th>SL#</th>
    		            <th>App Status</th>
    		            <th>Query Type</th>
    		            <th>Caller Type</th>
    		            <th>Object/Function</th>
    		        </tr>
    		    </thead>
				<tbody>
				<?php
				    $counter = 1;
					foreach ($dbParams as $key => $dbOpts) {
					    if(!isset($dbOpts['hooks'])) continue;
					    foreach($dbOpts['hooks'] as $key1 => $dbOpts1) {
					        foreach($dbOpts1 as $key2 => $dbOpts2) {
					            if(is_array($dbOpts2)) $dbOpts2 = implode(",", $dbOpts2);
					            
					            printf("<tr><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td></tr>",$counter,strtoupper(_ling($key)), strtoupper($key1), strtoupper($key2), $dbOpts2);
					            $counter++;
					        }
					    }
					}
				?>
				</tbody>
			</table>
			<p>You can edit db_params.json under config folder to add more DB Hooks</p>
			<div class='text-right'>
			    <button class='btn btn-primary' onclick='openDBParamsFile(this)'>Open File</button>
			</div>
    	</div>
    </div>
    <div role="tabpanel" class="tab-pane" id="dbMetaQuery">
        <div class='col-xs-12' style='margin-top: 20px;'>
            <label>DB MetaQuery are special sql queries that get executed when a certain activity occurs on a certain table.</label>
            <table class="table table-bordered table-hover table-condensed">
    		    <thead>
    		        <tr>
    		            <th>SL#</th>
    		            <th>App Status</th>
    		            <th>Table</th>
    		            <th>Query Type</th>
    		            <th>Query</th>
    		        </tr>
    		    </thead>
				<tbody>
				<?php
				    $counter = 1;
    				foreach ($dbParams as $key => $dbOpts) {
					    if(!isset($dbOpts['metaquery'])) continue;
					    foreach($dbOpts['metaquery'] as $key1 => $dbOpts1) {
					        foreach($dbOpts1 as $key2 => $dbOpts2) {
					            //printArray([$key, $key1, $key2, $dbOpts2]);
					            if(is_array($dbOpts2)) $dbOpts2 = implode("<hr>", $dbOpts2);
					            
					            printf("<tr><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td></tr>",$counter,strtoupper(_ling($key)), strtoupper($key1), strtoupper($key2), $dbOpts2);
					            $counter++;
					        }
					    }
					}
				?>
				</tbody>
			</table>
			<p>You can edit db_params.json under config folder to add more Meta Queries</p>
			<div class='text-right'>
			    <button class='btn btn-primary' onclick='openDBParamsFile(this)'>Open File</button>
			</div>
    	</div>
    </div>
  </div>
</div>
<script>
$(function() {
    $(".save_schema2").click(saveSchema2);
	loadDBStatus();
});
function loadDBStatus() {
  $("#dbStatus").load(_service("dbEdit","panel")+"&dkey="+dkey+"&panel=status");
}
function saveSchema2() {
    var filters = $("#save_schema_db_filter").val();
    if(filters==null) filters = [];
    processAJAXPostQuery(_service("dbEdit","dumpSchema")+"&dkey="+dkey, "filter="+filters.join(","),function(txt) {
		lgksAlert(txt);
	});
}
function openDBParamsFile() {
    lx=_link("modules/cmsEditor")+"&type=autocreate&src=%2Fconfig%2Fdb_params.json";
	parent.openLinkFrame("db_params.json",lx,true);
}
</script>