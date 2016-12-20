<?php
if(!defined('ROOT')) exit('No direct script access allowed');

checkServiceAccess();

if(!isset($_REQUEST["action"])) {
	printServiceErrorMsg("NotAcceptable","Action Not Defined.");
}

loadModuleLib("settings","api");

$cfgDir=ROOT.CFG_FOLDER."/jsonConfig/";

switch ($_REQUEST['action']) {
	case 'fetchPanel':
		if(!isset($_REQUEST["src"])) {
			printServiceErrorMsg("NotAcceptable","Component Type Not Defined.");
		}
		$_REQUEST['src']=strtolower($_REQUEST['src']);
		$cfgFile=__DIR__."/pages/{$_REQUEST['src']}.php";
		if(file_exists($cfgFile)) {
			if(!is_writable($cfgFile)) {
				//echo "<div class='errorBox detachParent alert alert-warning alert-dismissible' style='margin-top: 10px;margin-bottom: 10px;'>Config Source is Readonly.<button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times;</span></button></div>";
			}
			include_once $cfgFile;
		} else {
			echo "<div class='errorBox alert alert-danger'>Sorry, requested config not found.</div>";
		}
	break;

	case 'createNew':
	break;
	case 'edit':
	break;
	case 'delete':
	break;
	case 'update':
	break;
}

function printCFGCard($src,$key,$params,$config) {
	if(!isset($params['title'])) $params['title']="";
	if(!isset($params['body'])) $params['body']="";

	if($src=="GLOBALS") $src="<span class='label label-danger pull-right'>{$src}</span>";
	elseif($src=="APP" && $key=="app")  $src="<span class='label label-success pull-right'>{$src}</span>";
	else  $src="<span class='label label-default pull-right'>{$src}</span>";
?>
<div class="col-lg-3 col-md-4 col-xs-6 thumb">
    <div class="card">
	    <h2 class="card-heading simple">
	     	<?=$params['title']?>
	     	<?=$src?>
	    </h2>
	    <div class="card-body">
	        <div><?=$params['body']?></div>
	        <p class='bottomLink'>
	        	<!-- <a class="btn" href="#">Edit Card »</a> -->
	        </p>
	    </div>
      	<input type='checkbox' name='cardSelector' class='cardSelector' />
   </div>
</div>
<?php
}

function printJSONCFGForm($cfgFile) {
	$cfgName=basename($cfgFile);

	$cfgSchema=[];
	$schemaFile=ROOT.CFG_FOLDER."schemas/".str_replace(".cfg", ".php", $cfgName);
	if(file_exists($schemaFile)) {
		include_once $schemaFile;
	}
	if($cfgSchema==null) $cfgSchema=[];

	$html=[];
	$disabled=!is_writable($cfgFile);

	if(file_exists($cfgFile)) {
		$data=ConfigFileReader::LoadFile($cfgFile);
		$aKeys=array_keys($data);

		//$aKeys['GLOBALS']
		//$aKeys[$_REQUEST['forSite']]
		//printArray();

		if(in_array($_REQUEST['forSite'], $aKeys)) {

		} else {
			//echo "";
		}
	}

	return "<form class='form-horizontal' autocomplete=off>".implode("", $html)."</form>";
}
?>