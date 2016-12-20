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


$columns=[];
$data=[];
switch ($src[0]) {
	case 'tables':
	case 'views':
		$columns=_db($dbKey)->get_columnlist($src[1]);

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
			$newColumns=array_flip($columns);
			foreach ($newColumns as $key => $value) {
				$newColumns[$key]=$q;
			}
		}

		$sql=_db($dbKey)->_selectQ($src[1],"*",[])->_where($newColumns,"OR","OR")->_limit(25,0);
		$data=$sql->_get();
		break;
	
	default:
		echo "<h5>Sorry, viewing structure for type <b>{$src[0]}</b> is not supported yet</h5>";
		return;
		break;
}

if($data==null) $data=[];

if(count($data)>0) {
	printDataInTable($data,$columns);//,["deleteField"=>"fa fa-trash","editField"=>"fa fa-pencil"]
} else {
	echo "<h5>No data in for search query <b>{$_GET['q']}</b></h5>";
}
?>