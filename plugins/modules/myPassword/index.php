<?php
if(!defined('ROOT')) exit('No direct script access allowed');

echo _js(["myPassword","md5"]);
?>
<style>
.progress .bar {
	width: 0%;
	height: 100%;
	background: maroon;
}
</style>
<div class="col-xs-12">
	<div class="row">
		<div class="col-sm-12">
			<h1>
				<i class="fa fa-shield"></i>
				<span>My Password</span>
			</h1>
		</div>
	</div>
	<br/>
	<div class="row">
		<div class="panel panel-default col-sm-6 col-lg-6  col-sm-offset-3  col-lg-offset-3" style='padding:20px;'>
        	<form method="post" id="passwordForm">
        	  <div id='errorMsg' class="alert alert-danger collapse" role="alert">
				  <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
				  <span class="sr-only">Error:</span>
				  <span class='msg'>Enter a valid email address</span>
			  </div>
			  <div id='generatedPassword' class="form-group collapse">
			    <label for="pwd2">GeneratedPassword</label>
			    <div class="input-group">
				  <input type="text" class="form-control" >
				  <span class="input-group-btn">
			        <button class="btn btn-default" type="button" onclick="copyPWD()">Copy!</button>
			      </span>
				</div>
			  </div>
			  <div class="form-group pwdbox">
			    <label for="pwd1">New Password</label>
			    <div class="input-group">
				  <input type="password" class="form-control" name="pwd1" placeholder="Type your Password here.">
				  <span class="input-group-addon" id="basic-addon1"><span class="fa fa-key"></span></span>
				</div>
			  </div>
			  <div class="form-group pwdbox">
			    <label for="pwd2">Retype of Password</label>
			    <div class="input-group">
				  <input type="password" class="form-control" name="pwd2" placeholder="Please retype your Password">
				  <span class="input-group-addon" id="basic-addon1"><span class="fa fa-key"></span></span>
				</div>
			  </div>
			  <div class="form-group pwdbox">
			  	<label>Password Meter</label>
			  	<div class="progress"><div class="bar"></div></div>
			  </div>
			  <hr class='pwdbox'>
			  <div class="form-group text-center pwdbox">
			  	<button type="button" class="btn btn-warning" onclick="generatePwd()">Generate</button>
			  	<button type="submit" class="btn btn-default">Submit</button>
			  </div>
			</form>	
        </div>
    </div>
</div>
<script>
$(function() {
	$("#passwordForm input[name=pwd1]").on("keyup",function() {
		$("#errorMsg").hide("fade");
		pwd1=$("#passwordForm").find("input[name=pwd1]").val();
		updateProgressBar(pwd1);
	});

	$("#passwordForm input[name=pwd2]").on("keyup",function() {
		$("#errorMsg").hide("fade");
		pwd1=$("#passwordForm").find("input[name=pwd1]").val();
		pwd2=$("#passwordForm").find("input[name=pwd2]").val();

		if(pwd1==pwd2) {
			updateProgressBar(pwd1);
		} else {
			updateProgressBar(null);
		}
	});

	$("#passwordForm").submit(function() {
		$("#errorMsg").removeClass("alert-success");
		$("#errorMsg").addClass("alert-danger");

		pwd1=$("#passwordForm").find("input[name=pwd1]").val();
		pwd2=$("#passwordForm").find("input[name=pwd2]").val();
		if(pwd1==null || pwd1.length<=0) {
			$("#errorMsg .msg").html("Password Can Not Be Empty");
			$("#errorMsg").show("fade");
			return false;
		}
		if(pwd1!=pwd2) {
			$("#errorMsg .msg").html("Passwords in both the fields must be same.");
			$("#errorMsg").show("fade");
			return false;
		}
		score = scorePassword(pwd1);
		if(score<30) {
			$("#errorMsg .msg").html("Weak password can not be used.");
			$("#errorMsg").show("fade");
			return false;
		}

		pwd=md5(pwd1);
		processAJAXPostQuery(_service("myPassword","update"),"pwd="+pwd,function(txt) {
			if(txt.Data=="success") {
				$("#errorMsg").removeClass("alert-danger");
				$("#errorMsg").addClass("alert-success");
				
				$("#errorMsg .msg").html("Successfully updated your password.");
				$("#errorMsg").show("fade");

				updateProgressBar("");
				$("#passwordForm")[0].reset();
			} else {
				lgksAlert(txt.Data);
			}
		},"json");

		return false;
	});
});
function generatePwd() {
	pwd=generatePassword(8);
	$("#generatedPassword input").val(pwd);
	$("#generatedPassword").slideDown();
	$(".pwdbox").hide();
}
function copyPWD() {
	pwd=$("#generatedPassword input").val();
	$("#passwordForm").find("input[name=pwd1]").val(pwd);
	$("#passwordForm").find("input[name=pwd2]").val(pwd);
	updateProgressBar(pwd);

	$("#generatedPassword").fadeOut();
	$(".pwdbox").fadeIn();
}
</script>