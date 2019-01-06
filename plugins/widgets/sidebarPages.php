<?php
if(!defined('ROOT')) exit('No direct script access allowed');

?>
<style>
.sidebarPages {
	list-style: none;
	margin-left: -40px;
}
.sidebarPages a {
	/*display: block;
    padding: 7px;
    padding-left: 25px;
    
    font-size: 13px;*/
    color: white;
    text-decoration: none !important;
}
.sidebarPages .list-group-item:hover, .sidebarPages .list-group-item:hover a {
	background: #3c8dbc;
	cursor: pointer;
}
.sidebarPages  .list-group-item {
	background: transparent;
	border: 0px;
	border-radius: 0px;
}
.sidebarPages .list-group>.list-group-item.list-file a {
	padding-left: 30px;
}
</style>

<ul class="panel-group sidebarPages">
</ul>


<script>
$(function() {
  $(".sidebarPages").delegate(".list-file[data-path]","click",function(e) {
        ttl=$(this).text();
        href=$(this).data("path");

        lx=_link("modules/pageEditor")+"&comptype=pages&readonly=true&src="+encodeURIComponent(href);
		openLinkFrame(ttl,lx,true);

		return false;
    });
  loadPageList();
});
function loadPageList() {
	$(".sidebarPages").html("<div class='ajaxloading ajaxloading8'></div>");

	processAJAXQuery(_service("pageManager","getlist")+"&comptype=pages",function(txt) {
		fs=txt.Data;
		html="";html1="";
		$.each(fs,function(k,v) {
			kx=md5(k);
			if(v.folder) {
				html1+="<div class='list-group-item list-folder'><a href='#item-"+kx+"' data-toggle='collapse'><i class='glyphicon glyphicon-folder-close'></i>&nbsp;&nbsp;"+k+"</a></div>";
				html1+="<div class='list-group collapse' id='item-"+kx+"'>";
				$.each(v,function(m,n) {
					if(typeof n =="object") {
						html1+="<div class='list-group-item list-file' data-path='"+n.path+"'><a href='#'><i class='glyphicon glyphicon-file'></i>&nbsp;&nbsp;"+m+"</a></div>";//n.name
					}
				});
				html1+="</div>";
			} else {
				html+="<div class='list-group-item list-file' data-path='"+v.path+"'><a href='#'><i class='glyphicon glyphicon-file'></i>&nbsp;&nbsp;"+v.name+"</a></div>";
			}
		});
		$(".sidebarPages").html(html+html1);
	},"json");
}
</script>
