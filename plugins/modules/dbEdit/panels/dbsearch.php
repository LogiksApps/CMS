<?php
if(!defined('ROOT')) exit('No direct script access allowed');

if(!isset($_GET['q'])) {
	echo "<h3 align=center>Search Not Defined</h3>";
	return false;
}
if(!isset($_GET['src'])) {
	echo "<h3 align=center>Source Not Defined</h3>";
	return false;
}

$q=$_GET['q'];

$src=explode("/", $_GET['src']);
if(count($src)==0) {
	$src[1]=$src[0];
	$src[0]="tables";
}

$dataSRC="";
$columns=[];
$data=[];
switch ($src[0]) {
	case 'tables':
		$dataSRC="tables/{$src[1]}";
	case 'views':
		$columns=_db($dbKey)->get_columnlist($src[1],false);

		$newColumns=[];

		$q=explode(":", $q);
		if(count($q)>1) {
			if(in_array($q[0], $columns)) {
				$newColumns=[$q[0]=>$q[1]];
			} else {
				echo "<h5>Sorry, search column not found in table.</h5>";
				return;
			}
		} else {
// 			$newColumns=array_flip($columns);
			$checkNum=is_numeric($q);
			$checkFloat=is_float($q);
			foreach($columns as $a=>$b) {
				if(isset($b[1])) {
					$fx=current(explode("(",$b[1]));
					switch(strtolower($fx)) {
						case "int":
							if($checkNum) {
								$newColumns[$a]=$q;
							}
							break;
						case "float":
							if($checkFloat) {
								$newColumns[$a]=$q;
							}
							break;
						default:
							if(!$checkNum && !$checkFloat) {
								$newColumns[$a]=$q;
							}
					}
				} else {
					$newColumns[$a]=$q;
				}
			}
// 			foreach ($newColumns as $key => $value) {
// 				$newColumns[$key]=$q;
// 			}
		}
// printArray($newColumns);
		$sql=_db($dbKey)->_selectQ($src[1],"*",[])->_where($newColumns,"OR","OR")->_limit(100,0);
// 		exit($sql->_SQL());
		$data=$sql->_get();
		break;
	
	default:
		echo "<h5>Sorry, viewing structure for type <b>{$src[0]}</b> is not supported yet</h5>";
		return;
		break;
}

if($data==null) $data=[];

if(count($data)>0) {
	echo "<div id='dataContent' class='searchContent' data-src='{$dataSRC}'>";
	printDataInTable($data,array_keys($columns),["deleteRecord"=>"fa fa-trash","editRecord"=>"fa fa-pencil"]);//,["editField"=>"fa fa-pencil"]
	echo "</div>";
} else {
	echo "<h5>No data in for search query <b>{$_GET['q']}</b></h5>";
}
?>