<?php
if(!isset($jsonPage['css'])) $jsonPage['css']="";
if(!isset($jsonPage['js_preload'])) $jsonPage['js_preload']="";
if(!isset($jsonPage['js_postload'])) $jsonPage['js_postload']="";
if(!isset($jsonPage['modules_preload'])) $jsonPage['modules_preload']="";
if(!isset($jsonPage['modules_postload'])) $jsonPage['modules_postload']="";
?>
<div class="panel panel-default">
    <div class="panel-body">
        <form class="form-horizontal">
            <div class="form-group">
                <label class="col-sm-3 control-label">Styles</label>
                <div class="col-sm-9">
                  <input type="text" class="form-control" name="css" placeholder="Page Styles" value='<?=$jsonPage['css']?>'>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-3 control-label">Scripts Preloaded</label>
                <div class="col-sm-9">
                  <input type="text" class="form-control" name="js_preload" placeholder="Page Scripts Loaded Before Body" value='<?=$jsonPage['js_preload']?>'>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label">Scripts Postloaded</label>
                <div class="col-sm-9">
                  <input type="text" class="form-control" name="js_postload" placeholder="Page Scripts Loaded After Body" value='<?=$jsonPage['js_postload']?>'>
                </div>
            </div>


            <div class="form-group">
                <label class="col-sm-3 control-label">Modules Preloaded</label>
                <div class="col-sm-9">
                  <input type="text" class="form-control" name="modules_preload" placeholder="Page Modules Loaded Before Body" value='<?=$jsonPage['modules_preload']?>'>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label">Modules Postloaded</label>
                <div class="col-sm-9">
                  <input type="text" class="form-control" name="modules_postload" placeholder="Page Modules Loaded After Body" value='<?=$jsonPage['modules_postload']?>'>
                </div>
            </div>
        </form>
    </div>
</div>