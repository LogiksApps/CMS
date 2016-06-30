<?php
if(!defined('ROOT')) exit('No direct script access allowed');

checkServiceAccess();

if(!isset($_REQUEST["action"])) {
	printServiceErrorMsg("NotAcceptable","Action Not Defined.");
}
if(!isset($_REQUEST["src"])) {
	printServiceErrorMsg("NotAcceptable","Component Type Not Defined.");
}

include __DIR__."/api.php";

$logDir=getAllLogFolder().$_REQUEST["src"]."/";
if(!file_exists($logDir) && !is_dir($logDir)) {
	printServiceMsg([]);
	return;
}

switch ($_REQUEST['action']) {
	case 'listLog':
		$fs=scandir($logDir);
		$fs=array_splice($fs, 2);
		printServiceMsg(["src"=>$_REQUEST['src'],"list"=>$fs]);
	break;
	case "loadLog":
		if(isset($_REQUEST['file']) && strlen($_REQUEST['file'])>0) {
			$logFile=$logDir.$_REQUEST['file'];
			if(file_exists($logFile)) {
				if(!isset($_REQUEST['i'])) $_REQUEST['i']=0;
				if(!isset($_REQUEST['l'])) $_REQUEST['l']=50;

				$data=file($logFile,FILE_SKIP_EMPTY_LINES);
				if(count($data)>0) {
					$data=array_reverse($data);
					$limit=$_REQUEST['i']+$_REQUEST['l'];
					for($i=$_REQUEST['i'];$i<$limit;$i++) {
						if(!isset($data[$i])) break;
						echo printLogRecord($data[$i],$i);
					}
				} else {
					echo "<h3 align=center>Log file has no content</h3>";
				}
			} else {
				echo "<h3 align=center>Error in finding source log file</h3>";
			}
		} else {
			echo "<h3 align=center>Error defining source log file</h3>";
		}
	break;
	case "downloadLog":
		if(isset($_REQUEST['file']) && strlen($_REQUEST['file'])>0) {
			$logFile=$logDir.$_REQUEST['file'];
			if(file_exists($logFile)) {
				header("Content-type: text/plain");
				header("Content-Disposition: attachment; filename={$_REQUEST['file']}");
				header("Expires: 0");
				header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
				header("Content-Transfer-Encoding: binary");
				header('Pragma: public');
				header('Pragma: no-cache');

				readfile($logFile);
			} else {
				echo "<h3 align=center>Error in finding source log file</h3>";
			}
		} else {
			echo "<h3 align=center>Error defining source log file</h3>";
		}
	break;
	case "deleteLog":
		if(isset($_REQUEST['file']) && strlen($_REQUEST['file'])>0) {
			$files=explode(",", $_REQUEST['file']);

			foreach ($files as $f) {
				$logFile=$logDir.$f;

				if(file_exists($logFile)) {
					unlink($logFile);
				}
			}
			echo "done";
		} else {
			echo "<h3 align=center>Error defining source log file</h3>";
		}
	break;
}
function printLogRecord($log_line,$kx="0") {
	//([0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3})[^\[]*\[([^\]]*)\][^\"]*\"([^\"]*)\"\s([0-9]*)\s([0-9]*)(.*)
	//$pattern = "/([^\[]*\[([^\]]*)\][^\"])+([a-zA-Z.]+)/";
	//preg_match_all($pattern, $log_line, $matches);
	//printArray($matches);

	$logArr=explode(" > ", $log_line);
	
	$logArr[0]=explode(" ", $logArr[0]);
	$logArr[0][0]=_pDate(substr($logArr[0][0], 1));
	$logArr[0][1]=substr($logArr[0][1], 0,strlen($logArr[0][1])-1);

	$logArr[0][2]=explode(".", $logArr[0][2]);
	$logArr[0][3]=$logArr[0][2][1];
	$logArr[0][2]=$logArr[0][2][0];

	//printArray($logArr);

	//printArray($logArr[1]);

	$json1=json_encode(str_replace("'","",$logArr[1]));
	$json2=json_encode($logArr[3]);

	$html=[];

	$html[]="<td>{$logArr[0][0]} {$logArr[0][1]}</td>";
	$html[]="<td>{$logArr[0][3]}</td>";
	$html[]="<td>{$logArr[2]}</td>";
	$html[]="<td class='text-center'><i data-json='{$json1}' class='glyphicon glyphicon-info-sign'></i>&nbsp;&nbsp;&nbsp;<i data-json='{$json2}'  class='glyphicon glyphicon-screenshot'></i></td>";
	//println($log);

	return "<tr rel='{$kx}'>".implode("", $html)."</tr>";
}
?>