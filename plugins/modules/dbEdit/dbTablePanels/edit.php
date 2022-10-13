<?php
if(!defined('ROOT')) exit('No direct script access allowed');

$src=explode("/", $_GET['src']);
if(count($src)==0) {
	$src[1]=$src[0];
	$src[0]="tables";
}

$columnDefination=[];
$data=[];
switch ($src[0]) {
	case 'tables':
		$columnDefination=_db($dbKey)->get_defination($src[1]);

		$data=_db($dbKey)->_selectQ($src[1],"*",["id"=>$_GET['refid']])->_get();
		if(isset($data[0])) $data=$data[0];
		else $data=[];
		break;
	
	default:
		echo "<h5 align=center>Sorry, viewing data for type <b>{$src[0]}</b> is not supported yet</h5>";
		return;
		break;
}

//var_dump($data);
//var_dump($columnDefination);
?>
<div class='col-xx-12' style='max-width: 1200px;width: 80%;margin: auto;margin-top: 20px;border: 1px solid #AAA;padding: 10px;'>
    <h4>Editing Record : #<?=$_GET['refid']?></h4>
    <hr>
<form id='dataInsertForm'>
	<?php
		$autoFillColumns=array_keys($autoFillColumns);
		foreach ($columnDefination as $column) {
			if(in_array($column[0], $autoFillColumns)) {
				continue;
			}

			$name=strtoupper($column[0]);
			$id=md5($name);
			$required="";
			

			if($column[5]=="auto_increment" && $column[3]=="PRI") {
				continue;
			}
			if($column[2]=="NO" && !in_array($column[0],['created_by','created_on','edited_by','edited_on',])) {
				$required="required";
			}
			
			$html="<div class='form-group row {$required}'>";
			if($required) {
				$html.="<label for='{$id}' class='col-sm-3 form-control-label'>{$name} <citie>*</citie></label>";
			} else {
				$html.="<label for='{$id}' class='col-sm-3 form-control-label'>{$name} </label>";
			}
			$html.="<div class='col-sm-9'>";
			
			if(isset($data[$column[0]])) {
				$column[4]=$data[$column[0]];
			}
			$html.=getInputBlock($name,$required,$column);

			$html.="</div>";
			$html.="</div>";

			echo $html;
		}
	?>
	<hr>
	<div class='text-center'>
		<button type='reset' class='btn btn-danger'>Reset</button>
		<button type='submit' class='btn btn-success'>Submit</button>
	</div>
</form>
</div>
<script>
$(function() {
	$("#dataInsertForm").submit(function() {
		if($("#dataInsertForm").valid()) {
			q=$("#dataInsertForm").serialize();
			lx=_service("dbEdit","updateRecord")+"&dkey="+dkey+"&src=<?=$_GET['src']?>&refid=<?=$_GET['refid']?>";
			processAJAXPostQuery(lx,q,function(txt) {
				if(txt=="success") {
					loadDataContent("browse");
					lgksToast("Data updated successfully");
				} else {
					lgksToast(txt);
				}
			});
		}
		return false;
	});
});
</script>