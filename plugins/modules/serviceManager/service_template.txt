<?php
if(!defined('ROOT')) exit('No direct script access allowed');
checkServiceAccess();

switch($_REQUEST['action']) {
  case "do-something":
    //Write your service execution code here
  break;
}
?>