var currentItem="local";
$(function() {
	$("#pgworkspace").delegate(".cmdAction[cmd]","click",function(e) {
		cmd=$(this).attr("cmd");
		app=$(this).attr("appkey");
		
		switch(cmd) {
			case "editApp":
				processAJAXPostQuery(_service("appManager","appEditor"),"app="+app,function(html) {
					lgksMsg(html,"App Editor #"+app,{closeButton:true,buttons: {
														"Cancel":function(e) {
															return true;
														},
														"Save":function(e) {
															form=$(e.target).closest(".bootbox").find("form");
															q=["app="+app];
															$("input[name],select[name],textarea[name]",form).each(function() {
																q.push($(this).attr("name")+"="+encodeURIComponent($(this).val()));
															});
															processAJAXPostQuery(_service("appManager","saveApp"),q.join("&"),function(dataJSON) {
																lgksToast(dataJSON.Data.msg);
																if(dataJSON.Data.error) {
																	
																} else {
																	listApps();
																	bootbox.hideAll();
																}
															},"json");
															return false;
														}
	  										}});
				});
				break;
			case "cloneApp":
				processAJAXPostQuery(_service("appManager","appEditor"),"app="+app,function(html) {
					
				});
				break;
			case "exportApp":
				
				break;
			case "flushCache":
				
				break;
			case "deleteApp":
				
				break;
			default:
				lgksToast("App Action Not Defined.");
		}
	});
	listApps();
});

function listApps() {
	$("#pgworkspace").html("<div class='ajaxloading ajaxloading5'>Fetching Apps</div>");
	
	processAJAXQuery(_service("appManager","listApps"),function(dataJSON) {
		tmplHtml="<tr class='{{#if readonly}}danger{{/if}}'><th>{{k}}</th><td>{{title}}</td><td>{{vers}}</td><td>{{router}}</td>";
		tmplHtml+='<td>{{published}}</td>';
		tmplHtml+='<td>{{status}}</td>';
		tmplHtml+='<td>{{devmode}}</td>';
		tmplHtml+='<td>{{access}}</td>';
		tmplHtml+='<td>';
		tmplHtml+='<a href="{{url}}" target=_blank class="pull-right fa fa-eye" title="Preview"></a>';
		//tmplHtml+='<i class="fa fa-trash cmdAction pull-right" cmd="deleteApp" appkey="{{appkey}}" title=""></i>';
		tmplHtml+='<i class="fa fa-pencil cmdAction pull-left" cmd="editApp" appkey="{{appkey}}" title="Edit App"></i>';
		//tmplHtml+='{{#if allow_clone}}<i class="fa fa-copy cmdAction pull-left" cmd="copyApp" appkey="{{appkey}}" title="Clone App"></i>{{/if}}';
		tmplHtml+='</td></tr>';
		tmplCode = Handlebars.compile(tmplHtml);
		
		
		html="<div class='table-responsive' style='padding-right: 6px;'><table class='table table-striped table-hover table-condensed'>";
		html+="<thead><tr>";
		html+="<th width=50px>SL#</th><th>Title</th><th width=150px>Vers</th><th width=150px>Router</th><th width=100px>Published</th><th width=100px>Status</th><th width=100px>DevMode</th><th width=100px>Access</th>";
		html+="<th></th></tr></thead>";
		html+="<tbody>";
		
		$.each(dataJSON.Data,function(k,v) {
			v['k']=k+1;
			html+=tmplCode(v);
		});
		
		html+="</tbody>";
		html+="</table></div>";
		$("#pgworkspace").html(html);
	},"json");
}
function removeApps() {
	
}

function searchApps() {
	
}

function loadLocalApps() {
	
}
function loadMarket() {
	
}

