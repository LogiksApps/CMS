
var currentType="installed";
$(function() {
	$("#pgtoolbar .nav.navbar-left").append("<li style='margin-top: 3px;width: 170px;'><select class='form-control' id='typeDropdown'></select></li>");
	$("#pgtoolbar .nav.navbar-left").append("<li class='categoryDropown hidden' style='margin-top: 3px;width: 170px;margin-left:10px;'><select class='form-control' id='categoryDropdown'></select></li>");
	
	$("#typeDropdown").append("<option value='modules'>Modules</option><option value='widgets'>Widgets</option><option value='vendors'>Vendors</option><option value='packages'>Packages</option>");
	$("#typeDropdown").change(listPackages);
	$("#categoryDropdown").change(listPackages);
	
	$("#packageTable").delegate(".cmdAction[cmd]","click",function(e) {
		cmd=$(this).attr("cmd");
		packid=$(this).attr("packid");
		
		switch(cmd) {
			case "configurePlugin":
				
				break;
			case "editPlugin":
				
				break;
			case "infoPlugin":
				processAJAXPostQuery(_service("packMan","packinfo"),"packid="+packid,function(ans) {
					lgksMsg(ans);
				});
				break;
			default:
				lgksToast("Plugin Action Not Defined.");
		}
	});
	$('#pgToolbarSearch').submit(function() {
		return false;
	});
	$("#pgToolbarSearch").keyup(function(e) {
			if(e.keyCode==13) {
				searchPackages($("#pgToolbarSearch input").val());
				return false;
			}
		});
	$("#categoryDropdown").load(_service("packMan","categories","select")+"&src="+currentType+"&type="+$("#typeDropdown").val(),function() {
		listPackages();
	});
});
function listPackages() {
	$("#packageTable").html("<tr><td colspan=20><div class='ajaxloading ajaxloading5'>Fetching Packages</div></td></tr>");
	$("#categoryDropdown").show();
	
	processAJAXQuery(_service("packMan","getlist")+"&src="+currentType+"&type="+$("#typeDropdown").val(),function(dataJSON) {
		tmplCode = Handlebars.compile($("#packageRowTemplate").html());
		html=tmplCode({"data":dataJSON.Data});
		$("#packageTable").html(html);
		
		if($("#packageTable tr").length<=0) {
			$("#packageTable").html("<tr><td colspan=20><h3 align=center>No packages found</h3></td></tr>");
		} else {
			$("#packageTable tr").each(function() {
				$(this).find("th").html($(this).index()+1);
			});
		}
	},"json");
}
function searchPackages(txt) {
	if(txt==null) {
		return listPackages();
	}
	$("#packageTable").html("<tr><td colspan=20><div class='ajaxloading ajaxloading5'>Searching Packages</div></td></tr>");
	
	processAJAXQuery(_service("packMan","getlist")+"&src="+currentType+"&type="+$("#typeDropdown").val()+"&q="+txt,function(dataJSON) {
		tmplCode = Handlebars.compile($("#packageRowTemplate").html());
		html=tmplCode({"data":dataJSON.Data});
		$("#packageTable").html(html);
		
		if($("#packageTable tr").length<=0) {
			$("#packageTable").html("<tr><td colspan=20><h3 align=center>No packages found</h3></td></tr>");
		} else {
			$("#packageTable tr").each(function() {
				$(this).find("th").html($(this).index()+1);
			});
		}
	},"json");
}
function loadInstalled() {
	$("#packageTable").html("<tr><td colspan=20><div class='ajaxloading ajaxloading5'>Indexing Packages</div></td></tr>");
	
	$("#pgtoolbar .navbar-right li").removeClass("active");
	$("#pgtoolbar .navbar-right a#toolbtn_loadInstalled").parent().addClass("active");
	$("#pgtoolbar li.categoryDropown").addClass("hidden");
	
	currentType="installed";
	
	$("#categoryDropdown").load(_service("packMan","categories","select")+"&src="+currentType+"&type="+$("#typeDropdown").val(), function() {
		listPackages();
	});
}
function loadRepo() {
	$("#packageTable").html("<tr><td colspan=20><div class='ajaxloading ajaxloading5'>Indexing Packages</div></td></tr>");
	
	$("#pgtoolbar .navbar-right li").removeClass("active");
	$("#pgtoolbar .navbar-right a#toolbtn_loadRepo").parent().addClass("active");
	$("#pgtoolbar li.categoryDropown").removeClass("hidden");
	
	currentType="repos";
	
	$("#categoryDropdown").load(_service("packMan","categories","select")+"&src="+currentType+"&type="+$("#typeDropdown").val(), function() {
		listPackages();
	});
}
function loadStore() {
	$("#packageTable").html("<tr><td colspan=20><div class='ajaxloading ajaxloading5'>Indexing Packages</div></td></tr>");
	
	$("#pgtoolbar .navbar-right li").removeClass("active");
	$("#pgtoolbar .navbar-right a#toolbtn_loadStore").parent().addClass("active");
	$("#pgtoolbar li.categoryDropown").removeClass("hidden");
	
	currentType="estore";
	
	$("#categoryDropdown").load(_service("packMan","categories","select")+"&src="+currentType+"&type="+$("#typeDropdown").val(), function() {
		listPackages();
	});
}

function loadUploader() {
    $("#packageTable").html("<tr><td colspan=20><div class='ajaxloading ajaxloading5'>Loading Uploader</div></td></tr>");
	
	$("#pgtoolbar .navbar-right li").removeClass("active");
	$("#pgtoolbar .navbar-right a#toolbtn_loadUploader").parent().addClass("active");
	$("#pgtoolbar li.categoryDropown").removeClass("hidden");
	
	currentType="uploader";
	
	$("#categoryDropdown").hide();
}

