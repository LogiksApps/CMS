<?php
if(!defined('ROOT')) exit('No direct script access allowed');

$tblList = _db($_ENV['DBKEY'])->get_tableList();
?>
<div class='col-xx-12' style='max-width: 1200px;width: 80%;margin: auto;margin-top: 20px;border: 1px solid #AAA;padding: 10px;'>
    <h4>Create Trigger</h4>
    <hr>
    <form id='createTriggerForm' class="form-horizontal">
        <div class="form-group">
            <label class="control-label col-sm-3" for="title">Trigger Name:</label>
            <div class="col-sm-9">
              <input type="text" class="form-control" name="title" placeholder="Enter Trigger Name" required>
            </div>
        </div>
        
        <div class="form-group">
            <label class="control-label col-sm-3" for="title">Trigger Table:</label>
            <div class="col-sm-9">
                <select name='trigger_table' class='form-control'>
                  <?php
                    foreach($tblList as $tbl) echo "<option>{$tbl}</option>";
                  ?>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-sm-3" for="title">Trigger Time:</label>
            <div class="col-sm-9">
                <select name='trigger_time' class='form-control'>
                  <option value="BEFORE">Before</option>
                  <option value="AFTER">After</option>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-sm-3" for="title">Trigger Event:</label>
            <div class="col-sm-9">
                <select name='trigger_event' class='form-control'>
                  <option value="INSERT">INSERT</option>
                  <option value="UPDATE">UPDATE</option>
                  <option value="DELETE">DELETE</option>
                </select>
            </div>
        </div>
       
        <div class="form-group">
            <label class="control-label col-sm-3" for="columns">Definition:</label>
            <div class="col-sm-9">
                <textarea name='definition' class='form-control' id='definition' style="min-height: 100px;" required placeholder="SQL Statement"></textarea>
            </div>
        </div>
        
        <hr>
    	<div class='text-center'>
    		<button type='reset' class='btn btn-danger'>Reset</button>
    		<button type='button' class='btn btn-warning preview'>Preview</button>
    		<button type='submit' class='btn btn-success'>Submit</button>
    	</div>
    </form>
</div>
<script>
$(function() {
    $("button.preview").click(function() {
        if($("#createTriggerForm").valid()) {
			q=$("#createTriggerForm").serialize();
			lx=_service("dbEdit","createTrigger")+"&preview=true&dkey="+dkey+"&src=<?=$_GET['src']?>";
			processAJAXPostQuery(lx,q,function(txt) {
			    lgksAlert(txt);
			});
		}
    });
	$("#createTriggerForm").submit(function() {
		if($("#createTriggerForm").valid()) {
			q=$("#createTriggerForm").serialize();
			lx=_service("dbEdit","createTrigger")+"&dkey="+dkey+"&src=<?=$_GET['src']?>";
			processAJAXPostQuery(lx,q,function(txt) {
			    if(txt=="success") {
					$("#createTriggerForm")[0].reset();
					lgksToast("Trigger Created successfully");
					loadTableList('pages');
				} else {
					lgksAlert(txt);
				}
			});
		}
		return false;
	});
});
</script>