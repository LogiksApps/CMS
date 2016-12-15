<?php
echo _css("cmsUploader");
?>
<div class='col-md-12' style='background:#337ab7'>
  <div id='pathBreadCrumb' class="btn-group btn-breadcrumb pull-left">
    <a href="#" class="btn btn-primary"><i class="glyphicon glyphicon-home"></i></a>
  </div>
</div>
<div class='uploadForm upload-drop-zone' id="drop-zone">
  <form id='uploadForm' action="<?=_service("cmsUploader","upload")?>" method='POST' enctype="multipart/form-data" target='uploadFrame'>
    <h4 class='uploadTitle'>Uploading to /</h4>
    <hr>
    <input type="hidden" name='path' value='' />
    <input type="hidden" name='forsite' value='<?=$_REQUEST['forSite']?>' />
    <div class="form-group">
      <input type="file" name="files[]" id="js-upload-files" multiple style='display: none;'>
    </div>
    <div id='uploadMaskIcon'>
      <i class='glyphicon glyphicon-plus' style='font-size: 5em;color: #999;margin: auto;display: block;width: 1em;margin-top: 15%;padding-bottom: 20%;'></i>
    </div>
  </form>
  <div id='uploadLoader' class="ajaxloading ajaxloading5 hidden">UPLOADING ...</div>
</div>
<iframe id='uploadFrame' name='uploadFrame' style='display:none;'></iframe>
<script>
basePath=null;
$(function() {
  parent.registerFileTreeListener("cmsUploader",filePathChanged);
  
  if(parent.getFileTree().find(".branch.active").length<=0) {
    basePath="/";
  } else {
    basePath=parent.getFileTree().find(".branch.active").attr("basepath");
  }
  updatePathCrumb(basePath);
  
  $("#uploadForm input[type=file]").change(function() {
    uploadFiles();
  });
  
  $("#uploadMaskIcon").click(function() {
    $("#uploadForm input[type=file]").click();
  });
});
function uploadFiles() {
  $("#uploadForm").addClass("hidden");
  $("#uploadLoader").removeClass("hidden");
  
  $("#uploadForm").submit();
}
function uploadMsg(msg) {
  $("#uploadLoader").addClass("hidden");
  $("#uploadForm").removeClass("hidden");
  if(msg=="DONE") {
    $("#uploadForm")[0].reset();
    parent.loadFileTree();
  } else {
    lgksAlert(msg);
  }
}
function filePathChanged(path) {
  updatePathCrumb(path);
}
function updatePathCrumb(path) {
  if(path==null) path="/";
  
  basePath=path;
  
  $("#uploadForm input[name=path]").val(path);
  
  $("#uploadForm .uploadTitle").html("Uploading to "+path);
  
  pathArr=path.split("/");
  pathArr=pathArr.reverse();
  
  $("#pathBreadCrumb").html("");
  $.each(pathArr,function(k,v) {
    if(v==null || v.length<=0) return;
    hx=pathArr.slice(k);
    hx=hx.join("/");
    $("#pathBreadCrumb").prepend("<a href='#' data-path='"+hx+"' class='btn btn-primary'>"+v+"</a>");
  });
  $("#pathBreadCrumb").prepend('<a href="#" data-path="/" class="btn btn-primary"><i class="glyphicon glyphicon-home"></i></a>');  
}
</script>