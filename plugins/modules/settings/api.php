<?php
if(!defined('ROOT')) exit('No direct script access allowed');

if(!function_exists("getCfgField")) {
	
	$supportedCFGTypes=["core","apps","cms","plugins"];
	
	function getCfgField($cfgName,$bType,$key,$value,$title=false,$disabled=false,$keyHash=false,$cfgSchema=[]) {
		if(!$keyHash) $keyHash=md5($cfgName.$type.$key);
		if(!$title) $title=toTitle(_ling($key));
		if($disabled) $disabled="disabled";
		else $disabled="";

		$type="text";

		if($value=="true" || $value=="false") $type='CHECKBOX|TRUE';
		elseif($value=="yes" || $value=="no") $type='CHECKBOX|YES';
		elseif(is_numeric($value)) $type="NUMBER";
		elseif(strpos(strtoupper($value), "_DATE")>0) $type="DATE";

		switch (strtoupper(current(explode("|", $type)))) {
			case 'CHECKBOX':
				$value=strtoupper($value);
				$checkStats=($value=="TRUE"||$value=="YES"||$value==1)?"checked":"";
				$html="<input name='{$bType}:{$key}' type='checkbox' class='form-control switch' id='{$keyHash}' data-oldvalue='{$value}' {$checkStats} value='true' {$disabled} />";
				break;
			
			case "NUMBER":
				$html="<input name='{$bType}:{$key}' type='number' class='form-control' id='{$keyHash}' data-oldvalue='{$value}' value='{$value}' {$disabled} />";
				break;

			case "DATE":
				$value=_pDate($value);
				$html="<input name='{$bType}:{$key}' type='date' class='form-control' id='{$keyHash}' data-oldvalue='{$value}' value='{$value}' {$disabled} />";
				break;

			default:
				$html="<input name='{$bType}:{$key}' type='text' class='form-control' id='{$keyHash}' placeholder='{$title}'  value='{$value}' data-oldvalue='{$value}' {$disabled} />";
				break;
		}

		return $html;
	}

	function printCFGForm($cfgFile) {
		$cfgName=basename($cfgFile);

		$cfgSchema=[];
		$schemaFile=ROOT.CFG_FOLDER."schemas/".str_replace(".cfg", ".php", $cfgName);
		if(file_exists($schemaFile)) {
			include_once $schemaFile;
		}
		if($cfgSchema==null) $cfgSchema=[];

		$html=[];
		$disabled=!is_writable($cfgFile);
		if(file_exists($cfgFile)) {
			$data=ConfigFileReader::LoadFile($cfgFile);

			foreach ($data as $type => $configs) {
				foreach ($configs as $key => $value) {
					$keyHash=md5($cfgName.$type.$key);
					$title=toTitle(_ling($key));
					$html[]="<div class='form-group'>"."<label class='col-sm-4 control-label'>$title</label>".// for='{$keyHash}'
							"<div class='col-sm-8'>".getCfgField($cfgName,$type,$key,$value,$title,$disabled,$keyHash,$cfgSchema)."</div>".
							"</div>";
				}
			}
		}

		return "<form class='form-horizontal' autocomplete=off>".implode("", $html)."</form>";
	}
}
?>