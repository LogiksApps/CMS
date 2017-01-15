<?php
if(!defined('ROOT')) exit('No direct script access allowed');

$cfg=ROOT.APPS_FOLDER.$app."/apps.cfg";
if(!file_exists($cfg)) {
	echo "<h3 align=center>Sorry, app is not configurable or not found.</h3>";
	return;
}

$cfgArr=LogiksConfig::parseConfigFile($cfg);
	
//printArray($cfgArr);

function getEditorField($key,$keyName,$val) {
	$html="<input type='text' class='form-control' id='fld{$keyName}' placeholder='{$keyName}' name='{$key}' data-value='{$val}' value='{$val}' />";
	switch($keyName) {
		case "APPS_TEMPLATEENGINE":
			$html="<select class='form-control' id='fld{$keyName}' name='{$key}' data-value='{$val}'><option value='smarty' selected>smarty</option></select>";
			break;
		case "APPS_STATUS":
			$html="<select class='form-control' id='fld{$keyName}' name='{$key}' data-value='{$val}'>";
			$html.="<option value='development'".(($val=="development")?"selected":"").">Development</option>";
			$html.="<option value='staging'".(($val=="staging")?"selected":"").">Staging</option>";
			$html.="<option value='production'".(($val=="production")?"selected":"").">Production</option>";
			$html.="</select>";
			break;
		case "APPS_ROUTER":
			$html="<select class='form-control' id='fld{$keyName}' name='{$key}' data-value='{$val}'>";
			$html.="<option value='legacy'".(($val=="legacy")?"selected":"").">Legacy</option>";
			$html.="<option value='simple'".(($val=="simple")?"selected":"").">Simple</option>";
			$html.="<option value='logiks'".(($val=="logiks")?"selected":"").">Logiks</option>";
			$html.="<option value='route.php'".(($val=="route.php")?"selected":"").">Custom Route</option>";
			$html.="</select>";
			break;
		case "DEV_MODE_ENABLED":
			$html="<select class='form-control' id='fld{$keyName}' name='{$key}' data-value='{$val}'>";
			$html.="<option value='true'".(($val=="true")?"selected":"").">True</option>";
			$html.="<option value='false'".(($val=="false")?"selected":"").">False</option>";
			$html.="</select>";
			break;
		case "PUBLISH_MODE":
			$html="<select class='form-control' id='fld{$keyName}' name='{$key}' data-value='{$val}'>";
			$html.="<option value='publish'".(($val=="publish")?"selected":"").">Published</option>";
			$html.="<option value='maintainance'".(($val=="maintainance")?"selected":"").">Under Maintainance</option>";
			$html.="<option value='underconstruction'".(($val=="underconstruction")?"selected":"").">Under Construction</option>";
			$html.="<option value='restricted'".(($val=="restricted")?"selected":"").">Restricted (401)</option>";
			$html.="</select>";
			break;
		case "ACCESS":
			$html="<select class='form-control' id='fld{$keyName}' name='{$key}' data-value='{$val}'>";
			$html.="<option value='public'".(($val=="public")?"selected":"").">Public</option>";
			$html.="<option value='private'".(($val=="private")?"selected":"").">Private</option>";
			$html.="</select>";
			break;
		
		case "APPS_VERS":
			//$html="<input type='number' class='form-control' id='fld{$keyName}' name='{$key}' placeholder='{$keyName}' data-value='{$val}' value='{$val}' />";
			break;
		case "WEBMASTER_EMAIL":
			$html="<input type='email' class='form-control' id='fld{$keyName}' name='{$key}' placeholder='{$keyName}' data-value='{$val}' value='{$val}' />";
			break;
	}
	return $html;
}
?>
<div class='formbox' style='height: 60%;overflow: auto;'>
	<form class="form-horizontal">
		<?php
			foreach($cfgArr as $k=>$cfg) {
				$t=toTitle($cfg['name']);
		?>
		<div class="form-group" style='width:100%;'>
			<label for="fld<?=$cfg['name']?>" class="col-sm-4 control-label"><?=$t?></label>
			<div class="col-sm-8">
				<?=getEditorField($k,$cfg['name'],$cfg['value'])?>
			</div>
		</div>
		<?php
			}
		?>
	</form>
</div>