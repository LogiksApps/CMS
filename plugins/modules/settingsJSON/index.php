<?php
if(!defined('ROOT')) exit('No direct script access allowed');

if(!checkRootAccess()) {
    return;
}

loadModule("pages");

printPageComponent(false,[
		"toolbar"=>[
			"db"=>["title"=>"DB","align"=>"right","class"=>"active"],
			"cache"=>["title"=>"CACHE","align"=>"right"],
			"msg"=>["title"=>"MSG","align"=>"right"],
			"fs"=>["title"=>"FS","align"=>"right"],
// 			"log"=>["title"=>"ERROR-LOG","align"=>"right"],

			"refresh2"=>["icon"=>"<i class='fa fa-refresh'></i>"],
			['type'=>"bar"],
			"createNew"=>["icon"=>"<i class='fa fa-plus'></i>", "title"=>"Add"],
		],
		"contentArea"=>"pageContentArea"
	]);

echo _css(["cards","settingsJSON"]);
echo _js("settingsJSON");

function pageContentArea() {
	return "";
}
?>
<style>
.card .bottomLink {
    position: absolute;
    bottom: 12px;
    width: 91%;
    text-align: right;
}
</style>
<script>
var currentKey = "";
$(function() {
    $("#pgworkspace").delegate(".btn-edit", "click", function() {
        editCard(this);
    });
    $("#pgworkspace").delegate(".btn-trash", "click", function() {
        deleteCard(this);
    });
    $("body").delegate(".bootbox .save-config", "click", function() {
        saveConfig();
    });
     
});
function pgRefresh2() {
	window.document.location.reload();
}
function createNew() {
    lgksPrompt("Please give new KeyID (Space or special characters not allowed)", "New Key", function(ans) {
        if(ans && ans.length>0) {
            currentKey = ans;
            
            lgksOverlayDiv("#configForm", "Create - "+currentSRC, {});
            
            $(".bootbox input[name=src]").val(currentSRC);
            $(".bootbox input[name=type]").val("app");
            $(".bootbox input[name=key]").val(ans);
            $(".bootbox textarea").val("{}");
        }
    });
}
function editCard(ele) {
    currentKey = $(ele).data("key");
    
    lgksOverlayDiv("#configForm", "Edit - "+$(ele).data("key").toUpperCase(), {});
    
    $(".bootbox input[name=src]").val($(ele).data("src"));
    $(".bootbox input[name=type]").val($(ele).data("type"));
    $(".bootbox input[name=key]").val($(ele).data("key"));
    $(".bootbox textarea").val($(ele).closest(".card").find("textarea").val());
}
function deleteCard(ele) {
    currentKey = $(ele).data("key");
    
    lgksConfirm(`Do you want to delete the <b>${$(ele).data("key")}</b> key from Config`, "Delete Config", function(ans) {
        if(ans) {
            lgksLoader("Saving");
            processAJAXPostQuery(_service("settingsJSON", "deleteConfig"), `key=${$(ele).data("key")}&src=${$(ele).data("src")}&type=${$(ele).data("type")}`, function(data) {
                pgRefresh();
                lgksLoaderHide()
            }, "raw");
        }
    })
}
function saveConfig() {
    var qData = $(".bootbox form").serialize();
    
    lgksConfirm(`Do you want to save the <b>${currentKey}</b> key?`, "Update Config - "+currentKey, function(ans) {
        if(ans) {
            lgksLoader("Saving");
            processAJAXPostQuery(_service("settingsJSON", "saveConfig"), qData, function(data) {
                lgksLoaderHide()
                if(data.Data=="SUCCESS") {
                    $(".bootbox").modal("hide")
                    pgRefresh();
                } else {
                    lgksToast(data.Data);
                }
            }, "json");
        }
    })
}
</script>