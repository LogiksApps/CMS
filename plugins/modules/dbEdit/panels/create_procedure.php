<?php
if(!defined('ROOT')) exit('No direct script access allowed');

?>
<div class='col-xx-12' style='max-width: 1200px;width: 80%;margin: auto;margin-top: 20px;border: 1px solid #AAA;padding: 10px;'>
    <h4>Create Procedure</h4>
    <hr>
    <form id='createProcedureForm' class="form-horizontal">
        <div class="form-group">
            <label class="control-label col-sm-3" for="title">Procedure Name:</label>
            <div class="col-sm-9">
              <input type="text" class="form-control" name="title" placeholder="Enter Procedure Name" required>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-sm-3" for="columns">Parameters (One Per Line):</label>
            <div class="col-sm-9">
              <textarea name='parameters' class='form-control' id='parameters' style="min-height: 100px;" required placeholder='eg. IN a1 INT'></textarea>
            </div>
        </div>
        
        <div class="form-group">
            <label class="control-label col-sm-3" for="columns">Definition:</label>
            <div class="col-sm-9">
                <textarea name='definition' class='form-control' id='definition' style="min-height: 100px;" required placeholder="SQL Statement"></textarea>
            </div>
        </div>
        
        <div class="form-group">
            <label class="control-label col-sm-3" for="title">Is deterministic:</label>
            <div class="col-sm-9">
                <select name='is_deterministic' class='form-control'>
                  <option value="DETERMINISTIC">True</option>
                  <option value="NOT DETERMINISTIC">False</option>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-sm-3" for="title">Security Type:</label>
            <div class="col-sm-9">
                <select name='security_type' class='form-control'>
                  <option value="DEFINER">Definer</option>
                  <option value="INVOKER">Invoker</option>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-sm-3" for="title">SQL data access:</label>
            <div class="col-sm-9">
                <select name='sqldataaccess' class='form-control'>
                  <option>NO SQL</option>
                  <option>CONTAINS SQL</option>
                  <option>READS SQL DATA</option>
                  <option>MODIFIES SQL DATA</option> 
                </select>
            </div>
        </div>
        
        <div class="form-group">
            <label class="control-label col-sm-3" for="comment">Comment:</label>
            <div class="col-sm-9">
              <input type="text" class="form-control" name="comment" placeholder="Enter comments">
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
        if($("#createProcedureForm").valid()) {
			q=$("#createProcedureForm").serialize();
			lx=_service("dbEdit","createProcedure")+"&preview=true&dkey="+dkey+"&src=<?=$_GET['src']?>";
			processAJAXPostQuery(lx,q,function(txt) {
			    lgksAlert(txt);
			});
		}
    });
	$("#createProcedureForm").submit(function() {
		if($("#createProcedureForm").valid()) {
			q=$("#createProcedureForm").serialize();
			lx=_service("dbEdit","createProcedure")+"&dkey="+dkey+"&src=<?=$_GET['src']?>";
			processAJAXPostQuery(lx,q,function(txt) {
			    if(txt=="success") {
					$("#createProcedureForm")[0].reset();
					lgksToast("Procedure Created successfully");
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