<?php
if(!defined('ROOT')) exit('No direct script access allowed');

loadModuleLib("cmsEditor","embed");
?>
<div id='pageEditor' src='<?=$srcName?>'>
  <ul class="nav nav-tabs" role="tablist">
    <li role="presentation" class="active"><a class='' href="#info" aria-controls="info" role="tab" data-toggle="tab"><strong>
    <?=$title?></strong></a></li>
    <li role="presentation"><a class='' href="#meta" aria-controls="meta" role="tab" data-toggle="tab">META</a></li>
    <li role="presentation"><a class='' href="#layout" aria-controls="layout" role="tab" data-toggle="tab">OPTS</a></li>
    <li role="presentation"><a class=' showOpen' href="#markup" aria-controls="markup" role="tab" data-toggle="tab">MARKUP</a></li>
    <li role="presentation"><a class=' showOpen' href="#code" aria-controls="code" role="tab" data-toggle="tab">CODE</a></li>
    <li role="presentation"><a class=' showOpen' href="#style" aria-controls="style" role="tab" data-toggle="tab">STYLE</a></li>
    <li role="presentation"><a class=' showOpen' href="#javascript" aria-controls="javascript" role="tab" data-toggle="tab">SCRIPT</a></li>

    <li class='pull-right btn btn-success' cmd='save' title='Save Page'><i class='fa fa-save'></i></li>
    <!-- <li class='pull-right btn btn-info hidden' cmd='open' title='Open External Link'><i class='fa fa-external-link'></i></li> -->
    <!-- <li class='pull-right btn btn-warning' cmd='version' title=''><i class='fa fa-code-fork'></i></li> -->
  </ul>

  <div class="tab-content">
    <div role="tabpanel" class="tab-pane active" id="info">
    	<?php
            include "panels/info.php";
        ?>
    </div>
    <div role="tabpanel" class="tab-pane" id="meta">
    	<?php
            include "panels/meta.php";
        ?>
    </div>
    <div role="tabpanel" class="tab-pane" id="layout">
    	<?php
            include "panels/layout.php";
        ?>
    </div>

    <div role="tabpanel" class="tab-pane" id="markup">
    	<div id="editorMarkup" class='editorArea' ext='html'></div>
    </div>
    <div role="tabpanel" class="tab-pane" id="code">
    	<div id="editorCode" class='editorArea' ext='php'></div>
    </div>
    <div role="tabpanel" class="tab-pane" id="style">
    	<div id="editorStyle" class='editorArea' ext='css'></div>
    </div>
    <div role="tabpanel" class="tab-pane" id="javascript">
    	<div id="editorScript" class='editorArea' ext='javascript'></div>
    </div>
  </div>
</div>
<script>
var readonlyEditors=<?=(isset($_REQUEST['readonly']) && $_REQUEST['readonly']=="true")?"true":"false"?>;
</script>