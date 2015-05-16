<input name=id type=hidden value='0' />
<table width=100% border=0 cellspacing=0>
	<tr><th align=left width=100px>Title</th><td><input name=title type=text /></td></tr>
	<tr><th align=left width=100px>Category</th>
		<td>
			<input name=category type=text style='width:54%;' />
			<select class='categorySelector' style='width:40%;height:20px;' onchange="$(this).prev().val(this.value);">
			</select>
		</td>
	</tr>
	<tr><th align=left width=100px>Header</th><td><textarea name=header></textarea></td></tr>
	<tr><th align=left width=100px>Footer</th><td><textarea name=footer></textarea></td></tr>
	<tr><td colspan=10 style='height:5px'></td></tr>
	<tr><td colspan=10>
		Please edit control's privileges, as default is '<b>all permissions</b>'.
	</td></tr>
</table>
