<?php
loadModuleLib("reports","editprops");
$rpt_engines=getReportEngines();
$toolbtns=getToolButtons();
?>
<table width=800px border=0 cellpadding=0 cellspacing=4>
	<tr>
		<td class=titlecol width=200px>Report Engine</td>
		<td class=valuecol>
			<select id=engine name=engine>
				<?php
					foreach($rpt_engines as $a=>$b) {
						echo "<option value='$a'>$b</option>";
					}
				?>
			</select>
		</td>
		<td>This is how the Report Looks Like.</td>
	</tr>
	<tr>
		<td class=titlecol width=150px>ActionLink</td><td class=valuecol><input id=actionlink name=actionlink type=text class='textfield' value='' /></td>
	</tr>
</table>
