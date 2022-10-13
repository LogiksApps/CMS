<?php
if(!defined('ROOT')) exit('No direct script access allowed');


$src=explode("/", $_GET['src']);
if(count($src)==0) {
	$src[1]=$src[0];
	$src[0]="tables";
}
$data = "";
$dataInsert = "";
$dataInsert1 = "";
$dataInsert2 = "";
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
        
        $cols = _db($dbKey)->get_defination($src[1]);
        $dataInsert = [];
        $dataInsert2 = [];
        foreach($cols as $col) {
            if(in_array($col[0], ["id"])) continue;
            
            $dataInsert[$col[0]] = "";
            $dataInsert2[] = "'{$col[0]}'=> '',";
        }
        $dataInsert1 = json_encode($dataInsert, JSON_PRETTY_PRINT);
        $dataInsert = _db($dbKey)->_insertQ1($src[1], $dataInsert)->_SQL();
        
        $dataInsert2 = "_db()->_insertQ1('{$src[1]}', [\n\r<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".implode("\n\r<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;", $dataInsert2)."])->_RUN()";
	break;
    
  case 'views':
//      $data = _db($dbKey)->_RAW("SELECT VIEW_DEFINITION FROM INFORMATION_SCHEMA.VIEWS WHERE TABLE_NAME = '{$src[1]}'")->_GET();
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
    <button class='btn btn-default' onclick="copyToClipboard('#sqlCode')">
      <i class='fa fa-clipboard'></i>
      Copy
    </button>
    <b class='pull-left'>Create Table</b>
  </div>
  <code id='sqlCode'>
      <?=$data?>
  </code>
  <?php if($dataInsert1 && strlen($dataInsert1)>1) { ?>
  <hr>
  <div class='text-right'>
    <button class='btn btn-default' onclick="copyToClipboard('#insertCode1')">
      <i class='fa fa-clipboard'></i>
      Copy
    </button>
    <b class='pull-left'>Data Model</b>
  </div>
  <pre id='insertCode1'><?=$dataInsert1?></pre>
  <?php } ?>
  <?php if($dataInsert && strlen($dataInsert)>1) { ?>
  <hr>
  <div class='text-right'>
    <button class='btn btn-default' onclick="copyToClipboard('#insertCode')">
      <i class='fa fa-clipboard'></i>
      Copy
    </button>
    <b class='pull-left'>Insert Query 1</b>
  </div>
  <code id='insertCode'>
      <?=$dataInsert?>
  </code>
  <?php } ?>
  <?php if($dataInsert2 && strlen($dataInsert2)>1) { ?>
  <hr>
  <div class='text-right'>
    <button class='btn btn-default' onclick="copyToClipboard('#insertCode')">
      <i class='fa fa-clipboard'></i>
      Copy
    </button>
    <b class='pull-left'>Logiks Insert Code</b>
  </div>
  <code id='insertCode'>
      <?=$dataInsert2?>
  </code>
  <?php } ?>
</div>
<br><br>
<script>
function copyToClipboard(eleTag) {
    text = $(eleTag).text();
  
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