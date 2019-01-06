<?php
if(!defined('ROOT')) exit('No direct script access allowed');

$editorConfig=array(
        "fname"=>array(
            //"logiks.json"=>dirname(__FILE__)."/plugins/logikspackage.php",
        ),
		"layouts"=>array(
			"pages/defn/"=>dirname(__FILE__)."/plugins/code.php",
		),
		"mimes"=>array(
            //"ini"=>dirname(__FILE__)."/plugins/ini.php",
		),
		"srctype"=>array(
			"text"=>dirname(__FILE__)."/plugins/code.php",
			"image"=>dirname(__FILE__)."/plugins/image.php",
		),
		"mime-map"=>array(
			"js"=>"javascript",
			"tpl"=>"html",
			"htm"=>"html",
			"cfg"=>"ini"
		)
	);
?>
