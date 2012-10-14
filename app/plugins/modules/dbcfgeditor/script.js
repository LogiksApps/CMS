scope="";
$(function() {
	$("tr .hidden").parents("tr").css("display","none");
	
	$("button").button();	
	$(".tabs").tabs();
	
	$("input.datefield").each(function() {
			src='dd/mm/yy';
			if($(this).attr("src")!=null && $(this).attr("src").length>0) {
				src=$(this).attr("src");
			}
			$(this).datepicker({
					changeMonth: true,
					changeYear: true,
					showButtonPanel:false,
					dateFormat:src,
				});
		});
	
	$("select").addClass("ui-state-default ui-corner-all");
	$("select.multiple").attr("multiple","multiple");
	$("select.multiple").removeClass("ui-state-default");
	
	$("select.multiple").attr("class","multiple");
	$("select.multiple").multiselect({
			minWidth:100,
		});
	
	$("#cfg_workspace").find("input").blur(function() {
		v1=$(this).val();
		v2=$(this).attr("value");
		if(v1!=v2) {
			$(this).addClass("changed");
		} else {
			$(this).removeClass("changed");
		}
	});
	$("#cfg_workspace").find("select").change(function() {
		v1=$(this).val();
		v2=$(this).attr("value");
		if(v1!=v2) {
			$(this).addClass("changed");
		} else {
			$(this).removeClass("changed");
		}
	});
	$("#cfg_workspace").find(".color").each(function() {
			v1=$(this).val();
			/*$(this).blur(function() {				
				$(this).css("background","red");
			});*/
		});
	
	$("body").addClass("ui-widget-content");
});
function resetForm(id) {
	$(id).find(".changed").each(function() {
		v1=$(this).val();
		v2=$(this).attr("value");
		$(this).val(v2);
		$(this).removeClass("changed");
	});
}
function submitForm(id) {
	src=submitLink;
	params="";
	$(id).find("input.changed").each(function() {
		v=$(this).val();
		t=$(this).attr("name");
		$(this).attr("value",v);
		params+=t+"="+v+"&";
	});
	$(id).find("select.changed").each(function() {
		v=$(this).val();
		t=$(this).attr("name");
		$(this).attr("value",v);
		params+=t+"="+v+"&";
	});
	if(params.length>0) {
		params=params.substr(0,params.length-1);
		processAJAXPostQuery(src,params,function(txt) {
				if(txt.length>0) {
					if(typeof lgksAlert == "function") {
						lgksAlert(txt);
					} else {
						alert(txt);
					}
				}
				if(txt.toLowerCase().indexOf("success")>=0) {
					$(id).find(".changed").removeClass("changed");
				}
			});
	} else {
		$("#msgdiv").hide("fast");
		$("#msgdiv").html("No Change Found.");
		$("#msgdiv").slideDown(300).delay(1200).fadeOut(300);
	}
}
function popupInfo(btn,title) {
	lgksAlert($(btn).find(".popupdata").html(),title);
}
function popupLink(btn,title) {
	lgksOverlayURL($(btn).find(".popupdata").html(),title);
}
function openJS(btn,title) {
	func=$(btn).find(".popupdata").html();
	txt=null;
	chld=$(btn).parents("tr").find("td.value").children();
	if(chld.length>0) {
		txt=chld[0];
	}	
	if(func==null || func.length<=0) return;
	if(typeof(func)=='function') func(txt);
	else window[func](txt);
}
function showHelp() {
	jqPopupDiv("#helpInfo",null,true,"700","250");
}
