<?php
if(!defined('ROOT')) exit('No direct script access allowed');
session_check(true);

$webPath=getWebPath(__FILE__);

if(!isset($_REQUEST["forsite"])) {
	dispErrMessage("HostSite Is Required But Not Found","CMS Error",400);
	exit();
}

loadModule("page");

$layout="apppage";
$params=array("toolbar"=>"printToolbar","contentarea"=>"printContent");

loadModule("dbcon");

$dbCon=getDBControls();

$sql="SELECT menuid,site,count(*) FROM "._dbtable("links")." WHERE (site='{$_REQUEST["forsite"]}' || site='*') GROUP BY menuid";
$result=$dbCon->executeQuery($sql);
$_SESSION["menuArr"]=array();
if($result) {
	while($row = $dbCon->fetchData($result)) {
		$t=ucwords($row["menuid"]);//." [".$row["site"]."]";
		if(strlen($t)>0) {
			$_SESSION["menuArr"][$row["menuid"]]=$t;
		}
	}
}
printPageContent($layout,$params);

_js(array("jquery.multiselect","jquery.editinplace"));//,"jquery.listAttributes","jquery.ui-timepicker","jquery.tagit"
_css(array("jquery.multiselect"));//,"jquery.tagit","styletags","formtable","formfields"
?>
<script src='<?=$webPath?>script.js' type='text/javascript' language='javascript'></script>
<link href='<?=$webPath?>style.css' rel='stylesheet' type='text/css' media='all' />
<style>
#menuitemform tr.more {
	display:none;
}
</style>
<script language='javascript'>
forSite="<?=$_REQUEST["forsite"]?>";
basePath="<?=APPS_FOLDER.$_REQUEST["forsite"]."/"?>";
function getCMD(cmd) {
	if(cmd==null) cmd="menuedit";
	return "services/?scmd="+cmd+"&site=<?=SITENAME?>&forsite=<?=$_REQUEST["forsite"]?>";
}
</script>
<?php
function printContent() { ?>
<div id=menutree class='ui-widget-content ui-corner-all' style='float:right;border-top:0px;'>
	<div class='mtheader ui-widget-header'>
		<button onclick='createMenuItem();' style='float:right;width:30px;height:30px;'><div class='menuItemAddIcon' style='margin:-10px;'></div></button>
		<button onclick="loadMenuGroup($('#menuselector').val());" style='float:right;width:30px;height:30px;'><div class='reloadicon' style='margin:-10px;'></div></button>
		<h2 class='menuicon' align=left>Menu Tree</h2>
	</div>
	<div id=allmenus class='mtbody' style=''>
		<ul class="sortable"></ul>
	</div>
</div>
<div id=menuformPlaceHolder>
	<button onclick='createMenuItem();' style='width:200px;height:30px;margin:auto;'>
		<div class='menuItemAddIcon' style='margin:-10px;padding-top:8px;font-size:18px;'>New MenuItem</div>
	</button>
</div>
<div id=menuform class='ui-widget-content ui-corner-all' style='float:left;display:none;'>
	<div class='mtheader ui-widget-header'>
		<button onclick='closeMenuForm();' style='float:right;width:30px;height:30px;'><div class='deleteicon' style='margin:-10px;'></div></button>
		<h2 class='menuItemHeadIcon' align=left>MenuItem Editor</h2>
	</div>
	<div id=menuitemform class='mtbody' style=''>
		<table width=100% border=0 cellpadding=2 cellspacing=0 class='nostyle'>
			<input type=hidden name='id' value='0' />
			<tr><th width=100px align=left>Link Title</th><td><input name=title type=text value='' /></td></tr>
			
			<tr><th width=100px align=left>Is MenuGroup</th><td><input id=menugroup type=checkbox onchange="checkMenugroupForm(this.checked)" /></td></tr>
			
			<tr><th width=100px align=left>Parent Group</th><td>
				<select name=menugroup value=''>
				</select>
			</td></tr>
			<tr><th width=100px align=left>Category</th><td><input name=category type=text value='' /><div class='formMiniBtn categorySelector right'></div></td></tr>
			<tr><th width=100px align=left>Link</th><td><input name=link type=text value='' /><div class='formMiniBtn linkSelector right'></div></td></tr>
			<tr class='more'><th width=100px align=left>Icon</th><td><input name=iconpath type=text value='' /><div class='formMiniBtn iconSelector right'></div></td></tr>
			<tr class='more'><th width=100px align=left>Class</th><td><input name=class type=text value='' /><div class='formMiniBtn classSelector right'></div></td></tr>
			
			<tr><th width=100px align=left>Description</th><td><input name=tips type=text value='' /></td></tr>
			<tr><th width=100px align=left>Privilege</th><td>
				<select name=privilege value='*'>
					<option value='*'>Everybody</option>
				</select>
			</td></tr>
			
			<tr class='more'><th width=100px align=left>Blocked</th><td><select name=blocked value='false'><option value='true'>True</option><option value='false'>False</option></select></td></tr>
			<tr class='more'><th width=100px align=left>On Menu</th><td><select name=onmenu value='true'><option value='true'>True</option><option value='false'>False</option></select></td></tr>
			
			<tr class='more'><th width=100px align=left>Target</th><td>
				<select name=target value=''>
					<option value=''>Auto Target</option>
					<optgroup label='Document'>
						<option value='_blank'>New Window</option>
						<option value='_self'>In Document</option>
						<option value='_parent'>Parent Frame</option>
						<option value='_top'>Top Frame</option>
					</optgroup>
					<optgroup label='Dialogs'>
						<option value='overlay'>Overlay Dialog</option>
						<option value='popup'>Popup Dialog</option>
						<option value='minipopup'>Mini Popup</option>
					</optgroup>
				</select>
			</td></tr>
			<tr class='more'><th width=100px align=left>Device</th><td>
				<select name=device value='*'>
					<option value='*'>All Devices</option>
					<option value='pc'>Desktops (PC,Laptops,etc.)</option>
					<option value='mobile'>Mobiles</option>
					<option value='tablet'>Tablets</option>
				</select>
			</td></tr>
			
			<tr class='more'><th width=100px align=left>App-Site</th><td>
				<select name=site value='<?=$_REQUEST['forsite']?>'>
					<option value='<?=$_REQUEST['forsite']?>'>Only This Site</option>
					<option value='*'>All Sites</option>
					<option value=''>Others</option>
				</select>
			</td></tr>
			<tr><th width=100px align=left>Weight</th><td>
				<select name=weight value='0'>
						<option>-29</option><option>-28</option><option>-27</option><option>-26</option><option>-25</option><option>-24</option>
						<option>-23</option><option>-22</option><option>-21</option><option>-20</option><option>-19</option><option>-18</option>
						<option>-17</option><option>-16</option><option>-15</option><option>-14</option><option>-13</option><option>-12</option>
						<option>-11</option><option>-10</option><option>-9</option><option>-8</option><option>-7</option><option>-6</option>
						<option>-5</option><option>-4</option><option>-3</option><option>-2</option><option>-1</option><option>0</option>
						<option>1</option><option>2</option><option>3</option><option>4</option><option>5</option><option>6</option>
						<option>7</option><option>8</option><option>9</option><option>10</option><option>11</option><option>12</option>
						<option>13</option><option>14</option><option>15</option><option>16</option><option>17</option><option>18</option>
						<option>19</option><option>20</option><option>21</option><option>22</option><option>23</option><option>24</option>
						<option>25</option><option>26</option><option>27</option><option>28</option><option>29</option><option>30</option>
				</select>
			</td></tr>
			
			<tr class='showMore' title='Show More Menu Options'><td colspan=10><hr/></td></tr>
			<tr><td colspan=10 align=center>
				<button class='close' onclick='closeMenuForm()'>Close</button>
				<button class='reset' onclick='resetMenuItemForm()'>Reset</button>
				<button class='save' onclick="saveMenuItem('#menuitemform','resetMenuItemForm')">Save</button>
			</td></tr>
		</table>
	</div>
</div>
<div id=generatorform class='ui-widget-content ui-corner-all' style='display:none;'>
	<div class='mtheader ui-widget-header'>
		<!--<button onclick='createNewSource();' style='float:right;width:30px;height:30px;'><div class='addicon' style='margin:-10px;'></div></button>-->
		<h2 class='menuGeneratorsIcon' align=left>Menu Generators</h2>
	</div>
	<div id=generatorTable class='mtbody' style=''>
		<table class='datatable' width=100% border=0 cellpadding=2 cellspacing=0>
			<thead>
				<tr class='clr_darkmaroon'><th width=35px>Icon</th><th>Menu</th><th>DB-Table</th><th>Link Template</th><th width=30px>&nbsp;</th><th width=30px>A</th></tr>
			</thead>
			<tbody>
			</tbody>
		</table>
	</div>
</div>
<div style='display:none;'>
	<div id=linkSuggestor title='Suggest Links'>
		<select style='width:100%;height:180px;background:white !important;font-size:14px;' size=2>
		</select>
	</div>
	<div id=classSuggestor title='Suggest Classes'>
		<select style='width:100%;height:180px;background:white !important;font-size:14px;' size=2>
		</select>
	</div>
	<div id=categorySuggestor title='Suggest Categories'>
		<select style='width:100%;height:180px;background:white !important;font-size:14px;' size=2>
		</select>
	</div>
</div>
<?php 
}
function printToolbar() { ?>
<button id=generatorBtn onclick="toggleGenerators();" title='Toggle Menu/Generators' current='menus'><div class='gearicon'>Generators</div></button>
||
<select id=menuselector class='menus' onchange="loadMenuGroup(this.value);">	
	<?php
		foreach($_SESSION["menuArr"] as $a=>$b) {
			$b=ucwords($b);
			echo "<option value='$a'>$b</option>";
		}
		unset($_SESSION["menuArr"]);
	?>
</select>
<button class='menus' onclick="createMenuGroup();" title='Create New Menu Group'><div class='addicon'>New MenuGroup</div></button>
<button class='menus' onclick="deleteMenuGroup($('#menuselector').val());" title='Delete Menu Group'><div class='deleteicon'>Delete MenuGroup</div></button>
<!--
<button class='menus' onclick="renameMenuGroup($('#menuselector').val());" title='Rename Menu Group'><div class='renameicon'>&nbsp;&nbsp;Rename MenuGroup</div></button>
-->

<button class='generator' onclick="loadGenerator()" title='Reload Generator'><div class='reloadicon'>Reload Sources</div></button>
<button class='generator' onclick="saveGenerator()" title='Save Sources'><div class='saveicon'>Save Generator</div></button>
<?php } ?>
