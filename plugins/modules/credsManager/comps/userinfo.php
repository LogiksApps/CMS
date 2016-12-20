<?php
if(!defined('ROOT')) exit('No direct script access allowed');

$data=_db(true)->_selectQ(_dbTable("users",true),"*",['md5(id)'=>$slug['refid']])->_GET();

if(count($data)<=0) {
	print_error("User Not Found");return;
}
$data=$data[0];


$accessData=_db(true)->_selectQ(_dbTable("access",true),"id,sites,name as access_name")->_where([
		"id"=>$data['accessid'],
		"blocked"=>"false"
	])->_get();
if(count($accessData)>0) {
	$data['access_id']=$accessData[0]['id'];
	$data['access_key']=$accessData[0]['access_name'];
	$data['access_to']=$accessData[0]['sites'];
}

$privilegeData=_db(true)->_selectQ(_dbTable("privileges",true),"id,name as privilege_name")->_where([
		"id"=>$data['privilegeid'],
		"blocked"=>"false"
	])->_get();
if(count($privilegeData)>0) {
	$data['privilege_id']=$privilegeData[0]['id'];
	$data['privilege_name']=$privilegeData[0]['privilege_name'];
}

$groupData=_db(true)->_selectQ(_dbTable("users_group",true),"id,group_name,group_manager,group_descs")->_where([
		"id"=>$data['groupid']
	])->_get();
if(count($groupData)>0) {
	$data['group_id']=$groupData[0]['id'];
	$data['group_name']=$groupData[0]['group_name'];
	$data['group_manager']=$groupData[0]['group_manager'];
}


$noShow=["id","pwd","pwd_salt","avatar","avatar_type","accessid","privilegeid","groupid","","vcode","mauth"];
$colType=[
	"user_avatar"=>"URL",
	"dob"=>"DATE",
	"created_on"=>"DATE",
	"edited_on"=>"DATE",
];
//printArray($data);

$avatar=getUserAvatar($data);

$data['user_avatar']=$avatar;

ksort($data);

$title=$data['name'];
?>
<style>
.pglink {
	display: block;
	max-width: 70%;    width: 400px;
	height: 18px;
	overflow: hidden;
}
.thumbnail.userthumb {
	width: 40px;
	height: 40px;
	float: left;
	margin: 0px;
	margin-right: 10px;
	border: 0px;
	padding: 0px;
	background: transparent;
}
</style>
<div class="col-xs-12">
	<div class="row">
		<div class="col-sm-12">
			<h1>
				<div class='thumbnail userthumb'>
					<img src='<?=$data['user_avatar']?>' class='' />
				</div>
				<span><?=$title?></span>
				<i class='fa fa-times pull-right close-frame'></i>
			</h1>
		</div>
	</div>
	<br/>
	<div class="row">
		<div class="col-sm-12 col-lg-12">
				<table class="table table-condensed" style="margin-top: -30px;">
         	<tbody>
            <?php
                if(count($data)>0) {
                  foreach($data as $key=>$info) {
										if(in_array($key,$noShow)) continue;
										
										$ttl=toTitle($key);
										
										if(isset($colType[$key])) {
											switch(strtolower($colType[$key])) {
												case "url":
													$info="<a class='pglink' href='{$info}' target=_blank>{$info}</a>";
													break;
												case "date":
													$info=_pDate($info);
													break;
											}
										}
										
                    echo "<tr scrope='row' data-key='{$key}'><th>{$ttl}</th><td>{$info}</td></tr>";
                  }
                } else {
                  echo "<tr scrope='row'><th colspan=10 style='text-align: center;'>No user details found.</th></tr>";
                }
            ?>
          </tbody>
        </table>
		</div>
	</div>
</div>







<script>
$(function() {
	$(".close-frame").click(function() {
		parent.closeSidePanel();
	});
	parent.openSidePanel();
});
</script>