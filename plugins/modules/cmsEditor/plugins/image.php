<?php
if(!defined('ROOT')) exit('No direct script access allowed');

$toolbarTools=array(
		// "swap"=>[
		// 		"icon"=>"<i class='icon fa fa-upload'></i>",
		// 		"title"=>"Swap",
		// 	],
		// "rotate-left"=>[
		// 		"icon"=>"<i class='icon fa fa-rotate-left'></i>",
		// 	],
		// "rotate-right"=>[
		// 		"icon"=>"<i class='icon fa fa-rotate-right'></i>",
		// 	],
		// "resize"=>[
		// 		"icon"=>"<i class='icon fa fa-arrows'></i>",
		// 	],
		// "crop"=>[
		// 		"icon"=>"<i class='icon fa fa-crop'></i>",
		// 	],
	);

$imgSrc=getWebPath($srcFile);//.basename($srcFile)
?>
<?=_css("cmsEditor")?>
<style>
html,body,.container-fluid {
	height: 100%;
}
.container-fluid {
	padding-top: 30px;
}
#editorToolbar {
	border-bottom: 1px solid #DDD;
}
.imageHolder {
	text-align: center;
	height: 100%;
	overflow: auto;
	text-align: center;
	display: flex;
	justify-content: center;
	align-items: center;
	background: #1f1b1b;
}
.imageHolder img {
	max-width: 100%;
	max-height: 100%;
	margin: auto;
}
</style>
<div id='editorToolbar'>
	<?php
		foreach ($toolbarTools as $key => $value) {
			if(!isset($value['title'])) $value['title']="";
			$value['title']=_ling($value['title']);
			echo "<a href='#' class='btn' cmd='{$key}'>{$value['icon']}{$value['title']}</a>";
		}
	?>
	<div class='pull-right'>
		<a href='#' class='btn' cmd='settings'><i class='icon fa fa-cog'></i></a>
	</div>
</div>
<div class='imageHolder col-sm-12 col-xs-12 col-md-12'>
	<img src='<?=$imgSrc?>' />
</div>

