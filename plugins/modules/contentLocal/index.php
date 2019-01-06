<?php
if(!defined('ROOT')) exit('No direct script access allowed');

loadModule("pages");

loadModuleLib("cmsEditor","embed");

function pageSidebar() {
  // <form role='search'>
  //     <div class='form-group'>
  //       <input type='text' class='form-control' placeholder='Search'>
  //     </div>
  // </form>
  return "<div id='componentTree' class='componentTree list-group list-group-root well'></div>";
}

function pageContentArea() {
  return "<div id='componentSpace' class='componentSpace'><h2 align=center>Please load a content.</h2></div>".file_get_contents(__DIR__."/create.htm");
}

echo _css("contentLocal");
echo _js(["cmsEditor","contentLocal"]);

printPageComponent(false,[
  "toolbar"=>[
    "listContent"=>["icon"=>"<i class='fa fa-refresh'></i>"],
    "openCreateModal"=>["icon"=>"<i class='fa fa-plus'></i>","tips"=>"Create New"],
    ['type'=>"bar"],
    "deleteContent"=>["icon"=>"<i class='fa fa-trash'></i>","class"=>"onsidebarSelect"],
  ],
  "sidebar"=>"pageSidebar",
  "contentArea"=>"pageContentArea"
]);
?>
<script>
FORSITE='{$_REQUEST["forsite"]}';
</script>
