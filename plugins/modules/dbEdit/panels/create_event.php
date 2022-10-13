<?php
if(!defined('ROOT')) exit('No direct script access allowed');

$sql = "SHOW variables WHERE variable_name ='event_scheduler'";
$dataInfoEventSchedullerEnabled = _db($_ENV['DBKEY'])->_raw($sql)->_GET();
if($dataInfoEventSchedullerEnabled) {
    $dataInfoEventSchedullerEnabled = $dataInfoEventSchedullerEnabled[0]['Value'];
} else {
    $dataInfoEventSchedullerEnabled = "NA";
}
$eventSchedullerStatusMsg = "";
$eventSchedullerStatusIcon = "";
$eventSchedullerStatusClass = "";
if($dataInfoEventSchedullerEnabled=="OFF") {
    $eventSchedullerStatusMsg = "SCHEDULLER_OFF";
    $eventSchedullerStatusIcon = "fa-times";
    $eventSchedullerStatusClass = "danger";
} elseif($dataInfoEventSchedullerEnabled=="NA") {
    $eventSchedullerStatusMsg = "SCHEDULLER_UNDETERMINABLE";
    $eventSchedullerStatusIcon = "fa-ban";
    $eventSchedullerStatusClass = "warning";
} else {
    $eventSchedullerStatusMsg = "SCHEDULLER_ON";
    $eventSchedullerStatusIcon = "fa-check";
    $eventSchedullerStatusClass = "success";
}
?>
<div class='col-xx-12' style='max-width: 1200px;width: 80%;margin: auto;margin-top: 20px;border: 1px solid #AAA;padding: 10px;'>
    <h4>Create Event <label class='event_scheduller_status pull-right label label-<?=$eventSchedullerStatusClass?>'><i class='fa <?=$eventSchedullerStatusIcon?>'></i> <?=$eventSchedullerStatusMsg?></label></h4>
    <hr>
    <form id='createEventForm' class="form-horizontal">
        <div class="form-group">
            <label class="control-label col-sm-3" for="title">Event Name:</label>
            <div class="col-sm-9">
              <input type="text" class="form-control" name="title" placeholder="Enter Event Name" required>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-sm-3" for="title">Event Status:</label>
            <div class="col-sm-9">
                <select name='event_status' class='form-control'>
                  <option value="ENABLE">ENABLED</option>
                  <option value="DISABLE">DISABLED</option>
                  <option value="DISABLE ON SLAVE">SLAVESIDE_DISABLED</option>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-sm-3" for="title">Event Type:</label>
            <div class="col-sm-9">
                <select name='event_type' id='event_type' class='form-control'>
                    <option>ONE TIME</option>
                    <option>RECURRING</option>
                </select>
            </div>
        </div>
        <div id='event_type_fields'>
            <div class="form-group">
                <label class="control-label col-sm-3" for="title">Event Time:</label>
                <div class="col-sm-9">
                    <input type="datetime-local" class="form-control" name="event_time" placeholder="Enter Event Time" required>
                </div>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-sm-3" for="columns">Definition:</label>
            <div class="col-sm-9">
                <textarea name='definition' class='form-control' id='definition' style="min-height: 100px;" required placeholder="SQL Statement"></textarea>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-sm-3" for="title">On completion preserve:</label>
            <div class="col-sm-9">
                <select name='event_preserve' class='form-control'>
                  <option value="PRESERVE">PRESERVE</option>
                  <option value="NOT PRESERVE">NOT PRESERVE</option>
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
    <citie>* Please contact server admin to switch on/off the Event Scheduller</citie>
</div>
<div id='event_type_fields_holder' style='display: none !important;'>
    <div class='event_type_recurring'>
        <div class="form-group">
            <label class="control-label col-sm-3" for="title">Execute Every:</label>
            <div class="col-sm-4">
                <input type="int" class="form-control" name="event_period_value" required value="1">
            </div>
            <div class="col-sm-5">
                <select name='event_period' class='form-control'>
                  <option>YEAR</option><option>QUARTER</option><option>MONTH</option><option>DAY</option><option selected="selected">HOUR</option><option>MINUTE</option><option>WEEK</option><option>SECOND</option><option>YEAR_MONTH</option><option>DAY_HOUR</option><option>DAY_MINUTE</option><option>DAY_SECOND</option><option>HOUR_MINUTE</option><option>HOUR_SECOND</option><option>MINUTE_SECOND</option>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-sm-3" for="title">Event Start:</label>
            <div class="col-sm-9">
                <input type="datetime-local" class="form-control" name="event_start" placeholder="Enter Event Start Time" required>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-sm-3" for="title">Event End:</label>
            <div class="col-sm-9">
                <input type="datetime-local" class="form-control" name="event_end" placeholder="Enter Event End Time">
            </div>
        </div>
    </div>
    <div class='event_type_onetime'>
        <div class="form-group">
            <label class="control-label col-sm-3" for="title">Event Time:</label>
            <div class="col-sm-9">
                <input type="datetime-local" class="form-control" name="event_time" placeholder="Enter Event Time" required>
            </div>
        </div>
    </div>
</div>
<script>
$(function() {
    $("button.preview").click(function() {
        if($("#createEventForm").valid()) {
			q=$("#createEventForm").serialize();
			lx=_service("dbEdit","createEvent")+"&preview=true&dkey="+dkey+"&src=<?=$_GET['src']?>";
			processAJAXPostQuery(lx,q,function(txt) {
			    lgksAlert(txt);
			});
		}
    });
    $("#event_type").change(function() {
        if($(this).val()=="ONE TIME") {
            $("#event_type_fields").html($("#event_type_fields_holder .event_type_onetime").html());
        } else if($(this).val()=="RECURRING") {
            $("#event_type_fields").html($("#event_type_fields_holder .event_type_recurring").html());
        }
    });
	$("#createEventForm").submit(function() {
		if($("#createEventForm").valid()) {
			q=$("#createEventForm").serialize();
			lx=_service("dbEdit","createEvent")+"&dkey="+dkey+"&src=<?=$_GET['src']?>";
			processAJAXPostQuery(lx,q,function(txt) {
			    if(txt=="success") {
					$("#createEventForm")[0].reset();
					lgksToast("Event Created successfully");
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