<?php
if(!defined('ROOT')) exit('No direct script access allowed');

//echo $dbKey;

$src=explode("/", $_GET['src']);
if(count($src)==0) {
	$src[1]=$src[0];
	$src[0]="tables";
}

$columns=[];
$data=[];
switch ($src[0]) {
	case 'tables':
	case 'views':
		$data=_db($dbKey)->get_defination($src[1]);
		break;
	
	default:
		echo "<h5 align=center>Sorry, viewing structure for type <b>{$src[0]}</b> is not supported yet</h5>";
		return;
		break;
}

if($data==null) $data=[];



printDataInTable($data,["Field","Type","NULL","KEY","DEFAULT","EXTRA"],["deleteField"=>"fa fa-trash","editField"=>"fa fa-pencil"]);
?>
<script>
var selectedField=null;
$(function() {
	$("#dataContent thead th.action").html("<i class='fa fa-plus' cmd='addField'></i>");

	$("td.action i[cmd],th.action i[cmd]","#dataContent").click(function(e) {
		cmd=$(this).attr('cmd');
		selectedField=$(this).closest("tr");
		switch(cmd) {
			case "addField":
				lgksPrompt("Add new column field.<br>&nbsp;&nbsp;&nbsp;<small>column_name column_definations<small>","New Column!",function(txt) {
					if(txt!=null && txt.length>1) {
						lx=_service("dbEdit","addField")+"&src=<?=$_GET['src']?>";
						processAJAXPostQuery(lx,"field="+txt,function(dx) {
							if(dx=="success") {
								loadDataContent(currentDBQueryPanel);
								lgksToast("Column created successfully");
							} else {
								lgksToast(dx);
							}
						});
					}
				});
			break;
			case "deleteField":
				lgksConfirm("Are sure about deleting the selected field?","Delete Column!",function(txt) {
					key=$(selectedField).data("key");

					if(txt) {
						lx=_service("dbEdit","deleteField")+"&src=<?=$_GET['src']?>";
						processAJAXPostQuery(lx,"field="+key,function(txt) {
							if(txt=="success") {
								loadDataContent(currentDBQueryPanel);
								lgksToast("Column deleted successfully");
							} else {
								lgksToast(txt);
							}
						});
					}
				})
			break;
			case "editField":
				lgksPrompt("Change the field name/type...<br>&nbsp;&nbsp;&nbsp;<small>column_name column_type<small>","Update Column!",function(txt) {
					if(txt!=null && txt.length>1) {
						key=$(selectedField).data("key");

						lx=_service("dbEdit","updateField")+"&src=<?=$_GET['src']?>";
						processAJAXPostQuery(lx,"field="+key+"&field_new="+txt,function(dx) {
							if(dx=="success") {
								loadDataContent(currentDBQueryPanel);
								lgksToast("Column updated successfully");
							} else {
								lgksToast(dx);
							}
						});
					}
				});
			break;
		}
	});
});
</script>