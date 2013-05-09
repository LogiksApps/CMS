allViews=["thumbs","details"];
currentView=0;
limit=10;
currentIndex=0;
maxPhotos=limit;
currentMediaID=-1;

multiSelect=true;

var resizeMediaTimer=null;
function resizeMediaUI() {
	$("#photoGallery").css("width",(130*3+30));
	$("#photoViewer").css("width",($(window).width()-$("#photoGallery").width()-20));
}
$(function() {
	$(window).bind('resize', function() {
		if (resizePageTimer) {
			clearTimeout(resizePageTimer);
		}		
		resizeMediaTimer=setTimeout(resizeMediaUI, 100);
	});
	resizeMediaTimer=setTimeout(resizeMediaUI, 100);
	$("#photoGallery").delegate(".mediaholder","click",function() {
			id=$(this).attr("rel");
			$("#photoGallery .current").removeClass("current");
			$(this).addClass("current");
			viewMedia(id);
			if(multiSelect) {
				$(this).toggleClass("selectedItem");
			} else {
				$("#photoGallery .selectedItem").removeClass("selectedItem");
				$(this).addClass("current");
			}
		});
	$("#photoGallery").delegate(".infobtn","click",function() {
			lgksAlert($(this).next(".infomsg").html());
		});
	
	$("#searchfield").keypress(function(event) {
			if(event.charCode==13) {
				txt=$("#searchfield").val();
				if(txt.length>0) {
					refreshView();
				}
			}
		});
	
	$("#toolbar .right").css("width","320px");
	$("#toolbar .right").css("padding","0px");
	$("#toolbar .right").html($("#navigator").html());
	
	l=getCMD()+"&action=filterlist";//&quick=true";
	$("#fselector").html("<option>Loading ...</option>");
	$("#fselector").load(l,function(){	
		refreshView();
		resetViewer();
	});
});
function getCMD(){
	return getServiceCMD("mediamanager")+"&s="+site;
	//return "services/?scmd=mediamanager&s="+site;
}
function toggleChecked(checkId) {
	$(checkId).each( function() {
		this.checked=!this.checked;
	})
}
function toggleSelectAll() {
	if($("#photoGallery .mediaPane.active .mediaholder.selectedItem").length<=0) {
		$("#photoGallery .mediaPane.active .mediaholder").addClass("selectedItem");
	} else {
		$("#photoGallery .mediaPane.active .mediaholder").removeClass("selectedItem");
	}
}
function resetViewer() {
	$("#viewer").html("");
	$("#viewer").addClass("nophoto");
	currentMediaID=-1;
}
function refreshView() {
	l=getCMD()+"&action=count&src="+$("#fselector").val();
	processAJAXQuery(l, function(data) {
			if(!isNaN(data)) {
				maxPhotos=data;
			} else {
				maxPhotos=0;
			}
			$("#mediaMaxBtn").html(maxPhotos);
			$("#go1, #go2, #go3, #go4").button("enable");
			if(currentIndex<=0) {
				$("#go1, #go2").button("disable");
			} else if(currentIndex>=maxPhotos-limit) {
				$("#go3, #go4").button("disable");
			}
		});
	
	l=getCMD()+"&action=viewthumbs&src="+$("#fselector").val()+"&limit="+limit+"&index="+currentIndex;
	if($("#searchfield").val().length>0) {
		l+="&txt="+$("#searchfield").val();
	}
	loadCatalogViews(l);
}
function loadCatalogViews(l) {
	$("#photoGallery .mediaPane").removeClass("active");
	if(allViews[currentView]=="thumbs") {
		l+="&viewtype=thumbs";
		$("#thumbsview").html("<div class='ajaxloading'>Loading Media List ...<div>");
		$("#thumbsview").load(l,function() {
				$("#thumbsview li.thumbnail").addClass("ui-state-default");
				//$('#thumbsview a').lightBox();
				//$( "#selectable" ).selectable();
		});
		$("#gallery").addClass("active");
	} else if(allViews[currentView]=="details") {
		l+="&viewtype=details";
		$("#tableview tbody").html("<tr><td colspan=100><div class='ajaxloading'>Loading Media List ...<div></td></tr>");
		$("#tableview tbody").load(l,function() {
				
		});
		$("#tableview").addClass("active");
	}
}
function viewMedia(mediaID) {
	currentMediaID=mediaID;
	if(mediaID<0) {
		resetViewer();
		return;
	}
	lnk=getCMD()+"&action=viewmedia&src="+$("#fselector").val()+"&photo="+mediaID;
	imgHtml="<img src='"+lnk+"' alt='No Media Found' />";
	$("#viewer").html(imgHtml);
	$("#viewer").removeClass("nophoto");
	$("#mediaIDBtn").html(mediaID);
}

//#photoViewer #tools Functions
function updatePreview() {
	photFldDlg.dialog("close");
}
function uploadMedia() {
	lnk="?page=modules&site=cms&mod=photocrop&popup=true&func=updatePreview&src="+$("#fselector").val()+"&rel="+0;
	$("#photoFieldUploaderFrame iframe").attr("src",lnk);
	photFldDlg=lgksPopup("#photoFieldUploaderFrame","Upload Photo",{
					width:600,
					height:550,
					resizable:"none",
					buttons: {
							Close:function() {
								   $(this).dialog("close");
							},
					}
			});
}
function swapMedia(mediaID) {
	if(mediaID==null) mediaID=currentMediaID;
	if(mediaID<0) {
		lgksAlert("No Media Selected To Start Swap.");
		return;
	}
	lnk="?page=modules&mod=photocrop&popup=true&func=updatePreview&src="+$("#fselector").val()+"&rel="+mediaID;
	$("#photoFieldUploaderFrame iframe").attr("src",lnk);
	photFldDlg=lgksPopup("#photoFieldUploaderFrame","Upload Photo",{
					width:600,
					height:550,
					resizable:"none",
					buttons: {
							Close:function() {
								resetViewer();
								refreshView();
								viewMedia(mediaID);
								$(this).dialog("close");
							},
					}
			});
}
function deleteOneMedia(mediaID) {
	if(mediaID==null) mediaID=currentMediaID;
	if(mediaID<0) {
		lgksAlert("No Media Selected To Delete.");
		return;
	}
	lgksConfirm("Do you really want to delete selected Media?","Delete Media ?",function() {
				$("#loadingmsg").show("fast");
				lnk=getCMD()+"&action=delete&src="+$("#fselector").val()+"&photo="+mediaID;	
				processAJAXQuery(lnk,function(txt) {
							if(txt=="ok") {
								resetViewer();
								$("#photoGallery .active .mediaholder[rel="+mediaID+"]").detach();
								$("#mediaMaxBtn").html(maxPhotos-1);
							} else {
								lgksAlert(txt);
							}
						});
			});
}
function downloadMedia(mediaID) {
	if(mediaID==null) mediaID=currentMediaID;
	if(mediaID<0) {
		lgksAlert("No Media Selected To Download.");
		return;
	}
	lnk=getCMD()+"&action=download&src="+$("#fselector").val()+"&photo="+mediaID;
	window.open(lnk,"Download");
}
function getLink(mediaID) {
	if(mediaID==null) mediaID=currentMediaID;
	if(mediaID<0) {
		lgksAlert("No Media Selected To Download.");
		return;
	}
	lnk=getCMD()+"&action=viewmedia&src="+$("#fselector").val()+"&photo="+mediaID;
	html="<textarea style='width:600px;height:100px;resize:none;border:0px;' readonly>"+lnk+"</textarea>";
	lgksAlert(html,"Link To Media");
}

//#toolbar functions
function loadFilterList() {
	l=getCMD()+"&action=filterlist";
	$("#fselector").html("<option>Loading ...</option>");
	$("#fselector").load(l,function(){	
		$('#searchfield').hide('slow');
		$('#searchfield').removeClass('visible');
		$('#searchfield').val('');
		
		refreshView();
		resetViewer();
	});
}
function deleteMedias() {
	if($("#photoGallery .mediaPane.active .mediaholder.selectedItem").length<=0) {
		lgksAlert("No Media Selected");
		return;
	}
	ids=[];
	$("#photoGallery .mediaPane.active .mediaholder.selectedItem").each(function() {
			ids.push($(this).attr("rel"));
		});
	lgksConfirm("Do you really want to delete multiple selected medias ("+ids.length+") ?","Delete Multiple Media",function() {
				$("#loadingmsg").show("fast");
				ids=ids.join(",");
				lnk=getCMD()+"&action=delete&src="+$("#fselector").val()+"&photo="+ids;	
				processAJAXQuery(lnk,function(txt) {
							if(txt=="ok") {
								resetViewer();
								refreshView();
							} else {
								lgksAlert(txt);
							}
						});
			});
}
function changeView() {
	currentIndex=0;
	currentView=currentView+1;
	if(currentView>=allViews.length) currentView=0;
	refreshView();
	$("#viewbtn").attr("title","Current View :: "+allViews[currentView].toUpperCase());
}
function toggleFinder() {
	$('#searchfield').toggle('slow');
	$('#searchfield').toggleClass('visible');
	$('#searchfield').val('');
	if(!$('#searchfield').is('.visible')) {
		currentIndex=0;
		refreshView();
	}
}
/*Navigator Functions*/
function gotoFirst() {
	if(currentIndex==0) {
		return;
	}
	currentIndex=0;
	refreshView();
}
function gotoBack() {
	if(currentIndex<limit) {
		currentIndex=0;
		refreshView();
		return;
	}
	currentIndex=currentIndex-limit;
	if(currentIndex<=0) {
		currentIndex=0;
		return;
	}
	refreshView();
}
function gotoNext() {
	old=currentIndex;
	currentIndex=currentIndex+limit;
	if(currentIndex>maxPhotos) {
		currentIndex=old;
		return;
	}
	refreshView();
}
function gotoLast() {
	if(currentIndex>=maxPhotos-limit) {
		return;
	}
	currentIndex=maxPhotos-limit;
	refreshView();
}
