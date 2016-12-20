<?php
if(!defined('ROOT')) exit('No direct script access allowed');

function siteList() {
	$sites=_session("siteList");
	$html="<option value='*'>All Sites</option>";
	foreach ($sites as $key => $value) {
		$html.="<option value='{$key}'>{$value['title']}</option>";
	}
	return $html;
}
function userList() {
	$html="";$data=[];
	if($_SESSION['SESS_PRIVILEGE_ID']==1) {
		$data=_db(true)->_selectq(_dbTable("users",true),"userid,name")->_orderby("name")->_GET();
	} else {
		$data=_db(true)->_selectq(_dbTable("users",true),"userid,name",['guid'=>$_SESSION['SESS_GUID']])->_orderby("name")->_GET();
	}
	foreach ($data as $user) {
		$html.="<option value='{$user['userid']}'>{$user['name']}</option>";
	}
	return $html;
}
function privilegeList() {
	$html="";$data=[];
	if($_SESSION['SESS_PRIVILEGE_ID']==1) {
		$data=_db(true)->_selectq(_dbTable("privileges",true),"id,name")->_orderby("name")->_GET();
	} else {
		$data=_db(true)->_selectq(_dbTable("privileges",true),"id,name",['guid'=>$_SESSION['SESS_GUID']])->_orderby("name")->_GET();
	}
	foreach ($data as $user) {
		$html.="<option value='{$user['id']}'>{$user['name']}</option>";
	}
	return $html;
}
function accessList() {
	$html="";$data=[];
	if($_SESSION['SESS_PRIVILEGE_ID']==1) {
		$data=_db(true)->_selectq(_dbTable("access",true),"id,name")->_orderby("name")->_GET();
	} else {
		$data=_db(true)->_selectq(_dbTable("access",true),"id,name",['guid'=>$_SESSION['SESS_GUID']])->_orderby("name")->_GET();
	}
	foreach ($data as $user) {
		$html.="<option value='{$user['id']}'>{$user['name']}</option>";
	}
	return $html;
}

function groupList() {
	$html="";$data=[];
	if($_SESSION['SESS_PRIVILEGE_ID']==1) {
		$data=_db(true)->_selectq(_dbTable("users_group",true),"id,group_name as name, guid")->_orderby("guid,group_name")->_GET();
	} else {
		$data=_db(true)->_selectq(_dbTable("users_group",true),"id,group_name as name",['guid'=>$_SESSION['SESS_GUID']])->_orderby("guid,group_name")->_GET();
	}
	
	foreach ($data as $user) {
		if(isset($user['guid'])) {
			$html.="<option value='{$user['id']}'>{$user['name']} [{$user['guid']}]</option>";
		} else {
			$html.="<option value='{$user['id']}'>{$user['name']}</option>";
		}
	}
	return $html;
}

?>
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
        	<?php
        		printForm($mode,dirname(__DIR__)."/forms/{$form}.json",true,$where);
        	?>
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