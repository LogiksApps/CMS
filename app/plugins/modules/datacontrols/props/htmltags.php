<?php
$data=file_get_contents(dirname(dirname(__FILE__))."/resources/formTags/supportjs.tag");
$data=explode("\n",$data);
$jsTags="<option value=''>None</option>";
foreach($data as $a) {
	if(strlen($a)>0) {
		$r=explode("=",$a);
		if(count($r)>1) {
			$t=$r[0];
			unset($r[0]);
			if(count($r)>1) {
				$jsTags.="<option value=\"".implode("=",$r)."\">$t</option>";
			} else {
				$jsTags.="<option value=\"{$r[1]}\">$t</option>";
			}
		} else {
			$jsTags.="<option>$a</option>";
		}
	}
}
$spSelectorTag="";
$data=file_get_contents(dirname(dirname(__FILE__))."/resources/formTags/special_selectors.tag");
$data=explode("\n",$data);
foreach($data as $a) {
	if(strlen($a)>0) {
		$r=explode("=",$a);
		if(count($r)>1) {
			$t=$r[0];
			unset($r[0]);
			if(count($r)>1) {
				$spSelectorTag.="<option value=\"".implode("=",$r)."\">$t</option>";
			} else {
				$spSelectorTag.="<option value=\"{$r[1]}\">$t</option>";
			}
		} else {
			$spSelectorTag.="<option>$a</option>";
		}
	}
}

$dbGroupsTag="";
$sql="SELECT groupid FROM "._dbtable("lists")." group by groupid";
$r=_dbQuery($sql);
if($r) {
	$data=_dbData($r);
	_db()->freeResult($r);
	foreach($data as $a) {
		$dbGroupsTag.="<option value='<!--?=createDataSelector(_db(),\"{$a['groupid']}\");?-->'>{$a['groupid']}</option>";
	}
}

$folders=loadFolderConfig();
$layoutDir=$folders['APPROOT'].$folders['APPS_MISC_FOLDER']."lookups/";
$lookupsTag="";
if(is_dir($layoutDir)) {
	$lss=scandir($layoutDir);
	unset($lss[0]);unset($lss[1]);
	if(count($lss)>0) $lookupsTag.="<optgroup label='Local'>";
	foreach($lss as $a) {
		$a=substr($a,0,strlen($a)-4);
		$lookupsTag.="<option value='<!--?=createSelectorFromListFile(\"lookups/{$a}.dat\");?-->'>$a</option>";
	}
	if(count($lss)>0) $lookupsTag.="</optgroup>";
}
$layoutDir=ROOT.MISC_FOLDER."lookups/";
if(is_dir($layoutDir)) {
	$lss=scandir($layoutDir);
	unset($lss[0]);unset($lss[1]);
	if(count($lss)>0) $lookupsTag.="<optgroup label='Global'>";
	foreach($lss as $a) {
		$a=substr($a,0,strlen($a)-4);
		$lookupsTag.="<option value='<!--?=createSelectorFromListFile(\"lookups/{$a}.dat\");?-->'>$a</option>";
	}
	if(count($lss)>0) $lookupsTag.="</optgroup>";
}

$bgClasses="clr_pink,clr_green,clr_blue,clr_yellow,clr_red,clr_orange,clr_white,clr_white_inverted,clr_darkblue,clr_darkmaroon,clr_skyblue";
$data=explode(",",$bgClasses);
$bgClassesJSON=json_encode($data);
$bgClasses="<option value=''>None</option>";
foreach($data as $a) {
	$bgClasses.="<option>$a</option>";
}
?>
<div id=tagpropeditor class=tabs style='width:100%;height:100%;'>
	<ul>
		<li title='Change Attributes'><a href='#attr'><div class='minibtntag tabicon'>Attributes</div></a></li>
		<li title='Change Event Properties'><a href='#evnt'><div class='minibtnjs tabicon'>Events</div></a></li>
		<li title='View Settings'><a href='#advn'><div class='minibtngear tabicon'>Advanced</div></a></li>
		<li title='Close This Window'><a onclick="closePropertiesWindow(true);"><div class='deleteicon'>Close</div></a></li>
	</ul>
	<div id=attr style='padding:0px;'>
		<table class='nostyle' width=100% border=0 style='margin-top:-1px;'>
			<tr>
				<th width=100px align=left>Name</th><td><input name=fieldname type=text value='' /></td>
				<th width=100px align=left>Field Type</th>
				<td width=310px>
					<select name=fieldtype style='width:90% !important;' onchange='checkHtmlButton(this)'>
						<optgroup label='Text Type'>
							<option tag='input' tagtype='text' value='textfield'>Text Field</option>
							<option tag='input' tagtype='text' value='textfield autocomplete'>Autocomplete Text Field</option>
							<option tag='textarea' value='textarea'>Text Area</option>
							<option tag='input' tagtype='password' value='password'>Password Field</option>
							<option tag='input' tagtype='text' value='emailfield'>Email Field</option>
							<option tag='input' tagtype='text' value='phonefield'>Phone Field</option>
							<option tag='input' tagtype='text' value='telephonefield'>TelePhone Field</option>
							<option tag='input' tagtype='text' value='mobilefield'>Mobile Field</option>
							<option tag='input' tagtype='text' value='calculatorfield'>Calculator Field</option>
							<option tag='input' tagtype='text' value='currencyfield'>Currency Field</option>
							<option tag='input' tagtype='text' value='creditcardfield'>Credit Card Field</option>
							<option tag='input' tagtype='text' value='barcodefield'>BarCode Field</option>
							
							<option tag='input' tagtype='text' value='urlfield'>URL Field</option>
							<option tag='input' tagtype='text' value='tagfield'>Tag Field</option>
							
							<option tag='input' tagtype='text' value='numberfield'>Number Field</option>
							<option tag='input' tagtype='text' value='decimalfield'>Decimal Field</option>
							
							<option tag='input' tagtype='text' value='searchfield'>Search Field</option>
						</optgroup>
						<optgroup label='Date Type'>
							<option tag='input' tagtype='text' value='datefield'>Date Field</option>
							<option tag='input' tagtype='text' value='timefield'>Time Field</option>
							<option tag='input' tagtype='text' value='datetimefield'>DateTime Field</option>
						</optgroup>
						<optgroup label='Other Types'>
							<option tag='input' tagtype='checkbox' value='checkbox'>CheckBox</option>
							<option tag='input' tagtype='radio' value='radio'>Radio Box</option>
							<!--
								<option tag='input' tagtype='radio' value='radio'>TagField</option>
								Slider
								Checkbox/Radio List
							-->
						</optgroup>
						<optgroup label='File Types'>
							<option tag='input' tagtype='file' value='filefield'>File Field</option>
							<option tag='input' tagtype='text' value='photofield'>Photo Field</option>
							<option tag='input' tagtype='text' value='filebrowser'>File Browser</option>
							<option tag='input' tagtype='text' value='photobrowser'>Photo Browser</option>
							<!--
							<option tag='input' tagtype='text' value='docbrowser'>DB-Document Browser</option>
							<option tag='input' tagtype='text' value='mediabrowser'>DB-Media Browser</option>
							-->
						</optgroup>
						<optgroup label='Selectors'>
							<option tag='select' value='select'>Plain Selector</option>
							<option tag='select' value='dbselect'>DataLists</option>
							<option tag='select' value='lookupselect'>LookupLists</option>
							<option tag='select' value='dbcolselect'>DB Column Values</option>
							<option tag='select' value='spselect'>Special Selectors</option>
							<option tag='select' value='phpselect'>PHP Selector</option>
							<!--
							<option tag='select' value='lookupselect'>Lookup Selectors</option>
							<option tag='select' value='xmlselect'>XML Selectors</option>
							-->
						</optgroup>
						<optgroup label='Graphic Types'>
							<option tag='input' tagtype='text' value='progressbar'>Progress</option>
						</optgroup>
						<option tag='editable' value='custom'>Custom Code</option>
					</select>
					<div id=fieldhtmlcode class='pbtn minibtnhtm' style='float:right;display:none;'>
				</td>
			</tr>
			<tr>
				<th width=100px align=left>Style</th>
				<td><input name=fieldstyle type=text value='' style='width:90%;' /> <div class='pbtn clricon' style='float:right;'></div></td>
				<th width=100px align=left>Properties</th>
				<td>
					<select name=fieldproperties multiple>
						<optgroup label='Support Properties'>
							<option value='required'>Required</option>
							<option value='unique'>Unique</option>
							<option value='multiple'>Multiple/Multiselect</option>
						</optgroup>
						<optgroup label='Alignment Properties'>
							<option value='alignleft'>Align Left</option>
							<option value='alignright'>Align Right</option>
							<option value='aligncenter'>Align Center</option>
						</optgroup>
						<optgroup label='Text Properties'>
							<option value='capitalize'>Capitalize</option>
							<option value='uppercase'>UpperCase</option>
							<option value='lowercase'>LowerCase</option>
							<option value='overline'>Overline</option>
							<option value='linethrough'>Line Through</option>
							<option value='underline'>Underline</option>
							<option value='blink'>Blink</option>
							<option value='nowrap'>No Wrap</option>
							<option value='ghosttext'>GhostText</option>
						</optgroup>
						<optgroup label='Other Properties'>
							<option value='noresize'>No Resize</option>
							<option value='nooverflow'>No Overflow</option>
							<option value='autooverflow'>Auto Scroll Content</option>
							<option value='noautoreset'>No Auto Reset</option>
							<option value='hidden'>No Display</option>
							<option value='readonly'>ReadOnly</option>
							<option value='disabled'>Disabled</option>
						</optgroup>
					</select>
				</td>
			</tr>
			<tr>
				<th width=100px align=left>SRC</th>
				<td><input name=fieldsrc type=text value='' style='width:90%;'/><div class='pbtn srcicon' style='float:right;'></div></td>
				<th width=100px align=left>Tips</th><td><input name=fieldtitle type=text value='' /></td>
			</tr>
		</table>
	</div>
	<div id=evnt style='padding:0px;'>
		<table width=100% border=0 style='margin-top:-1px;'>			
			<tr>
				<th width=100px align=left>OnClick</th><td><input name=onclick type=text value='' /></td><td class='pbtn jsicon'></td>
				<th width=100px align=left>OnFocus</th><td><input name=onfocus type=text value='' /></td><td width=20px class='pbtn jsicon'></td>
			</tr>
			<tr>
				<th width=100px align=left>OnChange</th><td><input name=onchange type=text value='' /></td><td class='pbtn jsicon'></td>
				<th width=100px align=left>OnBlur</th><td><input name=onblur type=text value='' /></td><td class='pbtn jsicon'></td>
			</tr>
			<tr>
				<th width=100px align=left>OnKeyUp</th><td><input name=onkeyup type=text value='' /></td><td class='pbtn jsicon'></td>
				<th width=100px align=left>OnKeyDown</th><td><input name=onkeydown type=text value='' /></td><td class='pbtn jsicon'></td>
			</tr>
		</table>
	</div>
	<div id=advn style='padding:2px;'>
		<table width=100% border=0 style='margin-top:-1px;'>			
			<tr>
				<th width=100px align=left>Background</th>
				<td>
					<select name=bgclass class='forAllRows' style='width:90% !important;' onchange="setTRClass(this.value);" >
						<?=$bgClasses?>
					</select>
				</td><td class='pbtn blankicon'></td>
				
				<th width=100px align=left></th>
				<td>
					
				</td>
				<td width=20px class='pbtn blankicon'></td>
			</tr>
		</table>
	</div>
</div>

<div id=tageditors style='display:none'>
	<div id=formSrcSelector title='SRC Selector' align=center>
		<div class='srcEditPage' style="width:100%;height:100px;font-size:13px;font-weight:bold;" align=left>
		</div>
		<br/>
	</div>
	<div id=formJSSelector title='JS Selector' align=center>
		<select class='data' style="width:100%;height:25px;font-size:13px;font-weight:bold;">
			<?=$jsTags?>
		</select><br/><br/>
		<button onclick="$('#formJSSelector').dialog('close');">Cancel</button>
		<button onclick="selectDlgValue('#formJSSelector');">Select</button>
	</div>
	<div id=formCSSSelector title='CSS Editor' align=center style='padding:2px;'>
		<h3>CSSEditor Not Found</h3>
	</div>
	<div id=formTextSelector title='Editor' align=center style='padding:2px;'>
		<textarea class='data' style="width:95%;height:150px;margin:auto;font-size:15px;">
		</textarea><br/>
		<br/>
		<button onclick="$('#formTextSelector').dialog('close');">Cancel</button>
		<button onclick="selectDlgValue('#formTextSelector');">Select</button>
	</div>
	<div id=formLookupSelector1 title='Lookup Selector' align=center>
		<select class='data' style="width:50%;height:25px;font-size:13px;font-weight:bold;">
			<?= $lookupsTag ?>
		</select>
		<br/><br/>
		<button onclick="$('#formLookupSelector1').dialog('close');">Cancel</button>
		<button onclick="selectDlgValue('#formLookupSelector1');">Select</button>
	</div>
	<div id=formDBSelector1 title='Data Selector' align=center>
		<select class='data' style="width:50%;height:25px;font-size:13px;font-weight:bold;">
			<?= $dbGroupsTag ?>
		</select>
		<br/><br/>
		<button onclick="$('#formDBSelector1').dialog('close');">Cancel</button>
		<button onclick="selectDlgValue('#formDBSelector1');">Select</button>
	</div>
	<div id=formSPSelector1 title='Data Selector' align=center>
		<select class='data' style="width:50%;height:25px;font-size:13px;font-weight:bold;">
			<?= $spSelectorTag ?>
		</select>
		<br/><br/>
		<button onclick="$('#formSPSelector1').dialog('close');">Cancel</button>
		<button onclick="selectDlgValue('#formSPSelector1');">Select</button>
	</div>
	<div id=formPHPSelector1 title='Data Selector' align=center>
		<textarea class='data' style="width:95%;height:150px;margin:auto;font-size:15px;">
		</textarea>
		<br/><br/>
		<button onclick="$('#formPHPSelector1').dialog('close');">Cancel</button>
		<button onclick="selectDlgValue('#formPHPSelector1');">Done</button>
	</div>
	<div id=formDbColSelector1 title='Data Selector' align=center>
		<table width=100% border=0 cellpadding=2 cellspacing=0>
			<tr>
				<th align=left>Table</th>
				<td><select name='table' class='tablelist' style="width:100%;height:25px;font-size:13px;font-weight:bold;" 
					onchange="loadColumnList('#formDbColSelector1 select.columnlist',this.value,'select');"></select></td>
			</tr>
			<tr>
				<th align=left>Name Column</th>
				<td><select name='col1' class='columnlist' style="width:100%;height:25px;font-size:13px;font-weight:bold;"></select></td>
			</tr>
			<tr>
				<th align=left>Value Column</th>
				<td><select name='col2' class='columnlist' style="width:100%;height:25px;font-size:13px;font-weight:bold;"></select></td>
			</tr>
			<tr>
				<th align=left>Where Value</th>
				<td>
					<input name='where' style="width:100%;height:22px;font-size:13px;font-weight:bold;border:1px solid #aaa;" />
				</td>
			</tr>
		</table>
		<br/><br/>
		<button onclick="$('#formDbColSelector1').dialog('close');">Cancel</button>
		<button onclick="selectDBColData()">Done</button>
	</div>
</div>

<script language=javascript>
var forInput=null;
$(function() {
	$("#properties .pbtn").click(function() {
			//minibtnhtm,jsicon,clricon,srcicon
			if(getInputBox()==null) return;
			if($(this).hasClass("minibtnhtm")) {
				tdIn=getInputBox();
				forInput=tdIn;
				
				id="#formTextSelector";
				if(tdIn.tagName=="SELECT") {
					if($(tdIn).hasClass('dbselect')) {
						id="#formDBSelector1";
						s=$(tdIn).html().trim();
						$(id+" select[class=data]").val(s);
					} else if($(tdIn).hasClass('lookupselect')) {
						id="#formLookupSelector1";
						s=$(tdIn).html().trim();
						$(id+" select[class=data]").val(s);
					} else if($(tdIn).hasClass('dbcolselect')) {
						id="#formDbColSelector1";
					} else if($(tdIn).hasClass('spselect')) {
						id="#formSPSelector1";
					} else if($(tdIn).hasClass('lookupselect')) {
						id="#formLookupSelector1";
					} else if($(tdIn).hasClass('phpselect')) {
						id="#formPHPSelector1";
					} else {
						s=$(tdIn).html().trim();
						if(s.indexOf("<!--?")===0) {
							s="";
						} else {
							s=s.replace("\n","");
							s=s.replace("\t","");
							s=s.replace("</option>","</option>\n");
						}
						$(id+" textarea[class=data]").val(s);
					}
				} else {
					id="#formTextSelector";
					$(id+" textarea[class=data]").val($(tdIn).html().trim());
				}
				
				$(id).dialog({
					resizable:false,
					width:600,
					closeOnEscape:true,
				});
			} else if($(this).hasClass("jsicon")) {
				forInput=$(this).parents("tr").find("input[type=text]").get(0);
				$("#formJSSelector").dialog({
					resizable:false,
					width:600,
					closeOnEscape:true,
					close:function() {
						updateFieldPropertiesFromBox(forInput);
					}
				});
			} else if($(this).hasClass("clricon")) {
				$("#formCSSSelector").dialog({
					resizable:false,
					width:550,
					closeOnEscape:true,
					close:function() {
						updateFieldPropertiesFromBox(forInput);
					}
				});
			} else if($(this).hasClass("srcicon")) {
				forInput=$(this).parents("tr").find("input[type=text]").get(0);
				
				xx=$("#tagpropeditor #attr select[name=fieldtype]").val();
				xx=xx.replace("textfield","").trim();
				ll=lnkLst+"&action=srceditpage&srctype="+xx;
				$("#formSrcSelector .srcEditPage").load(ll,function() {
						$("#formSrcSelector").dialog({
								resizable:false,
								width:550,
								height:400,
								closeOnEscape:true,
								buttons:{
									Cancel:function(){
										$(this).dialog("close");
									},
									Save:function() {
										qq="";
										eles=$("#formSrcSelector .srcEditPage").find("input[name],textarea[name],select[name]").filter(":enabled");
										if(eles.length==0) {
											eles=$("#formSrcSelector .srcEditPage").find("input:not(.defered),textarea:not(.defered),select:not(.defered)").filter(":enabled");
											if(eles.length==1) {
												qq=eles.val();
											} else {
												eles.each(function() {
														qq+=$(this).val();
													});
											}
										} else {
											if(eles.length==1) {
												qq=eles.val();
											} else {
												eles.each(function() {
														nm=$(this).attr('name');
														if(nm!=null && nm.length>0) {
															if($(this).val().length>0)
																qq+="&"+nm+"="+$(this).val();
														} else {
															qq+=$(this).val();
														}
													});
											}
										}
										
										if($("#formSrcSelector .srcEditPage #actype").length>0)
											qq=$("#formSrcSelector .srcEditPage #actype").val()+qq;
										$(forInput).val(qq);
										updateFieldPropertiesFromBox(forInput);
										$(this).dialog("close");
									}
								},
							});
					});
			}
		});
	loadTableList("select.tablelist");
});
function selectDlgValue(dlg) {
	if(forInput!=null) {
		txt=$(dlg).find(".data").val();
		if(forInput.tagName=="INPUT") $(forInput).val(txt);
		else $(forInput).text(txt);
	}
	$(dlg).dialog("close");
}
function selectDBColData() {
	php="<"+"?php \n";
	php+="echo createDataSelectorFromUniques(_db(),'"+$("#formDbColSelector1 select[name=table]").val()+
		"','"+$("#formDbColSelector1 select[name=col1]").val()+
		"','"+$("#formDbColSelector1 select[name=col2]").val()+"',null,"+
		"'"+$("#formDbColSelector1 input[name=where]").val()+"');";
	php+="\n ?>";
	if(forInput!=null) {
		if(forInput.tagName=="INPUT") $(forInput).val(php);
		else $(forInput).text(php);		
	}
	$('#formDbColSelector1').dialog('close');
}
function setTRClass(curClass) {
	bgClassesJSON=<?=$bgClassesJSON?>;
	$(bgClassesJSON).each(function(k,v) {
			$(selectedTr).removeClass(v);
		});
	if(selectedTr!=null) $(selectedTr).addClass(curClass);
}
function checkHtmlButton(ele) {
	if($(ele).find("option:selected").attr('tag').toUpperCase()=="SELECT" ||
		$(ele).find("option:selected").attr('tag').toUpperCase()=="TEXTAREA")
		$("#properties #fieldhtmlcode").show();
	else
		$("#properties #fieldhtmlcode").hide();
}
</script>
