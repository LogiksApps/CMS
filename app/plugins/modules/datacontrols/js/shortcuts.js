$(function() {
	$(document).keydown(function(e) {
			/*if(e.keyCode==27) {//esc
				closePropertiesWindow(true);
			} else */
			if(e.keyCode==83 && (e.ctrlKey || e.metaKey)) {//ctrl-s
				e.preventDefault();
				saveControl();
			} else if(e.keyCode==80 && (e.altKey)) {//alt-p
				e.preventDefault();
				closePropertiesWindow('toggle');
			} else if(e.keyCode==79 && (e.altKey)) {//alt-o
				e.preventDefault();
				showPreview('toggle');
			} else if(e.keyCode==82 && (e.altKey)) {//alt-r
				e.preventDefault();
				reloadEditor();
			} else if(e.keyCode==67 && (e.altKey)) { //alt-c
				e.preventDefault();
				clearEditor();
			} else {
				//lgksAlert(e.keyCode);
			}
		});
});
