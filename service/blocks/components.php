<?php
if (!defined('ROOT')) exit('No direct script access allowed');
checkServiceSession();

if(!$_REQUEST["forsite"]) {
	printErr("WrongFormat","Command Missing Argument");
	exit();
}
checkUserSiteAccess($_REQUEST['forsite'],true);
if(isset($_REQUEST["action"])) {
	loadModule("dbcon");loadFolderConfig();
	$lf=$_SESSION["APP_FOLDER"]["APPROOT"].$_SESSION["APP_FOLDER"]["APPS_PAGES_FOLDER"]."comps/";
	
	if(!is_dir($lf)) {
		if(mkdir($lf,0777,true)) {
			chmod($lf,0777);
		}
	}
	if(!is_dir($lf)) {
		$arr=array(
				"Error"=>"Failed To Find Component Folder.",
			);
		printFormattedArray($arr);
		exit();
	}
	
	if($_REQUEST["action"]=="list") {
		if(is_dir($lf)) {
			$fs=scandir($lf);
			unset($fs[0]);unset($fs[1]);
			foreach($fs as $a) {
				$t=$a;
				$t=str_replace(".php","",$t);
				$t=str_replace(".htm","",$t);
				$t=str_replace(".html","",$t);
				$t=str_replace(".dgn","",$t);
				$t=ucwords($t);
				if(is_writable($lf.$a)) {
					echo "<tr rel='$a'><td class='okicon'></td><td>$t</td></tr>";
				} else {
					echo "<tr rel='$a'><td class='notokicon'></td><td style='padding-left:25px;'>$t</td></tr>";
				}
				
			}
		} else {
			echo "<tr><th>No Component Found</th></tr>";
		}
		exit();
	} elseif($_REQUEST["action"]=="data" && isset($_REQUEST["comp"])) {
		$f=$lf.$_REQUEST["comp"];
		if(file_exists($f)) {
			readfile($f);
			exit();
		} else {
			exit("No Component Found. May be it was deleted earlier.");
		}
	} elseif($_REQUEST["action"]=="save" && isset($_REQUEST["comp"]) && isset($_POST["data"])) {
		$f=$lf.$_REQUEST["comp"];
		if(is_writable($f)) {
			$data=$_POST["data"];
			$data=cleanText($data);
			$a=file_put_contents($f,$data);
			if($a==strlen($data)) echo "Successfully Saved Component";
			else {
				exit("Error In Updating Component. Try Again.");
			}
		} else {
			exit("The Component File Is ReadOnly. It Could Not Be Updated.");
		}
		exit();
	} elseif($_REQUEST["action"]=="delete" && isset($_REQUEST["comp"])) {
		$f=$lf.$_REQUEST["comp"];
		if(file_exists($f)) {
			if(!is_writable($f)) {
				exit("The component is readonly.");
			}
			if(!unlink($f)) {
				exit("The Component File Is ReadOnly. It Could Not Be Deleted.");
			}
			exit();
		} else {
			exit("No Component Found. May be it was deleted earlier.");
		}
	} elseif($_REQUEST["action"]=="clone" && isset($_REQUEST["comp"]) && isset($_REQUEST["newComp"])) {
		$_REQUEST["newComp"]=str_replace(" ","_",$_REQUEST["newComp"]);
		$src=$lf.$_REQUEST["comp"];
		$dest=$lf.$_REQUEST["newComp"].".php";
		if(copy($src,$dest)) {
			chmod($dest,0777);
		} else {
			echo "Failed To Clone Component";
		}
		exit();
	} elseif($_REQUEST["action"]=="blank" && isset($_REQUEST["comp"])) {
		$_REQUEST["comp"]=str_replace(" ","_",$_REQUEST["comp"]);
		$f=$lf.$_REQUEST["comp"].".php";
		if(file_exists($f)) {
			exit("<b>{$_REQUEST["comp"]}</b> Component Exists Already, Please change the name.");
			exit();
		}
		$a=file_put_contents($f,"");
		if(file_exists($f)) {
			chmod($f,0666);
		} else {
			exit("Error In Updating Component. Try Again.<br/>May Be The Component Directory Is ReadOnly.");
		}
		exit();
	}
}
printErr("WrongFormat");
exit();
?>
