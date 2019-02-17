<?php
if(!defined('ROOT')) exit('No direct script access allowed');

$dbInfo=_db($dbKey)->get_dbinfo();
$dbStatus=_db($dbKey)->get_dbstatus();
// $dbProcedures = _db($dbKey)->_raw("SHOW PROCEDURE STATUS")->_GET();
//var_dump($dbStatus);
?>
<div class=''>
  <ul class="nav nav-tabs" role="tablist">
    <li role="presentation" class="active"><a href="#informations" aria-controls="informations" role="tab" data-toggle="tab">Information</a></li>
    <li role="presentation"><a href="#stats" aria-controls="stats" role="tab" data-toggle="tab">DB Status</a></li>
    <li role="presentation"><a href="#dbStatus" aria-controls="dbStatus" role="tab" data-toggle="tab">Table Status</a></li>
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
    
  </div>
</div>
<script>
$(function() {
	loadDBStatus();
});
function loadDBStatus() {
  $("#dbStatus").load(_service("dbEdit","panel")+"&panel=status");
}
</script>