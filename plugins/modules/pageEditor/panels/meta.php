<?php
if(!defined('ROOT')) exit('No direct script access allowed');

if(!isset($jsonPage['meta'])) $jsonPage['meta']=[];
?>
<div class="panel panel-default">
    <div class="panel-body" style="padding: 0px;">
        <table id='metaTable' class='dataTable table table-hover table-striped table-condensed'>
        	<thead class="tableHead">
        		<tr>
					<th title='Metadata name'>Name</th>
					<th title='Pragma directive'>HTTP-Equiv</th>
					<th title='Value of the element'>Content</th>
					<th title='Character encoding declaration'>Charset</th>
					<th title='Language declaration'>Lang</th>
					<th width=30px>
						<i class='fa fa-plus actionBtn' cmd='addMeta'></i>
					</th>
				</tr>
        	</thead>
        	<tbody class="tableBody">
        		<?php
	        		foreach ($jsonPage['meta'] as $key => $value) {
	        			$html=["name"=>"","http-equiv"=>"","content"=>"","charset"=>"","lang"=>""];

	        			if(isset($value['name'])) $html['name']=$value['name'];
	        			if(isset($value['http-equiv'])) $html['http-equiv']=$value['http-equiv'];
	        			if(isset($value['content'])) $html['content']=$value['content'];
	        			if(isset($value['charset'])) $html['charset']=$value['charset'];
	        			if(isset($value['lang'])) $html['lang']=$value['lang'];
	        			$out=[];
	        			foreach ($html as $key => $value) {
	        				$out[]=sprintf("<td class='%s'><input type='text' name='%s[]' value='%s' /></td>",$key,$key,$value);
	        			}
	        			echo "<tr rel='{$key}'>".implode("", $out)."<td><i class='fa fa-times actionBtn' cmd='removeMeta'></i></td></tr>";
	        		}
	        	?>
        	</tbody>
        	<tfoot id='metaRowHidden' class='hidden'>
        		<tr>
					<td class='name'><input type='text' name='name[]' /></td>
					<td class='http-equiv'><input type='text' name='http-equiv[]' /></td>
					<td class='content'><input type='text' name='content[]' /></td>
					<td class='charset'><input type='text' name='charset[]' /></td>
					<td class='lang'><input type='text' name='lang[]' /></td>
					<td class=''><i class='fa fa-times actionBtn' cmd='removeMeta'></i></td>
				</tr>
        	</tfoot>
        </table>
    </div>
</div>
<script>
$(function() {
	$("#metaTable").delegate(".actionBtn[cmd]","click",function() {
		cmd=$(this).attr('cmd');
		switch(cmd) {
			case "removeMeta":
				$(this).closest("tr").hide();
			break;
			case "addMeta":
				addNewMeta();
			break;

		}
	});
	//addNewMeta();
});
function addNewMeta() {
	$("#metaTable tbody").append($("#metaRowHidden").html());
}
function saveMeta() {
	
}
function resetMeta() {
	$("#metaTable tbody tr").show();
}
</script>