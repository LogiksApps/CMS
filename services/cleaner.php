<?php
if(!defined('ROOT')) exit('No direct script access allowed');
checkServiceSession();

//echo $_REQUEST["forsite"];
$site=$_REQUEST["forsite"];

$cacheFolders=[
		"configs"=>[ROOT.TMP_FOLDER."configs/"],
		"logs"=>[ROOT.TMP_FOLDER."logs/$site/"],
		"classes"=>[ROOT.TMP_FOLDER."classes/"],
		"cache"=>[
				ROOT.TMP_FOLDER."cache/metaCache/$site/",
				ROOT.TMP_FOLDER."cache/dataCache/$site/",
			],
		"templates"=>[ROOT.CACHE_TEMPLATES_FOLDER."$site/"],
		"appcache"=>[ROOT.TMP_FOLDER."apps/$site/"],
	];

switch (strtoupper($_REQUEST['action'])) {
	case 'PURGE:CONFIGS':
		$cacheDir=$cacheFolders['configs'][0];
		startPurge($cacheDir);
		break;
	case 'PURGE:CLASSES':
		foreach ($cacheFolders['classes'] as $pth) {
			if(is_dir($pth)) {
				purge($pth);
			}
		}
		printServiceMsg("done");
		break;
	case 'PURGE:CACHE':
		foreach ($cacheFolders['cache'] as $pth) {
			if(is_dir($pth)) {
				purge($pth);
			}
		}
		
		$resHashID = uniqid();
		_cache("RESOURCEHASHID", $resHashID);
		
		printServiceMsg("done");
		break;
	case 'PURGE:TEMPLATES':
		foreach ($cacheFolders['templates'] as $pth) {
			if(is_dir($pth)) {
				purge($pth);
			}
		}
		printServiceMsg("done");
		break;
	case 'PURGE:APPCACHE':
		foreach ($cacheFolders['appcache'] as $pth) {
			if(is_dir($pth)) {
				purge($pth);
			}
		}
		printServiceMsg("done");
		break;


	// case 'PURGE:META':
	// 	printServiceMsg("PURGE META CACHE");
	// 	break;
	// case 'PURGE:DATA':
	// 	printServiceMsg("PURGE DATA CACHE");
	// 	break;
	// case 'PURGE:TEMPLATE':
	// 	printServiceMsg("PURGE TEMPLATE CACHE");
	// 	break;
	// case 'PURGE:ALL':
	// 	//PURGE ALL
	// 	echo "PURGE ALL CACHE";
	// 	break;



	case "TMPSIZE":
		loadHelpers('files');
		
		$data=$cacheFolders;
		foreach ($data as $key => $dirs) {
			$size=0;
			foreach ($dirs as $pth) {
				if(file_exists($pth)) {
					$nx=getDirSize($pth);
					if(is_numeric($nx)) $size+=$nx;
				}
			}
			$data[$key]=$size;
		}

		foreach ($data as $key => $value) {
			if(is_numeric($value)) {
				$data[$key]=[
						"size"=>$value,
						"text"=>getFileSizeInString($value),
					];
			} else {
				$data[$key]="0 B";
			}
		}
		printServiceMsg($data);
		break;
}

function startPurge($cacheDir) {
	if(!is_dir($cacheDir)) {
		printServiceMsg("Error purging, unable to find cache dir.");
		return false;
	}
	
	purge($cacheDir);

	if(!file_exists($cacheDir)) {
		mkdir($cacheDir,0777,true);
		file_put_contents("{$cacheDir}.htaccess", "deny for all");
	}

	$fs=scandir($cacheDir);
	if(count($fs)>3) {
		printServiceMsg("Error purging, please try clearing this manually.");
	} else {
		printServiceMsg("done");
	}
}

function purge($directory,$ext="*",$removeParent=false) {
    foreach(glob("{$directory}/{$ext}") as $file) {
        if(is_dir($file)) { 
            purge($file,$ext,true);
        } else {
            unlink($file);
        }
    }
    if($removeParent) {
    	rmdir($directory);
    }
}
?>