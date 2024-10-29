<?php
if(!defined('ROOT')) exit('No direct script access allowed');

loadModule("pages");

function pageSidebar() {
	return "<div id='componentTree' class='componentTree list-group list-group-root well'></div>";
}

function pageContentArea() {
	return "
	    <div class='container-fluid' style='padding-top: 50px;margin: auto;width: auto;text-align: center;'>
	    <div class='well' style='width: 400px;text-align: left;display: inline-block;'>
    	    <form action='"._service("pluginManager","upload")."&forsite=".CMS_SITENAME."' target=targetFrame method='POST' enctype='multipart/form-data'>
              <div class='form-group'>
                <div>Upload Package Zip File</div>
                <label for='exampleFormControlFile1' style='width: 100%;height: 32px;'>
                    <div id='attachFileName' style='text-align: center;padding-top: 6px;padding-bottom: 6px;border: 1px dashed #333;background: white;'>
                    	Click to attach file</div>
                    <input name='attachment' type='file' class='form-control-file' id='exampleFormControlFile1' style='display: none;'>
                </label>
              </div>
              <div class='form-group'>
                <button class='btn btn-info pull-left' type='reset'>Reset</button>
                <button class='btn btn-success pull-right' type='submit'>Upload</button>
              </div>
            </form>
        </div>
        
        <div class='well' style='width: 400px;text-align: left;display: inline-block;margin-left: 20px;'>
    	    <form action='"._service("pluginManager","attachuri")."&forsite=".CMS_SITENAME."' target=targetFrame method='POST' enctype='multipart/form-data'>
              <div class='form-group'>
                <div>Remote URL to package zip file</div>
                <label for='exampleFormControlFile2' style='width: 100%;height: 32px;'>
                    <input name='attachment' type='text' class='form-control' id='exampleFormControlFile2'>
                </label>
              </div>
              <div class='form-group'>
                <button class='btn btn-info pull-left' type='reset'>Reset</button>
                <button class='btn btn-success pull-right' type='submit'>Upload</button>
              </div>
            </form>
        </div>
        </div>
        <iframe id='targetFrame' name='targetFrame' style='display: none !important;'/>
	";
}

//Installed : Search, Configure (feature/localize), Editor (cms.php), Disable, Remove, Goto Market, Report Issue
//Repos : Search, Install, View (from market in iframe), Goto Market

echo _js(["pluginManager"]);
echo _css(["pluginManager"]);

printPageComponent(false,[
		"toolbar"=>[
		    "reloadPage"=>["icon"=>"<i class='fa fa-refresh'></i>"],
		    
			"loadPluginManager"=>["title"=>"Installed","align"=>"right"],
// 			"loadRepo"=>["title"=>"eStore","align"=>"right"],
            "loadUploader"=>["title"=>"Upload","align"=>"right","class"=>"active"],
		],
		"sidebar"=>false,//"pageSidebar",
		"contentArea"=>"pageContentArea"
	]);
?>
<script>
$(function() {
    currentType="uploader";
    //listPackages();
});
function loadPluginManager() {
    window.location = _link("modules/pluginManager");
}
function reloadPage() {
    window.location.reload();
}
</script>