<?php
if(!defined('ROOT')) exit('No direct script access allowed');

?>
<div class='col-xs-12' style='height: 25%;padding: 0px;z-index: 10000;'>
	<form>
		<div class='col-xs-9' style='padding: 0px;'>
			<textarea id='queryText' class='form-control' style='width: 100%;height: 100%;resize: none;outline: none;'
				placeholder="Support SQL92 and Logiks JSON object formats" 
				></textarea>
		</div>
		<div class='col-xs-3' style='padding: 3px;'>
			<select id='queryType' class='form-control'>
				<option>SQL</option>
				<option>JSON</option>
			</select>
			<div class='' style='text-align: center;margin-top: 10px;'>
				<button type='button' onclick="runQuery()" class='btn btn-success'><i class='fa fa-flash'></i>RUN</button>
			</div>
		</div>
	</form>
	<hr style='width: 100%;margin-top: 120px;'>
</div>
<div id='resultSetPanel' class='col-xs-12 table-responsive' style='height: 75%;padding: 2px;overflow: auto;'>
	<h5>SQL Processed Results Is Updated Here.</h5>
</div>
<script>
$(function() {
	$("#queryText").on("keyup",function(e) {
		if(e.keyCode==13 && e.ctrlKey) {
			runQuery();
		}
	});

	qry=localStorage.getItem('dbedit.lastquery');
	if(qry!=null && qry.length>1) {
		$("#queryText").val(qry);
	}
});
function runQuery() {
	type=$("#queryType").val();
	qry=$("#queryText").val();

	lx=_service("dbEdit","query")+"&type="+type;
	q="q="+qry;

	saveQueryLocal()

	$("#resultSetPanel").html("<div class='ajaxloading5'></div>");
	processAJAXPostQuery(lx,q,function(txt) {
		$("#resultSetPanel").html(txt);
	});
}
function saveQueryLocal() {
	if($("#queryText").length<=0) return;
	qry=$("#queryText").val();
	localStorage.setItem('dbedit.lastquery',qry);
}
</script>