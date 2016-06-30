<?php
if(!defined('ROOT')) exit('No direct script access allowed');

function siteList() {
	$sites=_session("siteList");
	$html="<option value='*'>All Sites</option>";
	foreach ($sites as $key => $value) {
		$html.="<option value='{$value['title']}'>{$key}</option>";
	}
	return $html;
}
?>
<div class="col-xs-12">
	<div class="row">
		<div class="col-sm-12">
			<h1>
				<i class="fa fa-user"></i>
				<span><?=$title?></span>
				<i class='fa fa-times pull-right close-frame'></i>
			</h1>
		</div>
	</div>
	<br/>
	<div class="row">
		<div class="col-sm-12 col-lg-12">
        	<?php
        		printForm($mode,__DIR__."/forms/{$form}.json",true,$where);
        	?>
        </div>
    </div>
</div>