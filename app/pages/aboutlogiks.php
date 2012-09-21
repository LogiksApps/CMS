<?php
	if (!defined('ROOT')) exit('No direct script access allowed');
?>
<style>
html,body {
	overflow:hidden;
}
.page {
	height:400px;
	overflow:hidden;
	margin:0px;
	padding:0px;
}
.txtholder {
	height:390px;overflow:auto;margin-left:5px;
}
.listholder {
	height:370px;overflow:auto;
}
.page.ui-tabs-panel.ui-widget-content {
	margin:0px;
	padding:5px;
}
</style>
<div class='tabs ui-widget-content ui-corner-all' style='width:700px;height:400px;margin:auto;margin-top:100px;'>
	<ul>
		<li><a href='services/?scmd=aboutlgks&abt=aboutsite'>About</a></li>
		<li><a href='services/?scmd=aboutlgks&abt=about'>Logiks</a></li>
		<li><a href='services/?scmd=aboutlgks&abt=techspecs'>TechSpecs</a></li>
		<li><a href='services/?scmd=aboutlgks&abt=license'>Licenses</a></li>
		<li><a href='services/?scmd=aboutlgks&abt=marketpolicy'>AppMarket Policies</a></li>		
	</ul>	
</div>
<script language=javascript>
$(function() {
	$("button").button();
	$(".tabs").tabs({
			spinner: 'Loading ...',
			crossDomain:true,
			cache:true,
			panelTemplate:"<div class='page'></div>"
		});
});
</script>
