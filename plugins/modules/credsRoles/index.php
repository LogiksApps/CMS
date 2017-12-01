<?php
if(!defined('ROOT')) exit('No direct script access allowed');

include_once __DIR__."/funcs.php";
if(!isset($_GET['DX'])) {
	return;
}

loadModule("pages");

echo _css("credsRoles");

function pageSidebar() {
  $dataPrivilegesFinal=$_GET['DX'];
  $dataRolesFinal=$_GET['DY'];
  
	// <form role='search'>
	//     <div class='form-group'>
	//       <input type='text' class='form-control' placeholder='Search'>
	//     </div>
	// </form>
  $html="<div id='componentTree' class='componentTree list-group list-group-root well'>";
  $html.="<ul class='list-group'>";
  
  $dx=0;
  foreach($dataPrivilegesFinal as $k=>$p) {
    if($p['id']<=ROLE_PRIME) {
      continue;
    }
		$title=toTitle(_ling($p['name']));
		$html.="<li class='list-group-item' title='Privilege : $title ({$p['id']})' ><a href='#{$p['hash']}' aria-controls='{$p['hash']}' role='tab' data-toggle='tab'>$title <i class='fa fa-chevron-right pull-right'></i></a></li>";
//     if(isset($dataRolesFinal[$p['hash']])) {
      
//       if($dx==0) {
// //         echo "<li role='presentation' title='Privilege : $title ({$p['id']})' class='active'><a href='#{$p['hash']}' aria-controls='{$p['hash']}' role='tab' data-toggle='tab'>$title</a></li>";
//         $html.="<li class='list-group-item' title='Privilege : $title ({$p['id']})' class='active'><a href='#{$p['hash']}' aria-controls='{$p['hash']}' role='tab' data-toggle='tab'>$title <i class='fa fa-chevron-right pull-right'></i></a></li>";
//       } else {
// //         echo "<li role='presentation' title='Privilege : $title ({$p['id']})' ><a href='#{$p['hash']}' aria-controls='{$p['hash']}' role='tab' data-toggle='tab'>$title</a></li>";
//         $html.="<li class='list-group-item' title='Privilege : $title ({$p['id']})' ><a href='#{$p['hash']}' aria-controls='{$p['hash']}' role='tab' data-toggle='tab'>$title <i class='fa fa-chevron-right pull-right'></i></a></li>";
//       }

//       $dx++;
//     }
  }
  
  $html.="</ul>";
  $html.="</div>";
	return $html;
}
function pageContentArea() {
  $dataPrivilegesFinal=$_GET['DX'];
  $dataRolesFinal=$_GET['DY'];
  
  ob_start();
?>
<div class='col-xs-12'>
<div id='roleTabs' class='row'>
  <ul class="nav nav-tabs hidden" role="tablist">
		<?php
			$dx=0;
			foreach($dataPrivilegesFinal as $k=>$p) {
				if($p['id']<=ROLE_PRIME) {
					continue;
				}
				//if(isset($dataRolesFinal[$p['hash']])) {
					$title=toTitle(_ling($p['name']));
					if($dx==0)
						echo "<li role='presentation' title='Privilege : $title ({$p['id']})' class='active'><a href='#{$p['hash']}' aria-controls='{$p['hash']}' role='tab' data-toggle='tab'>$title</a></li>";
					else
						echo "<li role='presentation' title='Privilege : $title ({$p['id']})' ><a href='#{$p['hash']}' aria-controls='{$p['hash']}' role='tab' data-toggle='tab'>$title</a></li>";
					
					$dx++;
				//}
			}
		?>
<!-- 		<li class="pull-right"><input class="form-control" type="search" id="searchModule" placeholder='Search Module' ></li> -->
  </ul>
  <div id='roleTabModel' class="tab-content roleTabModel">
		<?php
			$dx=0;
			foreach($dataPrivilegesFinal as $k=>$p) {
				if($p['id']<=ROLE_PRIME) {
					continue;
				}
				$titleHeader=toTitle(_ling($p['name']));
				if($dx==0)
					echo "<div role='tabpanel' class='tab-pane active' id='{$p['hash']}'>";
				else
					echo "<div role='tabpanel' class='tab-pane' id='{$p['hash']}'>";

				echo "<div class='panel-group' role='tablist' aria-multiselectable='false' id='accordion{$p['hash']}'>";
				echo "<div class='panel-heading panel-heading-bold'>$titleHeader</div>";

				if(isset($dataRolesFinal[$p['hash']])) {
					uksort($dataRolesFinal[$p['hash']],"roleSortModule");
					
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
									echo toTitle(_ling(strtolower($role['category']))." @ "._ling(strtolower($role['activity'])));
									echo " <citie class='datalink' data-type='guid-users' data-value='{$role['guid']}'>[{$role['guid']}]</citie>";
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
				} else {
					echo "<h3 class='text-center'><br><br>Permissions Not Found for the site</h3>";
				}
				echo "</div>";
				echo "</div>";
				$dx++;
			}
		?>
  </div>
</div>
</div>
<?php
  $htmlData=ob_get_contents();
  ob_end_clean();
	return "<div id='componentSpace' class='componentSpace'>$htmlData</div>";
}

$webPath=dirname(getWebPath(__FILE__))."/";

loadModuleLib("cmsEditor","embed");

echo _css("credsRoles");
echo _js(["credsRoles"]);

printPageComponent(false,[
		"toolbar"=>[
			//"loadTextEditor"=>["title"=>"Template","align"=>"right"],
			//"loadSQLEditor"=>["title"=>"Query","align"=>"right"],
			//"loadInfoComponent"=>["title"=>"About","align"=>"right"],
			//"loadPreviewComponent"=>["title"=>"Preview","align"=>"right"],

			["title"=>"Search Roles","type"=>"search","align"=>"right"]
			//"listTemplates"=>["icon"=>"<i class='fa fa-refresh'></i>"],
			//"createTemplate"=>["icon"=>"<i class='fa fa-plus'></i>","tips"=>"Create New"],
			//"openExternal"=>["icon"=>"<i class='fa fa-external-link'></i>","class"=>"onsidebarSelect"],
			//"preview"=>["icon"=>"<i class='fa fa-eye'></i>","class"=>"onsidebarSelect onOnlyOneSelect","tips"=>"Preview Content"],
			//['type'=>"bar"],
			//"rename"=>["icon"=>"<i class='fa fa-terminal'></i>","class"=>"onsidebarSelect onOnlyOneSelect","tips"=>"Rename Content"],
// 			"deleteTemplate"=>["icon"=>"<i class='fa fa-trash'></i>","class"=>"onsidebarSelect"],
		],
		"sidebar"=>"pageSidebar",
		"contentArea"=>"pageContentArea"
	]);

?>
<script>
FORSITE='{$_REQUEST["forsite"]}';
var uniStatus=true;
$(function() {
  $("#componentTree .list-group-item:first-child").addClass("active");
  $("#componentTree a").click(function() {
      href=$(this).attr("href")
      $("#roleTabs li>a[href='"+href+"']").tab("show");
      return true;
    });

	$("input[name=checkAll]","#roleTabModel").each(function() {
		if($(this).closest(".panel.panel-default").find("input[name=roleCheckbox]").length==$(this).closest(".panel.panel-default").find("input[name=roleCheckbox]:checked").length) {
			this.checked=true;
		}
	});

  $("#pgToolbarSearch").on('submit', function (e) {
        e.preventDefault();
        return false;
    });
  $("#pgToolbarSearch input").keyup(function(e) {
    if(e.keyCode==13) return false;
    vs=$("#pgToolbarSearch input").val();
		if(vs==null || vs.length<=0) {
			$(".tab-pane.active .panel").show();
			return;
		}
		$(".tab-pane.active .panel:not([data-module^='"+vs+"'])").hide();
		$(".tab-pane.active .panel[data-module^='"+vs+"']").show();
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
					lgksToast("Updated");
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