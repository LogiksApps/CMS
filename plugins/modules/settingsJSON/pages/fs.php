<?php
if(!defined('ROOT')) exit('No direct script access allowed');

$cfgFile=ROOT.CFG_FOLDER."jsonConfig/fs.json";
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
    	if(isset($config['dirs'])) unset($config['dirs']);
    	if(isset($config['exclude_dirs'])) unset($config['exclude_dirs']);
    		
    	printCFGCard("APP",$key,[
    				"title"=>toTitle(_ling("{$key} Key")),
    				"body"=>arrayToHTML($config,"table","table table-condensed table-striped"),
    			],$config,$cfgFile);
    }
    if($_SESSION['SESS_PRIVILEGE_ID']==1) {
        echo "<hr class='cardHR'>";
	    foreach ($globals as $key => $config) {
			if($key=="app") continue;
			if($key=="dir_rules") continue;
			
			if(isset($config['dirs'])) unset($config['dirs']);
			if(isset($config['exclude_dirs'])) unset($config['exclude_dirs']);
			
			printCFGCard("GLOBALS",$key,[
						"title"=>toTitle(_ling("{$key} Key")),
						"body"=>arrayToHTML($config,"table","table table-condensed table-striped"),
					],$config,$cfgFile);
		}
	}
?>
</div>