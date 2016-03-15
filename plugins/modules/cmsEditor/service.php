<?php
if(!defined('ROOT')) exit('No direct script access allowed');

checkServiceAccess();

if(!isset($_REQUEST["action"])) {
	printServiceErrorMsg("ERROR","Action Not Defined.");
}

include dirname(__FILE__)."/config.php";

switch ($_REQUEST['action']) {
	case 'getsrc':
		if(isset($_REQUEST['src'])) {
			$srcFile=getAppFile($_REQUEST['src']);
			echo file_get_contents($srcFile);
		} else {
			printServiceErrorMsg("ERROR","Source File Not Defined.");
		}
	break;
	case 'save':
		if(isset($_POST['src']) && isset($_POST['text'])) {
			if(md5($_POST['text'])==$_POST['hash']) {
				if(strlen($_POST['fname'])<=0) {
					$_POST['fname']=$_POST['src'];
				}
				if($_POST['fname']!=$_POST['src']) {
					$srcFile=getAppFile($_POST['src']);
					$srcFileOld=getAppFile($_POST['fname']);
					if(!is_dir(dirname($srcFile))) {
						mkdir(dirname($srcFile),0777,true);
					}
					copy($srcFileOld, $srcFile);
					unlink($srcFileOld);
				}
				$srcFile=getAppFile($_POST['src']);
				if(!file_exists($srcFile) || (is_writable($srcFile))) {
					$a=saveAppFile($srcFile,$_POST['text']);

					if($a>0) {
						printServiceMsg("saved");
					} else {
						printServiceErrorMsg("ERROR","Error writting source file, you should try again.");
					}
				} else {
					printServiceErrorMsg("ERROR","Source file readonly, no write permissions available.");
				}
			} else {
				printServiceErrorMsg("ERROR","Hash mismatch. Probably loss of data due to internet connection.");
			}
		} else {
			printServiceErrorMsg("ERROR","Source File Not Defined.");
		}
	break;
	case 'delete':
		if(isset($_POST['src'])) {
			if(strlen($_POST['fname'])<=0) {
				printServiceMsg("done");
				return;
			}
			if($_POST['fname']!=$_POST['src']) {
				$srcFile=getAppFile($_POST['src']);
				$srcFileOld=getAppFile($_POST['fname']);
				if(!is_dir(dirname($srcFile))) {
					mkdir(dirname($srcFile),0777,true);
				}
				copy($srcFileOld, $srcFile);
				unlink($srcFileOld);
			}
			$srcFile=getAppFile($_POST['src']);
			if(is_file($srcFile)) {
				if(is_writable($srcFile)) {
					unlink($srcFile);
					if(!file_exists($srcFile)) {
						printServiceMsg("deleted");
					} else {
						printServiceErrorMsg("ERROR","Error deleting source file, you should try again.");
					}
				} else {
					printServiceErrorMsg("ERROR","Source file readonly, no write permissions available.");
				}
			} else {
				printServiceErrorMsg("ERROR","Source File Not Found.");
			}
		} else {
			printServiceErrorMsg("ERROR","Source File Not Defined.");
		}
	break;
}
?>