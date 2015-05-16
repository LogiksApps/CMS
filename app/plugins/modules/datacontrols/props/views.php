<?php
loadModuleLib("views","editprops");
$v_engines=getViewEngines();
?>
<table width=800px border=0 cellpadding=0 cellspacing=4>
	<tr>
		<td class=titlecol width=200px>Report Engine</td>
		<td class=valuecol>
			<select id=engine name=engine>
				<?php
					foreach($v_engines as $a=>$b) {
						echo "<option value='$a'>$b</option>";
					}
				?>
			</select>
		</td>
	</tr>
</table>
