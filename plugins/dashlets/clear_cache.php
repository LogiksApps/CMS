<?php
if(!defined('ROOT')) exit('No direct script access allowed');

?>
<style>
.clearCacheTable>div>* {
	clear: both;
	display: block;
}
.clearCacheTable .fa {
	font-size: 4em;
}
.clearCacheTable citie {
	font-family: arial;
    font-size: 10px;
    margin: 10px;
    margin-left: 0px;
    margin-right: 0px;
}
.clearCacheTable citie:before {content:'( ';}
.clearCacheTable citie:after {content:' )';}
.clearCacheTable a {
	margin-right:10px;
}

.cacheBlock{
        background: #fff;
    
    padding: 30px;
    box-shadow: 0px 0px 9px -5px #9d9d9d;
    border-radius: 5px !important;
}
.cacheBlock>* {
    clear: both;
    display: block;
}
.cacheBlock img{
    margin: 0 auto;
}
.clearCacheTable>.cacheData {
    padding-left: 10px;
    padding-right: 0px;
}
.clearCacheTable>.cacheData:first-child {
    padding-left: 0px;
}
.clearCacheTable a {
    margin-right: 0;
    font-weight: 600;
    letter-spacing: 1px;
    border-radius: 20px !important;
    padding: 4px 10px;
    /* font-size: 14px; */
}
.clearCacheTable citie {
    font-family: arial;
    font-size: 11px;
    margin: 10px;
    margin-left: 0px;
    margin-right: 0px;
    font-weight: 600;
    color: #8f8f8f;
}
</style>
<div id='clearCacheTable' class="row1 text-center clearCacheTable">
	<div class="cacheData col-md-3 configs" data-rel='configs' data-toggle="tooltip" data-placement="bottom" title="Configuration Cache. Clear this cache if you want to clear all configurations related cache." >
	    <div  class="cacheBlock">
    		<img src="<?php echo loadMedia('images/settings.png')?>">
    		<citie>--</citie>
    		<a class='btn btn-warning btn-xs'>Clear</a>
		</div>
	</div>
	<div class="cacheData col-md-3 cache" data-rel='cache' data-toggle="tooltip" data-placement="bottom" title="Misc Cache. Clear this cache to get rid of all the Meta, Data cache, Resource Cache, etc.">
	    <div  class="cacheBlock">
    		<img src="<?php echo loadMedia('images/schedule.png')?>">
    		<citie>--</citie>
    		<a class='btn btn-info btn-xs'>Clear</a>
		</div>
	</div>
	<div class="cacheData col-md-3 templates" data-rel='templates' data-toggle="tooltip" data-placement="bottom" title="Template cache. Clear this cache for removing compiled template cache.">
	    <div  class="cacheBlock">
    		<img src="<?php echo loadMedia('images/coding.png')?>">
    		<citie>--</citie>
    		<a class='btn btn-info btn-xs'>Clear</a>
		</div>
	</div>
	<div class="cacheData col-md-3 appcache" data-rel='appcache' data-toggle="tooltip" data-placement="bottom" title="Apps Cache. Clear this cache to get rid of all the APP cache.">
	    <div  class="cacheBlock">
    		<img src="<?php echo loadMedia('images/book.png')?>">
    		<citie>--</citie>
    		<a class='btn btn-warning btn-xs'>Clear</a>
		</div>
	</div>
</div>
<script>
$(function() {
	$("a.btn","#clearCacheTable").click(function(e) {
		e.preventDefault();

		src=$(this).closest(".cacheData").data('rel');
		if(src!=null) {
			clearLogiksCache(src);
		} else {
			loadLogiksCacheInfo();
		}
	});

	loadLogiksCacheInfo();
});
function loadLogiksCacheInfo() {
	$(".cacheData[data-rel='configs'] citie","#clearCacheTable").html("--");
	$(".cacheData[data-rel='configs'] .fa","#clearCacheTable").css("color","black");

	$(".cacheData[data-rel='cache'] citie","#clearCacheTable").html("--");
	$(".cacheData[data-rel='cache'] .fa","#clearCacheTable").css("color","black");

	$(".cacheData[data-rel='appcache'] citie","#clearCacheTable").html("--");
	$(".cacheData[data-rel='appcache'] .fa","#clearCacheTable").css("color","black");

	processAJAXQuery(_service("cleaner","tmpSize"),function(jsonData) {
		try {
			$.each(jsonData.Data,function(k,v) {
				clr=getIcoColor(v.size);
				$(".cacheData[data-rel='"+k+"'] citie","#clearCacheTable").html(v.text);
				$(".cacheData[data-rel='"+k+"'] .fa","#clearCacheTable").css("color",clr);
			});
		} catch(e) {
			console.error(e);
		}
	},"json");
}
function clearLogiksCache(src) {
	processAJAXQuery(_service("cleaner","PURGE:"+src.toUpperCase()),function(data) {
		if(data.Data=="done") {
			lgksToast("All "+src+" cache cleared.");
		} else {
			lgksToast(data.Data);
		}
		loadLogiksCacheInfo();
	},"json");
}
function getIcoColor(size) {
	if(size>1073741824 && size%(1073741824)<1073741824) return "maroon";//GB
	else if(size>524288000 && size%(524288000)<524288000) return "red";//MB2
	else if(size>104857600 && size%(104857600)<104857600) return "orange";//MB1
	else if(size>1048576 && size%(1048576)<1048576) return "blue";//MB
	//else if(size>1024 && size%1024<1024) return "green";//KB

	return "black";
}
</script>