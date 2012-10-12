<?php
if(!defined('ROOT')) exit('No direct script access allowed');
checkServiceSession();
checkUserSiteAccess($_REQUEST['forsite'],true);
user_admin_check(true);

set_time_limit(0);

$sourcefileTobackup=ROOT.APPS_FOLDER.$_REQUEST["forsite"]."/";
$serviceTobackup=ROOT."services/php/".$_REQUEST["forsite"]."/";

$dir=ROOT.BACKUP_FOLDER.$_REQUEST["forsite"]."/";
$dirStamp=date("Y-m-d H:i:s");
$cacheDir=ROOT.CACHE_FOLDER."backup/";

$f2=$cacheDir.md5("backup_{$_REQUEST['forsite']}_".time().session_id())."/";

loadHelpers("files");

$a=mkdirs($f2);
if(!$a) {
	exit("Backup Cache Dir Could Not Be Configured ...");
}

if(!is_dir($dir)) {
	$a=mkdir($dir,0777,true);
	if(!$a) {
		exit("Backup Source Dir Could Not Be Configured ...");
	}
	chmod($dir,0777);
} else {
	if(!is_writable($dir)) {
		exit("Backup Source Dir Is ReadOnly ...");
	}
}

$htaccessFile=ROOT.BACKUP_FOLDER."/.htaccess";
if(!file_exists($htaccessFile))
	file_put_contents($fs,"deny from all\n");

$_SESSION["BACKUP"]["DEBUG"]=false;
$_SESSION["BACKUP"]["CACHE_DIR"]=$f2;
$_SESSION["BACKUP"]["BACKUP_DIR"]=$dir;
$_SESSION["BACKUP"]["BACKUP_FILE_NAME"]="{$dirStamp}.zip";

register_shutdown_function("cleanupBackupCache");

if(isset($_REQUEST["action"])) {
	if($_REQUEST['action']=='load_backup') {
		$backups=loadBackups($dir);
		if(is_array($backups)) {
			foreach($backups as $backup){
				$fx=$dir.$backup['name'];
				
				$dt1=date ("F d Y", filemtime($fx));
				$dt2=date ("H:i:s", filemtime($fx));
				$size=getFileSizeInString(fileSize($fx));
				
				$s="<tr>
					<td width=153px align=left>$dt1</td>
					<td width=153px align=center>$dt2</td>
					<td width=93px align=center>$size</td>
					<td width=83px align=center><button rel='".$backup['name']."' class='toolbutton' onclick='doDownloadBackup(this);return false;'
					id='downloadbtn'>Download</button></td>
					<td width=83px align=center><button rel='".$backup['name']."' class='toolbutton' onclick='doDeleteBackup(this);return false;' 
					id='deletebtn'>Delete</button></td>
					<td width=83px align=center><button rel='".$backup['name']."' class='toolbutton' onclick='doRollBack(this);return false;' 
					id='rollbackbtn'>Rollback</button></td></tr>";
					
				echo $s;
			}
		}
	} elseif($_REQUEST['action']=='do_backup'){
		$e=createBackup($sourcefileTobackup,$serviceTobackup);
		if($e){
			echo '<div class="successbox" id="success" style="display:;">New backup created successfully.</div>';
		}else{
			echo '<div class="errorbox" id="error" style="display:;">Sorry backup cannot be created.</div>';
		}
	} elseif($_REQUEST['action']=='do_delete') {
		deleteBackup($dir);
		//$fl=$basename($dir);
		//echo "Back up taken on $fl is removed from system";//$date and $time
	} elseif ($_REQUEST['action']=='do_download') {
		downloadBackup($dir);
	} elseif($_REQUEST['action']=='do_rollback') {
		rollbackBackup($dir);
	}
}

function loadBackups($directory, $filter=FALSE){
     if(substr($directory,-1) == '/'){
         $directory = substr($directory,0,-1);
     }
     if(!file_exists($directory) || !is_dir($directory)){
         return FALSE;
     } elseif(is_readable($directory)) {
         $directory_tree = array();
         $directory_list = opendir($directory);
         while($file = readdir($directory_list)){
             if($file != '.' && $file != '..'){
                 $path = $directory.'/'.$file;
                 if(is_readable($path)){
                     $subdirectories = explode('/',$path);
                     if(is_dir($path)) {
                         $directory_tree[] = array(
                             'path'      => $path,
                             'name'      => end($subdirectories),
                             'kind'      => 'directory',
                             'content'   => loadBackups($path, $filter));
                     }elseif(is_file($path)){
                         $extension = end(explode('.',end($subdirectories)));
                         if($filter === FALSE || $filter == $extension) {
                             $directory_tree[] = array(
                             'path'        => $path,
                             'name'        => end($subdirectories),
                             'extension' => $extension,
                             'size'        => filesize($path),
                             'kind'        => 'file');
                         }
                     }
                 }
             }
         }
         closedir($directory_list);
         return $directory_tree;
     }else{
         return false;
     }
}

function createBackup($source="",$service="") {
	$dir=$_SESSION["BACKUP"]["CACHE_DIR"];
	$xname=$_SESSION["BACKUP"]["BACKUP_FILE_NAME"];
	
	$source_size=foldersize($source);
	$service_size=foldersize($service);
	if(($source_size > 2000000000) || ($service_size > 2000000000)) {
		exit("Backup Error");
	} else {
		Zip($source, $dir."www.zip");
		Zip($service, $dir."service.zip");
	}
		
	$bool=backup_tables($dir);
	
	if(!$bool) {
		exit("Backup Error");
	} else {
		Zip($dir,$_SESSION["BACKUP"]["BACKUP_DIR"].$xname);
		chmod($_SESSION["BACKUP"]["BACKUP_DIR"].$xname,0777);
	}		
	return file_exists($_SESSION["BACKUP"]["BACKUP_DIR"].$xname);
}
function getDBControls($site) {
	$dbFile=ROOT.APPS_FOLDER.$site."/config/db.cfg";
	if(file_exists($dbFile)) {
		LoadConfigFile($dbFile);
		$con=new Database($GLOBALS['DBCONFIG']["DB_DRIVER"]);
		$con->connect($GLOBALS['DBCONFIG']["DB_USER"],$GLOBALS['DBCONFIG']["DB_PASSWORD"],$GLOBALS['DBCONFIG']["DB_HOST"],$GLOBALS['DBCONFIG']["DB_DATABASE"]);
		return $con;
	} else {
		printErr("NotSupported","DB Configuration Missing For Site");
	}
}
/*
function Zip($source, $destination){
    if(!extension_loaded('zip') || !file_exists($source)) {
		echo "Zip Library Not Loaded";
        return false;
    }

    $zip = new ZipArchive();
    if (!$zip->open($destination, ZIPARCHIVE::CREATE)) {
        return false;
    }

    $source = str_replace('\\', '/', realpath($source));

    if(is_dir($source) === true) {
        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source), RecursiveIteratorIterator::SELF_FIRST);
		foreach($files as $file){
            $file = str_replace('\\', '/', realpath($file));
			if($_SESSION["BACKUP"]["DEBUG"]) echo "$file <br/>";
			if(is_dir($file) === true) {
               // $zip->addEmptyDir(str_replace($source . '/', '', $file . '/'));
            } elseif (is_file($file) === true) {
                $zip->addFromString(str_replace($source . '/', '', $file), file_get_contents($file));
            }
        }
    } elseif(is_file($source) === true){
        $zip->addFromString(basename($source), file_get_contents($source));
    }
    if($_SESSION["BACKUP"]["DEBUG"]) echo "<br/>$source<hr/>";
    return $zip->close();
}
*/
/*-------------------------------zipping codes----------------------------------------------------------------*/
function recurse_zip($src,&$zip,$path) {
	$dir = opendir($src);
	while(false !== ( $file = readdir($dir)) ) {
		if (( $file != '.' ) && ( $file != '..' )) {
			if(is_readable($src."/".$file)){
				if ( is_dir($src . '/' . $file) ) {
					recurse_zip($src . '/' . $file,$zip,$path);
				} else {					 
					$zip->addFile($src . '/' . $file,substr($src . '/' . $file,$path));
					//echo $src . '/' . $file."<br>";
				}
			} else{
				echo "Nonreadable :".$src."/".$file."<br>";
			}
			
		}
	}
	closedir($dir);
}
function Zip($src,$dst='') {
	if(substr($src,-1)==='/') {$src=substr($src,0,-1);}
	if(substr($dst,-1)==='/') {$dst=substr($dst,0,-1);}
	$path=strlen(dirname($src).'/');
	$filename=substr($src,strrpos($src,'/')+1).'.zip';
	$dst=empty($dst)? $filename : $dst;
	//@unlink($dst);
	$zip = new ZipArchive;
	$res = $zip->open($dst, ZipArchive::CREATE);
	if($res !== TRUE){
		echo 'Error: Unable to create zip file';
		exit;
	}
	if(is_file($src)) {		
		$zip->addFile($src,substr($src,$path));		
	}
	else {
		if(!is_dir($src)){
			 $zip->close();
			 //@unlink($dst);
			 echo 'Error: File not found';
			 exit;
		}
		recurse_zip($src,$zip,$path);
	}
	
	$zip->close();       
	return $dst;
}

function foldersize($path) {
    $total_size = 0;
    $files = scandir($path);
    foreach($files as $t) {
        if (is_dir(rtrim($path, '/') . '/' . $t)) {
            if ($t<>"." && $t<>"..") {
                $size = foldersize(rtrim($path, '/') . '/' . $t);
                $total_size += $size;
            }
        } else {
            $size = filesize(rtrim($path, '/') . '/' . $t);
            $total_size += $size;
        }   
    }
    return $total_size;
}
function unzip($zip_file,$unzip_dir){	
	$zip = new ZipArchive;
	if ($zip->open($zip_file) === TRUE) {
		 for($i = 0; $i < $zip->numFiles; $i++) {
								 
				$zip->extractTo($unzip_dir, array($zip->getNameIndex($i)));				
				if($zip->getNameIndex($i)=='www.zip'){									
					unzip($unzip_dir.$zip->getNameIndex($i),$unzip_dir);
					unlink($unzip_dir.$zip->getNameIndex($i));
				}
				if($zip->getNameIndex($i)=='service.zip'){									
					unzip($unzip_dir.$zip->getNameIndex($i),ROOT."services/php/".$_REQUEST["forsite"]."/");
					unlink($unzip_dir.$zip->getNameIndex($i));
				}
				if($zip->getNameIndex($i)=='sql.zip'){
					unzip($unzip_dir.$zip->getNameIndex($i),$unzip_dir);
					unlink($unzip_dir.$zip->getNameIndex($i));					
					doDBRestore($unzip_dir.'db-backup.sql');
				}			
			}
		$zip->close();
	} else
	   return false;
}
function doDBRestore($file){
	$link =getDBControls($_REQUEST["forsite"]); 
	$sql_file=$file;
	$file_content = file($sql_file);
	$query = "";
	foreach($file_content as $sql_line) {
		if(trim($sql_line) != "" && strpos($sql_line, "--") === false){
			$query .= $sql_line;
			if (substr(rtrim($query), -1) == ';') {
				$result = $link->executeQuery($query);
				$query = "";
			}
		}
	}
	//echo $sqlfile;
	unlink($sql_file);
}
function backup_tables($dir,$tables = '*') { 
	$return="";
	$link =getDBControls($_REQUEST["forsite"]);  
	//get all of the tables    
	if($tables == '*'){
		$tables = $link->getTableList();   
	} else {
		$tables = is_array($tables) ? $tables : explode(',',$tables);
	}	
	//cycle through
	foreach($tables as $table) { 
		$result =  $link->executeQuery('SELECT * FROM '.$table);
		$num_fields = $link->columnCount($result);

		$return.= 'DROP TABLE IF EXISTS '.$table.';';    
		$r=$link->executeQuery('SHOW CREATE TABLE '.$table);
		$row2 =$link->fetchData($r,"array");    
		$return.= "\n\n".$row2[1].";\n\n";
		
		for ($i = 0; $i < $num_fields; $i++) {
			while($row = $link->fetchData($result,"array")) {
				$return.= 'INSERT INTO '.$table.' VALUES(';        
				for($j=0; $j<$num_fields; $j++) {                
					$row[$j] = addslashes($row[$j]);          
					$row[$j] = preg_replace("/\n/","\\n",$row[$j]);
					if (isset($row[$j])) { $return.= '"'.$row[$j].'"' ; } else { $return.= '""'; }
					if ($j<($num_fields-1)) { $return.= ','; }
				}
				$return.= ");\n";
			}
		}
		$return.="\n\n\n";
	}
	$fs=$dir.'db-backup.sql';
	file_put_contents($fs,$return);
	
	if(fileSize($fs) > 3000000000) {
		exit("Backup Error");
	} else {
		Zip($fs, $dir."sql.zip");
		unlink($fs);
	}
	return true;	
	
}
function downloadBackup($file_path) {
	$file_name=$_REQUEST['file'];
	$f=$file_path.$file_name;
	$ok = true;
	if($ok){
		//$path_parts = pathinfo($file_path);
		//$file_name = $path_parts['basename'];
		$file = @fopen($f,"rb");	
		if ($file){
			header("Cache-Control: public");
			header("Pragma: public");
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Cache-Control: public");
			header("Content-Description: File Transfer");
			header("Content-Type: application/zip");
			header("Content-Disposition: attachment; filename=\"{$file_name}\"");
			header("Content-Transfer-Encoding: binary");
			header("Content-Length: " . filesize($f));
			while(!feof($file)){
				echo fread($file, 500*1024);
				//echo readfile($file);
				flush();
				if (connection_status()!=0){
					@fclose($file);
					die();
				}
			}
			@fclose($file);
			exit();
		}
	}
}
function deleteBackup($file_path){
	$file_name=$_REQUEST['file'];
	$str1=explode(" ",$file_name);
	$date=$str1[0];
	$str2=$str1[1];
	$str3=explode(".",$str1[1]);
	$time=$str3[0];
	$f=$file_path.$file_name;
	if(is_dir($f)) {
		deleteDir($f);
	} else {
		unlink($f);
	}
}
function rollbackBackup($file_path){
	$file_name=$_REQUEST['file'];
	$str1=explode(" ",$file_name);
	$date=$str1[0];
	$str2=$str1[1];
	$str3=explode(".",$str1[1]);
	$time=$str3[0];
	$f=$file_path.$file_name;
	$rollback_dir=ROOT.APPS_FOLDER.$_REQUEST["forsite"]."/";
	if(!file_exists($rollback_dir) || !is_dir($rollback_dir)) {
		mkdir($rollback_dir,777,true);
		chmod($rollback_dir,777);
	}
	if(!is_writable($rollback_dir)) {
		echo "Error restoring Site, Target In ReadOnly Mode";
		return false;
	}
	unzip($f,$rollback_dir);
	echo "Successfully restored your apps to date $date and $time";
}
function cleanupBackupCache() {
	if(is_dir($_SESSION["BACKUP"]["CACHE_DIR"])) deleteDir($_SESSION["BACKUP"]["CACHE_DIR"]);
	unset($_SESSION["BACKUP"]);
}
?>
