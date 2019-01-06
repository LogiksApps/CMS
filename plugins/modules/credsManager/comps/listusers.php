<?php
if(!defined('ROOT')) exit('No direct script access allowed');

$data=[];
$cols="*";
switch($slug['panel']) {
	case "privileges":
		$data=_db(true)->_selectQ(_dbTable("users",true),$cols,['md5(privilegeid)'=>$slug['refid']])->_GET();
		break;
	case "access":
		$data=_db(true)->_selectQ(_dbTable("users",true),$cols,['md5(accessid)'=>$slug['refid']])->_GET();
		break;
	case 'group':case 'groups':
		$data=_db(true)->_selectQ(_dbTable("users",true),$cols,['md5(groupid)'=>$slug['refid']])->_GET();
		break;
	default:
		print_error("User Listing Not Supported");return;
		break;
}
// printArray($data);

$title="List Of Users";
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
				<table class="table table-condensed table-hover" style="margin-top: -30px;">
					<thead>
						<tr>
							<th>#</th>
							<th>Userid</th>
							<th>User Name</th>
							<th>Gender</th>
							<th>Mobile</th>
						</tr>
					</thead>
					<tbody>
						<?php
								if(count($data)>0) {
									foreach($data as $i=>$u) {
										$ii=$i+1;
										echo "<tr scrope='row'><th>{$ii}</th><td>{$u['userid']}</td><td>{$u['name']}</td><td>{$u['gender']}</td>".
													"<td>{$u['mobile']}</td></tr>";
									}
								} else {
									echo "<tr scrope='row'><th colspan=10 style='text-align: center;'>No users listed under this section.</th></tr>";
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