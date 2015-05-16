function loadTableList(id,func) {
	$(id).html("<option>Loading ...</option>");
	$(id).load(lnkLst+"&action=tablelist&format=select",func);
}
function loadSysTableList(id,func) {
	$(id).html("<option>Loading ...</option>");
	$(id).load(lnkLst+"&action=tablelist&format=select&system=true",func);
}
function loadColumnList(id,tbl,format,func) {
	$(id).html("<option>Loading ...</option>");
	if($(id+" .dbtable").length>0) $(id+" .dbtable").val(tbl);
	if(format==null) format="select";
	if(format=="ul") {
		$(id).load(lnkLst+"&action=columninfolist&format="+format+"&tbl="+tbl,func);
	} else {
		$(id).load(lnkLst+"&action=columnlist&format="+format+"&tbl="+tbl,func);
	}
}
function loadColumnListMore(id,tbl,format,func) {
	$(id).html("<option>Loading ...</option>");
	if($(id+" .dbtable").length>0) $(id+" .dbtable").val(tbl);
	if(format==null) format="select";
	if(format=="ul") {
		$(id).load(lnkLst+"&action=columninfolistmore&format="+format+"&tbl="+tbl,func);
	} else {
		$(id).load(lnkLst+"&action=columnlistmore&format="+format+"&tbl="+tbl,func);
	}
}
function emphasize(e) {
	if(typeof e=='object') {
		e.fadeOut('slow',function() {
			e.fadeIn('slow',function() {
				e.focus();
			});
		});		
	}	
}
function hasAttr(ele,attr) {
	var attr = $(ele).attr(attr);
	if (attr==null) {
		return false;
	}
	return true;
}
