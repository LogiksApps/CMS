<?php
if(!defined('ROOT')) exit('No direct script access allowed');
echo _js("bootstrap-typeahead");
?>
<style>
.btn-search {
    border-radius: 20px !important;
    opacity: 0.8;
    font-weight: bold;
    margin-top:1px;
}
</style>
<div class="input-group pull-right">
    <input type="hidden" class="form-control" placeholder="Search Logiks Functions, etc" id='searchCodeBase'>
    <div class="input-group-btn-1">
      <button onclick="searchMore()" type="button" class="btn btn-primary btn-search"><span class="fa fa-search"></span> Code Search</button>
    </div>
</div>
<script>
$(function() {
    $("#searchCodeBase").typeahead({
    	onSelect: function(item) {
    		$("#searchCodeBase").val(item.value);
    	},
    	ajax: {
    		url: _service("codeSearch","ajaxsearch"),
    		timeout: 500,
    		displayField: "title",
    		valueField: "value",
    		triggerLength: 1,
    		method: "get",
    		loadingClass: "loading-circle",
    		preDispatch: function (query) {
    			//showLoadingMask(true);
    			return {
    				search: query
    			}
    		},
    		preProcess: function (data) {
    			//showLoadingMask(false);
    			if (data.success === false) {
    				// Hide the list, there was some error
    				return false;
    			}
    			// We good!
    			return data.Data;
    		}
    	}
    });
});
function searchMore() {
    stxt=$("#searchCodeBase").val();
    
    if(stxt==null || stxt.length<=0) {
        stxt="";
    //   lgksToast("Please type something to search");
    //   return;
    }
    
    lx=_link("modules/codeSearch")+"&query="+stxt;
    top.openLinkFrame("Search",lx,true);
}
</script>