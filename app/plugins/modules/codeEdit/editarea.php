<?php
if(!defined('ROOT')) exit('No direct script access allowed');

_js("jquery.cookie");
loadHelpers("cookies");

$editor=checkModule("editor");
$webPath=getWebPath($editor);

?>
<style>

</style>
<script language=javascript>
editor=null;
function loadDevEditor(divID,ext) {
	$("#codearea").css("height",($(window).height()-$("#toolbar").height()-$("#statusbar").height()-5));
	$("#statusbar .right").hide();
	
	editor=divID;
	editAreaLoader.init({
				id: divID	// id of the textarea to transform		
				,start_highlight: true	// if start with highlight
				//,fullscreen: true
				,allow_resize: 'both' //'y'
				,allow_toggle: false
				,word_wrap: true
				,language: 'en'
				,syntax: ext //html
				,show_line_colors: true
				//new_document, save, load, |, , |, help
				,toolbar: 'charmap, fullscreen, |, search, go_to_line, |, undo, redo, |, select_font, |, syntax_selection, |, change_smooth_selection, highlight, reset_highlight'
				,syntax_selection_allow: 'css,html,js,php,python,vb,xml,c,cpp,sql,basic,pas,brainfuck'
				//,is_multi_files: true
				
				,font_size: '11'
				,font_family: 'verdana, monospace'
				
				,plugins: 'charmap'
				,charmap_default: 'arrows'

				//,display: 'later'
				//,replace_tab_by_spaces: 4
				//,min_height: 350
				
				//,load_callback: 'my_load'
				//,save_callback: 'my_save'
				//,EA_load_callback: 'editAreaLoaded'
				//,EA_load_callback:'doResize'
			});	
}
function getData() {
	return editAreaLoader.getValue(editor);
}
function setData(txt) {
	editAreaLoader.setValue(editor,txt);
}
function readonly() {
	editAreaLoader.execCommand(editor,'set_editable',!editAreaLoader.execCommand(editor,'is_editable'));
}
function setFontSize(size) {
}
function changeTheme(theme) {
}
function changeExtension(ext) {
}
function getSelectedRange() {
	sel=editAreaLoader.geSelectionRange(editor);
	return { from: sel['start'], to: sel['end'] };
}
function getSelectedText() {
	return editAreaLoader.geSelectedText(editor);
}
//Other Functions
</script>
