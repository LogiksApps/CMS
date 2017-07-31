<?php
if(!defined('ROOT')) exit('No direct script access allowed');

$dataPrivilegesFinal=[];
$dataRolesFinal=[];

$sql=_db(true)->_selectQ(_dbTable("privileges",true),"id,site,name,blocked,remarks,md5(concat(id,name)) as privilegehash,md5(concat(id,name)) as hash")
					//->_where(array("blocked"=>"false"))//,"length(hash)"=>[0,">"]
					->_whereOR("site",[SITENAME,'*',CMS_SITENAME])
					->_whereOR("guid",[$_SESSION['SESS_GUID'],'global']);

$r=_dbQuery($sql,true);
if($r) {
	$dataPrivileges=_dbData($r,true);
	_dbFree($r,true);
	
	foreach($dataPrivileges as $role) {
		if(!isset($dataRolesFinal[$role['privilegehash']])) {
			$dataPrivilegesFinal[$role['privilegehash']]=[];
		}

		$dataPrivilegesFinal[$role['privilegehash']]=$role;
	}
	
	
	$sql=_db(true)->_selectQ(_dbTable("rolemodel",true),"id,guid,category,module,activity,privilegehash,allow,role_type")
					->_where(array("site"=>$_GET['forsite']));
	//echo $sql->_SQL();
	
	$r=_dbQuery($sql,true);
	if($r) {
		$dataRoles=_dbData($r,true);
		_dbFree($r,true);
		
		foreach($dataRoles as $role) {
			if(!isset($dataRolesFinal[$role['privilegehash']])) {
				$dataRolesFinal[$role['privilegehash']]=[];
			}
			
			$dataRolesFinal[$role['privilegehash']][strtolower($role['module'])][]=$role;//[$role['category']][$role['activity']]
		}
		
		//printArray($dataRolesFinal);
	}
} else {
	$dataPrivilegesFinal=[];
	$dataRolesFinal=[];
}
// printArray($dataPrivilegesFinal);
// printArray($dataRolesFinal);
echo _css("credsRoles");

if(count($dataPrivilegesFinal)<=0) {
	print_error("Privleges Not Found for the site ".CMS_SITENAME);
	return;
}

function roleSortModule($a,$b) {
	return strcasecmp(strtolower($a), strtolower($b));
}
?>
<div class='col-xs-12'>
<div class='row'>
  <ul class="nav nav-tabs" role="tablist">
		<?php
			$dx=0;
			foreach($dataPrivilegesFinal as $k=>$p) {
				if($p['id']<=ROLE_PRIME) {
					continue;
				}
				if(isset($dataRolesFinal[$p['hash']])) {
					$title=toTitle(_ling($p['name']));
					if($dx==0)
						echo "<li role='presentation' title='Privilege : $title ({$p['id']})' class='active'><a href='#{$p['hash']}' aria-controls='{$p['hash']}' role='tab' data-toggle='tab'>$title</a></li>";
					else
						echo "<li role='presentation' title='Privilege : $title ({$p['id']})' ><a href='#{$p['hash']}' aria-controls='{$p['hash']}' role='tab' data-toggle='tab'>$title</a></li>";
					
					$dx++;
				}
			}
		?>
		<li class="pull-right"><input class="form-control" type="search" id="searchModule" placeholder='Search Module' ></li>
  </ul>
  <div id='roleTabModel' class="tab-content roleTabModel">
		<?php
			$dx=0;
			foreach($dataPrivilegesFinal as $k=>$p) {
				if($p['id']<=ROLE_PRIME) {
					continue;
				}
				
				if(isset($dataRolesFinal[$p['hash']])) {
					uksort($dataRolesFinal[$p['hash']],"roleSortModule");
					if($dx==0)
						echo "<div role='tabpanel' class='tab-pane active' id='{$p['hash']}'>";
					else
						echo "<div role='tabpanel' class='tab-pane' id='{$p['hash']}'>";
					
					echo "<div class='panel-group' role='tablist' aria-multiselectable='false' id='accordion{$p['hash']}'>";
					foreach($dataRolesFinal[$p['hash']] as $modName=>$modules) {
						$modHash=md5($modName.$p['hash']);
						echo "<div class='panel panel-default' data-module='".strtolower($modName)."' >";
						echo "<div class='panel-heading' role='tab' id='{$modHash}'>";
							echo "<h4 class='panel-title'>";
								//echo "<input class='pull-left' type='checkbox' name='checkAll' />";
								echo "<a class='accordion-toggle' role='button' data-toggle='collapse' data-parent='#accordion{$p['hash']}' href='#collapse{$modHash}' aria-expanded='true' aria-controls='collapse{$modHash}'>";
								echo toTitle(_ling($modName));
								echo "</a>";
							echo "</h4>";
						echo "</div>";
						echo "<div id='collapse{$modHash}' class='panel-collapse collapse' role='tabpanel' aria-labelledby='{$modHash}'>";
							echo "<div class='panel-body'>";
								echo "<ul class='list-group'>";
								foreach($modules as $role) {
									$roleHash=md5($role['id'].$role['privilegehash']);
									echo "<li class='list-group-item' guid='{$role['guid']}'><label>";
									echo _ling(str_replace("_"," ",strtolower($role['activity'])))." <citie class='datalink' data-type='guid-users' data-value='{$role['guid']}'>[{$role['guid']}]</citie>";
									if($role['allow']===true || $role['allow']=="true") {
										echo "<input class='pull-right' type='checkbox' name='roleCheckbox' data-hash='{$roleHash}' checked />";// data-x='".json_encode($p)."'
									} else {
										echo "<input class='pull-right' type='checkbox' name='roleCheckbox' data-hash='{$roleHash}' />";
									}
									echo "</label></li>";
								}
								echo "</ul>";
							echo "</div>";
						echo "</div>";
						echo "</div>";
					}
					echo "</div>";
					echo "</div>";
					$dx++;
				}
			}
		?>
  </div>
</div>
</div>
<script>
var uniStatus=true;
$(function() {
	$("input[name=checkAll]","#roleTabModel").each(function() {
		if($(this).closest(".panel.panel-default").find("input[name=roleCheckbox]").length==$(this).closest(".panel.panel-default").find("input[name=roleCheckbox]:checked").length) {
			this.checked=true;
		}
	});
	$("#searchModule").keyup(function(e) {
		if($("#searchModule").val()==null || $("#searchModule").val().length<=0) {
			$(".tab-pane.active .panel").show();
			return;
		}
		$(".tab-pane.active .panel:not([data-module^='"+$("#searchModule").val()+"'])").hide();
		$(".tab-pane.active .panel[data-module^='"+$("#searchModule").val()+"']").show();
	});
	$("#roleTabModel").delegate("input[name=checkAll]","change",function(e) {
			uniStatus=this.checked;
			$(this).closest(".panel.panel-default").find("input[name=roleCheckbox]").each(function() {
				roleHash=$(this).data("hash");
				this.checked=uniStatus;
				processAJAXPostQuery(_service("credsRoles","save"),roleHash+"="+uniStatus,function(ans) {
					try {
						json=$.parseJSON(ans);
						if(json.Data=="success") {

						} else {
							lgksToast("Sorry, could not update the role.");
						}
					} catch($e) {
						lgksToast("Sorry, could not update the role.");
					}
				});
			});
		});
	$("#roleTabModel").delegate("input[name=roleCheckbox]","change",function(e) {
		roleHash=$(this).data("hash");
		status=$(this).is(":checked");
		processAJAXPostQuery(_service("credsRoles","save"),roleHash+"="+status,function(ans) {
			try {
				json=$.parseJSON(ans);
				if(json.Data=="success") {
					
				} else {
					lgksToast("Sorry, could not update the role.");
				}
			} catch($e) {
				lgksToast("Sorry, could not update the role.");
			}
		});
	});
});
</script>