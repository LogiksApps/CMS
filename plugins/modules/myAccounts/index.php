<?php
if(!defined('ROOT')) exit('No direct script access allowed');

//unset($_SESSION["USERINFO"]);

loadHelpers("countries");

$user=getUserInfo($_SESSION['SESS_USER_ID'],true);

loadModuleLib("forms","api");
?>
<div class="col-xs-12">
	<div class="row">
		<div class="col-sm-12">
			<h1>
				<i class="fa fa-user"></i>
				<span>My Profile</span>
			</h1>
		</div>
	</div>
	<br/>
	<div class="row">
		<div class="col-sm-2 col-lg-2">
          <div class="box">
            <div class="box-content img-content">
              <img class="img-responsive" src="<?=$user['avatarlink']?>">
            </div>
          </div>
        </div>
        <div class="col-sm-10 col-lg-10">
        	<?php
        		printForm('update',__DIR__."/form.json",true,['userid'=>$user['userid']]);
        	?>
        </div>
    </div>
</div>