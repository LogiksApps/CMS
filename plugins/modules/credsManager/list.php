<?php
if(!defined('ROOT')) exit('No direct script access allowed');

loadModule("pages");
loadModuleLib("reports","api");

if(!isset($_REQUEST['panel'])) $_REQUEST['panel']="users";

if(!defined("ADMIN_PRIVILEGE_ID")) define("ADMIN_PRIVILEGE_ID",3);

$pageOptions=[
		"toolbar"=>[
			"users"=>["title"=>"Users","align"=>"right","class"=>($_REQUEST['panel']=="users")?"active":""],
			"groups"=>["title"=>"Groups","align"=>"right","class"=>($_REQUEST['panel']=="groups" || $_REQUEST['panel']=="orgchart")?"active":""],
			
			//"privileges"=>["title"=>"Privileges","align"=>"right","class"=>($_REQUEST['panel']=="privileges")?"active":""],
			"roles"=>["title"=>"Roles","align"=>"right","class"=>($_REQUEST['panel']=="roles")?"active":""],
			"access"=>["title"=>"Access","align"=>"right","class"=>($_REQUEST['panel']=="access")?"active":""],
            "guid"=>["title"=>"GUID","align"=>"right","class"=>($_REQUEST['panel']=="guid")?"active":""],
			// ['type'=>"bar"],

			// ["title"=>"Search Site","type"=>"search","align"=>"left"]
			 "reload"=>["icon"=>"<i class='fa fa-refresh'></i>"],
			 "createNew"=>["icon"=>"<i class='fa fa-plus'></i>","tips"=>"Create New"],
			['type'=>"bar"],
			"trash"=>["icon"=>"<i class='fa fa-trash'></i>"],//,"class"=>"onRowSelect"
		],
		"contentArea"=>"pageContentArea"
	];

if(RoleModel::getInstance()->getVersion()>=3) {
    // if(!getFeature("ALLOW_PRIVILEGE_MANAGER","credsManager")) {
    //     unset($pageOptions['toolbar']['privileges']);
    // }
    if(!getFeature("ALLOW_ROLES_MANAGER","credsManager")) {
        unset($pageOptions['toolbar']['roles']);
    }
    if(!getFeature("ALLOW_ACCESS_MANAGER","credsManager")) {
        unset($pageOptions['toolbar']['access']);
    }
    if(!getFeature("ALLOW_GROUPS_MANAGER","credsManager")) {
        unset($pageOptions['toolbar']['groups']);
    }
    if(!getFeature("ALLOW_GUID_MANAGER","credsManager")) {
        unset($pageOptions['toolbar']['guid']);
    }
} else {
    
}

if($_SESSION["SESS_PRIVILEGE_ID"]<=ADMIN_PRIVILEGE_ID) {//ADMIN_USERIDS
	
} else {
    if(isset($pageOptions['toolbar']['guid'])) unset($pageOptions['toolbar']['guid']);
    if(isset($pageOptions['toolbar']['access'])) unset($pageOptions['toolbar']['access']);
}

printPageComponent(false,$pageOptions);

echo _css("credsManager");
echo _js("credsManager");

function pageContentArea() {
	if($_SESSION["SESS_PRIVILEGE_ID"]<=ADMIN_PRIVILEGE_ID) {
		$rpt=__DIR__."/panels/{$_REQUEST['panel']}_root.json";
	} else {
		$rpt=__DIR__."/panels/{$_REQUEST['panel']}.json";
	}

	if(file_exists($rpt)) {
		ob_start();
    	echo "<div class='col-xs-12'>";// report-notoolbar
    	echo "<div class='row'>";
    	echo _css("reports");
    	echo "<div class='reportholder' style='width:100%;height:100%;'>";
    	$a=printReport($rpt,"core");
    	if(!$a) {
    		echo "<h3 align=center>Panel Source Corrupted</h3>";
    	}
    	echo "</div>";
    	echo _js(["FileSaver","html2canvas","reports"]);
    	echo "</div>";
    	echo "<div id='sliderPanel' class='sliderPanel'><iframe id='credsEditor' width=100% height=100% style='width:100%;height:100%;' frameborder=0 ></iframe></div>";
    	echo "</div>";
    	$html=ob_get_contents();
    	ob_end_clean();
    	
    	return $html;
	} else {
	    return "<h3 align=center>Sorry, no access enabled or requested panel not found for you.</h3>";
	}
}
?>
<style>
.reportTable .table-tools h1.reportTitle {
    padding: 7px;
}
.list-group-create {
    position: relative;
    display: block;
    padding: 10px 15px;
    margin-bottom: -1px;
    background-color: #fff;
    border: 1px solid #ddd;
    overflow: hidden;
    border-right: 0px;
    padding-left: 25px;
    cursor: pointer;
}
.reportTable .table-tools .btn {
        height: 34px !important;
}
</style>
<script>
$(function() {
    LGKSReports.getInstance(Object.keys(LGKSReportsInstances)[0]).addListener(postReportLoad, "postload");
});
function postReportLoad() {
    if($(".report-sidebar>.list-group-item.active").length>0 && $(".report-sidebar>.list-group-item.active").index()>1) {
        $(".control-primebar *[cmd=deletePrivilege]").removeClass("hidden");
        $(".control-primebar *[cmd=editPrivilege]").removeClass("hidden");
    } else {
        $(".control-primebar *[cmd=deletePrivilege]").addClass("hidden");
        $(".control-primebar *[cmd=editPrivilege]").addClass("hidden");
    }
    setTimeout(function() {
        $(".report-sidebar").append("<li class='list-group-create list-group-flush list-group-flush text-center' data-value=0><i class='fa fa-plus'></i> New Privilege</li>");
        
        $(".report-sidebar .list-group-create").click(createPrivilege);
    }, 500);
}
function onLoadSidebar() {
    
}

function editPrivilege() {
	if($(".report-sidebar>.list-group-item.active").length<=0 || $(".report-sidebar>.list-group-item.active").index()<=1) {
	    return;
	}
	
	waitingDialog.show('Loading Editor ...');
	$("#credsEditor").attr("src",_link("modules/credsManager/privileges/edit/"+$(".report-sidebar>.list-group-item.active").data("value")));
}
function createPrivilege() {
    waitingDialog.show('Loading Editor ...');
	$("#credsEditor").attr("src",_link("modules/credsManager/privileges/new"));
}
function editRoleRules(tr, row) {
    alert("Coming Soon");
}
</script>