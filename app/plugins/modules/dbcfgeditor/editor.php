<?php
if(!defined('ROOT')) exit('No direct script access allowed');

_js(array("dialogs","jquery.multiselect"));
_css(array("styletags","jquery.multiselect","formfields"));

include_once "CfgFunction.inc";

function loadScope($scope) {
	$webPath=getWebPath(__FILE__);
	$rootPath=getRootPath(__FILE__);
	
	$cfgArr=array();
	
	$sql="SELECT * FROM "._dbtable("config_sites",true)." WHERE site='{$_REQUEST['forsite']}' AND scope='$scope'";
	$result=_dbQuery($sql,true);
	if($result) {
		$data=_dbData($result);
		_dbFree($result,true);
		$cfgArr=$data;
	}
?>
<link href='<?=$webPath?>style.css' rel='stylesheet' type='text/css' media='all' /> 
<script src='<?=$webPath?>script.js' type='text/javascript' language='javascript'></script>
<div style='width:100%;height:100%;overflow:auto;overflow:hidden;'>
	<div id=toolbar class="toolbar ui-widget-header">
		<div class='left' style='margin-left:5px;'>
			<button title='Create A New Recruitment Process.' onclick="submitForm('#cfg_workspace')"><div class='saveicon'> Save</div> </button>
			<button title='Select A Recruitment Process To Start With.' onclick="resetForm('#cfg_workspace')"><div class='reseticon'> Cancel</div> </button>
			<button title='Show Help Contents.' onclick="showHelp()"><div class='helpicon'> Help</div> </button>
		</div>
		<div class='right' style='margin-right:5px;'>
			<h2>Scope :: <b><?=toTitle($scope)?></b></h2>
		</div>
	</div>
	<div id=cfg_workspace class="cfg_workspace">
		<table class='settingstable' border=0 cellpadding=0 cellspacing=0 style='width:100%;'>
			<?php
				foreach($cfgArr as $x) {
					$popup="";
					$a=$x['id'];
					$b=$x['value'];
					$c=$x['name'];
					$tips=$x['remarks'];
					$o="";
					if($b=="true" || $b=="false") {
						$o="<select id=\"$a\" name=\"$a\" value='$b'>";
						if(strtolower($b)=="true") $o.="<option value='true' selected>True</option>"; else $o.="<option value='true'>True</option>";
						if(strtolower($b)=="false") $o.="<option value='false' selected>False</option>"; else $o.="<option value='false'>False</option>";
						$o.="</select>";
					} elseif($b=="on" || $b=="off") {
						$o="<select id=\"$a\" name=\"$a\" value='$b'>";
						if(strtolower($b)=="true") $o.="<option value='on' selected>On</option>"; else $o.="<option value='on'>On</option>";
						if(strtolower($b)=="false") $o.="<option value='off' selected>Off</option>"; else $o.="<option value='off'>Off</option>";
						$o.="</select>";
					} elseif(isSetupDefined($x['type'])) {
						$oA=getHTMLElement($x,$a);
						$o=$oA["html"];
						$popup=$oA["popup"];
					} else {
						$o="<input id=\"$a\" name=\"$a\" type=text value=\"$b\" />";
					}
					$field=$o;
					printRow(array($c,$field,$tips,$popup),3);
				}
			?>
		</table>
	</div>	
</div>
<div id=msgdiv class='ui-state-highlight ui-corner-all'>Message Displayed Here</div>
<div style='display:none'>
	<div id='helpInfo' class='helpInfo' title='Help !' style='width:100%;text-align:justify;font-size:15px;font-family:verdana;'>
		<b>Modules Settings</b>, helps you manage the various configurations of different modules and scopes provided by the System.
	</div>
</div>
<script language=javascript>
scope="<?=$scope?>";
submitLink="services/?scmd=dbcfgeditor&site=<?=SITENAME?>&forsite=<?=$_REQUEST['forsite']?>&action=save&scope="+scope;
</script>
<?php
}
function printRow($arr) {
	$t=$arr[0];
	if(isset($arr[1])) $o=$arr[1]; else $o="";
	if(isset($arr[2])) $tips=$arr[2]; else $tips="";
	if(isset($arr[3])) $popup=$arr[3]; else $popup="";
	if(strlen($popup)>0) {
		if(strpos(".".$popup,"url#")==1) {
			$popup=substr($popup,4,strlen($popup)-4);
			$popup="<div class='linkicon' title='$tips' onclick=\"popupLink(this,'Help On : $t')\"><div class='popupdata' style='display:none'>$popup</div></div>";
		} elseif(strpos(".".$popup,"js#")==1) {
			$popup=substr($popup,3,strlen($popup)-3);
			$popup="<div class='btnicon' title='$tips' onclick=\"openJS(this,'Help On : $t')\"><div class='popupdata' style='display:none'>$popup</div></div>";
		} else {
			$popup="<div class='popupicon' title='$tips' onclick=\"popupInfo(this,'Help On : $t')\"><div class='popupdata' style='display:none'>$popup</div></div>";
		}
	}
	echo "<tr>";
	echo "<td class='title'>$t :</td>";
	echo "<td class='value'>$o</td>";
	echo "<td class='tips'>$popup $tips</td>";
	echo "</tr>";
}
function isSetupDefined($type) {
	$type=explode("#",$type);
	$ext="";
	if(isset($type[1])) $ext=$type[1];
	$type=$type[0];
	
	if($type=="int") return true;
	elseif($type=="list") return true;
	elseif($type=="listfunc") return true;
	elseif($type=="listfile") return true;
	elseif($type=="field") return true;
	elseif($type=="popup") return true;
	
	elseif(in_array($type,CfgSpecialFunction::$funcArr)) return true;
	
	return false;
}
//int,list,listfunc,listfile,scandir,field:date,popup
function getHTMLElement($arr,$name) {
	$a=$name;
	$b=$arr['value'];
	$clz=$arr['class'];
	
	$popup="";
	$s="";
	if($arr['type']=="int") {
		$s="<input id=\"$a\" name=\"$a\" value=\"$b\" type=text class='$clz' />";
	} elseif($arr['type']=="list") {
		$s="<select id=\"$a\" name=\"$a\" value='$b' class='$clz'>";
		$params=explode(",",$arr['edit_params']);
		foreach($params as $a) {
			$s.="<option value='$a'>".toTitle(_ling($a))."</option>";
		}
		$s.="</select>";
	} elseif($arr['type']=="listfunc") {
		$params=$arr['edit_params'];
		$arr=array();
		$cfgFunc=new CfgFunction();
		$s="";
		if(method_exists($cfgFunc,$params)) {
			if(PHP_VERSION_ID<50000) {
				$arr=call_user_func(array(&$cfgFunc, $params),$a,$b);
			} else {
				$arr=call_user_func(array($cfgFunc, $params),$a,$b);
			}
			$s=printList($arr,$a,$b,"","$clz");
		} elseif(function_exists($params)) {
			$arr=call_user_func($params);
			$s=printList($arr,$a,$b,"","$clz");
		} else {
			$s.="<b style='color:maroon;'>Not Supported Yet !</b>";
		}
	}
	
	elseif(in_array($arr['type'],CfgSpecialFunction::$funcArr)) {
		$params=$arr['edit_params'];
		$arr=call_user_func(array("CfgSpecialFunction",$arr['type']),$params);
		$s=printList($arr,$a,$b,"","$clz");
	}
	
	elseif($arr['type']=="field") {
		$params=$arr['edit_params'];
		$s="<input id=\"$a\" name=\"$a\" value=\"$b\" type=text class='{$params}' class='$clz' />";
	} elseif($arr['type']=="popup") {
		$params=$arr['edit_params'];
		$s="<input id=\"$a\" name=\"$a\" value=\"$b\" type=text class='$clz' />";
		$popup=$params;
	} else {
		$s="<b style='color:maroon;'>Not Supported Yet !</b>";
	}
	
	return array("html"=>$s,"popup"=>$popup);
}
function printList($arr,$t,$v="",$attr="",$class="") {
	$out="";
	$attr=str_replace("readonly","disabled",$attr);		
	if($class=="multiple") {
		$out="<select id=$t name=$t $attr class='$class' multiple>";
	} else {
		$out="<select id=$t name=$t $attr class='$class'>";
	}
	
	$keys=array_keys($arr);
	$values=array_values($arr);
	$keys=implode("",$keys);
	$values=implode("",$values);
	
	if(is_numeric($keys)) {
		$arr=array_flip($arr);
		foreach($arr as $a=>$b) {
			$arr[$a]=$a;
		}
	}
	if(is_numeric($values)) {
		foreach($arr as $a=>$b) {
			$arr[$a]=$a;
		}
	}
	if(strpos("##".$attr,"multiple")>=2) {
		$v=explode(",",$v);
	} elseif($class=="multiple") {
		$v=explode(",",$v);
	}
	
	if(is_array($v)) {
		foreach($arr as $b=>$a) {
			if(in_array($a,$v)) $out.="<option value='$a' selected>$b</option>";
			else $out.="<option value='$a'>$b</option>";
		}
	} else {
		foreach($arr as $b=>$a) {
			if($a==$v) $out.="<option value='$a' selected>$b</option>";
			else $out.="<option value='$a'>$b</option>";
		}
	}		
	$out.="</select>";
	return $out;
}
?>
