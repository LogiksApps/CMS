<?php
if(!defined('ROOT')) exit('No direct script access allowed');

class MenuTree {
	
	private $maxTags=4;
	private $format="ul,li,h2,a";
	private $colDefn=null;
	private $showImages=true;
	
	public function __construct() {
		$this->colDefn=ArrayToList::getColumnTagsHolder();
		$this->listTags($this->format);
	}
	
	public function colDefns($colDefn) {
		if($colDefn!=null) {
			$this->colDefn=$colDefn;
		} else {
			$this->colDefn=ArrayToList::getColumnTagsHolder();
		}
		return $this->colDefn;
	}
	public function listTags($format) {
		if($format!=null) {
			if(!is_array($format)) $this->format=explode(",",$format);
			else $this->format=$format;
			if(sizeOf($this->format)<$this->maxTags) { 
				for($i=sizeOf($this->format);$i<$this->maxTags;$i++) {
					array_push($this->format,"span");
				} 
			}
		}
		return $this->format;
	}
	
	public function showImages($b=null) {
		if($b!=null) {
			$this->showImages=$b;
		}
		return $this->showImages;
	}
	
	public function getTree($treeArray,$dataStr="data",$depth=0) {
		if(sizeOf($treeArray)<=0) return "";
		$colData=array();
		$s="";
		
		$format=$this->format;
		$colDefn=$this->colDefn;
		
		foreach($treeArray as $a=>$b) {
			$claz="";
			$tips="";
			$colData=array();
			$data=array();
			if(is_array($b)) {
				$hasData=array_key_exists($dataStr,$b);
				if($hasData) {
					$data=$b[$dataStr];
					unset($b[$dataStr]);
					
					if(isset($colDefn["classCol"]) && isset($data[$colDefn["classCol"]])) $claz="class='{$data[$colDefn['classCol']]}'";
					if(isset($colDefn["tipsCol"]) && isset($data[$colDefn["tipsCol"]])) $tips="title='{$data[$colDefn['tipsCol']]}'";
										
					foreach($colDefn as $m=>$n) {
						if(isset($data[$n])) {
							if($m=="linkCol" && $data[$n]=="#") $colData[$m]="";
							else $colData[$m]=$data[$n];
						}
					}
				}
				$n=count($b);
				if($n>0) {
					//$colData[$colDefn["titleCol"]]
					$as=$this->createTag($format[3],$a,$colData);
					if($depth==0) $s.="<{$format[1]}><{$format[2]}>{$as}</{$format[2]}>";
					else $s.="<{$format[1]}>{$as}";
					//$s.="<{$format[1]}>{$as}";
					$s.=$this->getTree($b,$dataStr,$depth+1);
					$s.="</{$format[1]}>";					
				} else {
					if(is_array($data[$colDefn["linkCol"]])) {
						$err=array();
						foreach($data as $q=>$w) {
							foreach($w as $p=>$o) {
								$err[$p][$q]=$o;
							}
						}
						foreach($err as $x) {
							$s1=$x[$colDefn["titleCol"]];
							$y=array(
										//"idCol"=>$x[$colDefn["idCol"]],
										"linkCol"=>$x[$colDefn["linkCol"]],
										"iconCol"=>$x[$colDefn["iconCol"]],
										"tipsCol"=>$x[$colDefn["tipsCol"]],
										"targetCol"=>$x[$colDefn["targetCol"]],
									);
							$as=$this->createTag($format[3],$s1,$y);
							$s.="<{$format[1]}>$as</{$format[1]}>";
						}						
					} elseif(strlen($data[$colDefn["linkCol"]])>1) {
						$as=$this->createTag($format[3],$a,$colData);
						$s.="<{$format[1]}>$as</{$format[1]}>";						
					} else {
						if(isset($data[$colDefn["groupCol"]]) && strlen($data[$colDefn["groupCol"]])>0) {
							$as=$this->createTag($format[3],$a,$colData);
							$s.="<{$format[1]} $tips >$as</{$format[1]}>";
						} else {
							$as=$this->createTag($format[3],$a,$colData);
							$s.="<{$format[1]}><{$format[2]}>{$as}</{$format[2]}></{$format[1]}>";
						}
					}
				}
			}
		}		
		if(strlen($s)<0) return "";
		else return "<{$format[0]}>$s</{$format[0]}>";
	}
	
	//This function creates the final tags to be put into the holder spaces.
	//It Needs the tag mostly li,h2,a,span
	public function createTag($tag, $title, $menuData=array()) {
		/*if(!isset($menuData['idCol'])) {
			return "";
		}*/
		$colDefn=array(
					"idCol"=>"rel='%s'",
					"titleCol"=>"title='%s'",
					"groupCol"=>"menugroup='%s'",
					"categoryCol"=>"category='%s'",
					
					"linkCol"=>"link='%s'",
					
					"iconCol"=>"iconpath='%s'",
					"classCol"=>"class='%s'",
					"styleCol"=>"style='%s'",
					
					"tipsCol"=>"tips='%s'",
					"privilegeCol"=>"privilege='%s'",
					
					"blockedCol"=>"blocked='%s'",
					"onmenuCol"=>"onmenu='%s'",
					
					"targetCol"=>"target='%s'",
					"deviceCol"=>"device='%s'",
					
					"siteCol"=>"site='%s'",
					
					"toCheckCol"=>"tocheck='%s'",
					"weightCol"=>"weight='%s'",
				);
		foreach($colDefn as $a=>$b) {
			if(isset($menuData[$a]) && strlen($menuData[$a])>0) {
				$colDefn[$a]=sprintf($b,$menuData[$a]);
			} else {
				$colDefn[$a]=sprintf($b,"");
				$menuData[$a]="";
			}
		}
		//printArray($colDefn);
		//printArray($menuData);
		$icn="";
		$imgTag="";
		if($this->showImages) {
			if(isset($menuData["iconCol"]) && strlen($menuData["iconCol"])>1) {
				$img=$this->findMedia($menuData['iconCol']);
				$imgTag=$img;
				if(strlen($img)>0)
					$icn="<img class='menuitemicon' src='$img' width=20px height=20px alt='' />";
				else
					$icn="";
			}
		} else {
			$imgTag=$this->findMedia($colDefn['iconCol']);
		}
		
		if(!isset($menuData['groupCol']) || strlen($menuData['groupCol'])<=0) $menuData['groupCol']=-1;
		if(!isset($menuData['idCol']) || strlen($menuData['idCol'])<=0) $menuData['idCol']=-1;
		
		$s="";
		if(count($menuData)>0) {
			$propsWindow=$this->createMenuPropsWindow($menuData);
			if(strlen($propsWindow)>0) {
				$editItem="<div class='slidewindow'>$propsWindow</div>";
			} else {
				$editItem="";
			}
			
			$s="<$tag name=\"{$title}\" title=\"{$title}\" ".implode(" ",$colDefn).">{$icn}<span class='textspan'>{$title}</span>";
			$s.="{$editItem}";
			$s.="</$tag>";
		}
		return $s;
	}
	
	function createMenuPropsWindow($menuData) {
		if($menuData['idCol']==-1) return "";
		$s="";
		$s.="<table width=100% border=0 cellpadding=2 cellspacing=0>";
		$s.="<input type=hidden name='id' value='{$menuData['idCol']}' />";
		$s.="<tr><th width=100px align=left>Title</th><td><input name=title type=text value='{$menuData['titleCol']}' /></td></tr>";
		$s.="<tr><th width=100px align=left>Link</th><td><input name=link type=text value='{$menuData['linkCol']}' /></td></tr>";
		$s.="<tr><th width=100px align=left>Blocked</th><td><select name=blocked value='{$menuData['blockedCol']}'><option value='false'>False</option><option value='true'>True</option></select></td></tr>";
		$s.="<tr><th width=100px align=left>On Menu</th><td><select name=onmenu value='{$menuData['onmenuCol']}'><option value='false'>False</option><option value='true'>True</option></select></td></tr>";
		$s.="<tr><td colspan=10 align=center><hr/><div class='smallbtn reset'>Reset</div><div class='smallbtn save'>Save</div></td></tr>";
		$s.="</table>";
		return $s;
	}
	
	public static function findMedia($name) {
		if(strlen($name)<=0) return "";
		$paths=array();
		if(isset($_SESSION["APP_FOLDER"])) {
			$appRoot=$_SESSION["APP_FOLDER"]["APPROOT"];
			
			if(isset($_SESSION["APP_FOLDER"]["APPS_MEDIA_FOLDER"])) {
				if(!in_array($appRoot . $_SESSION["APP_FOLDER"]["APPS_MEDIA_FOLDER"] . $name,$paths) && file_exists($appRoot.$_SESSION["APP_FOLDER"]["APPS_MEDIA_FOLDER"] . $name)) {
					array_push($paths,$appRoot . $_SESSION["APP_FOLDER"]["APPS_MEDIA_FOLDER"] . $name);
				}
			}
			$arr=getSiteConfigFor("uiconf");
			
			if(isset($arr['APPS_THEME'])) {
				if(!in_array(THEME_FOLDER.$arr['APPS_THEME']."/".$name,$paths) && file_exists(ROOT.THEME_FOLDER.$arr['APPS_THEME']."/".$name)) {
					array_push($paths,THEME_FOLDER.$arr['APPS_THEME']."/".$name);
				}
			}
		}
		if(sizeOf($paths)>0) {
			return str_replace(ROOT,SiteLocation,$paths[0]);
		} else {
			return loadMedia($name);
		}
	}
	
	public static function printMenuTree($result,$format="ul,li,h2,a") {
		if($result==null || !$result) return;
		$colDefn=MenuTree::getColumnTagsHolder();
		
		$groupCol=$colDefn["groupCol"];
		$titleCol=$colDefn["titleCol"];
		$toCheckCol=$colDefn["toCheckCol"];
		if(isset($colDefn["categoryCol"])) $categoryCol=$colDefn["categoryCol"]; else $categoryCol="";
		if($result) {
			$out=array();
			$master=array();			
			$cnt=0;
			
			$dbData=_dbData($result);
			_db()->freeResult($result);
			foreach($dbData as $a=>$row) {
				$master[$row[$colDefn["idCol"]]]=$row[$titleCol];
			}
			foreach($dbData as $a=>$row) {
				if(strlen($row[$groupCol])<=0) $row[$groupCol]="";
				elseif(is_numeric($row[$groupCol]) && isset($master[$row[$groupCol]])) $row[$groupCol]=$master[$row[$groupCol]];
				else $row[$groupCol]="".$row[$groupCol];
				
				$record=array("data"=>$row);
				
				$menuPath=$row[$groupCol];
				if(isset($row[$categoryCol]) && strlen($row[$categoryCol])>0) {
					if(strlen($menuPath)>0)
						$menuPath.="/".$row[$categoryCol];
					else
						$menuPath=$row[$categoryCol];
				}
				
				if(strpos($menuPath,"/")>=1) {
					$gs=str_replace("//","/",$menuPath);
					$r=explode("/",$gs);
					array_push($r,$row[$titleCol]);
					
					$arr=$record;
					$r1=array_reverse($r);
					foreach($r1 as $a) {
						$arr=array($a=>$arr);
					}
					$out[$cnt]=$arr;
				} else {
					if(strlen($row[$groupCol])<=0) {
						if(!isset($row[$categoryCol]) || strlen($row[$categoryCol])<=0) {
							$out[$cnt][$row[$titleCol]]=$record;
						} else {
							$out[$cnt][$row[$categoryCol]][$row[$titleCol]]=$record;
						}
					} else {
						if(!isset($row[$categoryCol]) || strlen($row[$categoryCol])<=0) {
							$out[$cnt][$row[$groupCol]][$row[$titleCol]]=$record;
						} else {
							$out[$cnt][$row[$groupCol]][$row[$categoryCol]][$row[$titleCol]]=$record;
						}
					}
				}
				$cnt++;
			}
			$treeArray=array();
			foreach($out as $a=>$b) {
				$treeArray=array_merge_recursive($treeArray,$b);
			}
			$atl=new MenuTree();
			$atl->colDefns($colDefn);
			$atl->listTags($format);
			$s=$atl->getTree($treeArray,"data");
			$s=substr($s,4,strlen($s)-9);
			return $s;
		}
		return "";
	}
	
	public static function getColumnTagsHolder() {
		return array(
					"idCol"=>"id",
					"titleCol"=>"title",
					"groupCol"=>"menugroup",
					"categoryCol"=>"category",
					
					"linkCol"=>"link",
					
					"iconCol"=>"iconpath",
					"classCol"=>"class",
					"styleCol"=>"style",
					
					"tipsCol"=>"tips",
					"privilegeCol"=>"privilege",
					
					"blockedCol"=>"blocked",
					"onmenuCol"=>"onmenu",
					
					"targetCol"=>"target",
					"deviceCol"=>"device",
					
					"siteCol"=>"site",
					
					"toCheckCol"=>"to_check",
					"weightCol"=>"weight",
				);
	}
}


?>
