<?php
if(!defined('ROOT')) exit('No direct script access allowed');

$data=_db($dbKey)->get_tablestatus();

// var_dump($data);

$colIndex=array_keys($data[0]);
$cols=[
		"Name"=>"Table Name",
		"Engine"=>"Engine",
		"Collation"=>"Collation",
		"Rows"=>"Rows",
		"Index_length"=>"Index",
		"Data_free"=>"Overhead",
		"Create_time"=>"Created",
		"Update_time"=>"Updated",
	];
?>
<div class='col-sm-12'>
	<table class='table table-bordered table-hover table-condensed'>
		<thead>
			<tr>
				<?php
					foreach ($cols as $c=>$ttl) {
						if(!in_array($c, $colIndex)) {
							unset($cols[$k]);
							continue;
						}
						$title=_ling($ttl);
						if($title==$ttl) $title=toTitle($ttl);//
						echo "<th class='$c'>$title</th>";
					}
				?>
				<th>-</th>
			</tr>
		</thead>
		<tbody>
			<?php
				foreach ($data as $key => $row) {
					echo "<tr data-key='{$key}'>";
					foreach ($cols as $c=>$k) {
						$value=$row[$c];
						if($c=="Engine" && !$value) {
						    $value="VIEW";
						}
						echo "<td class='{$c}'>{$value}</td>";
					}
					echo "<td data-tableid='{$row['Name']}'>";
					if($row['Engine']) {
    					echo "<i class='fa fa-pencil fa-2x' data-cmd='alterTable' title='Alter this table'></i>";
    					echo "<i class='fa fa-check fa-2x' data-cmd='optimiseTable' title='Optimise this table'></i>";
    					echo "<i class='fa fa-magic fa-2x' data-cmd='repairTable' title='Repair this table'></i>";
    					//echo "<i class='fa fa-times fa-2x text-danger pull-right' data-cmd='truncateTable' title='Empty this table'></i>";
    					//echo "<i class='fa fa-trash fa-2x text-danger pull-right' data-cmd='dropTable' title='Delete this table'></i>";    
					}
					echo "</td>";
					echo "</tr>";
				}
			?>
		</tbody>
	</table>
</div>