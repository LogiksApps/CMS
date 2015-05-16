var frmMode="insert";
var dateFormat="d/m/yy";
var timeFormat="h:mTT";
var showAMPM=false;
var yearRange='1950:2100';
var datefieldReadonly=true;

var selectedTr=null;
var isPreview=false;
var fieldType=[];
var multiselects=null;
var fieldOldData={};

$(function() {
	multiselects=$("#properties select[multiple]").multiselect({
			selectedList:3,
			minWidth:300,
			position: {
				my: 'left bottom',
				at: 'left top'
			},
			click: function(event, ui){
			  //$callback.text(ui.value + ' ' + (ui.checked ? 'checked' : 'unchecked') );
			  tdIn=getInputBox();
			  if($(this).attr("name")=="fieldproperties" && tdIn!=null) {
				  $(tdIn).removeClass(ui.value);
				  $(tdIn).removeAttr(ui.value);
				  if(ui.checked) {
					  $(tdIn).addClass(ui.value);
				  }
			  }
		   },
		   close:function(event, ui){
			   tdIn=getInputBox();
			   if($(this).attr("name")=="fieldproperties" && tdIn!=null) {
					updateFormUI("#editor");
			   }
		   }
		});
	$("#editor .formTable").delegate("tr","click",function() {
			$("#editor .formTable tr.active").removeClass("active");
			$(this).addClass("active");
			updatePropBox(this);
		});
	$("#editor .formTable").delegate("tr td.dbtns","dblclick",function() {
			closePropertiesWindow(false);
		});
	$("#editor .formTable").delegate(".minibtn","click",function() {
			var row = $(this).parents("tr:first");
			if ($(this).attr("name")=="up") {
				row.insertBefore(row.prev());
				row.addClass('clr_blue').delay(5000).removeClass('clr_blue');;
			} else if ($(this).attr("name")=="down") {
				row.insertAfter(row.next());
			} else if ($(this).attr("name")=="delete") {
				row.detach();
			} else if ($(this).attr("name")=="edit") {
				closePropertiesWindow(false);
			}
		});

	$("#properties #attr select[name=fieldtype] option").each(function() {
			fieldType.push($(this).attr("value"));
		});
	$("#properties #attr select[name=fieldtype]").change(function() {
			sAttrs="";
			tdIn=getInputBox();
			td=null;
			txt="";

			if(tdIn!=null) {
				td=$(tdIn).parent("td.columnInput");

				attrs=$(tdIn).listAttributes();
				$(attrs).each(function(k,v) {
					if(v!="class") sAttrs+=v+"='"+$(tdIn).attr(v)+"' ";
				});
				//lgksAlert(sAttrs);

				tag=$(this).find("option:selected").attr("tag");

				txt=fieldOldData[$(tdIn).attr('name')];
				if(txt==null) txt="";
				if($(tdIn).html().length>0) {
					fieldOldData[$(tdIn).attr('name')]=$(tdIn).html();
					txt=$(tdIn).html();
				}
			} else {
				td=$(selectedTr).find("td.columnInput").get(0);
			}

			if(tag.toLowerCase()=="input") {
				type=$(this).find("option:selected").attr("tagtype");
				s="<input type='"+type+"' class='"+$(this).val()+"' "+sAttrs+" />";
				$(td).html(s);
			} else if(tag.toLowerCase()=="textarea") {
				s="<textarea "+sAttrs+" class='"+$(this).val()+"' >"+txt+"</textarea>";
				$(td).html(s);
			} else if(tag.toLowerCase()=="select") {
				s="<select "+sAttrs+" class='"+$(this).val()+"' >"+txt+"</select>";
				$(td).html(s);
			} else if(tag.toLowerCase()=="editable") {
				s="<div "+sAttrs+" class='editable' />";
				$(td).html(s);
			} else if(tag.toLowerCase()=="div") {
				s="<input "+sAttrs+" class='"+$(this).val()+"' ></dix";
				$(td).html(s);
			}
			updateFormUI("#editor");
		});
	$("#tagpropeditor #attr,#tagpropeditor #evnt").delegate("input",'change',function() {
			updateFieldPropertiesFromBox(this);
		});
});
function updateFieldPropertiesFromBox(ele) {
	if(ele==null) return;
	nm=$(ele).attr('name').replace("field","");
	v=$(ele).val();
	fld=getInputBox();
	if(fld!=null) {
		$(fld).attr(nm,v);
		if(nm=="name") {
			$(fld).attr("id",v);
		}
	}
}
function resetFormUI(frmID) {
	$(frmID+" td.columnInput div.field_required").detach();
	$(frmID+" td.columnInput div.field_autocomplete").detach();
	$(frmID+" td.columnInput div.field_unique").detach();
}
function updateFormUI(frmid) {
	$("#loadingmsg").show();

	resetFormUI(frmid);

	$(frmid+" .formTable .formheader").addClass("ui-widget-header");//ui-widget-header,ui-state-default,ui-state-active
	$(frmid+" .formTable .formsubheader").addClass("ui-state-active");//ui-widget-header,ui-state-default,ui-state-active
	$(frmid+" .formTable .formfooter").addClass("clr_pink");//ui-widget-header,ui-state-default,ui-state-active
	$(frmid+" .pane").addClass("ui-widget-content");

	$(frmid+" .formTable").css("margin","auto");

	$(frmid+" .accordion").accordion({
				fillSpace: true
			});
	$(frmid+" .tabs").tabs();

	$(frmid+" .datetimefield").each(function() {
			$(this).datetimepicker({
					timeFormat:timeFormat,
					separator:' ',
					ampm:showAMPM,
					changeMonth:true,
					changeYear:true,
					showButtonPanel:true,
					yearRange:yearRange,
					dateFormat:dateFormat,
				});
		});
	$(frmid+" .datefield").each(function() {
			$(this).datepicker({
					changeMonth: true,
					changeYear: true,
					showButtonPanel: false,
					yearRange: yearRange,
					dateFormat:dateFormat,
				});
		});
	$(frmid+" .timefield").timepicker({
				timeFormat:timeFormat,
				ampm:showAMPM,
			});

	$(frmid+" button:not(.nostyle)").button();
	$(frmid+" select:not(.nostyle)").addClass("ui-state-default ui-corner-all");
	$(frmid+" select[multiple]").removeClass("ui-state-default");
	$(frmid+" select[size]").removeClass("ui-state-default");

	$("#editor .formTable tbody").tableDnD();

	//$(frmid+" input:file").uniform();

	if(datefieldReadonly) {
		$(frmid+" .datetimefield, "+frmid+" .datefield, "+frmid+" .timefield").attr("readonly","readonly");
	}

	/*$(frmid+" .progressbar").each(function() {
			x=0;
			if($(this).attr("value")!=null) x=parseInt($(this).attr("value"));
			$(this).progressbar({
				value:x,
			});
		});*/

	$(frmid+" .slider").each(function() {
			min1=0;
			max1=100;
			val=0;
			forWhom="";

			if($(this).attr("min")!=null) min1=parseInt($(this).attr("min"));
			if($(this).attr("max")!=null) max1=parseInt($(this).attr("max"));
			if($(this).attr("value")!=null) val=parseInt($(this).attr("value"));

			$(this).slider({
					min:min1,
					max:max1,
					value:val,
					orientation:"horizontal",
					range: "min",
					animate: true,
					slide: function( event, ui ) {
						if($(this).attr("for")!=null) {
							$($(this).attr("for")).val(ui.value);
							$($(this).attr("for")).html(ui.value);
						}
					}
				});
		});

	/*$(frmid+' .tagfield').each(function() {
			sf=true;
			as=false;
			if($(this).attr("singleField")!=null) sf=$(this).attr("singleField");
			if($(this).attr("allowSpaces")!=null) as=$(this).attr("allowSpaces");
			$(this).removeClass("tagfield");
			$(this).tagit({
				singleField:sf,
				allowSpaces:as,
			});
		});*/

	loadFormPlugins(frmid);
	loadEditorAttrs(frmid);

	$("#loadingmsg").hide();
}
function loadFormPlugins(frmID) {
	$(frmID+" .readonly").attr("readonly","readonly");
	$(frmID+" .disabled").attr("disabled","disabled");
	$(frmID+" .multiple").attr("multiple","multiple");

	if($(frmID+" .required").parents("td").find(".field_required").length<=0)
		$(frmID+" .required").parents("td").append("<div style='float:right;' class='field_required' title='Is A Required Field'></div>");
	if($(frmID+" .autocomplete").parents("td").find(".field_autocomplete").length<=0)
		$(frmID+" .autocomplete").parents("td").append("<div style='float:right;' class='field_autocomplete' onclick='listAutoCompleteField(this)' title='Autocomplete Supported'></div>");
	if($(frmID+" .unique").parents("td").find(".field_unique").length<=0)
		$(frmID+" .unique").parents("td").append("<div style='float:right;' class='field_unique' onclick='checkUnique(this)' title='Must Be Unique'></div>");
}
function loadEditorAttrs(frmID) {
	$(frmID+" td.columnName,"+frmID+" td.columnEqual,"+frmID+" .editable,"+frmID+" .formheader strong,"+frmID+" .formfooter strong").editInPlace({
			callback: function(original_element, html, original) { return html;},
			show_buttons:false,
			use_html:true,
			event:"dblclick",
		});

	setupRowTools(frmID,true);
}
function setupRowTools(frmID,setup) {
	if(setup) {
		btns=getRowToolButtons();
		$(frmID+" tbody tr").each(function() {
				if($(this).find("td.dbtns").length>0) {
					$(this).find("td.dbtns").html(btns);
				} else {
					$(this).prepend("<td class='dbtns' width=80px valign=top>"+btns+"</td>");
				}
			});
	} else {
		$(frmID+" tbody tr td.dbtns").detach();
	}
}
function getRowToolButtons() {
	btns="";
	btns+="<div name='delete' class='minibtn minibtndelete' ></div>";
	btns+="<div name='up' class='minibtn minibtn_up' ></div>";
	btns+="<div name='down' class='minibtn minibtn_down' ></div>";
	btns+="<div name='edit' class='minibtn minibtnedit' ></div>";
	return btns;
}
function showPreview(enable) {
	frmid="#editor";
	if(enable=="toggle") {
		$(frmid+" .formTable").toggleClass("ui-widget-content");
		$(frmid+" .formTable").toggleClass("debug");
	} else if(enable) {
		$(frmid+" .formTable").removeClass("debug");
		$(frmid+" .formTable").addClass("ui-widget-content");
	} else {
		$(frmid+" .formTable").removeClass("ui-widget-content");
		$(frmid+" .formTable").addClass("debug");
	}
	if($(frmid+' .formTable .photofield').length>1) {
		error="More then one photos are not allowed in a single form.<br/>";
	}
	/*isPreview=!$(frmid+" .formTable").hasClass("debug");
	if(isPreview) {
		closePropertiesWindow(true);


		else lgksAlert("Form Seems To Be Alright.");
	}*/
	error=checkFormForErrors();
	if(error.length>0) {
		lgksAlert(error);
		return;
	}
	if(enable) {
		l=getCMD("lists")+"&action=preview";
		parent.lgksOverlayFrame(l,"Preview !");
	}
}
function checkFormForErrors() {
	error="";
	if($(frmid+' .formTable .photofield').length>1) {
		error="More then one photos are not allowed in a single form.<br/>";
	}
	return error;
}
function getInputBox() {
	if(selectedTr==null) return null;

	tdIn=$(selectedTr).find("td.columnInput").children()[0];
	if(tdIn!=null) {
		if(tdIn.tagName=="DIV" && $(tdIn).hasClass("uploader")) {
			tdIn=$(tdIn).find("input");
			tdIn.tagName="INPUT";
		}
	}
	return tdIn;
}
function updatePropBox(tr) {
	selectedTr=null;
	if(tr==null) {
		$("#properties input").val("");
		$("#properties input").attr("disabled",'true');
		$("#properties textarea").attr("disabled",'true');
		$("#properties select").attr("disabled",'true');
		$("#properties #attr select[name=fieldtype]").val("custom");

		multiselects.multiselect('disable')
		return;
	}
	if($(tr).find("td.columnInput").length>0) {
		$("#properties input").removeAttr("disabled");
		$("#properties textarea").removeAttr("disabled");
		$("#properties select").removeAttr("disabled");

		multiselects.multiselect('enable');

		selectedTr=tr;
		tdIn=getInputBox();

		claz=$(tdIn).attr("class").split(" ");

		attrs=$(tdIn).listAttributes();
		noAttr=['id','name','class','style','type','src','title','onchange','onfocus','onblur','onkeydown','onkeyup','onclick','readonly','disabled'];
		attrsFinal=[];
		$(attrs).each(function(k,v) {
				if($.inArray(v, noAttr)<0) {
					x=v+"='"+$(tdIn).attr(v)+"'";
					attrsFinal.push(x);
				}
			});
		$("#properties input[name=fieldattrs]").val(attrsFinal.join(" "));

		$("#properties input[name=fieldsrc]").val($(tdIn).attr("src"));
		$("#properties input[name=fieldname]").val($(tdIn).attr("name"));
		$("#properties input[name=fieldstyle]").val($(tdIn).attr("style"));
		$("#properties input[name=fieldtitle]").val($(tdIn).attr("title"));

		$("#properties #evnt input[name=onchange]").val($(tdIn).attr("onchange"));
		$("#properties #evnt input[name=onfocus]").val($(tdIn).attr("onfocus"));
		$("#properties #evnt input[name=onblur]").val($(tdIn).attr("onblur"));
		$("#properties #evnt input[name=onkeydown]").val($(tdIn).attr("onkeydown"));
		$("#properties #evnt input[name=onkeyup]").val($(tdIn).attr("onkeyup"));
		$("#properties #evnt input[name=onclick]").val($(tdIn).attr("onclick"));

		$("#properties #attr select[name=fieldproperties]").multiselect("widget").find(":checkbox").each(function() {
						if(this.checked==true) {
							this.click();
						}
						if($.inArray($(this).val(),claz)>=0) {
							this.click();
						}
					});
		$("#properties #fieldhtmlcode").hide();
		$("#properties #attr select[name=fieldtype]").val('textfield');
		if(tdIn.tagName=="INPUT") {
			runSearch=true;
			$(fieldType).each(function(k,v) {
					if(!runSearch) return;
					if($(tdIn).hasClass(v)) {
						$("#properties #attr select[name=fieldtype]").val(v);
						//runSearch=false;
					}
				});
		} else if(tdIn.tagName=="SELECT") {
			$("#properties #attr select[name=fieldtype]").val('select');
			fieldType=['select','dbselect','dbcolselect','spselect','lookupselect','xmlselect','phpselect'];

			runSearch=true;
			$(fieldType).each(function(k,v) {
					if(!runSearch) return;
					if($(tdIn).hasClass(v)) {
						$("#properties #attr select[name=fieldtype]").val(v);
						runSearch=false;
					}
				});
			$("#properties #fieldhtmlcode").show();

			$("#properties #advn select[name=bgclass]").val($(tdIn).parents("tr").attr("class").replace("active","").trim());
		} else if(tdIn.tagName=="TEXTAREA") {
			$("#properties #attr select[name=fieldtype]").val('textarea');
			$("#properties #fieldhtmlcode").show();
		} else if(tdIn.tagName=="DIV") {
			//'slider','stars','checklist','radiolist'
			$("#properties #fieldhtmlcode").show();
		} else if(tdIn.tagName=="BUTTON") {

		}
	} else {
		$("#properties input").val("");
		$("#properties input").attr("disabled",'true');
		$("#properties textarea").attr("disabled",'true');
		$("#properties select").attr("disabled",'true');
		$("#properties #attr select[name=fieldtype]").val("custom");

		$("#properties .forAllRows").removeAttr("disabled");

		multiselects.multiselect('disable')
	}
}
function createInputForType(ele,val) {
	name=ele.text().trim();
	type=ele.attr('type').toLowerCase().trim();
	valD=ele.attr('default').trim();
	nullable=ele.attr('nullable').toLowerCase().trim();
	btype=ele.attr('btype').toLowerCase().trim();

	if(val==null || val.length==0) val=valD;

	s="<input name='"+name+"' class='textfield' type='text' id='"+name+"' value='"+val+ "' />";

	if(btype=="date") {
		s="<input name='"+name+"' class='datefield' type='text' id='"+name+"' value='"+val+"' />";
	} else if(btype=="datetime") {
		s="<input name='"+name+"' class='datetimefield' type='text' id='"+name+"' value='"+val+"' />";
	} else if(btype=="time") {
		s="<input name='"+name+"' class='timefield' type='text' id='"+name+"' value='"+val+"' />";
	} else if(btype=="blob") {
		if(type=="text") {
			s="<textarea name='"+name+"' class='textareafield' id='"+name+"'>"+val+"</textarea>";
		} else {
			s="<b>"+name+"</b> Is Not Editable ...";
		}
	}

	else if(btype=="int") {
		if(type.trim()=="tinyint(1)") {
			s="<input name='"+name+"' class='checkbox' type='checkbox' id='"+name+"' value='"+val+"' />";
		} else {
			s="<input name='"+name+"' class='numberfield' type='text' id='"+name+"' value='"+val+"' />";
		}
	} else if(btype=="real" || btype=="float" || btype=="double") {
		s="<input name='"+name+"' class='numberfield' type='text' id='"+name+"' value='"+val+"' />";
	}

	else if(btype=="string") {
		if(valD=="true" || valD=="false") {
			s="<select name='"+name+"' class='select' id='"+name+"' >";
			if(val.toLowerCase()=="true") s+="<option value='true' selected>True</option>";
			else s+="<option value='true'>True</option>";
			if(val.toLowerCase()=="false") s+="<option value='false' selected>False</option>";
			else s+="<option value='false'>False</option>";
			s+="</select>";
		} else if(type.indexOf("enum")==0) {
			r=type.trim().replace("enum(","");
			r=r.trim().replace(")","");
			r=r.trim().replace(/'/gi,"");
			r=r.split(",");

			s="<select name='"+name+"' class='select' id='"+name+"' >";
			for(i=0;i<r.length;i++) {
				if(r[i].trim().length>0) {
						s+="<option value='"+r[i]+"'>"+r[i]+"</option>";
				}
			}
			s+="</select>";
		} else if(type=="varchar(5)") {
			s="<select name='"+name+"' class='select' id='"+name+"' >";
			s+="<option value='true'>True</option>";
			s+="<option value='false'>False</option>";
			s+="</select>";
		} else {
			if(name.indexOf("mail")>=0) {
				s="<input name='"+name+"' class='emailfield' type='text' id='"+name+"' value='"+val+"' />";
			} else if(name.indexOf("phone")>=0) {
				s="<input name='"+name+"' class='phonefield' type='text' id='"+name+"' value='"+val+"' />";
			} else if(name.indexOf("mobile")>=0) {
				s="<input name='"+name+"' class='mobilefield' type='text' id='"+name+"' value='"+val+"' />";
			} else if(name.indexOf("creditcard")>=0 || name.indexOf("paymentcard")>=0) {
				s="<input name='"+name+"' class='creditcardfield' type='text' id='"+name+"' value='"+val+"' />";
			} else if(name.indexOf("currency")>=0 || name.indexOf("money")>=0) {
				s="<input name='"+name+"' class='currencyfield' type='text' id='"+name+"' value='"+val+"' />";
			} else if(name.indexOf("calc")>=0) {
				s="<input name='"+name+"' class='calculatorfield' type='text' id='"+name+"' value='"+val+"' />";
			} else if(name.indexOf("calc")>=0) {
				s="<input name='"+name+"' class='barcodefield' type='text' id='"+name+"' value='"+val+"' />";
			}

			else if(name.indexOf("file")>=0) {
				s="<input name='"+name+"' class='filefield' type='file' id='"+name+"' value='"+val+"' />";
			}
			else if(name.indexOf("photo")>=0) {
				//s="<input name='"+name+"' class='photofield' type='file' id='"+name+"' value='"+val+"' />";
			}

			else if(name.indexOf("url")>=0) {
				s="<input name='"+name+"' class='urlfield' type='text' id='"+name+"' value='"+val+"' />";
			} else if(name.indexOf("tags")>=0) {
				s="<input name='"+name+"' class='tagfield' type='text' id='"+name+"' value='"+val+"' />";
			} else if(name.indexOf("password")>=0 || name.indexOf("pwd")>=0) {
				s="<input name='"+name+"' class='password' type='password' id='"+name+"' value='' />";
			}
		}
	}

	if(s==null || s.trim().length<=0) {
		s="<input name='"+name+"' class='textfield' type='text' id='"+name+"' value='"+val+"' />";
	}
	return s;
}
