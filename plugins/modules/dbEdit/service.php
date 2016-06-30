<?php
if(!defined('ROOT')) exit('No direct script access allowed');

checkServiceAccess();

if(!isset($_REQUEST["action"])) {
	printServiceErrorMsg("NotAcceptable","Action Not Defined.");
}

include_once __DIR__."/commons.php";

$dbKey="app";
if(isset($_GET["dkey"])) {
	$dbKey=$_GET["dkey"];
}

$dbList=Database::getConnectionList();

switch ($_REQUEST['action']) {
	case "listDatabase":
		printServiceMsg($dbList);
	break;
	case "dbList":
		if(count($dbList)<=0) {
			$db=[];
		} else {
			$db=_db($dbKey)->get_dbObjects();
			foreach ($db as $key => $obj) {
				$db[$key]=array_keys($obj);
			}
			foreach ($db['tables'] as $key=>$tbl) {
				if(in_array($tbl, $db['views'])) unset($db['tables'][$key]);
				elseif(in_array($tbl, $db['triggers'])) unset($db['tables'][$key]);
				elseif(in_array($tbl, $db['events'])) unset($db['tables'][$key]);
				elseif(in_array($tbl, $db['routines'])) unset($db['tables'][$key]);
			}
			$db['tables']=array_values($db['tables']);
		}
		
		printServiceMsg($db);
	break;
	case "panel":
		if(count($dbList)<=0) {
			echo "<h2 align=center>This app does not have any database configured.</h2>";
		} else if(isset($_REQUEST['panel'])) {
			$panel=strtolower($_REQUEST['panel']);
			$panelFile=__DIR__."/panels/$panel.php";
			if(file_exists($panelFile)) {
				include_once $panelFile;
			} else {
				echo "<h2 align=center>Please load something to view its information.</h2>";
			}
		} else {
			echo "<h2 align=center>Selected Panel Not Supported Yet</h2>";
		}
	break;
	case "dbTablePanel":
		if(count($dbList)<=0) {
			echo "<h2 align=center>This app does not have any database configured.</h2>";
		} else if(isset($_REQUEST['panel'])) {
			$panel=strtolower($_REQUEST['panel']);
			$panelFile=__DIR__."/dbTablePanels/$panel.php";
			if(file_exists($panelFile)) {
				include_once $panelFile;
			} else {
				echo "<h2 align=center>Selected Panel Not Supported Yet</h2>";
			}
		} else {
			echo "<h2 align=center>Selected Panel Not Supported Yet</h2>";
		}
	break;


	case "query":
		if(isset($_POST['q'])) {
			if(strlen($_POST['q'])<=1) {
				echo "<h5>Empty query is not allowed. <br>You can try SQL92 or LogiksDB JSON format to query your database</h5>";
				return;
			}
			if(!isset($_REQUEST['type'])) $_REQUEST['type']="SQL";
			if(substr(trim($_POST['q']), 0,1)=="{") $_REQUEST['type']="JSON";
			else $_REQUEST['type']="SQL";

			switch (strtolower($_REQUEST['type'])) {
				case 'sql':
					$sql=_db($dbKey)->_raw($_POST['q']);
					$data=$sql->_get();
					if(isset($_REQUEST['showSQL']) && $_REQUEST['showSQL']=="true") {
						echo "<citie>".$sql->_SQL()."</citie>";
					}
					printDataInTable($data);
					break;
				
				case "json":
					$sql=AbstractQueryBuilder::fromJSON($_POST['q'],_db($dbKey));
					$data=$sql->_get();
					if(isset($_REQUEST['showSQL']) && $_REQUEST['showSQL']=="true") {
						echo "<citie>".$sql->_SQL()."</citie>";
					}
					printDataInTable($data);
					break;

				default:
					echo "<h5>Query Type not supported</h5>";
					break;
			}
			//echo "QUERY : {$_POST['q']}";
		} else {
			echo "<h5>Query not defined <br>You can try SQL92 or LogiksDB JSON format to query your database</h5>";
		}
	break;


	case "deleteTable":
		if(isset($_POST['src'])) {
			$src=explode(",", $_POST['src']);

			foreach ($src as $s) {
				$s=explode("/", $s);
				switch ($s[0]) {
					case 'tables':
						$sql=_db($dbKey)->_raw("DROP TABLE {$s[1]}");
						$res=$sql->_run();
						break;
					case 'views':
						$sql=_db($dbKey)->_raw("DROP VIEW {$s[1]}");
						$res=$sql->_run();
						break;
					
				}
			}
		} else {
			echo "<h5>Source table not defined</h5>";
		}
	break;

	case "insertRecord":
		if(isset($_GET['src'])) {
			$src=explode("/", $_GET['src']);
			if(count($src)==0) {
				$src[1]=$src[0];
				$src[0]="tables";
			}
			if($src[0]=="tables") {
				$cols=_db($dbKey)->get_columnlist($src[1]);
				foreach ($cols as $c) {
					if(array_key_exists($c, $autoFillColumns)) {
						$_POST[$c]=$autoFillColumns[$c];
					}
				}
				$sql=_db($dbKey)->_insertQ1($src[1],$_POST);
				$res=$sql->_run();

				if($res) echo "success";
				else echo "Sorry, failed to create new record.";
			} else {
				echo "<h5>Source format '{$src[0]}' not supported</h5>";
			}
		} else {
			echo "<h5>Source table not defined</h5>";
		}
	break;

	case "updateRecord":
		if(isset($_GET['src']) && isset($_GET['refid']) && $_GET['refid']>0) {
			$src=explode("/", $_GET['src']);
			if(count($src)==0) {
				$src[1]=$src[0];
				$src[0]="tables";
			}
			if($src[0]=="tables") {
				$cols=_db($dbKey)->get_columnlist($src[1]);
				foreach ($cols as $c) {
					if(array_key_exists($c, $autoFillColumns)) {
						$_POST[$c]=$autoFillColumns[$c];
					}
				}
				$sql=_db($dbKey)->_updateQ($src[1],$_POST,["id"=>$_GET['refid']]);
				$res=$sql->_run();

				if($res) echo "success";
				else echo "Sorry, failed to update new record.";
			} else {
				echo "<h5>Source format '{$src[0]}' not supported</h5>";
			}
		} else {
			echo "<h5>Source table not defined</h5>";
		}
	break;

	case "deleteRecord":
		if(isset($_GET['src'])) {
			$src=explode("/", $_GET['src']);
			if(count($src)==0) {
				$src[1]=$src[0];
				$src[0]="tables";
			}
			if($src[0]=="tables") {
				$sql=_db($dbKey)->_deleteQ($src[1],$_POST);
				$res=$sql->_run();

				if($res) echo "success";
				else echo "Sorry, failed to delete the record.";
			} else {
				echo "<h5>Source format '{$src[0]}' not supported</h5>";
			}
		} else {
			echo "<h5>Source table not defined</h5>";
		}
	break;	

	case "addField":
		if(isset($_GET['src']) && isset($_POST['field']) && strlen($_POST['field'])>0) {
			$src=explode("/", $_GET['src']);
			if(count($src)==0) {
				$src[1]=$src[0];
				$src[0]="tables";
			}
			if($src[0]=="tables") {
				$sql=_db($dbKey)->_raw("ALTER TABLE {$src[1]} ADD COLUMN {$_POST['field']}");
				//echo $sql->_SQL();exit();
				$res=$sql->_run();

				if($res) echo "success";
				else echo "Sorry, failed to add new Column. : ";
			} else {
				echo "<h5>Source format '{$src[0]}' not supported</h5>";
			}
		} else {
			echo "<h5>Source table not defined</h5>";
		}
	break;	

	case "updateField":
		if(isset($_GET['src']) && isset($_POST['field']) && strlen($_POST['field'])>0  && isset($_POST['field_new']) && strlen($_POST['field_new'])>0) {
			$src=explode("/", $_GET['src']);
			if(count($src)==0) {
				$src[1]=$src[0];
				$src[0]="tables";
			}
			if($src[0]=="tables") {
				$sql=_db($dbKey)->_raw("ALTER TABLE {$src[1]} CHANGE COLUMN {$_POST['field']} {$_POST['field_new']}");
				//echo $sql->_SQL();exit();
				$res=$sql->_run();

				if($res) echo "success";
				else echo "Sorry, failed to update the selected Column. : ";
			} else {
				echo "<h5>Source format '{$src[0]}' not supported</h5>";
			}
		} else {
			echo "<h5>Source table not defined</h5>";
		}
	break;

	case "deleteField":
		if(isset($_GET['src']) && isset($_POST['field']) && strlen($_POST['field'])>0) {
			$src=explode("/", $_GET['src']);
			if(count($src)==0) {
				$src[1]=$src[0];
				$src[0]="tables";
			}
			if($src[0]=="tables") {
				$sql=_db($dbKey)->_raw("ALTER TABLE {$src[1]} DROP COLUMN {$_POST['field']}");
				$res=$sql->_run();

				if($res) echo "success";
				else echo "Sorry, failed to delete the selected Column.";
			} else {
				echo "<h5>Source format '{$src[0]}' not supported</h5>";
			}
		} else {
			echo "<h5>Source table not defined</h5>";
		}
	break;	
}
?>