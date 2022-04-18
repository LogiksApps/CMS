<?php
if(!defined('ROOT')) exit('No direct script access allowed');

//echo $dbKey;

$limit = 10;
if(isset($_GET['limit'])) {
    setCookie('CMS-DBEDIT-BROWSER-PERPAGE', $_GET['limit']);
    $limit = $_GET['limit'];
} elseif(isset($_COOKIE['CMS-DBEDIT-BROWSER-PERPAGE'])) {
    $limit = $_COOKIE['CMS-DBEDIT-BROWSER-PERPAGE'];
} else {
    setCookie('CMS-DBEDIT-BROWSER-PERPAGE', 10);
    $limit = 10;
}
$_GET['limit'] = $limit;

if(!isset($_GET['page'])) $_GET['page'] = 0;

$src=explode("/", $_GET['src']);
if(count($src)==0) {
	$src[1]=$src[0];
	$src[0]="tables";
}

//$columnDefination=_db($dbKey)->get_defination($src[1]);

$columns=[];
$data=[];
switch ($src[0]) {
	case 'tables':
	case 'views':
		$columns=_db($dbKey)->get_columnlist($src[1]);

		$db=_db($dbKey)->_selectQ($src[1],"*");
		$db=$db->_orderby("id desc");
		$db=$db->_limit($_GET['limit'], $_GET['page']*$_GET['limit']);
// 		echo $db->_SQL();

		$data=$db->_get();
		break;
	
	default:
		echo "<h5 align=center>Sorry, viewing data for type <b>{$src[0]}</b> is not supported yet</h5>";
		return;
		break;
}

if($data==null) $data=[];

//var_dump($data);

if(count($data)>0) {
    include_once dirname(__DIR__)."/panels/browser_toolbar.php";
	printDataInTable($data, $columns, ["deleteRecord"=>"fa fa-trash","editRecord"=>"fa fa-pen fa-pencil"]);
} else {
	echo "<h5>No data in {$src[0]} : {$src[1]}</h5>";
}
?>
<script>
var selectedRecord=null;
$(function() {
	$("#dataContent td.action i[cmd]").click(function(e) {
		cmd=$(this).attr('cmd');
		selectedRecord=$(this).closest("tr");
		switch(cmd) {
			case "deleteRecord":
				lgksConfirm("Are sure about deleting the selected record?","Delete Record!",function(txt) {
					key=$(selectedRecord).data("key");
					col=$(selectedRecord).data("col");

					if(txt) {
						q=col+"="+key;
						lx=_service("dbEdit","deleteRecord")+"&dkey="+dkey+"&src=<?=$_GET['src']?>";
						processAJAXPostQuery(lx,q,function(txt) {
							if(txt=="success") {
								loadDataContent(currentDBQueryPanel);
								lgksToast("Data deleted successfully");
							} else {
								lgksToast(txt);
							}
						});
					}
				})
			break;
			case "editRecord":
				key=$(selectedRecord).data("key");
				col=$(selectedRecord).data("col");
				if(col=="id") {
					loadDataContent("edit","&src=<?=$_GET['src']?>&refid="+key);
				} else {
					lgksToast("Sorry, editing is supported only if the table has ID column.");
				}
			break;
		}
	});
});
</script>