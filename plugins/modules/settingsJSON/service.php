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
			exit();
		}
		$_REQUEST['src']=strtolower($_REQUEST['src']);
		$cfgFile=__DIR__."/pages/{$_REQUEST['src']}.php";
		if(file_exists($cfgFile)) {
			if(!is_writable($cfgFile)) {
				echo "<div class='errorBox detachParent alert alert-warning alert-dismissible' style='margin-top: 10px;margin-bottom: 10px;'>Config Source is Readonly.<button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times;</span></button></div>";
			}
			include_once $cfgFile;
			include_once __DIR__."/pages/editor.php";
		} else {
			echo "<div class='errorBox alert alert-danger'>Sorry, requested config not found.</div>";
		}
	break;

	case 'saveConfig':
	    if(!isset($_POST["src"])) {
			printServiceErrorMsg("NotAcceptable","Component Type Not Defined.");
			exit();
		}
		if(!isset($_POST["type"])) {
			printServiceErrorMsg("NotAcceptable","Component Type Not Defined.");
			exit();
		}
		if(!isset($_POST["key"])) {
			printServiceErrorMsg("NotAcceptable","Component Type Not Defined.");
			exit();
		}
		if(!isset($_POST["textConfig"])) {
			printServiceErrorMsg("NotAcceptable","Component Type Not Defined.");
			exit();
		}
		$jsonConfig = json_decode($_POST["textConfig"], true);
		if(!$jsonConfig) {
		    printServiceErrorMsg("NotAcceptable","Input Configuration should be valid JSON");
		    exit();
		}
	    
	    $cfgFile=ROOT.CFG_FOLDER."jsonConfig/".strtolower($_POST['src']).".json";
	    if(!is_writable($cfgFile)) {
	        printServiceErrorMsg("NotAcceptable","Config File not editable");
		    exit();
	    }
	    if(!file_exists($cfgFile)) {
	        printServiceErrorMsg("NotAcceptable","Config File not found");
		    exit();
	    }
	    
	    switch(strtolower($_POST["type"])) {
	        case "globals":
	        case "global":
	            $_POST["src"] = "GLOBALS";
	            break;
	        default:
	            $_POST["src"] = CMS_SITENAME;
	    }
	    
        $jsonMap=json_decode(file_get_contents($cfgFile),true);	
	    if(!isset($jsonMap[$_POST["src"]])) $jsonMap[$_POST["src"]] = [];
	    
	    $jsonMap[$_POST["src"]][$_POST["key"]] = $jsonConfig;
	    
	    $a = file_put_contents($cfgFile, json_encode($jsonMap, JSON_PRETTY_PRINT));
	    if($a) printServiceMsg("SUCCESS");
	    else printServiceErrorMsg("NotAcceptable","Error Updating Config File");
	break;
	case 'deleteConfig':
	    if(!isset($_POST["src"])) {
			printServiceErrorMsg("NotAcceptable","Component Type Not Defined.");
			exit();
		}
		if(!isset($_POST["type"])) {
			printServiceErrorMsg("NotAcceptable","Component Type Not Defined.");
			exit();
		}
		if(!isset($_POST["key"])) {
			printServiceErrorMsg("NotAcceptable","Component Type Not Defined.");
			exit();
		}
		
	    $cfgFile=ROOT.CFG_FOLDER."jsonConfig/".strtolower($_POST['src']).".json";
	    if(!is_writable($cfgFile)) {
	        printServiceErrorMsg("NotAcceptable","Config File not editable");
		    exit();
	    }
	    if(!file_exists($cfgFile)) {
	        printServiceErrorMsg("NotAcceptable","Config File not found");
		    exit();
	    }
	    
	    switch(strtolower($_POST["type"])) {
	        case "globals":
	        case "global":
	            $_POST["src"] = "GLOBALS";
	            break;
	        default:
	            $_POST["src"] = CMS_SITENAME;
	    }
	    
        $jsonMap=json_decode(file_get_contents($cfgFile),true);	
	    if(!isset($jsonMap[$_POST["src"]])) $jsonMap[$_POST["src"]] = [];
	    
	    if(!isset($jsonMap["TRASH"])) $jsonMap["TRASH"] = [];
	    if(!isset($jsonMap["TRASH"][$_POST["src"]])) $jsonMap["TRASH"][$_POST["src"]] = [];
	    
	    
	    if(isset($jsonMap[$_POST["src"]][$_POST["key"]])) {
	        $jsonMap["TRASH"][$_POST["src"]][$_POST["key"]] = $jsonMap[$_POST["src"]][$_POST["key"]];
	        unset($jsonMap[$_POST["src"]][$_POST["key"]]);
	    }
	    
	    $a = file_put_contents($cfgFile, json_encode($jsonMap, JSON_PRETTY_PRINT));
	    if($a) printServiceMsg("SUCCESS");
	    else printServiceErrorMsg("NotAcceptable","Error Updating Config File");
	break;
}

function printCFGCard($src,$key,$params,$config,$cfgFile) {
	if(!isset($params['title'])) $params['title']="";
	if(!isset($params['body'])) $params['body']="";
	
	$disabled=!is_writable($cfgFile);

    $srcHTML = "";
	if($src=="GLOBALS") {
	    if($_SESSION['SESS_RPIVILEGE_ID']>2) $disabled = true;
	    $srcHTML="<span class='label label-danger pull-right'>{$src}</span>";
	}
	elseif($src=="APP" && $key=="app")  $srcHTML="<span class='label label-success pull-right'>{$src}</span>";
	else  $srcHTML="<span class='label label-default pull-right'>{$src}</span>";
	//echo "$key $src";
?>
<div class="col-lg-3 col-md-4 col-sm-6 col-xs-12 thumb">
    <div class="card" data-src='<?=$_REQUEST["src"]?>' data-key='<?=$key?>' data-type='<?=$src?>'>
	    <h2 class="card-heading simple">
	     	<?=$params['title']?>
	     	<?=$srcHTML?>
	    </h2>
	    <div class="card-body">
	        <div><?=$params['body']?></div>
	        <p class='bottomLink'>
	            <?php if(!$disabled) { ?>
	            <a class="btn btn-edit" data-src='<?=$_REQUEST["src"]?>' data-key='<?=$key?>' data-type='<?=$src?>' href="#"><i class='fa fa-pencil'></i></a> 
	        	<a class="btn btn-trash" data-src='<?=$_REQUEST["src"]?>' data-key='<?=$key?>' data-type='<?=$src?>' href="#"><i class='fa fa-trash'></i></a> 
	            <?php } ?>
	        </p>
	        <textarea class='d-none hidden'><?=json_encode($config, JSON_PRETTY_PRINT)?></textarea>
	    </div>
      	<!--<input type='radio' name='cardSelector' class='cardSelector' />-->
   </div>
</div>
<?php
}

function printJSONCFGForm($cfgFile) {
	$cfgName=basename($cfgFile);

	$cfgSchema=[];
// 	$schemaFile=ROOT.CFG_FOLDER."schemas/".str_replace(".cfg", ".php", $cfgName);
// 	if(file_exists($schemaFile)) {
// 		include_once $schemaFile;
// 	}
// 	if($cfgSchema==null) $cfgSchema=[];

	$html=[];
	$disabled=!is_writable($cfgFile);

	if(file_exists($cfgFile)) {
		$data=ConfigFileReader::LoadFile($cfgFile);
		$aKeys=array_keys($data);

		//$aKeys['GLOBALS']
		//$aKeys[$_REQUEST['forSite']]
		//printArray();
        printArray($data);
		if(in_array($_REQUEST['forSite'], $aKeys)) {

		} else {
			//echo "";
		}
	}

	return "<form class='form-horizontal' autocomplete=off>".implode("", $html)."</form>";
}
?>