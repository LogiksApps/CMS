<?php
if(!defined('ROOT')) exit('No direct script access allowed');

?>
<style>
form input[required], form input[required], form textarea[required] {
    border:1px solid rgba(177, 92, 92, 0.8);
}
</style>
<div class='container-fluid' style='margin:auto;width:90%;margin-top:20px;'>
    <h4>Create View</h4>
    <hr>
    <form id='viewCreateForm' class="form-horizontal">
      <div class="form-group">
        <label class="control-label col-sm-2" for="title">VIEW NAME:</label>
        <div class="col-sm-10">
          <input type="text" class="form-control" name="title" placeholder="Enter View Name" required>
        </div>
      </div>
      <div class="form-group">
        <label class="control-label col-sm-2" for="ALGORITHM">ALGORITHM:</label>
        <div class="col-sm-10">
          <select name='algorithm' class='form-control'>
              <option value="UNDEFINED">UNDEFINED</option><option value="MERGE">MERGE</option><option value="TEMPTABLE">TEMPTABLE</option>
          </select>
        </div>
      </div>
      <div class="form-group">
        <label class="control-label col-sm-2" for="sql_security">SQL SECURITY:</label>
        <div class="col-sm-10">
          <select name='sql_security' class='form-control'>
              <option value=""></option><option value="DEFINER">DEFINER</option><option value="INVOKER">INVOKER</option>
          </select>
        </div>
      </div>
      <div class="form-group">
        <label class="control-label col-sm-2" for="sql_security">WITH CHECK OPTION:</label>
        <div class="col-sm-10">
          <select name='with_options' class='form-control'>
              <option value=""></option><option value="CASCADED">CASCADED</option><option value="LOCAL">LOCAL</option>
          </select>
        </div>
      </div>
      
      <div class="form-group">
        <label class="control-label col-sm-2" for="ALGORITHM">DEFINER:</label>
        <div class="col-sm-10">
          <input type="text" class="form-control" name="definer" placeholder="Enter Definer">
        </div>
      </div>
      <div class="form-group">
        <label class="control-label col-sm-2" for="columns">COLUMN NAMES:</label>
        <div class="col-sm-10">
          <input type="text" class="form-control" name="columns" placeholder="Enter Columns">
        </div>
      </div>
      
      <div class="form-group">
        <label class="control-label col-sm-2" for="columns">QUERY:</label>
        <div class="col-sm-10">
          <textarea name='query' class='form-control' id='view_query' style="min-height: 200px;" required></textarea>
        </div>
      </div>
      
      <div class="form-group buttonbar">
        <div class="">
            <button type="reset" class="btn btn-danger">Reset</button>
            <button type="button" class="btn btn-success pull-right" onclick="saveNewView(this)">Submit</button>
        </div>
      </div>
    </form>
</div>
<script>
$(function() {
    
});
function saveNewView() {
    $("#viewCreateForm .buttonbar").addClass("hidden");
    
    //validate structure
    emptyFields = 0;
    for(i=0;i<$("#viewCreateForm input[required],#viewCreateForm textarea[required]").length;i++) {
        if($($("#viewCreateForm input[required],#viewCreateForm textarea[required]")[i]).val().length<=0) {
            emptyFields++;
        }
    }
    if(emptyFields>0) {
        lgksAlert("All required fields are not filled.");
        return;
    }
    //ajax submit
    lx=_service("dbEdit","createView")+"&dkey="+dkey;
    qData = $("#viewCreateForm").serialize();
    processAJAXPostQuery(lx,qData, function(data) {
        if(data==null || data.length<=0) data = "Unknown Error Occured While Creating Table";
        
        if(data=="success") {
            loadTableList('pages');
            lgksAlert("Database View Created Successfully")
        } else {
            lgksAlert(data);
            $("#viewCreateForm .buttonbar").removeClass("hidden");
        }
    });
}
</script>