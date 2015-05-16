<?php
$arr=$dbCon->getTableList();
$tables="";
foreach($arr as $a) {
	$cols=$dbCon->getColumnList($a);
	$cols=array_keys($cols);
	if(in_array("image_data",$cols) && in_array("image_type",$cols)) {
		$tables.="<option>$a</option>";
	}
}
?>
<h4>Please select a datatable for holding the image</h4>
<select class='tablelist'>
	<?=$tables?>
</select>
