<div id="content">
    <ul id="myTab" class="nav nav-tabs" data-tabs="tabs">
        <li role="presentation" class="active"><a href="#dashboard">Dashboard</a></li>
        <!-- <li role="presentation"><a href="#profile">Profile</a></li> -->
    </ul>
    <div id="myTab-content" class="tab-content">
        <div class="tab-pane active dashboard" id="dashboard">
            {module src='dashboard'}
        </div>
        <!-- <div class="tab-pane" id="profile"><div class="ajaxloading5"></div></div> -->
    </div>
</div>
<script>
$(function() {
    $("#myTab").delegate(".closeTab","click",function(e) {
        var tabContentId = $(this).parent().attr("href");
        $(this).parent().parent().remove(); //remove li of tab
        $('#myTab a:last').tab('show'); // Select first tab
        $(tabContentId).remove(); //remove respective tab content
    });
    $('#myTab a').click(function (e) {
          e.preventDefault();
          $(this).tab('show');
        });
});
function openLink(tabName,tabLink,closable) {
    ref="TAB_"+tabName.replace(/\W/g,"_");
    if($("#myTab a[href='#"+ref+"']").length>0) {
        $("#myTab a[href='#"+ref+"']").tab("show");
        return;
    }
    $("#myTab-content").append('<div class="tab-pane" id="'+ref+'"><div class="ajaxloading5"></div></div>');
    if(closable===false) {
        $("#myTab").append('<li><a href="#'+ref+'" data-toggle="tab">'+tabName+'</a></li>');
    } else {
        $("#myTab").append('<li><a href="#'+ref+'" data-toggle="tab">'+tabName+'<i class="closeTab fa fa-times"></i></a></li>');
    }
    $("#"+ref).load(tabLink);
    $('#myTab a[href="#'+ref+'"]').tab("show");
}
function openLinkFrame(tabName,tabLink,closable) {
    ref="TAB_"+tabName.replace(/\W/g,"_");
    if($("#myTab a[href='#"+ref+"']").length>0) {
        $("#myTab a[href='#"+ref+"']").tab("show");
        return;
    }
    $("#myTab-content").append('<div class="tab-pane" id="'+ref+'"><iframe width=100% height=100% frameborder=0 src="'+tabLink+'"></iframe></div>');
    if(closable===false) {
        $("#myTab").append('<li><a href="#'+ref+'" data-toggle="tab">'+tabName+'</a></li>');
    } else {
        $("#myTab").append('<li><a href="#'+ref+'" data-toggle="tab">'+tabName+'<i class="closeTab fa fa-times"></i></a></li>');
    }
    $('#myTab a[href="#'+ref+'"]').tab("show");
}
</script>