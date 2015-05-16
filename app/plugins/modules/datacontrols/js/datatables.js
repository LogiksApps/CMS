var sqlOpts="";
var attemptRectify=false;
var ColLimit=-1;
$(function() {
	$("#designer .editortable").delegate(".minibtn","click",function() {
			var row = $(this).parents("tr:first");
			if ($(this).attr("name")=="up") {
				row.insertBefore(row.prev());
				row.addClass('clr_blue').delay(5000).removeClass('clr_blue');;
			} else if ($(this).attr("name")=="down") {
				row.insertAfter(row.next());
			} else if ($(this).attr("name")=="delete") {
				row.detach();
				updateDatatableTableField();
			}
		});
	$("#designer").delegate("select.wtables","change",function() {
			updateRelativeColumnSelector(this);
		});
});
function updateDatatableTableField() {
	s="";
	$("#datatable_col_details").find("td.table").each(function() {
			if(!$(this).hasClass("func")) {
				if(s.indexOf($(this).text()+",")<0)
					s+=$(this).text()+",";
			}
		});
	if(s!=$("#datatable_tables").val()) {
		$("#datatable_tables").val(s);
		updateSelector("#datatable_where_details tbody .wtables",getActiveTablesList());
	}
}
function rectifyError() {
	lastTable=$("#datatable_tables").val();
	if(lastTable!=null) {
		lastTable=lastTable.split(",");
		tblCnt=lastTable.length;
		lastTable=lastTable[0].trim();
	}
	recified=false;
	$("#datatable_col_details tr").each(function() {
			td=$(this).find("td.table");
			if(td.is(":empty")) {
				td.text(lastTable);
				$(this).addClass("rectified");
				recified=true;
			} else {
				lastTable=td.text();
			}
		});
	if(recified) {
		lgksAlert("Defective Columns Found (Table Names Are Missing). Attempted To Rectify Them.");
	}
	/*
	if(tblCnt<=1) {
		$("#datatable_col_details").find("td.table:empty").parent("tr").addClass("rectified");
		$("#datatable_col_details").find("td.table:empty").text(lastTable);
	} else if($("#datatable_col_details").find("td.table:empty").length>0) {
		$("#datatable_col_details").find("td.table:empty").parent("tr").addClass("defective");
		lgksAlert("Defective Columns Have Been Marked. Please rectify them.<br/>Table Names Are Missing.");
	}*/
}
function addTableColumn(ele) {
	if(ColLimit>0 && $("#datatable_col_details tbody tr").length>=ColLimit) {
		lgksAlert("Current Report Engine <b>"+engine.toUpperCase()+"</b> Allows Only <b>"+ColLimit+"</b> Columns At A Time.","No More Columns");
		return;
	}

	er=$("#dbtables").val()+"."+$(ele).text();
	s="<tr colpath='"+er+"'>";
	s+="<td align=center>"+$("#datatable_col_details tr").length+"</td>";
	s+="<td class=table>"+$("#dbtables").val()+"</td>";
	s+="<td class=col>"+$(ele).text()+"</td>";
	s+="<td class='title editable'>"+$(ele).attr("title")+"</td>";
	s+="<td class='hide' align=center><input class=hidden type=checkbox /></td>";
	s+="<td class='search' align=center><input class=searchable type=checkbox checked /></td>";
	s+="<td class='sort' align=center><input class=sortable type=checkbox checked /></td>";
	s+="<td class='class' align=center></td>";

	s+="<td>";
	s+="<div name='delete' class='minibtn minibtndelete'></div>";
	s+="<div name='up' class='minibtn minibtn_up' ></div>";
	s+="<div name='down' class='minibtn minibtn_down' ></div>";
	s+="</td>";
	s+="</tr>";
	$(s).appendTo("#datatable_col_details tbody");

	if($("#datatable_tables").val().indexOf($("#dbtables").val()+",")<0) {
		$("#datatable_tables").val($("#datatable_tables").val()+$("#dbtables").val()+",");

		updateSelector("#datatable_where_details tbody .wtables",getActiveTablesList());
	}

	initEditableAreas();
}
function addSQLFunction(ele) {
	if(ColLimit>0 && $("#datatable_col_details tbody tr").length>=ColLimit) {
		lgksAlert("Current Report Engine <b>"+engine.toUpperCase()+"</b> Allows Only <b>"+ColLimit+"</b> Columns At A Time.","No More Columns");
		return;
	}

	$("#mysqlFuncEditor input[name=sqlfunc]").val("");
	$("#mysqlFuncEditor").attr("title","SQL Function");

	if($("#datatable_col_details tbody tr").length<=0 && $(ele).attr('value')!="##") {
		lgksAlert("No Tables Connected Yet.");
		return;
	}

	if($(ele).attr('value')=="##") {
		lgksPrompt("Please give the Custom Function.","Custom Function",null,function(txt) {
				if(txt.length>0) {
					q=txt;
					q1=txt.substring(0,40);

					s="<tr class='sqlfunc' colpath='"+q+"'>";
					s+="<td align=center>"+$("#datatable_col_details tr").length+"</td>";
					s+="<td class='table func'>CUSTOM</td>";
					s+="<td class=col title='"+q+"'>"+q1+"</td>";
					s+="<td class='title editable'>Custom</td>";
					s+="<td class='hide' align=center><input class=hidden type=checkbox /></td>";
					s+="<td class='search' align=center><input class=searchable type=checkbox /></td>";
					s+="<td class='sort' align=center><input class=sortable type=checkbox /></td>";
					s+="<td class='class' align=center></td>";
					s+="<td>";
					s+="<div name='delete' class='minibtn minibtndelete'></div>";
					s+="<div name='up' class='minibtn minibtn_up' ></div>";
					s+="<div name='down' class='minibtn minibtn_down' ></div>";
					s+="</td>";
					s+="</tr>";
					$(s).appendTo("#datatable_col_details tbody");

					initEditableAreas();
				}
			});
	} else {
		n=0;
		if($(ele).attr('value')=="*") n=100; else n=$(ele).attr('value');

		s="";
		$("#datatable_col_details tbody tr").each(function() {
				tbl=$(this).find("td.table").text();
				col=$(this).find("td.col").text();
				if(!$(this).hasClass("sqlfunc"))
					s+="<option>"+tbl+"."+col+"</option>";
			});

		headerTxt="Choose Only "+n+" Columns!";
		if(n==100) {
			headerTxt="Choose Required Columns!";
		}

		$("#mysqlFuncEditor select.data").html(s);
		$("#mysqlFuncEditor select[multiple]").multiselect("destroy");
		$("#mysqlFuncEditor select[multiple]").multiselect({
				selectedList:3,
				minWidth:570,
				header: headerTxt,
				click: function(e) {
					if($(this).multiselect("widget").find("input:checked").length > n ){
					   return false;
					} else {
					   return true;
					}
			   }
			});
		$("#mysqlFuncEditor input[name=sqlfunc]").val($(ele).text());
		$("#mysqlFuncEditor").attr("title","SQL Function : "+$(ele).text());
		$("#mysqlFuncEditor").dialog({
				width:600,
				resizable:false,
				closeOnEscape:true,
			});
	}
}
function selectMysqlValue(editDlg) {
	cols=$("#mysqlFuncEditor select.data").val();
	func=$("#mysqlFuncEditor input[name=sqlfunc]").val();
	if(cols.length>0) {
		q=func+"("+cols+") ";
		q1=(""+cols).substring(0,40);

		s="<tr class='sqlfunc' colpath='"+q+"'>";
		s+="<td align=center>"+$("#datatable_col_details tr").length+"</td>";
		s+="<td class='table func'>"+func+"</td>";
		s+="<td class=col title='"+q+"'>"+q1+"</td>";
		s+="<td class='title editable'>"+func+"</td>";
		//s+="<td class='hide' align=center>auto</td>";
		s+="<td class='hide' align=center><input class=hidden type=checkbox /></td>";
		s+="<td class='search' align=center><input class=searchable type=checkbox /></td>";
		s+="<td class='sort' align=center><input class=sortable type=checkbox /></td>";
		s+="<td class='class' align=center></td>";
		s+="<td>";
		s+="<div name='delete' class='minibtn minibtndelete'></div>";
		s+="<div name='up' class='minibtn minibtn_up' ></div>";
		s+="<div name='down' class='minibtn minibtn_down' ></div>";
		s+="</td>";
		s+="</tr>";
		$(s).appendTo("#datatable_col_details tbody");

		initEditableAreas();
	}
	$('#mysqlFuncEditor').dialog('close');
}
function addBlankWhereRow(type,bypass) {
	if(type==null) type=1;
	if(bypass==null) bypass=false;
	if($("#datatable_tables").val().length<=0) {
		lgksAlert("No Tables Connected Yet.");
		return;
	}
	s="";
	cnt=$("#datatable_where_details tbody tr").length;
	if(type==1) {
		s="<tr class=where type=1 cnt="+cnt+" >";
		s+="<td class=multiq width=5%>"+getRSelector()+"</td>";
		s+="<td class=table1 for=col1 width=19%>"+getWTableSelector()+"</td>";
		s+="<td class=col1 width=19%>"+getWColSelector()+"</td>";
		s+="<td class=equals width=19%>"+getEQSelector()+"</td>";
		s+="<td class=table2 for=col2 width=19%>"+getWTableSelector()+"</td>";
		s+="<td class=col2 width=19%>"+getWColSelector()+"</td>";
		s+="<td class=dsbtn width=5%>";
		s+="<div name='delete' class='minibtn minibtndelete'></div>";
		s+="</td>";
		s+="</tr>";
	} else if(type==2) {
		s="<tr class=where type=2 cnt="+cnt+" >";
		s+="<td class=multiq width=5%>"+getRSelector()+"</td>";
		s+="<td class=table1 for=col1 width=19%>"+getWTableSelector()+"</td>";
		s+="<td class=col1 width=19%>"+getWColSelector()+"</td>";
		s+="<td class=equals width=19%>"+getEQSelector()+"</td>";
		s+="<td class=dvalue for=col2 width=38% colspan=2><input type=text style='width:100%;height:23px;' /></td>";
		s+="<td class=dsbtn width=5%>";
		s+="<div name='delete' class='minibtn minibtndelete'></div>";
		s+="</td>";
		s+="</tr>";

		if(!bypass)
			$("#helptip1").show("slow").delay(1500).fadeOut("slow");
	} else if(type==3) {
		s="<tr class=where type=3 cnt="+cnt+" >";
		s+="<td class=multiq width=5%>"+getRSelector()+"</td>";
		s+="<td class=dvalue colspan=5><input type=text style='width:100%;height:23px;' /></td>";
		s+="<td class=dsbtn width=5%>";
		s+="<div name='delete' class='minibtn minibtndelete'></div>";
		s+="</td>";
		s+="</tr>";
	} else {
		lgksAlert("Type Mismatch Error");
		return;
	}
	if(s.length>0) {
		$(s).appendTo("#datatable_where_details tbody");

		if(!bypass) {
			tr=$("#datatable_where_details tbody tr");
			$(tr[tr.length-1]).find("select.wtables").each(function() {
					updateRelativeColumnSelector(this);
				});
		}
	}
}

//Other Functions
function getEQSelector() {
	s="<select class='ui-widget-header' style=''>";
	if(sqlOpts.length>0) {
		s+=sqlOpts;
	} else {
		s+="<option value='='>Equals</option>";
		s+="<option value='<>'>Not Equal</option>";
	}
	s+="<select>";
	return s;
}
function getRSelector() {
	if($("#datatable_where_details tbody tr").length<1) return "&nbsp;";
	s="<select class='ui-widget-header' style=''>";
	s+="<option value='OR'>OR</option>";
	s+="<option value='AND'>AND</option>";
	s+="<select>";
	return s;
}
function getWTableSelector() {
	s="<select class='wtables ui-widget-header' style=''>";
	s+=getActiveTablesList();
	s+="<select>";
	return s;
}
function getWColSelector() {
	s="<select class='wcolumns ui-widget-header' style=''>";
	s+="<select>";
	return s;
}
function updateSelector(id,txt) {
	if(txt.length<=0) {
		$("#designer #datatable_where_details tbody").html("");
		return;
	}
	$(id).each(function() {
			val=$(this).val();
			$(this).html(txt);
			$(this).val(val);

			updateRelativeColumnSelector(this);
		});
}
function getActiveTablesList() {
	s="";
	arr=$("#datatable_tables").val().split(",");
	$(arr).each(function(k,v) {
			if(v.length>0) s+="<option>"+v+"</option>";
		});
	return s;
}
function updateRelativeColumnSelector(wtableSelector) {
	nm=$(wtableSelector).parent("td").attr("for");
	if($(wtableSelector).parents("tr").find("td."+nm+" select").length>0) {
		v=$(wtableSelector).val();
		sel=$(wtableSelector).parents("tr").find("td."+nm+" select").get(0);
		loadColumnListMore(sel,v,'select');
	}
}

/*Loading Functions*/
function addColumns(cols,colNames,colHidden,colSearch,colSort,colClasses) {
	row=0;
	$(cols).each(function(k, v) {
			ar=[];
			isFunc=false;
			if(v.indexOf("(")==0) {
				ar[0]="CUSTOM";
				ar[1]=v;
				isFunc=true;
			} else if(v.indexOf("(")>=0) {
				ar[0]=v.substr(0,v.indexOf("("));
				ar[1]=v.substr(v.indexOf("(")+1);
				isFunc=true;
			} else {
				ar=v.trim().split(".");
				if(ar.length==1) {
					er=ar[0].trim();
					ar=["",er];
				}
			}

			s="<tr colpath='"+v+"'>";
			s+="<td align=center>"+$("#datatable_col_details tr").length+"</td>";
			if(isFunc)
				s+="<td class='table func' title='"+ar[0].trim()+"'>"+ar[0].trim().substr(0,25)+"</td>";
			else
				s+="<td class='table' title='"+ar[0].trim()+"'>"+ar[0].trim().substr(0,25)+"</td>";
			s+="<td class=col title='"+v.trim()+"'>"+ar[1].trim().substr(0,25)+"</td>";
			s+="<td class='title editable'>"+colNames[row]+"</td>";
			if($.inArray(v.trim(),colHidden)>=0) {
				s+="<td class='hide' align=center><input class=hidden type=checkbox checked /></td>";
			} else {
				s+="<td class='hide' align=center><input class=hidden type=checkbox /></td>";
			}
			if($.inArray(v.trim(),colSearch)>=0) {
				s+="<td class='search' align=center><input class=hidden type=checkbox checked /></td>";
			} else {
				s+="<td class='search' align=center><input class=hidden type=checkbox /></td>";
			}
			if($.inArray(v.trim(),colSort)>=0) {
				s+="<td class='sort' align=center><input class=hidden type=checkbox checked /></td>";
			} else {
				s+="<td class='sort' align=center><input class=hidden type=checkbox /></td>";
			}
			if(colClasses[k]!=null) {
				s+="<td class='class' align=center>"+colClasses[k]+"</td>";
			} else {
				s+="<td class='class' align=center></td>";
			}

			s+="<td>";
			s+="<div name='delete' class='minibtn minibtndelete'></div>";
			s+="<div name='up' class='minibtn minibtn_up' ></div>";
			s+="<div name='down' class='minibtn minibtn_down' ></div>";
			s+="</td>";
			s+="</tr>";
			$(s).appendTo("#datatable_col_details tbody");

			row++;
		});
	s="";
	lastTable="";
	tblCnt=0;
	$("#datatable_col_details").find("td.table").each(function() {
			if(s.indexOf($(this).text()+",")<0) {
				s+=$(this).text()+",";
				lastTable=$(this).text();
				tblCnt++;
			}
		});
	if(lastTable.length<=0) {
		if($("#designer #datatable_tables").val().length>0) {
			rt=$("#designer #datatable_tables").val().split(",");
			lastTable=rt[0];
		}
	}
	$("#datatable_tables").val(s);
	updateSelector("#datatable_where_details tbody .wtables",getActiveTablesList());

	if(attemptRectify) {
		if(tblCnt<=1) {
			$("#datatable_col_details").find("td.table:empty").parent("tr").addClass("rectified");
			$("#datatable_col_details").find("td.table:empty").text(lastTable);
			lgksAlert("Defective Columns Found (Table Names Are Missing). Trying To Rectify Them.");
		} else if($("#datatable_col_details").find("td.table:empty").length>0) {
			$("#datatable_col_details").find("td.table:empty").parent("tr").addClass("defective");
			lgksAlert("Defective Columns Have Been Marked. Please rectify them.<br/>Table Names Are Missing.");
		}
	}
	initEditableAreas();

	$("#sidebar select#dbtables").val(lastTable);
	loadColumnListMore('#sidebar #form_TableColumns',$("#sidebar select#dbtables").val(),'ul',activateDnD);
}
function addWColumns(colWhere) {
	//alert(colWhere);
	//lgksAlert(getEQRegEx());
	regx=getEQRegEx();
	last="";
	$(colWhere).each(function(k, v) {
			v=v.trim();
			if(v!=null && v.length>0) {
				if(v.toLowerCase()=="and" || v.toLowerCase()=="or") {
					last=v.trim().toUpperCase();
					return;
				}
				n=v.split(".").length-1;
				tr=null;
				v=v.trim();
				if(v.indexOf("(")==0 && v.lastIndexOf(")")==v.length-1) {
					addBlankWhereRow(3,true);
					tr=$("#datatable_where_details tbody tr");
					tr=$(tr[tr.length-1]);
					tr.find("td.dvalue input").val(v.substr(1,v.length-2));
				} else if(n==2) {
					x=v.split(new RegExp(regx));
					if(x.length<=2) {
						addBlankWhereRow(3,true);
						tr=$("#datatable_where_details tbody tr");
						tr=$(tr[tr.length-1]);
						tr.find("td.dvalue input").val(v);
					} else {
						ar1=x[0].split(".");
						ar2=x[2].split(".");
						if(ar1.length==1) {
							er=ar1[0].trim();
							ar1=["",er];
						}
						if(ar2.length==1) {
							er=ar2[0].trim();
							ar2=["",er];
						}
						addBlankWhereRow(1,true);
						tr=$("#datatable_where_details tbody tr");
						tr=$(tr[tr.length-1]);

						tr.find("td.table1 select.wtables").val(ar1[0].trim());
						tr.find("td.table2 select.wtables").val(ar2[0].trim());
						tr.find("td.equals select").val(x[1].trim());

						$(tr.find("td.col1 select.wcolumns").get(0)).attr("rel",ar1[1].trim());
						$(tr.find("td.col2 select.wcolumns").get(0)).attr("rel",ar2[1].trim());

						loadColumnListMore(tr.find("td.col1 select.wcolumns").get(0),ar1[0].trim(),'select',function() {
								$(this).val($(this).attr('rel'));
							});
						loadColumnListMore(tr.find("td.col2 select.wcolumns").get(0),ar2[0].trim(),'select',function(txt) {
								$(this).val($(this).attr('rel'));
							});
						//lgksAlert(v+":: "+ar1[0]+" "+ar1[1]+" "+ar2[0]+" "+ar2[1]+" "+x[1]);
					}
				} else if(n==1) {
					x=v.split(new RegExp(regx));
					if(x.length<=2) {
						addBlankWhereRow(3);
						tr=$("#datatable_where_details tbody tr");
						tr=$(tr[tr.length-1]);
						tr.find("td.dvalue input").val(v);
					} else {
						ar=x[0].split(".");
						if(ar.length==1) {
							er=ar[0].trim();
							ar=["",er];
						}
						addBlankWhereRow(2,true);
						tr=$("#datatable_where_details tbody tr");
						tr=$(tr[tr.length-1]);

						tr.find("td.equals select").val(x[1].trim());
						tr.find("td.dvalue input").val(x[2].trim());

						tr.find("td.table1 select.wtables").val(ar[0].trim());

						$(tr.find("td.col1 select.wcolumns").get(0)).attr("rel",ar[1].trim());
						loadColumnListMore(tr.find("td.col1 select.wcolumns"),ar[0],'select',function() {
								$(this).val($(this).attr('rel'));
							});
					}
				} else {
					addBlankWhereRow(3,true);
					tr=$("#datatable_where_details tbody tr");
					tr=$(tr[tr.length-1]);
					tr.find("td.dvalue input").val(v);
				}
				if(tr!=null) {
					if(tr.find("td.multiq select").length>0 && last.length>0) {
						tr.find("td.multiq select").val(last);
					}
				}
			}
		});
}
function getEQRegEx() {
	s="";
	if(sqlOpts.length>0) {
		sr1=[];
		sr2=[];
		sr3=[];
		sr4=[];
		$("#sqlopts option").each(function() {
				x=$(this).attr("value");
				if(x.length==1) sr1.push(x);
				else if(x.length==2) sr2.push(x);
				else if(x.length==3) sr3.push(x);
				else sr4.push(x);
			});
		if(sr4.length>0) s+=sr4.join("|");
		if(sr3.length>0) if(s.length>0) s+="|"+sr3.join("|"); else s+=sr3.join("|");
		if(sr2.length>0) if(s.length>0) s+="|"+sr2.join("|"); else s+=sr2.join("|");
		if(sr1.length>0) if(s.length>0) s+="|"+sr1.join("|"); else s+=sr1.join("|");
		if(s.length>0) s="("+s+")";
	} else {
		s="(=|<>)";
	}
	return s;
}
function initEditableAreas() {
	$("#datatable_col_details tbody .editable").editInPlace({
			callback: function(original_element, html, original) { return html;},
			show_buttons:false,
		});

	$("#datatable_col_details").delegate("tbody td.class","click",function() {
			ele=$(this);
			$("#colClasses select[multiple]").multiselect("destroy");
			$("#colClasses select[multiple]").val($(this).html().split(" "));
			$("#colClasses select[multiple]").multiselect({
				selectedList:4,
				minWidth:350,
			});
			$("#colClasses select[single]").multiselect("destroy");
			$("#colClasses select[single]").val($(this).html().split(" "));
			$("#colClasses select[single]").multiselect({
				selectedList:1,
				minWidth:350,
			});
			jqPopupDiv("#colClasses").dialog({
					width:400,
					height:350,
					resizable:false,
					buttons:{
						Done:function() {
							s1=$("#colClasses select#clz").val();
							s2=$("#colClasses select#disp").val();
							s3=$("#colClasses select#clrs").val();
							s4=$("#colClasses select#math").val();
							if(s1!=null) s1=s1.toString(); else s1="";
							if(s2!=null) s2=s2.toString(); else s2="";
							if(s3!=null) s3=s3.toString(); else s3="";
							s=s1+","+s2+","+s3+","+s4;
							s=s.replace(/,/g," ").trim();
							s=s.replace(/  /g," ").trim();
							ele.html(s);
							$(this).dialog("close");
						},
						Cancel:function() {
							$(this).dialog("close");
						},
					}
				});
		});

	$("#datatable_col_details tbody").tableDnD();
}
