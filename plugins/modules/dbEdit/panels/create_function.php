<?php
if(!defined('ROOT')) exit('No direct script access allowed');

?>
<div class='col-xx-12' style='max-width: 1200px;width: 80%;margin: auto;margin-top: 20px;border: 1px solid #AAA;padding: 10px;'>
    <h4>Create Function</h4>
    <hr>
    <form id='createFunctionForm' class="form-horizontal">
        <div class="form-group">
            <label class="control-label col-sm-3" for="title">Function Name:</label>
            <div class="col-sm-9">
              <input type="text" class="form-control" name="title" placeholder="Enter Function Name" required>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-sm-3" for="columns">Parameters (One Per Line):</label>
            <div class="col-sm-9">
              <textarea name='parameters' class='form-control' id='parameters' style="min-height: 100px;" required placeholder='eg. a1 INT'></textarea>
            </div>
        </div>
        
        <div class="form-group">
            <label class="control-label col-sm-3" for="title">Return type:</label>
            <div class="col-sm-9">
                <select name='return_type' class='form-control'>
                    <option value="int">Int</option>
                    <option value="varchar">Varchar</option>
                    <option value="text">Text</option>
                    <option value="date">Date</option>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-sm-3" for="title">Return length/values:</label>
            <div class="col-sm-9">
                <input type="int" class="form-control" name="return_length" placeholder="Enter Return Length" required value='10'>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-sm-3" for="title">Return options:</label>
            <div class="col-sm-9">
                <select name='return_options' class='form-control'>
                    <option value="">None</option>
                    <option value="UNSIGNED">UNSIGNED</option>
                    <option value="ZEROFILL">ZEROFILL</option>
                    <option value="UNSIGNED ZEROFILL">UNSIGNED ZEROFILL</option>
                </select>
            </div>
        </div>
        
        <div class="form-group">
            <label class="control-label col-sm-3" for="columns">Definition:</label>
            <div class="col-sm-9">
                <textarea name='definition' class='form-control' id='definition' style="min-height: 100px;" required placeholder="SQL Statement with Return Statement"></textarea>
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
              <input type="text" class="form-control" name="comment" placeholder="Enter Comments">
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
        if($("#createFunctionForm").valid()) {
			q=$("#createFunctionForm").serialize();
			lx=_service("dbEdit","createFunction")+"&preview=true&dkey="+dkey+"&src=<?=$_GET['src']?>";
			processAJAXPostQuery(lx,q,function(txt) {
			    lgksAlert(txt);
			});
		}
    });
	$("#createFunctionForm").submit(function() {
		if($("#createFunctionForm").valid()) {
			q=$("#createFunctionForm").serialize();
			lx=_service("dbEdit","createFunction")+"&dkey="+dkey+"&src=<?=$_GET['src']?>";
			processAJAXPostQuery(lx,q,function(txt) {
				if(txt=="success") {
				    loadTableList('pages');
					$("#createFunctionForm")[0].reset();
					lgksToast("Function Created successfully");
				} else {
					lgksAlert(txt);
				}
			});
		}
		return false;
	});
});
</script>