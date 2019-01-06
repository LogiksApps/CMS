var lastComponent="local";
var tempCopiedRecords={};
var classCopied="active";
var classFound="warning";
var classEdited="danger";
$(function() {
	$("#serviceList").delegate("td .cmdbtn[cmd]","click",function(e) {
		if($(this)==null || $(this).length<=0) {
			return;
		}
		cmd=$(this).attr("cmd");
		tr=$(this).closest("tr");
		skey=tr.data('key');
		
		switch(cmd) {
			case "copyService":
				tempCopiedRecords[skey]=tr;
				$("#serviceList").find('tr[data-key='+skey+']').addClass(classCopied);
				lgksToast("Record Copied. Please open Local Services and Save it for finalising.");
				break;
			case "detachService":
				if(tempCopiedRecords[skey]!=null) {
					delete tempCopiedRecords[skey];
				}
				tr.detach();
				break;
			case "removeService":
				if(tempCopiedRecords[skey]!=null) {
					delete tempCopiedRecords[skey];
				}
				tr.detach();
				break;
		}
	});
	
	Handlebars.registerHelper('is_status', function(msg, matchMsg, options) {
        if(msg === matchMsg || msg===true)
            return true;
        else
            return false;
    });
	Handlebars.registerHelper('is_notnull', function(msg, matchMsg, options) {
        if(msg !=null && msg.length>0)
            return true;
        else
            return false;
    });
	loadLocal();
});

function listServices() {
	$("#serviceList").html("<tr><th colspan=10><div class='ajaxloading ajaxloading5'></div></th></tr>");
	processAJAXQuery(_service("serviceManager","getlist")+"&comptype="+lastComponent,function(txt) {
		
		tmpl = Handlebars.compile(getRowTemplate(""));
		
		html = tmpl(txt);
		
		$("#serviceList").html(html);
		
		if(lastComponent=="local") {
			$.each(tempCopiedRecords,function(k,tr) {
				tr=tr.attr("class",classCopied);
				$("#serviceList").append(tr);
				$("#serviceList tr.active td.action").find("i.fa-copy").replaceWith("<i class='fa fa-remove cmdbtn' cmd='detachService'></i>");
			});
		} else {
			$.each(tempCopiedRecords,function(k,tr) {
				$("#serviceList").find('tr[data-key='+k+']').addClass(classCopied);
			});
		}
	},"json");
}
function findServices() {
	$("#serviceList").append("<tr><th colspan=10><div class='ajaxloading ajaxloading5'></div></th></tr>");
	processAJAXQuery(_service("serviceManager","findmore"),function(txt) {
		
		tmpl = Handlebars.compile(getRowTemplate(classFound));
		
		$.each(txt.Data,function(k,v) {
			if($("#serviceList tr[data-key="+v.skey+"]").length>0) {
				delete txt.Data[k];
			}
		});
		
		html = tmpl(txt);
		$("#serviceList").find(".ajaxloading").closest("tr").detach();
		$("#serviceList").append(html);
	},"json");
}
function loadLocal() {
	$("#pgtoolbar .navbar-right li").removeClass("active");
	$("#pgtoolbar #toolbtn_loadLocal").parent().addClass("active");
	
	$("#pgtoolbar #toolbtn_saveServiceConfig").show();
	$("#pgtoolbar #toolbtn_findServices").show();
	
	lastComponent="local";
	
	return listServices();
}
function loadGlobal() {
	$("#pgtoolbar .navbar-right li").removeClass("active");
	$("#pgtoolbar #toolbtn_loadGlobal").parent().addClass("active");
	
	$("#pgtoolbar #toolbtn_saveServiceConfig").hide();
	$("#pgtoolbar #toolbtn_findServices").hide();
	
	lastComponent="globals";
	
	return listServices();
}
function saveServiceConfig() {
	if($("#serviceList").find("tr."+classCopied).length<=0) {
		
	}
	if($("#serviceList").find("tr."+classFound).length<=0) {
		
	}
	if($("#serviceList").find("tr."+classEdited).length<=0) {
		
	}
	q=[];
	$("input[name],select[name]","#serviceList").each(function() {
		if($(this).attr("type")=="checkbox") {
			if(this.checked) {
				q.push($(this).attr("name")+"=true");
			} else {
				q.push($(this).attr("name")+"=false");
			}
		} else {
			q.push($(this).attr("name")+"="+$(this).val());
		}
	});
	
	processAJAXPostQuery(_service("serviceManager","update"),q.join("&"),function(txt) {
		if(txt.Data!=null && txt.Data.toLowerCase().indexOf("error")<0) {
			lgksToast("Service Configuration Updated Successfully");
			loadLocal();
		} else {
			if(txt.Data!=null && txt.Data.length>2) {
				lgksToast(txt.Data.substr(6));
			} else {
				lgksToast("Error Updating Service Configuration");
			}
		}
	},"json");
}



function getRowTemplate(clz) {
	return "{{#Data}}{{#if (is_notnull skey)}}<tr class='"+clz+"' data-key='{{skey}}'>"+
					//"<td align=center><input type='checkbox' class='rowSelector' /></td>"+
					"<th data-value='{{skey}}'>{{skey}}</th>"+
					"<td data-value='{{src}}'>{{src}}</td>"+
					"<td data-value='{{format}}'>{{format}}</td>"+
					"<td align=center data-value='{{debug}}'>{{#if (is_status debug 'true')}}<input name='s[{{skey}}][debug]' type='checkbox' checked='true' {{#if readonly}}disabled{{/if}} />{{else}}<input name='s[{{skey}}][debug]' type='checkbox' {{#if readonly }}disabled{{/if}} />{{/if}}</td>"+
					"<td align=center data-value='{{cache}}'>{{#if (is_status cache 'true')}}<input name='s[{{skey}}][cache]' type='checkbox' checked='true' {{#if readonly}}disabled{{/if}} />{{else}}<input name='s[{{skey}}][cache]' type='checkbox' {{#if readonly }}disabled{{/if}} />{{/if}}</td>"+
					"<td align=center data-value='{{autoformat}}'>{{#if (is_status autoformat 'true')}}<input name='s[{{skey}}][autoformat]' type='checkbox' checked='true' {{#if readonly}}disabled{{/if}} />{{else}}<input name='s[{{skey}}][autoformat]' type='checkbox' {{#if readonly}}disabled{{/if}} />{{/if}}</td>"+
					"<td data-value='{{access_control}}'>{{access_control}}<div class='popupinfo hidden'>{{privilege_model}}</div></td>"+
					"<td class='action' align=center>{{#if (is_status type 'GLOBALS')}} <i class='fa fa-copy cmdbtn' cmd='copyService'></i> {{else}} <i class='fa fa-close cmdbtn' cmd='removeService'></i> {{/if}}"+
					
					"<input type='hidden' name='s[{{skey}}][src]' value='{{src}}' />"+
					"<input type='hidden' name='s[{{skey}}][format]' value='{{format}}' />"+
					"<input type='hidden' name='s[{{skey}}][access_control]' value='{{access_control}}' />"+
					"<input type='hidden' name='s[{{skey}}][privilege_model]' value='{{privilege_model}}' />"+

					"</td>"+
					"</tr>{{/if}}{{/Data}}";
}