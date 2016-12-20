<?php
if(!defined('ROOT')) exit('No direct script access allowed');

$editorConfig=array(
		"layouts"=>array(
			"pages/defn/"=>dirname(__FILE__)."/plugins/code.php",
		),
		"mimes"=>array(

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
