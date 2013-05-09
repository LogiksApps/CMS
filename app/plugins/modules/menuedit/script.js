brwsDlg=null;
brwsFld=null;
$(function() {
	$("select:not(multiple)").addClass("ui-state-default ui-corner-all");
	$("#toolbar #generatorBtn").addClass("clr_green");
	
	$("#menutree .mtbody").css("height",($(window).height()-$("#toolbar").height()-$("#menutree .mtheader").height()-50)+"px");
	$("#menuform .mtbody").css("height",($(window).height()-$("#toolbar").height()-$("#menutree .mtheader").height()-50)+"px");
	
	$("#allmenus").delegate(".toolicon","click",function(event,ui) {
			$("#allmenus .slidewindow").slideUp("fast");
			if($(this).hasClass("qediticon_down")) {
				$(this).parent("a").find(".slidewindow").slideDown("fast");
				$(this).removeClass("qediticon_down");
				$(this).addClass("qediticon_up");
			} else if($(this).hasClass("qediticon_up")) {
				$(this).parent("a").find(".slidewindow").slideUp("fast");
				$(this).removeClass("qediticon_up");
				$(this).addClass("qediticon_down");
			} else if($(this).hasClass("trashicon")) {
				id=$(this).parent("a").attr("rel");
				name=$(this).parent("a").attr("name");
				deleteMenuItem(id,name);
			} else if($(this).hasClass("editicon")) {
				id=$(this).parent("a").attr("rel");
				name=$(this).parent("a").attr("name");
				editMenuItem(id,name);
			} else if($(this).hasClass("viewicon")) {
				name=$(this).parent("a").attr("name");
				lnk=$(this).parents("a").find("input[name=link]").val();
				previewLink(lnk,name);
			}
		});
	$("#allmenus").delegate(".smallbtn","click",function(event,ui) {
			panel=$(this).parents("div.slidewindow");
			if($(this).hasClass("reset")) {
				panel.find("input,select,textarea").each(function() {
						$(this).val($(this).attr('value'));
					});
			} else if($(this).hasClass("save")) {
				quickSave(panel);
			}
		});
	$("#menuitemform").delegate(".formMiniBtn","click",function() {
			if($(this).hasClass("iconSelector")) {
				brwsFld=$(this).parents("tr").find("input");
				baseDir="media/";
				url="index.php?page=modules&mod=fileselectors&popup=direct&type=Others&action=js&func=closeBrowser&baseDir="+baseDir;
				brwsDlg=lgksOverlayFrame(url,"Browse");
			} else if($(this).hasClass("categorySelector")) {
				brwsFld=$(this).parents("tr").find("input");
				jqPopupDiv("#categorySuggestor").dialog({
						resizable:false,
						buttons:{
							Close:function() {
								$(this).dialog("close");
							},
							Select:function() {
								brwsFld.val($("#categorySuggestor select").val());
								$(this).dialog("close");
							}
							
						}
					});
			} else if($(this).hasClass("linkSelector")) {
				brwsFld=$(this).parents("tr").find("input");
				jqPopupDiv("#linkSuggestor").dialog({
						resizable:false,
						buttons:{
							Close:function() {
								$(this).dialog("close");
							},
							Select:function() {
								brwsFld.val($("#linkSuggestor select").val());
								$(this).dialog("close");
							}
							
						}
					});
			} else if($(this).hasClass("classSelector")) {
				brwsFld=$(this).parents("tr").find("input");
				jqPopupDiv("#classSuggestor").dialog({
						resizable:false,
						buttons:{
							Close:function() {
								$(this).dialog("close");
							},
							Select:function() {
								brwsFld.val($("#classSuggestor select").val());
								$(this).dialog("close");
							}
							
						}
					});
			}
		});
	$("#menuitemform").delegate("tr.showMore","click",function() {
			$("#menuitemform tr.more").show("fade","slow");
			$("#menuitemform tr.showMore").addClass("showLess");
			$("#menuitemform tr.showMore").removeClass("showMore");
		});
	$("#menuitemform").delegate("tr.showLess","click",function() {
			$("#menuitemform tr.more").hide("fade","slow");
			$("#menuitemform tr.showLess").addClass("showMore");
			$("#menuitemform tr.showLess").removeClass("showLess");
		});
	$("#menuform select[name=privilege]").multiselect({
					minWidth:300,
					header:"Select Privileges"
				});
	loadMenuGroup($('#menuselector').val());
	loadGenerator();
});
function closeBrowser(fl) {
	if(fl.indexOf(basePath)==0) {
		fl=fl.substr(basePath.length);
	}
	if(brwsDlg!=null) brwsDlg.dialog("close");
	if(brwsFld!=null) brwsFld.val(decodeURIComponent(fl));
}
function refreshTree(treeID) {
	$(treeID).find("h2>a").addClass("clr_green ui-widget-header ui-corner-all");
	$(treeID).find("li>a").addClass("clr_skyblue ui-widget-header ui-corner-all");
	
	$(treeID).find("a").each(function() {
			if($(this).find(".slidewindow").length>0) {
				$(this).css("opacity","0.8");
				$(this).prepend("<div class='toolicon viewicon' title='Preview Link'></div>");
				$(this).prepend("<div class='toolicon editicon menuItemEditIcon' title='Edit Menu Item'></div>");
				$(this).prepend("<div class='toolicon trashicon' title='Delete Menu Item'></div>");
				$(this).prepend("<div class='toolicon qediticon_down' title='Quick Edit Menu Item'></div>");
				
				$(this).find("select").each(function() {
						$(this).val($(this).attr('value'));
						$(this).addClass("ui-state-default ui-corner-all");
					});
			}
		});
	/*$(treeID+" ul").sortable({
			placeholder: "clr_yellow ui-corner-all dragItem",
			revert: true,
			connectWith:treeID+" ul",
			dropOnEmpty: true,
			cancel: "h2,.slidewindow",
			handle:"a",
			//tolerance: 'pointer',
			stop: function(event, ui) {
				//ui.item
			},
		});*/
	
	$(treeID+" ul").disableSelection();
	resetMenuItemForm();
	reloadMenuItemForm();
	$("#menuform").css("display","none");
	
	if($("#menuselector").val()!=null) {
		$("#menutree .mtheader h2.menuicon").html("Menu :: "+$("#menuselector").val());
		$("#menuformPlaceHolder").css("display","block");
	} else {
		$("#menuformPlaceHolder").css("display","none");
	}
}
function loadMenuGroup(menu) {
	$("#loadingmsg").show();
	$("#allmenus ul").html("<div class=ajaxloading6>Loading ...<div>");
	l=getCMD()+"&action=menulist&menuid="+menu;
	$("#allmenus ul").load(l,function(txt) {
			$("#loadingmsg").hide();
			refreshTree("#allmenus");
		});
}
function createMenuGroup() {
	lgksPrompt("Please give a new unique name for the MenuGroup?<br/>No blank space please.<br/>Please note that if you don't add new menu items, this group will not exist on next session.","New MenuGroup",null,function(txt) {
			if(txt.length>0) {
				txt=txt.split(" ").join("_");
				$("#menuselector").append("<option value='"+txt+"' style='text-transformation:capitalize;'>"+txt+"</option>");
				$("#menuselector").val(txt);
				$("#allmenus ul").html("");
				refreshTree("#allmenus");
			}
		});
}
function deleteMenuGroup(menu) {
	lgksConfirm("Do you really want to delete MenuGroup :: <b>"+menu+"</b>?<br/>Deleting will remove all MenuItems From Database.","Delete MenuGroup!",function() {
				l=getCMD()+"&action=menudelete&menuid="+menu;
				processAJAXQuery(l,function(data) {
						if(data.length>0) lgksAlert(data);
						else {
							$("#menuselector option[value="+menu+"]").detach();
							loadMenuGroup($("#menuselector").val());
						}
					});
		});
}

function reloadMenuItemForm() {
	menu=$("#menuselector").val();
	l=getCMD()+"&action=menugroups&menuid="+menu;
	$("#loadingmsg").show();
	$("#menuform select[name=menugroup]").html("<option>Loading ...</option>");
	$("#menuform select[name=menugroup]").load(l, function() {
			$(this).val($(this).attr('value'));
			$("#loadingmsg").hide();
		});
	l=getCMD('qtools')+"&action=privilegelist";
	$("#menuform select[name=privilege]").html("<option>Loading ...</option>");
	$("#menuform select[name=privilege]").load(l, function() {
			$(this).prepend("<option value='*'>Everybody</option>");
			$(this).val($(this).attr('*'));
			$(this).multiselect("destroy");
			$(this).multiselect({
					minWidth:300
				});
		});
	l=getCMD()+"&action=linksuggestions";	
	$("#linkSuggestor select").load(l);
	l=getCMD()+"&action=classsuggestions";	
	$("#classSuggestor select").load(l);
	l=getCMD()+"&action=categorysuggestions";	
	$("#categorySuggestor select").load(l);
}
function resetMenuItemForm() {
	$("#menuform").find("input,select,textarea").each(function() {
			$(this).val($(this).attr('value'));
		});
}
function closeMenuForm() {
	resetMenuItemForm();
	$("#menuform").slideUp("fast",function() {
			$("#menuformPlaceHolder").css("display","block");
		});
}
function createMenuItem() {
	resetMenuItemForm();
	$("#menuform  input[name=id]").val(0);
	//reloadMenuItemForm();
	$("#menuform .menuItemHeadIcon").html("MenuItem Editor");
	$("#menuformPlaceHolder").css("display","none");
	$("#menuform").slideDown("fast");
}
function editMenuItem(id,title) {
	menu=$("#menuselector").val();
	l=getCMD()+"&action=itemview&format=json&menuid="+menu+"&itemid="+id;
	$("#loadingmsg").show();
	processAJAXQuery(l,function(data) {
			$("#loadingmsg").hide();
			
			json=$.parseJSON(data);
			if(json!=null) {
				$.each(json,function(k,v) {
						$("#menuform").find("input[name="+k+"],select[name="+k+"]").val(v);
					});
				if(json.isMenuGroup) {
					$("#menuform input#menugroup").get(0).checked=true;
				} else {
					$("#menuform input#menugroup").get(0).checked=false;
				}
				checkMenugroupForm($("#menuform input#menugroup").get(0).checked);
				$("#menuform .menuItemHeadIcon").html("Edit :: "+title);
				$("#menuformPlaceHolder").css("display","none");
				$("#menuform").slideDown("fast");
			} else {
				lgksAlert("Error In Fetching MenuItem Details");
			}
		});
}
function deleteMenuItem(id,title) {
	menu=$("#menuselector").val();
	lgksConfirm("Do you really want to delete MenuItem ::"+title+" ("+id+")?<br/>This will also delete all submenuitems within it.","Delete MenuItem!",function() {
				l=getCMD()+"&action=itemdelete&menuid="+menu+"&itemid="+id;
				processAJAXQuery(l,function(data) {
						if(data.length>0) lgksAlert(data);
						else {
							loadMenuGroup($("#menuselector").val());
						}
					});
		});
}
function previewLink(lnk,name) {
	if(lnk==null || lnk.length<=0) {
		lgksAlert("This MenuItem Has No Assocciated Link.");
		return;
	}
	l=getCMD()+"&action=preview&link="+encodeURIComponent(lnk);
	parent.lgksOverlayFrame(l,"Preview");
}
function saveMenuItem(form,callBack) {
	if($(form+" input[name=title]").val().length<=0) {
		lgksAlert("MenuItem/Group's Title Is Must");
		return;
	}
	if(!$(form+" input#menugroup").is(":checked")) {
		if($(form+" select[name=menugroup]").val().length<=0) {
			lgksAlert("No MenuItem Can have Empty Menu Group");
			return;
		}
	}
	menu=$("#menuselector").val();
	id=$(form+" input[name=id]").val();
	if(id==null) id=0;
	l=getCMD()+"&action=itemsave&menuid="+menu+"&itemid="+id;
	q=[];
	$(form).find("input[name],select[name]").each(function() {
			nm=$(this).attr('name');
			if(nm.length>0) {
				if(nm=="site") nm='appsite';
				q.push(nm+"="+encodeURIComponent($(this).val()));
			}
		});
	if($("#menuform input#menugroup").get(0).checked) {
		q.push("isMenuGroup=true");
	} else {
		q.push("isMenuGroup=false");
	}
	q.push("menuid="+menu);
	$(form).find(">table").hide();
	$(form).append("<div class='ajax ajaxloading3'></div>");
	processAJAXPostQuery(l,q.join("&"),function(txt) {
			$(form).find(">div.ajax").detach();
			$(form).find(">table").show();
			if(txt.length>0) lgksAlert(txt);
			else {
				loadMenuGroup($("#menuselector").val());
				closeMenuForm();
			}
		});
}
function quickSave(panel) {
	if($(panel).find("input[name=title]").val().length<=0) {
		lgksAlert("MenuItem/Group's Title Is Must");
		return;
	}
	menu=$("#menuselector").val();
	id=$(panel).find("input[name=id]").val();
	if(id==null || id<=0) {
		lgksAlert("Can't Edit This Item.");
		return;
	}
	l=getCMD()+"&action=itemsave&menuid="+menu+"&itemid="+id;
	q=[];
	$(panel).find("input[name],select[name]").each(function() {
			nm=$(this).attr('name');
			if(nm.length>0) {
				if(nm=="site") nm='appsite';
				q.push(nm+"="+encodeURIComponent($(this).val()));
			}
		});
	$(panel).find(">table").hide();
	$(panel).append("<div class='ajax ajaxloading3'></div>");
	processAJAXPostQuery(l,q.join("&"),function(txt) {
			if(txt.length>0) lgksAlert(txt);
			$(panel).find(">div.ajax").detach();
			$(panel).find(">table").show();
			$(panel).parents("a").find("span.textspan").html($(panel).find("input[name=title]").val());
		});
}
function toggleGenerators() {
	$("#menuform").hide();
	$("#menutree").hide();
	$("#generatorform").hide();
	$("#menuformPlaceHolder").css("display","none");
	$("#toolbar .menus").hide();
	$("#toolbar .generator").hide();
	if($("#generatorBtn").attr("current")=="menus") {
		$("#generatorBtn").attr("current","generator");
		$("#generatorBtn span div").html("Menus");
		
		$("#generatorform").show();
		
		$("#toolbar .generator").show();
	} else {
		$("#generatorBtn").attr("current","menus");
		$("#generatorBtn span div").html("Generators");
		
		$("#menutree").show();
		
		$("#toolbar .menus").show();
		if($("#menuselector").val()!=null) {
			$("#menuformPlaceHolder").css("display","block");
		}
	}
}
function loadGenerator() {
	$("#generatorTable tbody").html("<tr><td colspan=10 class='ajaxloading6'></td></tr>");
	l=getCMD()+"&action=generators&format=table";
	$("#generatorTable tbody").load(l,function(txt) {
			$("#generatorTable tbody td.editable").editInPlace({
					callback: function(original_element, html, original) { return html;},
					show_buttons:false,
				});
		});
}
function saveGenerator() {
	l=getCMD()+"&action=savesources";
	q="";
	row=0;
	$("#generatorTable tbody tr").each(function() {
			$(this).find("td[name]").each(function() {
					nm=$(this).attr("name");
					rel=$(this).attr("rel");
					v=$(this).text();
					if(nm=="enabled") {
						v=$(this).find("input[type=checkbox]").is(":checked");
					}
					if(v==null || v.length<=0) v=rel;
					q+="menu["+(row)+"]["+nm+"]="+encodeURIComponent(v)+"&";
				});
			row++;
		});
	processAJAXPostQuery(l,q,function(data) {
			if(data.length>2) lgksAlert(data);
		});
}


function checkMenugroupForm(v) {
	if(v) {
		enableFormElement("#menuform select[name=menugroup]",false);
		enableFormElement("#menuform input[name=category]",false);
		enableFormElement("#menuform select[name=blocked]",false,'false');
		enableFormElement("#menuform select[name=onmenu]",false,'true');
		enableFormElement("#menuform select[name=site]",false,'*');
	} else {
		enableFormElement("#menuform select[name=menugroup]",true);
		enableFormElement("#menuform input[name=category]",true);
		enableFormElement("#menuform select[name=blocked]",true,'false');
		enableFormElement("#menuform select[name=onmenu]",true,'true');
		enableFormElement("#menuform select[name=site]",true,'*');
	}
}
function enableFormElement(selector,enable,v) {
	if(v==null) v="";
	if(enable) {
		$(selector).removeAttr('disabled');
		$(selector).removeClass("ui-state-disabled");
		$(selector).parent("td").find(".formMiniBtn").show();
		if(v.length>0) $(selector).val(v);
	} else {
		$(selector).val(v);
		$(selector).attr('disabled','disabled');
		$(selector).addClass("ui-state-disabled");
		$(selector).parent("td").find(".formMiniBtn").hide();
	}
}

