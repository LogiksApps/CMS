<?php
if(!defined('ROOT')) exit('No direct script access allowed');

if(isset($_POST['ref']) && strlen($_POST['ref'])>0) {
  $tbl = $_POST['ref'];
  $tbl = clean($tbl);
  $sql = "DROP TABLE `{$tbl}`";
  $data=_db($dbKey)->_RAW($sql)->_GET();

  echo "<p>Dropped Table</p>";
  if(count($data)==1) {
    echo arrayToHTML($data[0],"table","table table-bordered table-hover");
  } elseif(count($data)>1) {
    printDataInTable($data[0]);
  } else {
    echo "No output from command query";
  }
} else {
  echo "Reference Not Found";
}
?>