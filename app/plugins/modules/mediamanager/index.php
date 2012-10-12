<?php
if(!defined('ROOT')) exit('No direct script access allowed');

loadModule("page");
$layout="apppage";
$params=array("toolbar"=>"printToolbar","contentarea"=>"printContent");

printPageContent($layout,$params);

function printToolbar() {
//<button style='width:45px;' onclick="manageFilter()" title='Manage Media Source'><div class='gearicon'>&nbsp;</div></button>
?>
<button style='width:45px;' onclick="loadFilterList()" title='Reload Media Panes'><div class='reloadicon'>&nbsp;</div></button>
<select class="ui-widget-header ui-corner-all fselector" id=fselector onchange='refreshView();resetViewer();'></select>
||
<button style='width:45px;' onclick="deleteMedias()" title='Delete Selected Medias (Multiple)'><div class='deleteicon'>&nbsp;</div></button>
<button style='width:45px;' onclick="uploadMedia(0)" title='Upload New Media'><div class='uploadicon'>&nbsp;</div></button>
||
<button id=viewbtn style='width:45px;' onclick="changeView()" title='Current View :: THUMBS'><div class='viewicon'>&nbsp;</div></button>
<input id=searchfield type=text class='ui-corner-all' title='Press Enter To Search' />
<button style='width:45px;' onclick="toggleFinder();" title='Find Media'><div class='findicon'>&nbsp;</div></button>
||
<button style='width:45px;' onclick="toggleSelectAll()" title='Toggle Select All'><div class='toggleicon'>&nbsp;</div></button>
<?php 
}

function printContent() {
$webPath=getWebPath(__FILE__);
$rootPath=getRootPath(__FILE__);
$user=$_SESSION['SESS_USER_ID']; 
?>
<link href='<?=$webPath?>style.css' rel='stylesheet' type='text/css' media='all' /> 
<script src='<?=$webPath?>script.js' type='text/javascript' language='javascript'></script>
<div id=photoGallery>
	<div id='gallery' class="mediaPane active">
		<ul id="thumbsview"></ul>
	</div>
	<div id='tableview' class='mediaPane'>
		<table cellspacing=0 cellpadding=0 border=0 width=100% >
			<thead>
				<th>Media</th>
				<th width='100px'>Type</th>
				<th width='100px'>Size</th>
				<th>Author</th>
				<th width=20px>Info</th>
			</thead>
			<tbody></tbody>
		</table>
	</div>
</div>
<div id=photoViewer>
	<div id=viewer class='nophoto'></div>
	<div id=tools class='ui-widget-header ui-corner-all'>
		<button id=mediaIDBtn class='left' title='Media ID' onclick="getLink()">0</button>
		<button class='left' title='Swap Media With Another' onclick="swapMedia()"><div class='bigswapicon'>&nbsp;</div></button>
		<button class='left' title='Delete This Media' onclick="deleteOneMedia()"><div class='bigdeleteicon'>&nbsp;</div></button>
		<button class='left' title='Download This Media' onclick="downloadMedia()"><div class='bigdownloadicon'>&nbsp;</div></button>
		
		<button id=mediaMaxBtn class='right' title='Max Media Count' onclick="">0</button>
	</div>
</div>
<div style='display:none'>
	<div id=navigator>
		<button id=go4 class='right' style='width:45px;' onclick="gotoLast();" title=''><div class='golasticon'>&nbsp;</div></button>
		<button id=go3 class='right' style='width:45px;' onclick="gotoNext();" title=''><div class='gonexticon'>&nbsp;</div></button>
		<select class="ui-widget-header ui-corner-all right" onchange='limit=parseInt(this.value);refreshView();' style='width:65px;margin-top:3px;height:28px;'>
			<option>10</option><option>25</option><option>50</option><option>100</option><option>200</option>
		</select>
		<button id=go2 class='right' style='width:45px;' onclick="gotoBack();" title=''><div class='gobackicon'>&nbsp;</div></button>
		<button id=go1 class='right' style='width:45px;' onclick="gotoFirst();" title=''><div class='gofirsticon'>&nbsp;</div></button>
	</div>
	<div id=photoFieldUploaderFrame style='display:none;width:100%;overflow:hidden;margin:0px;padding:0px;' title='Upload Photo'>
        	<iframe style='width:100%;height:100%;border:0px;' src='' frameborder=0></iframe>
	</div>
</div>
<?php
}
?>
<script language='javascript'>
site="<?=$_REQUEST['forsite']?>";
</script>
