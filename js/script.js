$(function() {
  $("#header #toolsMenu").delegate("a[href]:not(.noauto)","click",function(e) {
      e.preventDefault();
      href=$(this).attr('href');
      if(href!=null && href.length>2) {
          ttl=$(this).text();
          openLinkFrame(ttl,href);
      }
  });

  $('body').delegate(".datalink[data-type]","click",function(e) {
    type=$(this).data("type");
    val=$(this).data("value");
    
    dataInfo(type,val);
  });
  
  $("body").delegate("#header .show-sidebar-menu","click",function(e) {
    //$("#sidebar").toggleClass("active");
    if($("#sidebar").hasClass("slide-in")) {
      $("#sidebar").toggleClass("slide-out").removeClass("slide-in");
    } else {
      $("#sidebar").toggleClass("slide-in").removeClass("slide-out");
    }
  });

  $('[data-toggle="tooltip"]').tooltip();

  $('#sidebarMenuTree').metisMenu();

});

//Loads the correct sidebar on window load,
//collapses the sidebar on window resize.
// Sets the min-height of #page-wrapper to window size
$(function() {
    $(window).bind("load resize", function() {
        topOffset = 50;
        width = (this.window.innerWidth > 0) ? this.window.innerWidth : this.screen.width;
        if (width < 800) {
            $('div.navbar-collapse').addClass('collapse');
            topOffset = 100; // 2-row-menu
        } else {
            $('div.navbar-collapse').removeClass('collapse');
            $("#sidebar").removeClass("slide-out").removeClass("slide-in");
        }

        height = ((this.window.innerHeight > 0) ? this.window.innerHeight : this.screen.height) - 1;
        height = height - topOffset;
        if (height < 1) height = 1;
        if (height > topOffset) {
            $("#page-wrapper").css("min-height", (height) + "px");
        }
    });

    var url = window.location;
    var element = $('ul.nav a').filter(function() {
        return this.href == url || url.href.indexOf(this.href) == 0;
    }).addClass('active').parent().parent().addClass('in').parent();
    if (element.is('li')) {
        element.addClass('active');
    }
});


$.fn.extend({
    treed: function (o) {
      
      var openedClass = 'glyphicon-minus-sign';
      var closedClass = 'glyphicon-plus-sign';
      var fileClass = 'glyphicon-info-sign';
      
      if (typeof o != 'undefined'){
        if (typeof o.openedClass != 'undefined'){
        openedClass = o.openedClass;
        }
        if (typeof o.closedClass != 'undefined'){
        closedClass = o.closedClass;
        }
      };
      
        //initialize each of the top levels
        var tree = $(this);
        tree.addClass("tree");
        tree.find('li').has("ul").each(function () {
            var branch = $(this); //li with children ul
            if(!branch.has("i.indicator")) branch.prepend("<i class='indicator glyphicon " + closedClass + "'></i>");
            branch.addClass('branch');
            branch.on('click', function (e) {
                if (this == e.target) {
                    var icon = $(this).children('i:first');
                    icon.toggleClass(openedClass + " " + closedClass);
                    $(this).children().children().toggle();
                    
                    tree.find(".branch.active").removeClass("active");
                    $(this).addClass("active");
                }
            })
            branch.children().children().toggle();
        });
        //fire event from the dynamically added icon
      tree.find('.branch .indicator').each(function(){
        $(this).on('click', function () {
            $(this).closest('li').click();
        });
      });
        //fire event to open branch if the li contains an anchor instead of text
        tree.find('.branch>a').each(function () {
            $(this).on('click', function (e) {
                $(this).closest('li').click();
                e.preventDefault();
            });
        });
        //fire event to open branch if the li contains a button instead of text
        tree.find('.branch>button').each(function () {
            $(this).on('click', function (e) {
                $(this).closest('li').click();
                e.preventDefault();
            });
        });
    }
});

function dataInfo(type, val) {
  //console.log(type+" "+val);
  type=type.split("-");
  switch(type[0]) {
    case "guid":case "guid-users":
      
      break;
    case "privilege":case "privilege-users":
      
      break;
    case "user":
      break;
  }
}

function toTitle(s) {
    if(s==null || s.length<=0) return "";
    return s.charAt(0).toUpperCase()+s.substr(1);
}

function getFileTree() {
  return $('#sidebarFileTree');
}
function showLoader(){
	$("body .loader_bg").detach();
	$("body").append("<div class='loader_bg'><div class='loader_wrapper'><div class='inner'><span>L</span><span>o</span><span>a</span><span>d</span><span>i</span><span>n</span><span>g</span></div></div></div>");
}
function hideLoader(){
	$("body .loader_bg").detach();
}
