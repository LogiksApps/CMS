var currentType="installed";
$(function() {
	$("#pgworkspace").delegate(".cmdAction[cmd]","click",function(e) {
		cmd=$(this).attr("cmd");
		app=$(this).attr("packkey");
		
		switch(cmd) {
			case "editPlugin":
				
				break;
			case "removePlugin":
				
				break;
			case "installPlugin":
				
				break;
			case "blockPlugin":
				
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
	
	listPackages();
});
function listPackages() {
	$("#pgworkspace").html("<div class='ajaxloading ajaxloading5'>Fetching Plugins</div>");
	
	processAJAXQuery(_service("packMan","getlist")+"&type="+currentType,function(dataJSON) {
		tmplHtml="<tr ><td>{{name}}</td><td>{{type}}</td><td>{{src}}</td>";//<th>{{k}}</th>class='{{#if is_global}}info{{/if}}'
		tmplHtml+='<td>{{is_installed}}</td>';
		tmplHtml+='<td>';
		
		tmplHtml+='{{#unless is_global}}<i class="fa fa-pencil cmdAction pull-left" cmd="editPlugin" appkey="{{pkey}}" title="Edit App"></i>{{/unless}}';
		tmplHtml+='</td></tr>';
		tmplCode = Handlebars.compile(tmplHtml);
		
		
		html="<div class='table-responsive' style='padding-right: 6px;'><table class='table table-striped table-hover table-condensed'>";
		html+="<thead><tr>";
		html+="<th>Plugin Name</th><th width=150px>Type</th><th width=150px>Source</th><th width=100px>Installed</th>";//<th width=50px>SL#</th>
		//<th width=100px>Status</th><th width=100px>DevMode</th><th width=100px>Access</th>
		html+="<th></th></tr></thead>";
		html+="<tbody>";
		
		$.each(dataJSON.Data.MODULES,function(k,v) {
			v['k']=k+1;
			html+=tmplCode(v);
		});
		$.each(dataJSON.Data.VENDORS,function(k,v) {
			v['k']=k+1;
			html+=tmplCode(v);
		});
		$.each(dataJSON.Data.WIDGETS,function(k,v) {
			v['k']=k+1;
			html+=tmplCode(v);
		});
		
		html+="</tbody>";
		html+="</table></div>";
		$("#pgworkspace").html(html);
	},"json");
}
function searchPackages(txt) {
	if(txt==null) {
		return listPackages();
	}
	$("#pgworkspace").html("<div class='ajaxloading ajaxloading5'>Fetching Plugins</div>");
	
	processAJAXQuery(_service("packMan","getlist")+"&type="+currentType+"&q="+txt,function(dataJSON) {
		tmplHtml="<tr ><td>{{name}}</td><td>{{type}}</td><td>{{src}}</td>";//<th>{{k}}</th>class='{{#if is_global}}info{{/if}}'
		tmplHtml+='<td>{{is_installed}}</td>';
		tmplHtml+='<td>';
		
		tmplHtml+='{{#unless is_global}}<i class="fa fa-pencil cmdAction pull-left" cmd="editPlugin" appkey="{{pkey}}" title="Edit App"></i>{{/unless}}';
		
		tmplHtml+='</td></tr>';
		tmplCode = Handlebars.compile(tmplHtml);
		
		
		html="<div class='table-responsive' style='padding-right: 6px;'><table class='table table-striped table-hover table-condensed'>";
		html+="<thead><tr>";
		html+="<th>Plugin Name</th><th width=150px>Type</th><th width=150px>Source</th><th width=100px>Installed</th>";//<th width=50px>SL#</th>
		//<th width=100px>Status</th><th width=100px>DevMode</th><th width=100px>Access</th>
		html+="<th></th></tr></thead>";
		html+="<tbody>";
		
		$.each(dataJSON.Data.MODULES,function(k,v) {
			v['k']=k+1;
			html+=tmplCode(v);
		});
		$.each(dataJSON.Data.VENDORS,function(k,v) {
			v['k']=k+1;
			html+=tmplCode(v);
		});
		$.each(dataJSON.Data.WIDGETS,function(k,v) {
			v['k']=k+1;
			html+=tmplCode(v);
		});
		
		html+="</tbody>";
		html+="</table></div>";
		$("#pgworkspace").html(html);
	},"json");
}
function loadInstalled() {
	$("#pgtoolbar .navbar-right li").removeClass("active");
	$("#pgtoolbar .navbar-right a#toolbtn_loadInstalled").parent().addClass("active");
	
	currentType="installed";
	listPackages();
}
function loadRepo() {
	$("#pgtoolbar .navbar-right li").removeClass("active");
	$("#pgtoolbar .navbar-right a#toolbtn_loadRepo").parent().addClass("active");
	
	currentType="repos";
	listPackages();
}
