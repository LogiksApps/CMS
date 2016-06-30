<?php
if(!defined('ROOT')) exit('No direct script access allowed');

checkServiceAccess();

if(!isset($_REQUEST["action"])) {
	printServiceErrorMsg("NotAcceptable","Action Not Defined.");
}

$cfgFile=ROOT.CFG_FOLDER."jsonConfig/domainmap.json";
$domainMap=json_decode(file_get_contents($cfgFile),true);	

switch ($_REQUEST['action']) {
	case 'listall':
		$domainTable=[];
		foreach ($domainMap['GLOBALS'] as $host => $data) {
			$domainTable[md5($host)]=[
					"host"=>$host,
					"app"=>$data['appsite'],
					"node"=>$data['node'],
					"rule"=>[],
					"active"=>$data['active'],
					"created"=>$data['doc'],
					"last_update"=>$data['doe'],
				];
		}
		echo printDomainTable($domainTable);
	break;
	case 'save':
		if(!is_writable($cfgFile)) {
			echo "Sorry, Domain Map file is readonly.";
			return false;
		}

		$defaultNode=[
				"host"=>"",
				"appsite"=>"",
				"node"=>"",
				"rule"=>"",
				"active"=>"",
				"created"=>"",
				"last_update"=>"",
			];

		$jsonData=[];
		foreach ($_POST as $key => $node) {
			if(strlen($node['host'])<=0 || strlen($node['appsite'])<=0) continue;

			if(isset($domainMap['GLOBALS'][$node['host']])) {
				$map=array_merge($domainMap['GLOBALS'][$node['host']],$node);
			} else {
				$map=array_merge($defaultNode,$node);
				$map['doc']=date("Y-m-d");
			}
			$map['doe']=date("Y-m-d");

			$map['active']=($map['active']=="true")?true:false;

			$jsonData[$node['host']]=$map;
		}

		$jsonData=["GLOBALS"=>$jsonData];
		//printArray($_POST);
		$jsonData=json_encode($jsonData,JSON_PRETTY_PRINT);

		file_put_contents($cfgFile, $jsonData);
		echo "done";
	break;
}
function printDomainTable($domainTable) {
	$siteList=array_keys(_session("siteList"));
	$siteListHTML=[];
	foreach ($siteList as $sx) {
		$siteListHTML[]="<option value='$sx'>".toTitle(_ling($sx))."</option>";
	}
	$siteListHTML=implode("", $siteListHTML);



	$html=[];

	$html[]="<table class='table table-bordered'>";
	$html[]="<thead><tr>";
	$html[]="<th width=50px></th>";
	$html[]="<th>Host/Domain</th>";
	$html[]="<th>APP</th>";
	$html[]="<th width=150px>Status</th>";
	$html[]="<th width=150px>Created</th>";
	$html[]="<th width=150px>Updated</th>";
	//$html[]="<th width=100px>--</th>";
	$html[]="</tr></thead>";
	$html[]="<tbody>";

	foreach ($domainTable as $key => $map) {
		//<input type='text' name='' value='{$map['app']}' readonly />
		$sx="<tr data-rowkey='{$key}'>
				<td class='text-center'><input type='radio' name='rowSelector' /></td>
				<td><input type='text' name='host' value='{$map['host']}' default-value='{$map['host']}' /></td>
				<td><select name='app' value='{$map['app']}' default-value='{$map['app']}' >";
		
		foreach ($siteList as $site) {
			if($site==$map['app'])
				$sx.="<option value='$site' selected>".toTitle(_ling($site))."</option>";
			else
				$sx.="<option value='$site'>".toTitle(_ling($site))."</option>";
		}	
		
		$sx.="</select></td>
				<td><input type='checkbox' name='active' class='switch' ".($map['active']?"checked":"")." /></td>
				<td>{$map['created']}</td>
				<td>{$map['last_update']}</td>
				</tr>";

		$html[]=$sx;
	}

	$html[]="</tbody>";
	$html[]="<tfoot>";
	$html[]="<tr data-rowkey='NA'>
				<td class='text-center'><input type='radio' name='rowSelector' /></td>
				<td><input type='text' name='host' value='' /></td>
				<td><select name='app'>{$siteListHTML}</select></td>
				<td><input type='checkbox' name='active' class='switch' /></td>
				<td></td>
				<td></td>
				</tr>";
	$html[]="</tfoot>";
	$html[]="</table>";



	return implode("", $html);
}
?>