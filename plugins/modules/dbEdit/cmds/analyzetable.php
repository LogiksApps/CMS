<?php
if(!defined('ROOT')) exit('No direct script access allowed');

if(isset($_POST['ref']) && strlen($_POST['ref'])>0) {
  $tbl = $_POST['ref'];
  $tbl = clean($tbl);

  $data1=_db($dbKey)->_RAW("ANALYZE TABLE `{$tbl}`")->_GET();  
  $data2=_db($dbKey)->_RAW("CHECK TABLE `{$tbl}`")->_GET();

  if(count($data1)==0 && count($data2)==0) {
    echo "No output from command query";
  } else {
      echo "<p>Analysis of Table</p>";
      if(count($data1)==1) {
        echo arrayToHTML($data1[0],"table","table table-bordered table-hover");
      } else {
        printDataInTable($data1[0]);
      }
    
      echo "<p>Checking of Table</p>";
      if(count($data2)==1) {
        echo arrayToHTML($data2[0],"table","table table-bordered table-hover");
      } else {
        printDataInTable($data2[0]);
      }
  }
} else {
  echo "Reference Not Found";
}
?>