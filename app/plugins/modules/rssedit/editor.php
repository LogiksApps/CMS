<form>
	<input type=hidden name=id value=0 />
	<div id=rssEditor class=tabs>
		<ul>
			<li><a href='#rss_a'>General</a></li>
			<li><a href='#rss_b'>Data Source</a></li>
			<li><a href='#rss_c'>Items</a></li>
		</ul>
		<div id=rss_a>
			<table width=100% cellpadding=2 cellspacing=2 border=0>
				<tr>
					<th align=left width=150px>RSSID *</th>
					<td><input type=text name=rssid onchange="this.value=this.value.replace(' ','');" /></td>
					<td colspan=10><h5>This is a unique ID used to access this RSS Feed via External sources.</h5></td>
				</tr>
				<tr>
					<td colspan=10><hr/></td>
				</tr>
				<tr>
					<th align=left width=150px>Title *</th>
					<td><input type=text name=title /></td>
					
					<th align=left width=150px>Category</th>
					<td><input type=text name=category /></td>
				</tr>
				<tr>
					<th align=left width=150px>Language *</th>
					<td><input type=text name=language value='en-US' /></td>
					
					<th align=left width=150px>Author</th>
					<td><input type=text name=author /></td>
				</tr>
				<tr>
					<th align=left width=150px>Available Till</th>
					<td><input type=text name=avlbl_till class='datefield' /></td>
					
					<th align=left width=150px>Description</th>
					<td><textarea name=descs></textarea></td>
				</tr>
				<tr>
					<th align=left width=150px>Reference Link</th>
					<td><input type=text name=ref_url /></td>
					
					<th align=left width=150px>RSS Image</th>
					<td><input type=text name=image_link value='images/rss.png' /></td>
				</tr>
				
				<tr>
					<th align=left width=150px>Blocked</th>
					<td>
						<select name=blocked class='ui-widget-header ui-corner-all'>
							<option value='false'>False</option>
							<option value='true'>True</option>
						</select>
					</td>
					
					<th align=left width=150px>Secure</th>
					<td>
						<select name=secure class='ui-widget-header ui-corner-all'>
							<option value='false'>False</option>
							<option value='true'>True</option>
						</select>
					</td>
				</tr>
				
				<tr>
					<th align=right colspan=10>
						<a href='#' onclick='$("#rssEditor").tabs("select",1);' style='color:maroon;'>Next</a>
					</th>
				</tr>
			</table>
		</div>
		<div id=rss_b>
			<table width=100% cellpadding=2 cellspacing=2 border=0>
				<tr><th align=left width=150px></th><td width=300px></td><th align=left width=150px></th><td width=300px></td></tr>
				
				<tr>
					<th align=left width=150px>Source Tables</th>
					<td>
						<select id=datatable_table name=datatable_table class='' multiple size=5>
						</select>
					</td>
				</tr>
				<tr>
					<th align=left width=150px>Source Columns</th>
					<td>
						<select id=datatable_cols name=datatable_cols class='' multiple size=5>
						</select>
					</td>
				</tr>
				<tr>
					<th align=left width=150px>Source Order</th>
					<td>
						<select name=datatable_orderby class='ui-widget-header ui-corner-all'>
						</select>
					</td>
				</tr>
				<tr>
					<th align=left width=150px>Photo/Image Source</th>
					<td>
						<select name=attributes_itemImageCol class='ui-widget-header ui-corner-all'>
						</select>
					</td>
				</tr>
				<tr>
					<th align=left width=150px>Source Condition</th>
					<td colspan=3>
						<textarea name=datatable_where></textarea>
					</td>
				</tr>
				
				<tr><th align=left width=150px></th><td width=300px></td><th align=left width=150px></th><td width=300px></td></tr>
				<tr>
					<th align=right colspan=10>
						<a href='#' onclick='$("#rssEditor").tabs("select",0);' style='color:maroon;'>Previous</a>
						<a href='#' onclick='$("#rssEditor").tabs("select",2);' style='color:maroon;'>Next</a>
					</th>
				</tr>
			</table>
		</div>
		<div id=rss_c>
			<table width=100% cellpadding=2 cellspacing=2 border=0>
				<tr>
					<th align=left width=150px>Feed Limit</th>
					<td><input type=text name=attributes_limit /></td>
					
					<th align=left width=150px>Feed Template</th>
					<td>
						<select name=attributes_template class='ui-widget-header ui-corner-all'>
							<option value=0>Data And Image</option>
							<option value=1>Data Only</option>
							<option value=2>Image Only</option>
						</select>
					</td>
				</tr>
				<tr>
					<th align=left width=150px>Item Link</th>
					<td><input type=text name=attributes_href /></td>
				</tr>
				
				<tr><th align=left width=150px></th><td width=300px></td><th align=left width=150px></th><td width=300px></td></tr>
				<tr>
					<th align=right colspan=10>
						<a href='#' onclick='$("#rssEditor").tabs("select",1);' style='color:maroon;'>Previous</a>
					</th>
				</tr>
			</table>
		</div>
	</div>
</form>
