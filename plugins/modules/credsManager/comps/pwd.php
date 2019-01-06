<?php
if(!defined('ROOT')) exit('No direct script access allowed');

$data=_db(true)->_selectQ(_dbTable("users",true),"*",['md5(id)'=>$slug['refid']])->_GET();

if(count($data)<=0) {
	print_error("User Not Found");return;
}
$data=$data[0];

//printArray($data);

$title="Password Manager";
?>
<style>
	.password-form .fa-eye {
		cursor:pointer;
	}
</style>
<div class="col-xs-12">
	<div class="row">
		<div class="col-sm-12">
			<h1>
				<i class="fa fa-user"></i>
				<span><?=$title?></span>
				<i class='fa fa-times pull-right close-frame'></i>
			</h1>
		</div>
	</div>
	<br/>
	<div class="row">
		<div class="col-sm-12 col-lg-12">
				<form class="form-horizontal form-responsive password-form" style='max-width:350px;margin:auto;border:1px solid #DEDEDE;'>
					<input type='hidden' name='q' value='<?=$slug['refid']?>' />
					<legend style='padding:10px;'><i class='fa fa-user'></i> <?=substr($data['name'],0,30)?></legend>
					<fieldset style='padding:10px;'>
						<!-- Password input-->
						<?php
							if($_SESSION['SESS_PRIVILEGE_ID']!=1) {
						?>
						<div class="form-group">
							<label class="col-md-4 control-label" for="pwCurrPass">OLD Password</label>
							<div class="col-md-8">
								<div class="input-group">
									<div class="input-group-addon"><i class='fa fa-key'></i></div>
									<input name="pwCurrPass" type="password" placeholder="" class="form-control input-md" required="" autocomplete=off>
									<div class="input-group-addon"><i class='fa fa-eye'></i></div>
								</div>
							</div>
						</div>
						<?php
							}	
						?>
						<!-- Password input-->
						<div class="form-group">
							<label class="col-md-4 control-label" for="pwNewPass">New Password</label>
							<div class="col-md-8">
								<div class="input-group">
									<div class="input-group-addon"><i class='fa fa-key'></i></div>
									<input name="pwNewPass" type="password" placeholder="" class="form-control input-md" required="" autocomplete=off>
									<div class="input-group-addon"><i class='fa fa-eye'></i></div>
								</div>
							</div>
						</div>
						
						<!-- Password input-->
						<div class="form-group">
							<label class="col-md-4 control-label" for="pwNewPassRepeat">Retype Password</label>
							<div class="col-md-8">
								<div class="input-group">
									<div class="input-group-addon"><i class='fa fa-key'></i></div>
									<input name="pwNewPassRepeat" type="password" placeholder="" class="form-control input-md" required="" autocomplete=off>
									<div class="input-group-addon"><i class='fa fa-eye'></i></div>
								</div>
							</div>
						</div>

						<div class="form-group">
							<div class="col-md-12">
								<button type='button' class="btn btn-warning pull-left" onclick='pwdGenerate()'>Generate</button>
								<button type='button' class="btn btn-success pull-right" onclick='updatePWD()'>Update</button>
							</div>
						</div>
					</fieldset>
				</form>
		</div>
	</div>
</div>

<script>
$(function() {
	$(".password-form .fa-eye").click(function() {
		if($(this).closest(".input-group").find("input").attr("type")=="password") {
			$(this).closest(".input-group").find("input").attr("type","text");
		} else {
			$(this).closest(".input-group").find("input").attr("type","password");
		}
	});
	$(".close-frame").click(function() {
		parent.closeSidePanel();
	});
	parent.openSidePanel();
});
function updatePWD() {
	if($("input[name=pwCurrPass]",".password-form").length>0 && ($("input[name=pwCurrPass]",".password-form").val()==null || $("input[name=pwCurrPass]",".password-form").val().length<=0)) {
		lgksToast("Old Password field can not be empty !");
		$("input[name=pwCurrPass]",".password-form").css("border-color","red");
		return;
	}
	if($("input[name=pwNewPass]",".password-form").val()==null || $("input[name=pwNewPass]",".password-form").val().length<=0) {
		lgksToast("Password field can not be empty !");
		$("input[name=pwNewPass]",".password-form").css("border-color","red");
		return;
	}
	if($("input[name=pwNewPass]",".password-form").val()!=$("input[name=pwNewPassRepeat]",".password-form").val()) {
		lgksToast("Password fields don't match. Please retype the password properly. !");
		$("input[name=pwNewPassRepeat]",".password-form").css("border-color","red");
		return;
	}
	
	q=[];
	$("input[name]",".password-form").each(function() {
		q.push($(this).attr("name")+"="+$(this).val());
	});
	processAJAXPostQuery(_service("credsManager","pwd"),"&src=users&"+q.join("&"),function(txt) {
				txt=$.parseJSON(txt);
				if(txt.Data=="success") {
					parent.lgksToast("Password update successfull");
					parent.closeSidePanel();
				} else {
					parent.lgksToast(txt.Data);
				}
			});
}
function pwdGenerate() {
	pass=pwd();
	$("input[name=pwNewPass],input[name=pwNewPassRepeat]",".password-form").val(pass);
	$("input[name=pwNewPass]",".password-form").attr("type","text");
}
function pwd(length) {
	if(length==null || length<0) length=8;
  charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789@#$%^&*!()[]{}";
	retVal = "";
	for (var i = 0, n = charset.length; i < length; ++i) {
		retVal += charset.charAt(Math.floor(Math.random() * n));
	}
	return retVal;
}
</script>