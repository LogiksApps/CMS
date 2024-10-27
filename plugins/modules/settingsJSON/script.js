var currentSRC=null;
$(function() {
	pgDb();
});

function pgRefresh() {
	//window.document.location.reload();
	loadJSONConfig(currentSRC);
}

function pgDb() {
	$("#pgtoolbar .navbar-right li.active").removeClass('active');
	$($("#pgtoolbar .navbar-right>li")[0]).addClass("active");

	loadJSONConfig('DB');
}
function pgFs() {
	$("#pgtoolbar .navbar-right li.active").removeClass('active');
	$($("#pgtoolbar .navbar-right>li")[3]).addClass("active");

	loadJSONConfig('FS');
}
function pgLog() {
	$("#pgtoolbar .navbar-right li.active").removeClass('active');
	$($("#pgtoolbar .navbar-right>li")[4]).addClass("active");

	loadJSONConfig('LOG');
}
function pgMsg() {
	$("#pgtoolbar .navbar-right li.active").removeClass('active');
	$($("#pgtoolbar .navbar-right>li")[2]).addClass("active");

	loadJSONConfig('MESSAGE');
}
function pgCache() {
	$("#pgtoolbar .navbar-right li.active").removeClass('active');
	$($("#pgtoolbar .navbar-right>li")[1]).addClass("active");

	loadJSONConfig('CACHE');
}



function loadJSONConfig(src) {
	currentSRC=src;
	$("#pgworkspace").html("<div class='ajaxloading ajaxloading5'></div>");
	$("#pgworkspace").load(_service("settingsJSON","fetchPanel","html")+"&src="+currentSRC,function() {
		$("#pgworkspace .card table.table").each(function() {$(this).find("tr:gt(5)").hide();});
		
		if($("#pgworkspace .card").length<=0) {
			$("#pgworkspace").html("<h2 align=center>Sorry, no configuration defined for this appsite.</h2>");
		}
	});
}
