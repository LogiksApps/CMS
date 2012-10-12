<?php
if (!defined('ROOT')) exit('No direct script access allowed');

if(!isset($_REQUEST["forsite"])) {
	dispErrMessage("HostSite Is Required But Not Found","CMS Error",400);
	exit();
}

$webPath=getWebPath(__FILE__);

loadModule("page");

$params["toolbar"]=array(
		array("title"=>"Reload","icon"=>"reloadicon","tips"=>"Load Backup List","onclick"=>"reloadList()"),
		array("title"=>"Backup","icon"=>"openicon","tips"=>"Create New Backup Now","onclick"=>"doAppBackup()"),
		array("title"=>"About","icon"=>"infoicon","tips"=>"About AppBackup","onclick"=>"showAbout()"),
	);
$params["contentarea"]="printContent";

printPageContent("apppage",$params);
?>
<link href='<?=$webPath?>style.css' rel='stylesheet' type='text/css' media='all' /> 
<?php function printContent() { ?>
	<div id='appbackupops' style=''>
		<div id='header' style='background-color:#444444;padding-left:10px;'>
			<div class='backupicon' style='float:right;margin:7px;'></div>
			<h2>Create Backup For your web Files,Folders And Databases</h2>
		</div>
		<div id='backupholder' style='overflow:auto;'>
			 <table width="100%" border=1 cellspacing="0" cellpadding="0" class="backupTable nostyle" id="backupTable">
				<thead>
					  <tr>
							<th width=153px>Date</th>
							<th width=100px>Time</th>
							<th width=93px>Size</th>
							<th width=100px>Download</th>
							<th width=100px>Delete</th>
							<th width=100px>Rollback</th>
					  </tr>
				  </thead>
				  <tbody>
				  </tbody>
			</table>
		</div>
	</div>
	<div id='backuptooloperationtext' title='About Logiks AppBackup' style='display:none;'>
		<div id='introtext'>	
		<p>Logiks Backup System helps you with tasks as backup files, backup databases.
		This script is a must have for every webdeveloper/webmaster that want to save some time.</p>
			<?php
				include "features.html";
			?>
		</div>		
	</div>
<?php }?>
<script language=javascript>
forsite="<?=$_REQUEST["forsite"]?>";
lnk="services?scmd=appbackup&action=load_backup&forsite="+forsite;
<?php
	include "script.js";
?>
$(function() {
	reloadList();
});
</script>
