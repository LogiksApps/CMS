<?php
$arr=$dbCon->getTableList();
$tables="";
foreach($arr as $a) {
	$cols=$dbCon->getColumnList($a);
	$cols=array_keys($cols);
	if(in_array("file_data",$cols) && in_array("file_type",$cols)) {
		$tables.="<option>$a</option>";
	}
}
?>
<table width=100% border=0 cellpadding=2 cellspacing=0>
	<tr>
		<th align=left width=100px>Source Type</th>
		<td>
			<select onchange="changeFFieldType(this);">
				<option value='fs#'>Files</option>
				<option value='db#'>DataTable</option>
			</select>
		</td>
	</tr>
	<tr>
		<th align=left width=100px>Source Type</th>
		<td>
			<input class='fslist' type=text style='border:1px solid #aaa;width:92%;'/>
			<select class='tablelist' style='display:none' disabled=disabled>
				<?=$tables?>
			</select>
		</td>
	</tr>
</table>
<script language=javascript>
function changeFFieldType(e) {
	v=e.value;
	if(v=='fs#') {
		$(e).parents('table').find('.tablelist').attr('disabled','disabled').hide();
		$(e).parents('table').find('.fslist').removeAttr('disabled').show();
	} else {
		$(e).parents('table').find('.fslist').attr('disabled','disabled').hide();
		$(e).parents('table').find('.tablelist').removeAttr('disabled').show();
	}
}
</script>
