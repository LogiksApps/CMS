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
			    <th>-</th>
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
					echo "<td>";
					if($row['Engine']) {
					    echo "<i class='fa fa-trash text-danger pull-right' data-cmd='dropTable' data-refid='{$row['Name']}' data-type='tables' title='Delete this table'></i>";  
					} else {
					    echo "<i class='fa fa-trash text-danger pull-right' data-cmd='dropView' data-refid='{$row['Name']}' data-type='views' title='Delete this view'></i>";    
					}
					echo "</td>";
					foreach ($cols as $c=>$k) {
						$value=$row[$c];
						if($c=="Engine" && !$value) {
						    $value="VIEW";
						}
						echo "<td class='{$c}'>{$value}</td>";
					}
					echo "<td class='actionbtns' data-refid='{$row['Name']}'>";
					if($row['Engine']) {
            echo "<i class='fa fa-pencil fa-pencil-alt' data-cmd='alterTable' data-refid='{$row['Name']}' data-type='tables' title='Alter this table'></i>";
            
            echo "<i class='fa fa-check' data-cmd='optimiseTable' data-refid='{$row['Name']}' data-type='tables' title='Optimise this table'></i>";
            echo "<i class='fa fa-magic' data-cmd='repairTable' data-refid='{$row['Name']}' data-type='tables' title='Repair this table'></i>";
            
            echo "<i class='fa fa-ticket fa-ticket-alt' data-cmd='analyzeTable' data-refid='{$row['Name']}' data-type='tables' title='Analyze this table'></i>";
            echo "<i class='fa fa-hashtag' data-cmd='checksum' data-refid='{$row['Name']}' data-type='tables' title='Checksum this table'></i>";

            echo "<i class='fa fa-ban text-danger pull-right' data-cmd='truncateTable' data-refid='{$row['Name']}' data-type='tables' title='Empty this table'></i>";
					} else {
            echo "<i class='fa fa-pencil fa-pencil-alt' data-cmd='alterTable' data-refid='{$row['Name']}' data-type='views' title='Alter this view'></i>";
          }
					echo "</td>";
					echo "</tr>";
				}
			?>
		</tbody>
	</table>
</div>
<style>
  .actionbtns .fa {
    font-size: 16px;
  }
</style>
<script>
$(".actionbtns i[data-cmd]").click(function() {
  cmd = $(this).data("cmd");
  refid = $(this).data("refid");
  type = $(this).data("type");
//   alert(cmd+" "+refid);
  
  switch(cmd) {
    case "alterTable":
      openTable(type+"/"+refid);
      break;
   
    case "dropTable":
      lgksConfirm("Do you want to delete the table ","Delete Table", function(a) {
            if(a) {
                lx=_service("dbEdit","cmd")+"&dkey="+dkey+"&src="+cmd;
                processAJAXPostQuery(lx,"&ref="+refid, function(data) {
                  lgksAlert(data);
                  loadDBStatus();
                });
              }
          });
      break;
    case "truncateTable":
      lgksConfirm("Do you want to truncate/empty the table ","Truncate Table", function(a) {
            if(a) {
                lx=_service("dbEdit","cmd")+"&dkey="+dkey+"&src="+cmd;
                processAJAXPostQuery(lx,"&ref="+refid, function(data) {
                  lgksAlert(data);
                  loadDBStatus();
                });
            }
          });
      break;
    
    case "dropView":
      lgksConfirm("Do you want to delete the view ","Delete View", function(a) {
          if(a) {
              lx=_service("dbEdit","cmd")+"&dkey="+dkey+"&src="+cmd;
              processAJAXPostQuery(lx,"&ref="+refid, function(data) {
                lgksAlert(data);
                loadDBStatus();
              });
          }
        });
      break;
    
    default:
      lx=_service("dbEdit","cmd")+"&dkey="+dkey+"&src="+cmd;
	    processAJAXPostQuery(lx,"&ref="+refid, function(data) {
        lgksAlert(data);
      });
  }
});
</script>