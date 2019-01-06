var currentFile=null;
var newFile=false;
$(function() {
	$("#toolbtn_langDropdown").delegate(".dropdown-menu li","click",function() {
		file=$(this).data("name");
		loadLingFle(file);
	});
	$("#lingTable tbody").delegate("tr:last-child input:last-child","keyup",function(e) {
		if(e.keyCode==13) {
			addNewString();
		}
	});
	$("#lingTable tbody").delegate("tr td i.remove","click",function(e) {
		removeString(this);
	});
	loadLingDropdown();
});
function loadLingDropdown() {
	newFile=false;
	$("#toolbtn_langDropdown .dropdown-menu").html("");
	$("#lingTable tbody").html("");
	processAJAXQuery(_service("lingEditor","listLing"),function(ans) {
			$.each(ans.Data,function(k,v) {
				$("#toolbtn_langDropdown .dropdown-menu").append("<li data-name='"+k+"'><a>"+v+"</a></li>");
			});
			if($("#toolbtn_langDropdown .dropdown-menu li").length>0) {
				file=$("#toolbtn_langDropdown .dropdown-menu li:first-child").data("name");
				loadLingFle(file);
			} else {
				$("#lingTable tbody").html("<tr class='error'><td colspan=10><h3 class='text-center'>No Language File Found, Create One</h3></td></tr>");
			}
		},"json");
}
function loadLingFle(file) {
	currentFile=file;
	$("#lingTable tbody").html("<tr class='loading'><td colspan=10><div class='ajaxloading ajaxloading5'></div></td></tr>");
	$("#lingFileName").html("LING :: "+ file);
	$("#toolbtn_langDropdown button").html("Selected : "+file+" <span class='caret'></span>");
	
	processAJAXPostQuery(_service("lingEditor","lingFile"),"file="+file,function(ans) {
			$("#lingTable tbody").html("");
		
			if(ans.Data.error!=null) {
				lgksToast(ans.Data.error);
			} else {
				$.each(ans.Data,function(k,v) {
					if(v.hidden==true) {
						htm="<tr class='hidden'><th class='name'><input type=text value='"+v.title+"' class='form-control' /></th><td class='v1'><input type=text value='"+v.value+"' class='form-control' /></td><td width=50px><i class='fa fa-times fa-2x remove'></i></td></tr>";
					} else {
						htm="<tr><th class='name'><input type=text value='"+v.title+"' class='form-control' /></th><td class='v1'><input type=text value='"+v.value+"' class='form-control' /></td><td width=50px><i class='fa fa-times fa-2x remove'></i></td></tr>";
					}
					
					$("#lingTable tbody").append(htm);
				});
			}
		},"json");
}
function saveFile() {
	if(currentFile==null) {
		lgksToast("No Language File Selected");
		return;
	}
	q=[];
	$("#lingTable tbody tr.danger").removeClass("danger");
	$("#lingTable tbody tr.warning").removeClass("warning");
	
	$("#lingTable tbody tr").each(function(k,v) {
		if($(this).hasClass("active")) {
			return;
		}
		nm=$(this).find("th.name input").val();
		v=$(this).find("td.v1 input").val();
		if(nm!=null && nm.length>0) {
			q.push(encodeURIComponent(nm)+"="+encodeURIComponent(v));
		} else {
			$(this).addClass("danger");
		}
		if((v==null || v.length<=0) && !$(this).hasClass("hidden")) {
			$(this).addClass("warning");
		}
	});
	if($("#lingTable tbody tr.danger").length>0) {
		lgksToast("Some of the records are not having name, they have been marked.");
		return;
	}
	if($("#lingTable tbody tr.warning").length>0) {
		lgksToast("Some of the records are not having value, they have been marked.");
	}
	processAJAXPostQuery(_service("lingEditor","saveFile"),"file="+currentFile+"&"+q.join("&"),function(ans) {
			lgksToast(ans.Data);
			if(newFile) loadLingDropdown();
		},"json");
}
function resetFile() {
	if(currentFile==null) return;
	lgksConfirm("Are you sure to reset. All changes will be lost.","Reset File",function(ans) {
		if(ans) {
			$("#lingTable tbody").html("");
			loadLingFle(currentFile);
		}
	});
}
function createNew() {
	lgksPrompt("Please give the new file name.<br>Please save the before you do this.","New File",function(ans) {
		if(ans!=null && ans.length>0) {
			currentFile=ans;
			newFile=true;
			$("#lingTable tbody").html("");
		}
	});
}

function addNewString() {
	htm="<tr><th class='name'><input type=text value='' class='form-control' /></th><td class='v1'><input type=text value='' class='form-control' /></td><td width=50px><i class='fa fa-times fa-2x remove'></i></td></tr>";
	$("#lingTable tbody").append(htm);
}
function removeString(btn) {
	$(btn).closest("tr").toggleClass("active");
}