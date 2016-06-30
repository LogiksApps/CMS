<?php
if(!defined('ROOT')) exit('No direct script access allowed');

$cfgFile=ROOT.CFG_FOLDER."jsonConfig/cache.json";
$jsonMap=json_decode(file_get_contents($cfgFile),true);	

if(isset($jsonMap["GLOBALS"])) $globals=$jsonMap['GLOBALS'];
else $globals=[];
if(isset($jsonMap[$_REQUEST['forSite']])) $locals=$jsonMap[$_REQUEST['forSite']];
else $locals=[];

//printArray($globals);

?>
<div class="row">
<?php
	if($_SESSION['SESS_PRIVILEGE_ID']==1) {
		foreach ($globals as $key => $config) {
			$cfgPrint=$config;
			unset($cfgPrint['params']);
			$html1="";
			if(isset($config['params'])) {
				$html1="<h5>PARAMS</h5>".arrayToHTML($config['params'],"table","table table-condensed table-striped");
			}
			printCFGCard("GLOBALS",$key,[
						"title"=>toTitle(_ling("{$key} Key")),
						"body"=>arrayToHTML($cfgPrint,"table","table table-condensed table-striped").$html1,
					],$config);
		}
		echo "<hr class='cardHR'>";
	}
	foreach ($locals as $key => $config) {
		$cfgPrint=$config;
		unset($cfgPrint['params']);
		$html1="";
		if(isset($config['params'])) {
			$html1="<h5>PARAMS</h5>".arrayToHTML($config['params'],"table","table table-condensed table-striped");
		}
		printCFGCard("APP",$key,[
					"title"=>toTitle(_ling("{$key} Key")),
					"body"=>arrayToHTML($cfgPrint,"table","table table-condensed table-striped").$html1,
				],$config);
	}
?>
</div>