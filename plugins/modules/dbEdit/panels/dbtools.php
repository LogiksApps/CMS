<?php
if(!defined('ROOT')) exit('No direct script access allowed');

$buttonList = [
  ["title"=>"Optimize Database","cmd"=>"optimizeDB","class"=>"info"]
];
?>
<div class='container-fluid' style='margin:auto;width:90%;margin-top:20px;'>
  <div class='text-center'>
    <?php
      foreach($buttonList as $btn) {
        echo "<button class='btn btn-default btn-{$btn['class']}' data-cmd='{$btn['cmd']}' onclick='activateBtn(this)'>{$btn['title']}</button>";
      }
    ?>
  </div>
  <div id='cmdOutput' class='textOutput' >
    Select from commands above to run them.
  </div>
</div>
<script>
function activateBtn(src) {
  cmd =$(src).data("cmd");
  if(cmd==null) return;
  
  $("#cmdOutput").html("<div class='ajaxloading5'></div>").removeClass("hidden");

	lx=_service("dbEdit","cmd")+"&dkey="+dkey+"&src="+cmd;
	$("#cmdOutput").load(lx);
}
</script>