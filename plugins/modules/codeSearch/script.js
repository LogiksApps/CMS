var startTime = 0;
$(function() {
    resetSearch();
    
    $("#searchTextField").keyup(function(e) {
        e.preventDefault();
        
        if(e.keyCode==13) {
            if(this.value==null || this.value.length<=0) {
          lgksToast("Please type something to search");
          return;
        }
        
        //   lx=_link("modules/codeSearch")+"&query="+term;
        //   top.openLinkFrame("Search : "+term,lx);
            searchCode($("#searchTextField").val());
        }
        
        return false;
      });
    $("#searchResults").delegate("a","click", function(e) {
        return openCodeLink(this);
    })
    
    $("input[name=filters]")[1].checked=true;
    $("#searchTextField").val(firstSearchTerm);
    
    if($("#searchTextField").val()!=null && $("#searchTextField").val().length>0) {
        searchCode($("#searchTextField").val());
    }
});

function openCodeLink(src) {
    href=$(src).attr("href");
    if(href.length<3) return false;
    
	txt=$(src).text();
	txt=txt.split("/");
	txt=txt[txt.length-1];
    if(href.substr(0,8)=="https://" || href.substr(0,7)=="http://" || href.substr(0,5)=="ftp://") {
		return true;
	} else {
		href=href.split("@");
		lx=_link(href[0])+href[1];
		lx=lx.replace("?&","?");
		parent.openLinkFrame(txt,lx);
	}
    
    return false;
}

function searchCode(term) {
    if(term==null || term.length<=0) {
        lgksToast("What do you intend to search");
        return;
    }
    if($("input[name=filters]:checked").length<=0) {
        lgksToast("Please select a search source");
        return;
    }
    if($("input[name=lang]:checked").length<=0) {
       $("input[name=lang][value='*']")[0].checked=true 
    }
    
    q=[];q1=[];
    
    q0=[];
    $("input[name=filters]:checked").each(function() {q0.push(this.value);});
    q.push("filters="+q0.join(","));
    
    q0=[];
    $("input[name=lang]:checked").each(function() {q0.push(this.value);});
    q.push("lang="+q0.join(","));
    
    if($("#repo").length>0) {
        q.push("repo="+$("#repo").val());
    }
    
    if($("#type").length>0) {
        term=$("#type").val()+" "+term;
    }
    
    $("#searchInfo").html('Searching for <strong class="text-danger">'+term+'</strong>');
    $("#searchResults").html("<div><div class='ajaxloading ajaxloading5'></div></div>");
    $(".searchResultContainer").removeClass("hidden");
    startTime = new Date().getTime();
    
    //"&filters="+q.join(",")+"&lang="+q2.join(",")+q1
    processAJAXPostQuery(_service("codeSearch","search"),"term="+term+"&"+q.join("&"), function(data) {
        requestTime = (new Date().getTime() - startTime)/1000;
        
        template = Handlebars.compile($("#search-template").html());
        if(data.Data.results!=null) {
            if(data.Data.results.length>0) {
                $("#searchResults").html(template({"RESULTS":data.Data.results}));
            } else {
                $("#searchResults").html("<h2 align=center>Nothing found ...</h2>");
            }
            $("#searchInfo").html('<strong class="text-danger">'+data.Data.max+'</strong> results were found for the search for <strong class="text-danger">'+
                    term+'</strong> <b>'+requestTime+' secs</b>');
        } else if(data.Data.error!=null && data.Data.error.length>0) {
            $("#searchResults").html("<h2 align=center>"+data.Data.error+"</h2>");
            $("#searchInfo").html('Try again ...');
        } else {
            $("#searchResults").html("<h2 align=center>Error searching specified term</h2>");
            $("#searchInfo").html('Try again ...');
        }
    },"json");
}

function resetSearch() {
    $(".searchResultContainer").addClass("hidden");
    $("#searchTextField").val("");
    $("#searchInfo").html("");
    $("#searchResults").html("");
}