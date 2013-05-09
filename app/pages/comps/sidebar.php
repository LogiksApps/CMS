<ul id=sidebarMenu></ul>
<script language=javascript>
$(function() {
	updateSideBar();
});
function updateSideBar() {
	l=getServiceCMD("cmsmenu")+"&forsite="+$("#sitemanager").val()+"&action=loadmenu";
	$("#sidebarMenu").html("<div class='ajaxloading3'></div>");
	$("#sidebarMenu").load(l,function() {
			$("#sidebarMenu li ul a").hover(function() {
					$(this).animate({
								paddingLeft:'35px',
							}, 300 );
				},function() {
					$(this).animate({
								paddingLeft:'20px',
							}, 300 );
				});
			$("#sidebarMenu a").each(function(event) {
					var r=$(this).attr("href");
					if(r!="#" && r!="" && r!=null) {
						$(this).attr("href",r+"&forsite="+$("#sitemanager").val());
					}			
				});
			loadSidebar("#sidebar");
		});
}
</script>
