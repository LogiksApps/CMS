<?php
if(!defined('ROOT')) exit('No direct script access allowed');


if(!isset($_GET['src'])) {
	echo "<h3 align=center>Source Not Defined</h3>";
	return false;
}

$src=explode("/", $_GET['src']);
if(count($src)==0) {
	$src[1]=$src[0];
	$src[0]="tables";
}

$tabsArr=[
		"structure"
	];
switch ($src[0]) {
	case 'tables':
		$tabsArr=[
				"structure",
				"columns",
				"browse",
				"insert",
                "create",
			];
		break;

	case 'views':
		$tabsArr=[
				"structure",
				"columns",
				"browse",
                "create",
			];
		break;

    case "functions":
    case "procedures":
        $tabsArr = [
            "structure",
            "create",
          ];
        break;
    
    case "events":
        $tabsArr = [
            "create",
          ];
        break;
    
	default:
		echo "<h5 align=center>Sorry, viewing structure for type <b>{$src[0]}</b> is not supported yet</h5>";
		return;
		break;
}

// printArray($tabsArr);
//$dbKey
?>
<ul id='dbTableNav' class="nav nav-tabs tabs-up">
	<?php
		foreach ($tabsArr as $tab) {
			$ttl=toTitle(_ling($tab));
			echo "<li><a href='#{$tab}' class='span'>{$ttl}</a></li>";
		}
	?>
</ul>
<div id="dataContent">
	
</div>
<script>
var TABARR = <?=json_encode($tabsArr)?>;
$(function() {
	$("#dbTableNav li>a").click(function() {
		comptype=$(this).attr('href');
		loadDataContent(comptype.substr(1));
	});
	if(currentDBQueryPanel==null) currentDBQueryPanel="<?=$tabsArr[0]?>";
	if(TABARR.indexOf(currentDBQueryPanel)<0) currentDBQueryPanel = TABARR[0];
	
	loadDataContent(currentDBQueryPanel);
});
function loadDataContent(panel,q) {
	if(q==null) q='';

	$("#dataContent").html("<div class='ajaxloading5'></div>");

	lx=_service("dbEdit","dbTablePanel")+"&dkey="+dkey+"&panel="+panel+"&src=<?=$_GET['src']?>"+q;
	$("#dataContent").load(lx);

	if(panel!="edit") {
		currentDBQueryPanel=panel;
	}

	$("#dbTableNav li.active").removeClass("active");
	$("#dbTableNav li a[href='#"+currentDBQueryPanel+"']").closest("li").addClass("active");
}
</script>