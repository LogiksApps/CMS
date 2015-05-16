<?php
$sql=$dbCon->_selectQ($srcTable,"continent",null,"continent");
$res=$dbCon->executeQuery($sql);
$continent=_dbData($res);
$dbCon->freeResult($res);

_js("jquery");

loadModule("editor");
loadEditor("ckeditor");

$data=null;
if(isset($_REQUEST['ref'])) {
	$sql=$dbCon->_selectQ($srcTable,"*",array("id"=>$_REQUEST['ref']));
	$res=$dbCon->executeQuery($sql);
	$data=_dbData($res);
	$dbCon->freeResult($res);
	if(count($data)>0) $data=$data[0];
	else $data=null;
}
if($data==null) {
	$data=array();
	$data['country']="";
	$data['continent']="";
	$data['banner_descs']="";
	$data['details']="";
	$data['blocked']="";
	$data['banner_photo']="";
	//$data['']="";
}
?>
<style>
#contentEditor th {
	text-align:left;
}
#contentEditor input[type=text],#contentEditor select {
	width:100%;height:25px;
	border:1px solid #aaa;
}
</style>
<form action='<?=_service("contentEditor")?>&forsite=<?=$_REQUEST['forsite']?>&action=update&src=<?=$_REQUEST['src']?>' method=POST enctype='multipart/form-data' target=targetFrame>
<input name=id value='<?=$data['id']?>' class='textfield' type=hidden>
<table id=contentEditor width=90% cellpadding=2 cellspacing=0 style='margin:auto;'>
	<tr>
		<th width=150px>Country</th>
		<td><input name=country value='<?=$data['country']?>' class='textfield' type=text></td>
		<th width=150px>Continent</th>
		<td>
			<select name=continent value='<?=$data['continent']?>'>
				<?php
				foreach($continent as $a) {
					if($a['continent']==$data['continent'])
						echo "<option value='{$a['continent']}' selected>{$a['continent']}</option>";
					else
						echo "<option value='{$a['continent']}'>{$a['continent']}</option>";
				}
				?>
			</select>
		</td>
	</tr>
	<tr>
		<th width=150px>Banner Photo</th>
		<td><input name=banner_photo value='' class='filefield' type=file></td>
		<th width=150px>Blocked</th>
		<td>
			<select name=blocked >
				<?php if($data['blocked']=='false') { ?>
					<option value='false' selected>False</option>
					<option value='true'>True</option>
				<?php } else { ?>
					<option value='false'>False</option>
					<option value='true' selected>True</option>
				<?php } ?>
			</select>
		</td>
	</tr>
	<tr>
		<th width=150px>Place Description</th>
	</tr>
	<tr>
		<th colspan=10><textarea name=banner_descs class='ckeditor'><?=$data['banner_descs']?></textarea></th>
	</tr>
	<tr>
		<th width=150px>Place Details</th>
	</tr>
	<tr>
		<th colspan=10><textarea name=details class='ckeditor'><?=$data['details']?></textarea></th>
	</tr>
</table>
</form>
<iframe name=targetFrame id=targetFrame style='display:none'></iframe>
<script>
$(function() {
	setTimeout(function() {
		$("#targetFrame").load(function() {
			parent.closeEditor();
		});
	},200);
});
</script>