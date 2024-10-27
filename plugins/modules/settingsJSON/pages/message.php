<?php
if(!defined('ROOT')) exit('No direct script access allowed');

$cfgFile=ROOT.CFG_FOLDER."jsonConfig/message.json";
$jsonMap=json_decode(file_get_contents($cfgFile),true);	

if(isset($jsonMap["GLOBALS"])) $globals=$jsonMap['GLOBALS'];
else $globals=[];
if(isset($jsonMap[$_REQUEST['forSite']])) $locals=$jsonMap[$_REQUEST['forSite']];
else $locals=[];

//printArray($globals);

?>
<div class="row">
<?php
    foreach ($locals as $key => $config) {
		printCFGCard("APP",$key,[
					"title"=>toTitle(_ling("{$key} Key")),
					"body"=>arrayToHTML($config,"table","table table-condensed table-striped"),
				],$config,$cfgFile);
	}
	if($_SESSION['SESS_PRIVILEGE_ID']==1) {
	    echo "<hr class='cardHR'>";
		foreach ($globals as $key => $config) {
			printCFGCard("GLOBALS",$key,[
						"title"=>toTitle(_ling("{$key} Key")),
						"body"=>arrayToHTML($config,"table","table table-condensed table-striped"),
					],$config,$cfgFile);
		}
	}
?>
</div>