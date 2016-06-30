<style>
#sidebarFileTree {
    width: 100%;
    /*height: 93%;*/
    overflow-x: auto;
    position: absolute;
    bottom: 0px;
    top: 32px;
    left: 0px;
    right: 0px;
}
</style>
<!-- <div class="searchField">
    <input type='text' placeholder='Search files' />
</div> -->
<ul id="sidebarFileTree" class='sidebarTree'></ul>
<div class='popupoverIcon sidebarFileTreeButtons'>
    <div class='fa fa-plus actionIcon' cmd='createnew'></div>
    <div class='fa fa-upload actionIcon' cmd='upload'></div>
    <div class='fa fa-refresh actionIcon' cmd='refresh'></div>
</div>
<script>
listFileMode="listFiles";
hiddenFolders=["/tmp/"];
$(function() {
    $("#sidebarFileTree").delegate("li.file[basepath]>span","click",function(e) {
        e.preventDefault();

        $("#sidebarFileTree").find("li.active").removeClass('active');
        //$(this).addClass('active');

        file=$(this).parent();
        bp=file.attr("filepath");
        ttl=file.text();
        lx=_link("modules/cmsEditor")+"&type=edit&src="+encodeURIComponent(bp);
        openLinkFrame(ttl,lx);
    });
    $(".sidebarFileTreeButtons").delegate(".actionIcon[cmd]","click",function(e) {
        e.preventDefault();

        cmd=$(this).attr("cmd");
        switch(cmd) {
            case "refresh":
                loadFileTree();
            break;
            case "createnew":
                lx=_link("modules/cmsEditor")+"&type=new&ext=text";
                openLinkFrame("New File",lx);
            break;
            case "upload":
                lx=_link("modules/cmsUploader");
                openLinkFrame("New Uploads",lx);
            break;
            case "searchfiles":

            break;
        }
    });
    loadFileTree();
});
function loadFileTree(file) {
    $('#sidebarFileTree').html("<div class='ajaxloading ajaxloading8'></div>");
    lx=_service("files")+"&action="+listFileMode+"&format=json";
    processAJAXQuery(lx,function(txt) {
        try {
            json=$.parseJSON(txt);
            $('#sidebarFileTree').html("");
            loadFileTreeObj(json.Data,"/");
            $('#sidebarFileTree').treed({openedClass:'glyphicon-folder-open', closedClass:'glyphicon-folder-close'});

            revealFile(file);
        } catch(e) {
            console.error(e);
        }
    });
}
function loadFileTreeObj(obj,basePath) {
    if(hiddenFolders.indexOf(basePath)>=0) return;
    $.each(obj,function(k,v) {
        if((typeof v)=="object") {
            newBasePath=basePath+k+"/";
            if(hiddenFolders.indexOf(newBasePath)>=0) return;
            html="<li class='folder' basepath='"+newBasePath+"'><i class='indicator glyphicon glyphicon-folder-close'></i>"+k+"<ul></ul></li>";
            if(basePath.length>1) {
                $("#sidebarFileTree li[basepath='"+basePath+"']>ul").prepend(html);
            } else {
                $('#sidebarFileTree').append(html);
            }

            loadFileTreeObj(v,newBasePath);
        } else {
            html="<li class='file' basepath='"+basePath+"' filepath='"+((basePath+"/"+v).replace("//","/"))+"'><i class='indicator glyphicon glyphicon-file'></i><span>"+v+"</span></li>";
            if(basePath.length>1) {
                $("#sidebarFileTree li[basepath='"+basePath+"']>ul").prepend(html);
            } else {
                $('#sidebarFileTree').append(html);
            }
        }
    });
    $('#sidebarFileTree>li[basepath="/"]').each(function() {$('#sidebarFileTree').append(this);});
}
function revealFile(file) {
    if(file==null) return;
    if(typeof file=='string') {
        file=$("#sidebarFileTree li[filepath='"+file+"']");
    }
    file.addClass("active").parent().find(">li").css("display","list-item");
    if(!file.closest("ul").hasClass("sidebarTree")) {revealItem(file.parent().parent());}
}
</script>
