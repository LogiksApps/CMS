<?php
if(!defined('ROOT')) exit('No direct script access allowed');


$src=explode("/", $_GET['src']);
if(count($src)==0) {
	$src[1]=$src[0];
	$src[0]="tables";
}
$data = "";

// $finalData=["info"=>_db()->get_dbinfo()];
// $finalData=array_merge($finalData,_db()->get_dbObjects());

// $tables=$finalData['tables'];//_db()->get_tablelist();//TABLE List
// foreach($tables as $tbl=>$tblInfo) {
//   $finalData['tables'][$tbl]['FIELDS']=_db()->get_defination($tbl);//FIELDS
//   $finalData['tables'][$tbl]['KEYS']=_db()->get_allkeys($tbl);//KEYS
//   $finalData['tables'][$tbl]['SCHEMA']=_db()->get_schema($tbl,false);//SQL
// }
//find how to save indexes
//printArray($finalData);


switch ($src[0]) {
	case 'tables':
    $data = _db($dbKey)->_RAW("SHOW CREATE TABLE {$src[1]}")->_GET();
    if(isset($data[0])) {
      $data = $data[0]['Create Table'];
    }
	break;
    
  case 'views':
//     $data = _db($dbKey)->_RAW("SELECT VIEW_DEFINITION FROM INFORMATION_SCHEMA.VIEWS WHERE TABLE_NAME = '{$src[1]}'")->_GET();
    $data = _db($dbKey)->_RAW("SHOW CREATE VIEW {$src[1]}")->_GET();
    if(isset($data[0])) {
      //$data = $data[0]['VIEW_DEFINITION'];
      $data = $data[0]['Create View'];
    }
  	break;
	
  case "functions":
    $data = _db($dbKey)->_RAW("SHOW CREATE FUNCTION {$src[1]}")->_GET();
    if(isset($data[0])) {
      $data = $data[0]['Create Function'];
    }
    break;
    
  case "procedures":
    $data = _db($dbKey)->_RAW("SHOW CREATE PROCEDURE {$src[1]}")->_GET();
    if(isset($data[0])) {
      $data = $data[0]['Create Procedure'];
    }
    break;
    
  case "events":
    $data = _db($dbKey)->_RAW("SHOW CREATE EVENT {$src[1]}")->_GET();
    if(isset($data[0])) {
      $data = $data[0]['Create Event'];
    }
    break;
    
  default:
		echo "<h5 align=center>Sorry, viewing create for type <b>{$src[0]}</b> is not supported yet</h5>";
		return;
		break;
}

if(strlen($data)<=0) {
  echo "<h5 align=center>Sorry, viewing create for {$src[0]} / {$src[1]} is not available</h5>";
  return;
}

// printArray($data);
?>
<div class='container-fluid' style='width: 90%;margin: auto;margin-top: 20px;'>
  <div class='text-right'>
    <button class='btn btn-default' onclick="copyToClipboard()">
      <i class='fa fa-clipboard'></i>
      Copy
    </button>
  </div>
  <code id='sqlCode'>
      <?=$data?>
  </code>
</div>
<script>
function copyToClipboard() {
    text = $("#sqlCode").text();
  
    if (window.clipboardData && window.clipboardData.setData) {
        // IE specific code path to prevent textarea being shown while dialog is visible.
          lgksToast("Copied the code to Clipboard");
        return clipboardData.setData("Text", text); 

    } else if (document.queryCommandSupported && document.queryCommandSupported("copy")) {
        var textarea = document.createElement("textarea");
        textarea.textContent = text;
        textarea.style.position = "fixed";  // Prevent scrolling to bottom of page in MS Edge.
        document.body.appendChild(textarea);
        textarea.select();
        try {
            lgksToast("Copied the code to Clipboard");
            return document.execCommand("copy");  // Security exception may be thrown by some browsers.
        } catch (ex) {
            console.warn("Copy to clipboard failed.", ex);
            return false;
        } finally {
            document.body.removeChild(textarea);
        }
    }
}
</script>