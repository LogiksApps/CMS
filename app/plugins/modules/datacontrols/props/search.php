<?php
loadModuleLib("search","editprops");
$s_engines=getSearchEngines();
?>
<table width=800px border=0 cellpadding=0 cellspacing=4>
	<tr>
		<td class=titlecol width=200px>Report Engine</td>
		<td class=valuecol>
			<select id=engine name=engine>
				<?php
					foreach($s_engines as $a=>$b) {
						echo "<option value='$a'>$b</option>";
					}
				?>
			</select>
		</td>
	</tr>
	<tr>
		<td class=titlecol width=150px>ActionLink</td><td class=valuecol><input id=actionlink name=actionlink type=text class='textfield' value='' /></td>
	</tr>
</table>
