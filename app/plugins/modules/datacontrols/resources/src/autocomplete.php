<?php
$arr=$dbCon->getTableList();
$tables="";
foreach($arr as $a) {
	$tables.="<option>$a</option>";
}
$lookups="<option></option>";
$layoutDir=$folders['APPROOT'].$folders['APPS_MISC_FOLDER']."lookups/";
if(is_dir($layoutDir)) {
	$lss=scandir($layoutDir);
	unset($lss[0]);unset($lss[1]);
	if(count($lss)>0) $lookups.="<optgroup label='Local'>";
	foreach($lss as $a) {
		$a=substr($a,0,strlen($a)-4);
		$lookups.="<option>$a</option>";
	}
	if(count($lss)>0) $lookups.="</optgroup>";
}
$layoutDir=ROOT.MISC_FOLDER."lookups/";
if(is_dir($layoutDir)) {
	$lss=scandir($layoutDir);
	unset($lss[0]);unset($lss[1]);
	if(count($lss)>0) $lookups.="<optgroup label='Global'>";
	foreach($lss as $a) {
		$a=substr($a,0,strlen($a)-4);
		$lookups.="<option>$a</option>";
	}
	if(count($lss)>0) $lookups.="</optgroup>";
}
?>
<div style='width:100%;'>
<table id=autocompleteEditor width=100% border=0 cellpadding=2 cellspacing=0>
	<tr>
		<th align=left width=150px>Autocomplete Type</th>
		<td>
			<select id=actype onchange="changeFFieldType(this);">
				<option value='services/?scmd=lookups&src='>Lookups</option>
				<option value='services/?scmd=autocomplete&src=sqltbl'>DataTable</option>
			</select>
		</td>
	</tr>
	<tr>
		<th align=left width=100px valign=top>Autocomplete Source</th>
		<td>
			<input name='src' class='alist' type=text style='border:1px solid #aaa;width:45%;height:20px;'/>
			<select class='alist' onchange="$(this).parent('td').find('input[name=src]').val(this.value);" style='width:45%;'>
				<?=$lookups?>
			</select>
			<select name='tbl' class='tablelist' style='display:none' disabled=disabled onchange="loadColumnList('select.columnlist',this.value,'select')">
				<?=$tables?>
			</select>
		</td>
	</tr>
	<tr class='columnlist' style='display:none'>
		<th align=left width=100px>View Columns</th>
		<td>
			<select name='cols' class='columnlist' style='display:none' disabled=disabled multiple>
			</select>
		</td>
	</tr>
	<tr class='columnlist' style='display:none'>
		<th align=left width=100px>FORM Fields</th>
		<td>
			<select name='form' class='columnlist' style='display:none' disabled=disabled multiple>
			</select>
		</td>
	</tr>
	<tr class='columnlist' style='display:none'>
		<th align=left width=100px>Where Condition</th>
		<td>
			<input name='where' class='columnlist' type=text disabled=disabled style='border:1px solid #aaa;width:92%;display:none' />
		</td>
	</tr>
</table>
</div>
<script language=javascript>
function changeFFieldType(e) {
	v=e.value;
	if(v=='services/?scmd=lookups') {
		$(e).parents('table').find('.tablelist').attr('disabled','disabled').hide();
		$(e).parents('table').find('.columnlist').attr('disabled','disabled').hide();
		$(e).parents('table').find('.alist').removeAttr('disabled').show();
	} else {
		$(e).parents('table').find('.alist').attr('disabled','disabled').hide();
		$(e).parents('table').find('.tablelist').removeAttr('disabled').show();
		$(e).parents('table').find('.columnlist').removeAttr('disabled').show();
	}
}
</script>
