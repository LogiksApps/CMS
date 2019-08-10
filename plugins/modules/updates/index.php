<?php
if(!defined('ROOT')) exit('No direct script access allowed');

loadModule("pages");

printPageComponent(false,[
		"toolbar"=>[
// 			"loggers"=>["title"=>"Loggers","align"=>"right","type"=>"dropdown","options"=>$loggers],
			// "pages"=>["title"=>"Pages","align"=>"right"],
			// "comps"=>["title"=>"Components","align"=>"right"],
			// "layouts"=>["title"=>"Layouts","align"=>"right"],
			// ['type'=>"bar"],

			// ["title"=>"Search Site","type"=>"search","align"=>"left"]
			"reloadUI"=>["icon"=>"<i class='fa fa-refresh'></i>"],
			['type'=>"bar"],
// 			"download"=>["icon"=>"<i class='fa fa-download'></i>","class"=>"onsidebarSelect onOnlyOneSelect"],
// 			"trash"=>["icon"=>"<i class='fa fa-trash'></i>","class"=>"onsidebarSelect"],
		],
		"sidebar"=>false,
		"contentArea"=>"pageContentArea"
	]);

function pageSidebar() {
	return "
	<div id='componentTree' class='componentTree list-group list-group-root well'>
	</div>
	";
}
function pageContentArea() {
	return "
	    <div class='container'>
	        <div class='well' style='margin-top: 30px;'>
	            <b>Logiks Updates</b><br><br>
	            <div id='updateConsole'>
	                Start checking out for updates by pressing below button<br>
	            </div>
	        </div>
	        <div class='text-center'>
	            <button class='btn btn-info' onclick='checkForUpdate()'><i class='fa fa-redo'></i> Check App Updates</button>
	        </div>
	    </div>
	";
}
?>
<script>
function reloadUI () {
    window.location.reload();
}
function checkForUpdate() {
    $("#updateConsole").html("Collecting data for installation ...<br>");
    
    processAJAXQuery(_service("updates","for-app"), function(data) {
        $("#updateConsole").append(data);
        
        processAJAXQuery(_service("updates","for-plugins-all"), function(data) {
            $("#updateConsole").append(data);
            lgksToast("Update checking complete");
        },'raw');
    },'raw');
}
function installUpdate() {
    $("#updateConsole").html("Collecting data for the update ...<br>");
}
</script>