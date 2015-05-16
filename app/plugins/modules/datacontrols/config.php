<?php
if(!defined('ROOT')) exit('No direct script access allowed');

$cntrls=array();
$cntrls["forms"]=array("cols"=>"id,title,category,engine,layout,adapter as al,submit_table as datatable_table,blocked,onmenu,privilege,doc,doe");
$cntrls["reports"]=array("cols"=>"id,title,category,engine,actionlink as al,datatable_table,blocked,onmenu,privilege,doc,doe");
$cntrls["search"]=array("cols"=>"id,title,category,engine,actionlink as al,datatable_table,blocked,onmenu,privilege,doc,doe");
$cntrls["views"]=array("cols"=>"id,title,category,engine,blocked,onmenu,privilege,doc,doe");

$colsTbl=array();
$colsTbl["forms"]=array("<input type=checkbox onclick='checkAll(this.checked)' />",
	"Category","Name","Engine","Adapter","DB","Created","Edited","Blocked","OnMenu","Privilege","EDIT","--");
$colsTbl["reports"]=array("<input type=checkbox onclick='checkAll(this.checked)' />",
	"Category","Name","Engine","Link","DB","Created","Edited","Blocked","OnMenu","Privilege","EDIT","--");
$colsTbl["search"]=array("<input type=checkbox onclick='checkAll(this.checked)' />",
	"Category","Name","Engine","Link","DB","Created","Edited","Blocked","OnMenu","Privilege","EDIT","--");
$colsTbl["views"]=array("<input type=checkbox onclick='checkAll(this.checked)' />",
	"Category","Name","Engine","Created","Edited","Blocked","OnMenu","Privilege","EDIT","--");

$properties=array();
$properties["forms"]=array("cols"=>"id,title,header,footer,engine,layout,def_mode,adapter,submit_table,submit_action,submit_wherecol");
$properties["reports"]=array("cols"=>"id,title,header,footer,engine,actionlink");
$properties["search"]=array("cols"=>"id,title,header,footer,engine,actionlink");

$editors=array();
$editors["forms"]=array("editor"=>"forms.php");
$editors["datatable"]=array("editor"=>"datatables.php");
$editors["template"]=array("editor"=>"template.php");

$manageButtons1=array();
$manageButtons1["template"]=array("checkcol"=>"template","html"=>"<div name='template' title='Template Editor' class='colbtn icon_template' rel='%s'></div>");
$manageButtons1["datatable"]=array("checkcol"=>"datatable_table","html"=>"<div name='datatable' title='DataTable Designer' class='colbtn icon_datatable' rel='%s'></div>");
$manageButtons1["forms"]=array("checkcol"=>"frmdata","html"=>"<div name='form' title='Form Designer' class='colbtn icon_form' rel='%s'></div>");
$manageButtons1["properties"]=array("checkcol"=>"header","html"=>"<div name='properties' title='Edit Properties' class='colbtn icon_props' rel='%s'></div>");

$manageButtons2=array();
$manageButtons2["script"]=array("check"=>"","html"=>"<div name='js' title='Edit Script' class='colbtn icon_script' rel='%s'></div>");
$manageButtons2["style"]=array("check"=>"","html"=>"<div name='style' title='Edit Style' class='colbtn icon_style' rel='%s'></div>");
$manageButtons2["edit"]=array("check"=>"","html"=>"<div name='ctrl' title='Edit Control' class='editicon colbtn' rel='%s'></div>");
?>
