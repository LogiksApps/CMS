<?php
if(!defined('ROOT')) exit('No direct script access allowed');

?>
<style>
.buildCodeTable>div>* {
	clear: both;
	display: block;
}
.buildCodeTable .fa {
	font-size: 4em;
}
.buildCodeTable citie {
	font-family: arial;
    font-size: 10px;
    margin: 10px;
    margin-left: 0px;
    margin-right: 0px;
}
.buildCodeTable citie:before {content:'( ';}
.buildCodeTable citie:after {content:' )';}
.buildCodeTable a {
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
.buildCodeTable>.buildCoreData {
    padding-left: 10px;
    padding-right: 0px;
}
.buildCodeTable>.buildCoreData:first-child {
    padding-left: 0px;
}
.buildCodeTable a {
    margin-right: 0;
    font-weight: 600;
    letter-spacing: 1px;
    border-radius: 20px !important;
    padding: 4px 10px;
    /* font-size: 14px; */
}
.buildCodeTable citie {
    font-family: arial;
    font-size: 11px;
    margin: 10px;
    margin-left: 0px;
    margin-right: 0px;
    font-weight: 600;
    color: #8f8f8f;
}
</style>
<div id='buildCodeTable' class="row1 text-center buildCodeTable">
    <div class="buildCoreData col-md-3" data-rel='core' data-toggle="tooltip" data-placement="bottom" title="Build Application Code" >
	    <div  class="cacheBlock">
    		<img src="<?php echo loadMedia('images/concepts.png')?>">
    		<citie>Core Cache</citie>
    		<a class='btn btn-warning btn-xs'>Build</a>
		</div>
	</div>
	<div class="buildCoreData col-md-3" data-rel='app' data-toggle="tooltip" data-placement="bottom" title="Build Application Code" >
	    <div  class="cacheBlock">
    		<img src="<?php echo loadMedia('images/concepts.png')?>">
    		<citie><?=CMS_SITENAME?> Cache</citie>
    		<a class='btn btn-warning btn-xs'>Build</a>
		</div>
	</div>
</div>
<script>
$(function() {
	$("a.btn","#buildCodeTable").click(function(e) {
		e.preventDefault();

		src=$(this).closest(".buildCoreData").data('rel');
		if(src!=null) {
			buildLogiksCodeCache(src);
		} else {
			loadLogiksCacheInfo();
		}
	});

	loadLogiksBuildInfo();
});
function loadLogiksBuildInfo() {
    
}
function buildLogiksCodeCache(src) {
    processAJAXQuery(_service("codeIndexer", "index")+"&src="+src, function(data) {
		lgksToast(data.Data);
		loadLogiksBuildInfo();
	},"json");
}
</script>