<?php
if(!defined('ROOT')) exit('No direct script access allowed');

$autoFillColumns=[
		"userid"=>$_SESSION['SESS_USER_ID'],
		"edited_by"=>$_SESSION['SESS_USER_ID'],
		"edited_on"=>date("Y-m-d H:i:s"),
		"doe"=>date("Y-m-d"),
		"dtoe"=>date("Y-m-d H:i:s"),
		"client_ip"=>$_SERVER['REMOTE_ADDR'],
	];

$insertFillColumns=[
		"created_by"=>$_SESSION['SESS_USER_ID'],
		"created_on"=>date("Y-m-d H:i:s"),
		"doc"=>date("Y-m-d"),
		"dtoc"=>date("Y-m-d H:i:s"),
	];
	
$arrCmds=array(
		"checktable"=>array("CHECK TABLE %s","single_run"),
		"analyzetable"=>array("ANALYZE TABLE %s","single_run"),
		"repairtable"=>array("REPAIR TABLE %s","single_run"),
		"optimizetable"=>array("OPTIMIZE TABLE %s","single_run"),
		//"flushtable"=>array("",""),
		"emptytable"=>array("TRUNCATE TABLE %s","multi_run"),
		"droptable"=>array("DROP TABLE %s","multi_run"),
		
		"exporttable"=>array("exportTable","func"),
		"importtable"=>array("importTable","func"),
		"templatetable"=>array("templatizeTable","func"),
	);


function getInputBlock($name,$required,$column) {
	$type="text";
	$defaultValue="";
	$id=md5($name);

	if($column[4]) {
		$defaultValue=$column[4];
	}
    $placeHolderText = "Enter ".toTitle(strtolower($name));
    
	$fieldType=strtolower($column[1]);
	if($fieldType=="date") {
		return "<input type='date' class='form-control {$required}' id='{$id}' name='{$column[0]}' placeholder='{$placeHolderText}' value='{$defaultValue}' />";
	} elseif($fieldType=="time") {
		return "<input type='time' class='form-control {$required}' id='{$id}' name='{$column[0]}' placeholder='{$placeHolderText}' value='{$defaultValue}' />";
	} elseif($fieldType=="datetime" || $fieldType=="timestamp") {
		return "<input type='datetime-local' class='form-control {$required}' id='{$id}' name='{$column[0]}' placeholder='{$placeHolderText}' value='{$defaultValue}' />";
	} elseif(substr($fieldType, 0,3)=="int" || strpos($fieldType, "int")>1) {
		//float,decimal,real,double
		return "<input type='number' class='form-control {$required}' id='{$id}' name='{$column[0]}' placeholder='{$placeHolderText}' value='{$defaultValue}' />";
	} elseif(substr($fieldType, 0,4)=="blob" || strpos($fieldType, "blob")>1) {
		return "<textarea class='form-control {$required}' id='{$id}' name='{$column[0]}' placeholder='{$placeHolderText}' value='{$defaultValue}' ></textarea>";
	} elseif(substr($fieldType, 0,3)=="bit") {
		if($defaultValue) {
			return "<label><input type='radio' value='0' name='{$column[0]}' />No</label> &nbsp;&nbsp;<label><input type='radio' value='1' name='{$column[0]}' checked />Yes</label>";
		} else {
			return "<label><input type='radio' value='0' name='{$column[0]}' checked />No</label> &nbsp;&nbsp;<label><input type='radio' value='1' name='{$column[0]}' />Yes</label>";
		}
	} elseif(substr($fieldType, 0,4)=="enum" || substr($fieldType, 0,3)=="set") {
		$dx=trim(substr($fieldType, strpos($fieldType, "(")));
		$dx=substr($dx, 1,strlen($dx)-2);
		$dx=str_replace("'", "", $dx);
		$dx=explode(",", $dx);
		$htmlDX="<select class='form-control {$required}' id='{$id}' name='{$column[0]}' placeholder='{$placeHolderText}' value='{$defaultValue}' data-selected='{$defaultValue}'>";
		foreach ($dx as $vx) {
			$htmlDX.="<option value='{$vx}'>".toTitle(_ling($vx))."</option>";
		}
		$htmlDX.="</select>";
		return $htmlDX;
	}

	return "<input type='{$type}' class='form-control {$required}' id='{$id}' name='{$column[0]}' placeholder='{$placeHolderText}' value='{$defaultValue}' />";
}

function printDataInTable($data,$cols=false,$actionCol=false) {
	if(is_string($data)) {
		echo $data;
		return;
	} elseif($data==null) {
		echo "No Data Returned By Query";
		return;
	}
	if(count($data)<=0) return "";
	$indexCol=array_keys($data[0])[0];
	// table-condensed
?>
<table class='table table-bordered table-hover'>
	<thead>
		<tr>
			<?php
				if(!$cols) {
					$cols=array_keys($data[0]);
				}
				
				foreach ($cols as $c) {
					$title=_ling("DBTABLE:$c");
					if($title=="DBTABLE:$c") $title=($c);//toTitle
					echo "<th class='$c'>$title</th>";
				}
				if($actionCol) {
					echo "<th class='action' width=80px></th>";
				}
			?>
		</tr>
	</thead>
	<tbody>
		<?php
			foreach ($data as $key => $row) {
				echo "<tr data-key='{$row[$indexCol]}' data-col='{$indexCol}'>";
				foreach ($row as $col => $value) {
					echo "<td class='{$col}'>{$value}</td>";
				}
				if($actionCol) {
					echo "<td class='action'>";
					if(is_array($actionCol)) {
						foreach ($actionCol as $cmd => $icon) {
							echo "<i class='{$icon}' cmd='$cmd'></i>";
						}
					}
					echo "</td>";
				}
				echo "</tr>";
			}
		?>
	</tbody>
</table>
<?php
}

function exportTable($tbl) {
	ob_start("ob_gzhandler");
	header('Content-type: text/comma-separated-values');
	header("Content-Disposition: attachment; filename=db_{$tbl}_".date('YmdHis').'.csv');
	CSVToDBExport::export($tbl);
	ob_end_flush();
}
function importTable($tbl) {
	$file=$_FILES["csvfile"]['tmp_name'];
	$tbl=$_POST["dbtbl"];
	if(file_exists($file)) {
		$a=CSVToDBImport::importCSV($tbl,$file);
		if(is_array($a) && isset($a["error"])) {
			echo $a["error"];
		} else {
			echo "<script>parent.lgksAlert('Data Imported Into $table Successfully');parent.closeCSVDlg();</script>";
		}
	}
}
function templatizeTable($tbl) {
	ob_start("ob_gzhandler");
	header('Content-type: text/comma-separated-values');
	header("Content-Disposition: attachment; filename=db_{$tbl}_".date('YmdHis').'.csv');
	CSVToDBExport::exportHeader($tbl);
	ob_end_flush();
}
?>