<?php
loadModuleLib("forms","editprops");
$form_engines=getFormEngines();
$form_layouts=getFormLayouts();
$toolbtns=getToolButtons();
$def_mode=getFormModes();
$adapter=getFormAdapters();
$submitAction=getSubmitAction();

$tableList="";
$arr=$dbCon->getTableList();
foreach($arr as $a) {
	$t=$a;
	if(!(isset($_REQUEST["system"]) && $_REQUEST["system"]=="true")) {
		if(strpos($a,$GLOBALS['DBCONFIG']["DB_APPS"]."_")===0 || strpos($a,$GLOBALS['DBCONFIG']["DB_SYSTEM"]."_")===0) {
			continue;
		}
	}
	$tableList.="<option value='$a'>$t</option>";
}
?>
<table width=850px border=0 cellpadding=0 cellspacing=10>
	<tr>
		<td class=titlecol width=200px>Form Engine</td>
		<td class=valuecol >
			<select id=engine name=engine>
				<?php
					foreach($form_engines as $a=>$b) {
						echo "<option value='$a'>$b</option>";
					}
				?>
			</select>
		</td>
	</tr>
	<tr>
		<td class=titlecol width=150px>Form Layout</td>
		<td class=valuecol>
			<select id=layout name=layout>
				<?php
					foreach($form_layouts as $a=>$b) {
						echo "<option value='$a'>$b</option>";
					}
				?>
			</select>
		</td>
	</tr>
	<tr>
		<td class=titlecol width=150px>Form Mode</td>
		<td class=valuecol>
			<select id=def_mode name=def_mode>
				<?php
					foreach($def_mode as $a=>$b) {
						echo "<option value='$a'>$b</option>";
					}
				?>
			</select>
		</td>
	</tr>
	<tr>
		<td class=titlecol width=150px>Form Adapter</td>
		<td class=valuecol>
			<select id=adapter name=adapter>
				<?php
					foreach($adapter as $a=>$b) {
						echo "<option value='$a'>$b</option>";
					}
				?>
			</select>
		</td>
	</tr>
	<tr>
		<td class=titlecol width=150px>On Submit Action</td>
		<td class=valuecol><input id=submit_action name=submit_action type=text class='textfield' value='reload' /></td>
		<td class=supportcol>
			<select id=submit_action_selector onchange="$('#submit_action').val($(this).val());">
				<?php
					foreach($submitAction as $a=>$b) {
						echo "<option value='$a'>$b</option>";
					}
				?>
			</select>
		</td>
	</tr>
	<tr>
		<td class=titlecol width=150px>Submit To DBTable</td>
		<td class=valuecol>
			<select id=submit_table name=submit_table class="dbtable" for=submit_wherecol onchange="loadColumnList('.columnlist',$(this).val());loadColumnList('.table_columns',$(this).val(),'ul');">
				<?=$tableList?>
			</select>
		</td>
		<td>Please set this property in the Form Designer.</td>
	</tr>
	<tr>
		<td class=titlecol width=150px>Submit Where (Column)</td>
		<td class=valuecol>
			<select id=submit_wherecol class='columnlist' name=submit_wherecol >
				<option value="">Select One Column</option>
			</select>
		</td>
		<td>
			<div><input disabled type=checkbox onchange="if($(this).is(':checked')) loadSysTableList('.dbtable'); else loadTableList('.dbtable');" />Show System Tables Also</div>
		</td>
	</tr>
</table>
