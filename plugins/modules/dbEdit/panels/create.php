<?php
if(!defined('ROOT')) exit('No direct script access allowed');

//create_table
?>
<style>
.card {
    height: 100px;
    cursor: pointer;
    background: whitesmoke;
}
.card span {
    font-size: 22px;
    padding-top: 4px;
    display: inline-block;
}
</style>
<div class="container">
    <div id='create_panel' class="row">
        <br><br>
        <div class='col-md-4'>
            <div class="card panel panel-default" data-type='table'>
                  <div class="card-body panel-body text-center">
                        <span><i class='fa fa-table'></i> Create Table</span>
                  </div>
            </div>
        </div>
        <div class='col-md-4'>
            <div class="card panel panel-default" data-type='view'>
                  <div class="card-body panel-body text-center">
                        <span><i class='fa fa-file-contract'></i> Create View</span>
                  </div>
            </div>
        </div>
        <div class='col-md-4'>
            <div class="card panel panel-default" data-type='procedure'>
                  <div class="card-body panel-body text-center">
                        <span><i class='fa fa-file-code'></i> Create Procedure</span>
                  </div>
            </div>
        </div>
        <div class='col-md-4'>
            <div class="card panel panel-default" data-type='function'>
                  <div class="card-body panel-body text-center">
                        <span><i class='fa fa-file-alt'></i> Create Function</span>
                  </div>
            </div>
        </div>
        <div class='col-md-4'>
            <div class="card panel panel-default" data-type='trigger'>
                  <div class="card-body panel-body text-center">
                        <span><i class='fa fa-tasks'></i> Create Triggers</span>
                  </div>
            </div>
        </div>
        <div class='col-md-4'>
            <div class="card panel panel-default" data-type='event'>
                  <div class="card-body panel-body text-center">
                        <span><i class='fa fa-business-time'></i> Create Events</span>
                  </div>
            </div>
        </div>
    </div>
</div>
<script>
$(function() {
    $("#create_panel .card").click(createObject);
});
function createObject() {
    var typ = $(this).data("type");
	lx=_service("dbEdit","panel")+"&dkey="+dkey+"&panel=create_"+typ;
	$("#pgcontent").load(lx);
}
</script>