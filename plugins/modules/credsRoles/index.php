<?php
if(!defined('ROOT')) exit('No direct script access allowed');

if(isset($_GET['ui']) && $_GET['ui']=="ui1") {
  include_once __DIR__."/ui/ui1.php";
} elseif(isset($_GET['ui']) && $_GET['ui']=="ui0") {
  include_once __DIR__."/ui/ui0.php";
} else {
  if(isset($_COOKIE['USERROLES-UI']))
    include_once __DIR__."/ui/{$_COOKIE['USERROLES-UI']}.php";
  else
    include_once __DIR__."/ui/ui0.php";
}
?>
<div id="uploaderModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="btn btn-default pull-right" data-dismiss="modal">Cancel</button>
        <h4 class="modal-title">Role CSV Uploader</h4>
      </div>
      <div class="modal-body  text-center">
        <form class="fileform" action="<?=_service("userRoles","uploadcsv")?>" method="post" enctype="multipart/form-data" target="targetFrame">
                <label class="nav-label-button">
                    <input type="file" name="attachment" class="hidden" onchange="$(this).closest('form')[0].submit();">
                    <i class="fa fa-upload"></i> Upload
                </label>
            </form>
      </div>
    </div>
  </div>
</div>
<iframe id='targetFrame' name='targetFrame' class='hidden'></iframe>
