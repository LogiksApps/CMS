var TOOLS_LIST = {};

$(function() {
  //$.ajaxSetup({cache: true});
  $("#header #toolsMenu").delegate("a[href]:not(.noauto)","click",function(e) {
      e.preventDefault();
      href=$(this).attr('href');
      if(href!=null && href.length>2) {
          ttl=$(this).text();
          openLinkFrame(ttl,href);
      }
  });
  
  $("#leftMenuOpen").click(function() {
      $("body").toggleClass("no_sidebar");
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
  
  $("#header .btn-open-file").click(function() {
      lgksPrompt("Please give the file path relative to APPROOT.", "Open File", function(filePath) {
            if(filePath) {
                var fileName = filePath.split("/");
                var ttl = fileName[fileName.length-1];
                lx = _link("modules/cmsEditor") + "&type=edit&src=" + encodeURIComponent(filePath);
                openLinkFrame(ttl, lx);
            }
        });
  });
  
  $(".sidebarMenu .panel .panel-body a").click(function() {
      $("body").removeClass("no_sidebar");
  });

  $('[data-toggle="tooltip"]').tooltip();

  $('#sidebarMenuTree').metisMenu();
  
  if($("#sidebarAccordion").length>0) {
        $("body").addClass("opend");
        
        $("body").delegate("#sidebarAccordion .mainTitle a[aria-expanded='true']", "click", function(e){
                setTimeout(function () {
                    $("body").removeClass("opend")
                }, 400);
            });
            
        $("body").delegate("#sidebarAccordion .mainTitle a[aria-expanded='false']", "click", function(e){
            $("body").addClass("opend")
        });
    }
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

function openEStore() {
    openLinkFrame("eStore", _link("modules/estore"), true);
}
function openCMSTodos() {
    openLinkFrame("Todos", _link("modules/devTodos"), true);
}
function openCodeSearch() {
    openLinkFrame("Search", _link("modules/codeSearch"), true);
}

//Tools Section
function loadStudioTools() {
    if($("#runToolsMenu .submenu").children().length>0) return;
    
    $("#runToolsMenu .submenu").html("<div class='ajaxloading ajaxloading8'></div>");
    
    processAJAXQuery(_service("studiotools", "list"), a=>{
        $("#runToolsMenu .submenu").html("");
        TOOLS_LIST = a.Data;
        $.each(a.Data, function(k, row) {
            if(row.icon==null) row.icon = "fa fa-star";
            if(row.type=="divider") {
                $("#runToolsMenu .submenu").append(`<hr>`);
                return;
            }
            if(row.href!=null) {
                if(row.counter!=null) {
                    $("#runToolsMenu .submenu").append(`<li data-refid='${k}'><a href='#'><i class='${row.icon}'></i> ${row.label} <label class='label label-info pull-right'>${row.counter}</label></a></li>`);
                } else {
                    $("#runToolsMenu .submenu").append(`<li data-refid='${k}'><a href='#'><i class='${row.icon}'></i> ${row.label}</a></li>`);
                }
            } else {
                if(row.counter!=null) {
                    $("#runToolsMenu .submenu").append(`<li data-refid='${k}'><a href='#'><i class='${row.icon}'></i> ${row.label} <label class='label label-info pull-right'>${row.counter}</label></a></li>`);
                } else {
                    $("#runToolsMenu .submenu").append(`<li data-refid='${k}'><a href='#'><i class='${row.icon}'></i> ${row.label}</a></li>`);
                }
            }
        });

        $("#runToolsMenu .submenu li[data-refid]").click(function(e) {
            var refid = $(this).data("refid");
            
            if(refid==null) {
                return;
            }
            
            if(TOOLS_LIST[refid].type == null || TOOLS_LIST[refid].type == null) {
                console.info("TOOL Params Not Found", TOOLS_LIST[refid]);
                return;
            }
            
            switch(TOOLS_LIST[refid].type) {
                case "method":
                    if(TOOLS_LIST[refid].src != null && window[TOOLS_LIST[refid].src] != null && typeof window[TOOLS_LIST[refid].src] == "function") {
                        window[TOOLS_LIST[refid].src](this);
                    } else {
                        console.info("TOOL Method Src Not Found", TOOLS_LIST[refid]);
                    }
                    break;
                case "ctrl":
                    processAJAXPostQuery(_service("studiotools", "run_ctrl"), "cmd="+TOOLS_LIST[refid].src, function(ans) {
                        if(ans.Data!=null && typeof ans.Data=="string") lgksAlert(ans.Data);
                        else lgksToast("Successfully Ran Tool - "+TOOLS_LIST[refid].src);
                        
                        console.log("SUCCESSFULL_RUN_CTRL", TOOLS_LIST[refid].src, ans.Data);
                    }, "json");
                    break;
                case "package":
                    processAJAXPostQuery(_service("studiotools", "run_package"), "cmd="+TOOLS_LIST[refid].src, function(ans) {
                        if(ans.Data!=null && typeof ans.Data=="string") lgksAlert(ans.Data);
                        else lgksToast("Successfully Ran Tool - "+TOOLS_LIST[refid].src);
                        
                        console.log("SUCCESSFULL_RUN_PACKAGE", TOOLS_LIST[refid].src, ans.Data);
                    }, "json");
                    break;
                case "ajax":
                    var src = TOOLS_LIST[refid].src.split(".");
                    if(src[1]==null) src[1] = "";
                    processAJAXQuery(_service(src[0], src[1]), function(ans) {
                        if(ans.Data!=null && typeof ans.Data=="string") lgksAlert(ans.Data);
                        else lgksToast("Successfully Ran Tool - "+TOOLS_LIST[refid].src);
                        
                        console.log("SUCCESSFULL_RUN", TOOLS_LIST[refid].src, ans.Data);
                    }, "json");
                    break;
                default:
                    lgksToast("TOOL_TYPE Not Supported Yet : "+ TOOLS_LIST[refid].type);
            }
        });
    }, "json")
}
function refreshStudioTools() {
    $("#runToolsMenu .submenu").html("");
    loadStudioTools();
}

function toolsClearCache() {
    var counter = 0;
    $.each(["configs","cache","templates","appcache"], function(k, src) {
        processAJAXQuery(_service("cleaner","PURGE:"+src.toUpperCase()),function(data) {
            counter++;
    // 		if(data.Data=="done") {
    // 			lgksToast("All "+src+" cache cleared.");
    // 		} else {
    // 			lgksToast(data.Data);
    // 		}
    		if(counter>=4) {
    		    lgksToast("All Cache is cleared");
    		}
    	},"json");    
    })
}