/*Editors*/
isRunning=false;
$(function() {
	loadCodeEditor("codearea","php");
	$('#codearea').parent().find('.CodeMirror').each(function() {
		$(this).find('.CodeMirror-scroll').css('height',($(window).height()-160));
	});
	editor.setOption("autofocus",true);
});
function editCode(id,col,ext,closeOnEsc) {
	oExt=ext.toUpperCase();
	if(ext=="js") ext="javascript";
	else if(ext=="html" || ext=="htm") ext="htmlmixed";
	else if(ext=="php") ext="php";
	else ext="htmlmixed";
	if(closeOnEsc==null) closeOnEsc=true;
	params={
			width:$(window).width()-50,
			height:$(window).height()-50,
			modal:true,
			stack:true,
			show:{effect: 'fade', speed: 2000},
			hide:{effect: 'fade', speed: 2000},
			resizable:false,
			draggable:false,
			closeOnEscape:closeOnEsc,
			dialogClass:'alert',
			title:"Edit : "+col.toUpperCase()+" ("+oExt+")",
			buttons: {
				Save: function() {
					if(isRunning) return false;
					data=editor.getValue();
					l1=getCMD("lists")+"&action=save";
					q="&id="+id+"&col="+col+"&code="+encodeURIComponent(data);
					isRunning=true;
					$("#loadingmsg").show();
					processAJAXPostQuery(l1,q,function(txt) {
							isRunning=false;
							if(txt.trim().length>0) {
								lgksAlert(txt);								
							} else {
								$("#codeEditor").dialog("close");
							}
						});
				},
				Cancel: function() {
					if(isRunning) return false;
					$(this).dialog( "close" );
				}
			},
			close: function(event, ui) {
				$("#loadingmsg").hide();
			},
			beforeClose:function() {
				if(isRunning) return false;
				else return true;
			},
		};
	
	$("#loadingmsg").show();
	l=getCMD("lists")+"&action=fetch";
	q="&id="+id+"&col="+col;
	processAJAXPostQuery(l,q,function(txt) {
			editor.setOption("mode",ext);
			$("#loadingmsg").hide();
			$("#codeEditor").dialog(params);
			editor.setValue(txt);
		});
}
function editForm(id,title) {
	l=openEditorLink+"&editor=forms&id="+id;
	showWindow(l,mode.toUpperCase()+': Forms ['+id+']');
}
function editDataTable(id,title) {
	l=openEditorLink+"&editor=datatable&id="+id;
	showWindow(l,mode.toUpperCase()+': DataTables ['+id+']');
}
function editTemplate(id,title) {
	l=openEditorLink+"&editor=template&id="+id;
	showWindow(l,mode.toUpperCase()+': Template ['+id+']');
}
