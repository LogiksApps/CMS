<?php
function printProperties() {
	$colParams=array();
	$colParams['rptSearchOpts_multipleSearch']="true";
	$colParams['rptSearchOpts_multipleGroup']="true";
	$colParams['rptSearchOpts_showQuery']="false";
	$colParams['rptSearchOpts_modal']="true";
	$colParams['rptSearchOpts_caption']="Search Report";
	$colParams['rptSearchOpts_sopt']=array('eq','ne','lt','le','gt','ge','bw','bn','ew','en','cn','nc','nn','nu','in','ni');
	
	$colParams['rptOptions_rownumbers']="true";
	$colParams['rptOptions_rownumWidth']="40";
	
	$colParams['rptOptions_sortname']="";
	$colParams['rptOptions_sortorder']="asc";
	
	$colParams['rptOptions_grouping']="false";
	$colParams['rptOptions_groupField']="[]";
	$colParams['rptOptions_groupDataSorted']="false";
	$colParams['rptOptions_groupCollapse']="true";
	$colParams['rptOptions_groupColumnShow']="[true]";
	$colParams['rptOptions_groupText']="['{0} - {1} Item(s)']";
	$colParams['rptOptions_groupSummary']="[false]";
	$colParams['rptOptions_showSummaryOnHide']="true";
	
	$colParams['rptOptions_filterToolbar']="false";
	$colParams['rptOptions_filterSearchOnEnter']="true";
	
	$colParams['rptOptions_altRows']="true";
	$colParams['rptOptions_altclass']="ui-priority-secondary";
	$colParams['rptOptions_rowNum']="30";
	$colParams['rptOptions_rowList']=array(10,30,60,100,250,500,1000,2500,5000,10000,25000);
	
	$colParams['rptOptions_scroll']="false";
	$colParams['rptOptions_multiselect']="false";
	$colParams['rptOptions_autowidth']="true";
	$colParams['rptOptions_forceFit']="false";
	$colParams['rptOptions_shrinkToFit']="false";
	$colParams['rptOptions_gridview']="false";
	$colParams['rptOptions_viewrecords']="true";
	$colParams['rptOptions_loadonce']="false";
	
	$colParams['rptOptions_footerSummary']="false";
	$colParams['rptOptions_cellEdit']="false";
	
	$colParams['rptOptions_actOnDblclick']="true";
	$colParams['rptOptions_actionLinkInNewPage']="true";
	
	$keys=array_keys($colParams);
	for($i=0;$i<sizeOf($colParams);$i=$i+2) {
		$t1=$keys[$i];
		$t2=$keys[$i+1];
		$t1=str_replace("rptSearchOpts_","",$t1);
		$t2=str_replace("rptSearchOpts_","",$t2);
		$t1=str_replace("rptOptions_","",$t1);
		$t2=str_replace("rptOptions_","",$t2);
		$t1=ucwords($t1);
		$t2=ucwords($t2);
		
		$s1=createPropsRow($colParams,$keys[$i],$t1);
		$s2=createPropsRow($colParams,$keys[$i+1],$t1);
		
		$t1=splitByCaps($t1);
		$t2=splitByCaps($t2);
		
		echo "<tr align=left valign=top>";
		echo "<th class='title' name='".strtoupper(trim($t1))."' width=15%>$t1</th><td width=35%>$s1</td>";
		echo "<th class='title' name='".strtoupper(trim($t2))."' width=15%>$t2</th><td width=35%>$s2</td>";
		echo "</tr>";
		
		if($i>=6 && $i<7) {
			echo "<tr><td colspan=10><hr/></td></tr>";
		} else if($i>=8 && $i<9) {
			echo "<tr><td colspan=10><hr/></td></tr>";
		} else if($i>=16 && $i<17) {
			echo "<tr><td colspan=10><hr/></td></tr>";
		} else if($i>=18 && $i<19) {
			echo "<tr><td colspan=10><hr/></td></tr>";
		}
		
		/*$clz1="";
		$clz2="";
		if(!is_array($colParams[$keys[$i]])) {
			if(strpos($colParams[$keys[$i]],"[")===0 && strpos($colParams[$keys[$i]],"]")===strlen($colParams[$keys[$i]])-1) {
				$clz1="json_array";
				$colParams[$keys[$i]]=substr($colParams[$keys[$i]],1,strlen($colParams[$keys[$i]])-2);
			}
		}
		if(!is_array($colParams[$keys[$i+1]])) {
			if(strpos($colParams[$keys[$i+1]],"[")===0 && strpos($colParams[$keys[$i+1]],"]")===strlen($colParams[$keys[$i+1]])-1) {
				$clz2="json_array";
				$colParams[$keys[$i+1]]=substr($colParams[$keys[$i+1]],1,strlen($colParams[$keys[$i+1]])-2);
			}
		}
		
		$s1="<input name=\"{$keys[$i]}\" value=\"{$colParams[$keys[$i]]}\" ori=\"{$colParams[$keys[$i]]}\" type=text class='textfield $clz1' />";
		$s2="<input name=\"{$keys[$i+1]}\" value=\"{$colParams[$keys[$i+1]]}\" ori=\"{$colParams[$keys[$i+1]]}\" type=text class='textfield $clz2' />";
		
		if(is_array($colParams[$keys[$i]])) {
			$s1="<select name=\"{$keys[$i]}\" value=\"{$colParams[$keys[$i]]}\" ori=\"\" class='array json_array' multiple >";
			foreach($colParams[$keys[$i]] as $a) {
				$s1.="<option value=\"'$a'\" selected>$a</option>";
			}
			$s1.="</select>";
		} elseif(strtolower($colParams[$keys[$i]])=="true" || strtolower($colParams[$keys[$i]])=="false") {
			$s1="<select name=\"{$keys[$i]}\" value=\"{$colParams[$keys[$i]]}\" ori=\"{$colParams[$keys[$i]]}\" class='$clz1' >
					<option value='false'>False</option>
					<option value='true'>True</option>
				</select>";
		} elseif(!is_numeric($colParams[$keys[$i]])) {
			$s1="<input name=\"{$keys[$i]}\" value=\"{$colParams[$keys[$i]]}\" ori=\"{$colParams[$keys[$i]]}\" type=text class='textfield texttype $clz1' />";
		}
		
		if(is_array($colParams[$keys[$i+1]])) {
			$s2="<select name=\"{$keys[$i+1]}\" value=\"{$colParams[$keys[$i+1]]}\" ori=\"\" class='array json_array' multiple >";
			foreach($colParams[$keys[$i+1]] as $a) {
				$s2.="<option value=\"'$a'\" selected>$a</option>";
			}
			$s2.="</select>";
		} elseif(strtolower($colParams[$keys[$i+1]])=="true" || strtolower($colParams[$keys[$i+1]])=="false") {
			$s2="<select name=\"{$keys[$i+1]}\" value=\"{$colParams[$keys[$i+1]]}\" ori=\"{$colParams[$keys[$i+1]]}\" class='$clz2'  >
					<option value='false'>False</option>
					<option value='true'>True</option>
				</select>";
		} elseif(!is_numeric($colParams[$keys[$i+1]])) {
			$s2="<input name=\"{$keys[$i+1]}\" value=\"{$colParams[$keys[$i+1]]}\" ori=\"{$colParams[$keys[$i+1]]}\" type=text class='textfield texttype $clz2' />";
		}
		
		echo "<tr align=left valign=top>";
		echo "<th width=10%>$t1</th><td width=40%>$s1</td>";
		echo "<th width=10%>$t2</th><td width=40%>$s2</td>";
		echo "</tr>";
		if($i>=6 && $i<7) {
			echo "<tr><td colspan=10><hr/></td></tr>";
		}*/
	}
}
function createPropsRow($colParams,$key,$t1) {
	$clz1="";
	if(!is_array($colParams[$key])) {
		if(strpos($colParams[$key],"[")===0 && strpos($colParams[$key],"]")===strlen($colParams[$key])-1) {
			$clz1="json_array";
			$colParams[$key]=substr($colParams[$key],1,strlen($colParams[$key])-2);
		}
	}
	
	$s1="<input name=\"{$key}\" value=\"{$colParams[$key]}\" ori=\"{$colParams[$key]}\" type=text class='textfield $clz1' />";
	if(is_array($colParams[$key])) {
		$s1="<select name=\"{$key}\" value=\"{$colParams[$key]}\" ori=\"\" class='array json_array' multiple >";
		foreach($colParams[$key] as $a) {
			$s1.="<option value=\"'$a'\" selected>$a</option>";
		}
		$s1.="</select>";
	} elseif(strtolower($colParams[$key])=="true" || strtolower($colParams[$key])=="false") {
		$s1="<select name=\"{$key}\" value=\"{$colParams[$key]}\" ori=\"{$colParams[$key]}\" class='$clz1' >
				<option value='false'>False</option>
				<option value='true'>True</option>
			</select>";
	} elseif(strtolower($colParams[$key])=="asc" || strtolower($colParams[$key])=="desc") {
		$s1="<select name=\"{$key}\" value=\"{$colParams[$key]}\" ori=\"{$colParams[$key]}\" class='$clz1' >
				<option value=\"'asc'\">Ascending (asc)</option>
				<option value=\"'desc'\">Decending (desc)</option>
			</select>";
	} elseif(!is_numeric($colParams[$key])) {
		$s1="<input name=\"{$key}\" value=\"{$colParams[$key]}\" ori=\"{$colParams[$key]}\" type=text class='textfield texttype $clz1' />";
	}
	return $s1;
}
?>
