<?php
if(!defined('ROOT')) exit('No direct script access allowed');

loadModuleLib("logiksIDE", "api");

$arrCompsList = getCompsList();

echo _css("jquery.contextMenu");
echo _js(["jquery.contextMenu"]);//,"sidebarFiles"
?>
<style>
#sidebarPane {
    height: 100%;
    overflow: auto;
    padding-bottom: 70px;
}
.sidebarTree {
    border-top: 1px solid #AAA;
    padding-bottom: 60px;
}
.sidebarTree .panel-group {
    
}
.sidebarTree .panel-group .panel {
    margin: 0px;
    border-radius: 0px;
    background: transparent;
    border: 0px;
    border-bottom: 1px solid #999;
}
.sidebarTree .panel-group .panel:last-child {
    /*border-bottom: 0px;*/
}
.sidebarTree .panel-group .panel * {
    background: transparent;
    color: #FFF;
    text-decoration: none;
    font-weight: normal;
}
.sidebarTree .panel-group .panel .panel-body {
    padding: inherit;
    padding-left: 8px;
}
.sidebarTree .panel-group .panel .panel-title {
    font-size: 14px;
}
.sidebarTree .panel-group .panel .list-group-item {
    border: 0px;
    padding: 4px;
    padding-left: 15px;
    cursor: pointer;
}
.sidebarTree .panel-group .panel .list-group {
    border: 0px;
    margin-top: -5px;
    margin-bottom: 5px;
}
.list-group .list-group {
    margin-top: 3px !important;
    margin-bottom: -3px !important;
}
.sidebarTree .fa {
    cursor: pointer;
}
.modal_create_item .modal-body {
    padding: 2px 15px;
}
.sidebarTree .panel-group .panel .panel-heading {
    padding-right: 5px;
    padding-left: 12px;
}
.sidebarTree .panel-group .panel .panel-title .fa {
    margin-right: 5px;
}
.sidebarTree .group_icon {
    font-size: 18px;
    opacity: 0.8;
}
.sidebarTree .panel-heading a {
    /*font-size: 16px;*/
    padding-left: 3px;
    line-height: 20px;
}
</style>
<div id='searchField' class="searchField hidden d-none">
    <input type='text' placeholder='Search files' />
</div>
<div id="sidebarSourceTree" class='sidebarTree' basepath='/'>
    <div class="panel-group">
<?php
    foreach($arrCompsList as $key=>$attrs) {
        $hashKey = md5($key);
        if(isset($attrs["label"])) $label = $attrs["label"];
        else $label = _ling(str_replace("_", " ", $key));
        if(!isset($attrs['help_link'])) $attrs['help_link'] = "https://github.com/Logiks/Logiks-Core/wiki";
?>
    
      <div class="panel panel-default" data-key='<?=$key?>'>
        <div class="panel-heading">
          <h4 class="panel-title">
                <i class='fa fa-question-circle pull-right help-item' data-key='<?=$key?>' data-help_link="<?=$attrs['help_link']?>"></i>
                <i class='fa fa-refresh pull-right refresh-list' data-key='<?=$key?>'></i>
                <i class='fa fa-plus pull-right create-item' data-key='<?=$key?>'></i>
                
                <i class='fa fa-<?=$attrs['icon']?> group_icon pull-left'></i>
                
                <a data-toggle="collapse" href="#<?=$hashKey?>"> <?=$label?></a>
          </h4>
        </div>
        <div id="<?=$hashKey?>" class="panel-body panel-collapse collapse">
          <ul class="list-group">
            
          </ul>
        </div>
      </div>
<?php
    }
?>
    </div>
</div>
<script id="create-item-template" type="text/x-handlebars-template">
    asdas dasd
</script>
<script id="list-group-item-template" type="text/x-handlebars-template">
    {{#if Data.groups}}
        {{#each Data.items}}
            <li class="list-group-item"><i class='fa fa-folder'></i> {{@key}}
                <ul class="list-group">
                    {{#each this}}
                        <li class="list-group-item list-group-item-file" data-type='{{type}}' data-path='{{path}}'><i class='fa fa-{{icon}}'></i> &nbsp;{{title}}</li>
                    {{/each}}
                </ul>
            </li>
        {{/each}}
    {{else}}
        {{#each Data.items}}
            <li class="list-group-item list-group-item-file" data-type='{{type}}' data-path='{{path}}'><i class='fa fa-{{icon}}'></i> &nbsp;{{title}}</li>
        {{/each}}
    {{/if}}
</script>
<script>
$(function() {
    $(".sidebarTree").delegate(".create-item", "click", function() {
        var dataKey = $(this).data("key");
        
        createItem(dataKey);
    });
    $(".sidebarTree").delegate(".refresh-list", "click", function() {
        var dataKey = $(this).data("key");
        
        refreshItemList(dataKey);
    });
    
    $(".sidebarTree").delegate(".help-item", "click", function() {
        var dataKey = $(this).data("key");
        var helpLink = $(this).data("help_link");
        if(helpLink!=null && helpLink.length>1) {
            window.open(helpLink);
        }
    });
    
    $(".sidebarTree").delegate(".list-group-item-file", "click", function() {
        var filePath = $(this).data("path");
        var dataKey = $(this).data("type");
        
        openItem(filePath, dataKey);
    });
    
    $(".sidebarTree").delegate(".panel-heading a", "click", function() {
        if($(this).closest(".panel").find(".panel-body>ul").children().length<=0 ||
            $(this).closest(".panel").find(".panel-body>ul .loader").length>0) {
            var dataKey = $(this).closest(".panel").data("key");
            viewItemList(dataKey);
        } else {
            return;
        }
    });
});
function viewItemList(typeKey) {
    $("#sidebarSourceTree .panel[data-key='"+typeKey+"'] .panel-body>ul").html("<div class='loader text-center'><i class='fa fa-spin fa-spinner fa-2x'></i></div>");
    processAJAXQuery(_service("files2", "listItems")+"&typekey="+typeKey, function(data) {
        
        var template = Handlebars.compile($("#list-group-item-template").html());
        $("#sidebarSourceTree .panel[data-key='"+typeKey+"'] .panel-body>ul").html(template(data));
    }, "json");
}
function refreshItemList(typeKey) {
    $("#sidebarSourceTree .panel[data-key='"+typeKey+"'] .panel-body>ul").html("<div class='loader text-center'><i class='fa fa-spin fa-spinner fa-2x'></i></div>");
    processAJAXQuery(_service("files2", "listItems")+"&refresh=true&typekey="+typeKey, function(data) {
        
        var template = Handlebars.compile($("#list-group-item-template").html());
        $("#sidebarSourceTree .panel[data-key='"+typeKey+"'] .panel-body>ul").html(template(data));
    }, "json");
}
function openItem(filePath, typeKey) {
    lgksLoader("Opening Source ...");
    processAJAXPostQuery(_service("files2", "openItem"), "&typekey="+typeKey+"&path="+filePath, function(data) {
        lgksLoaderHide();
        if(data.Data.status) {
            openLinkFrame(data.Data.title, data.Data.link, true, false);
        } else {
            if(data.Data.msg==null) data.Data.msg = "Unkown error occured while opening the source";
            lgksAlert(data.Data.msg);
        }
    }, "json");
}
function createItem(typeKey) {
    //lgksAlert($("#create-item-template").html(), "Create Item", function() {});
    
    lgksLoader("Opening Source ...");
    processAJAXPostQuery(_service("files2", "createItemPanel"), "&typekey="+typeKey, function(data) {
        lgksLoaderHide();
        
        bootbox.dialog({
            title: "Create "+toTitle(typeKey.replace(/_/g,' ')),
            message: data,//$("#create-item-template").html(),
            closeButton: true,
            className: "modal_create_item",
            callback: function() {
                // alert("Testing");
            } 
        });
    }, "html");
}
</script>
