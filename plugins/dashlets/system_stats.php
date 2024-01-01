<?php
if(!defined('ROOT')) exit('No direct script access allowed');

?>
<style>

.system_stats_div_container h2,
.system_stats_div_container h5,
.system_stats_div_container .h2,
.system_stats_div_container .h5 {
  font-family: inherit;
  font-weight: 600;
  line-height: 1.5;
  margin-bottom: .5rem;
  color: #32325d;
}

.system_stats_div_container h5,
.system_stats_div_container .h5 {
  font-size: .8125rem;
}

@media (min-width: 992px) {
  .system_stats_div_container .col-lg-6 {
    max-width: 50%;
    flex: 0 0 50%;
  }
}

@media (min-width: 1200px) {
  
  .system_stats_div_container  .col-xl-3 {
    max-width: 25%;
    flex: 0 0 25%;
  }
  
  .system_stats_div_container  .col-xl-6 {
    max-width: 50%;
    flex: 0 0 50%;
  }
}


.system_stats_div_container .bg-danger {
  background-color: #f5365c !important;
}



@media (min-width: 1200px) {
  
  .system_stats_div_container .justify-content-xl-between {
    justify-content: space-between !important;
  }
}


.system_stats_div_container .pt-5 {
  padding-top: 3rem !important;
}

.system_stats_div_container .pb-8 {
  padding-bottom: 8rem !important;
}

@media (min-width: 768px) {
  
  .system_stats_div_container .pt-md-8 {
    padding-top: 8rem !important;
  }
}

@media (min-width: 1200px) {
  
  .system_stats_div_container .mb-xl-0 {
    margin-bottom: 0 !important;
  }
}




.system_stats_div_container .font-weight-bold {
  font-weight: 600 !important;
}


.system_stats_div_container a.text-success:hover,
.system_stats_div_container a.text-success:focus {
  color: #24a46d !important;
}

.system_stats_div_container .text-warning {
  color: #fb6340 !important;
}

.system_stats_div_container a.text-warning:hover,
.system_stats_div_container a.text-warning:focus {
  color: #fa3a0e !important;
}

.system_stats_div_container .text-danger {
  color: #f5365c !important;
}

.system_stats_div_container a.text-danger:hover,
.system_stats_div_container a.text-danger:focus {
  color: #ec0c38 !important;
}

.system_stats_div_container .text-white {
  color: #fff !important;
}

.system_stats_div_container a.text-white:hover,
.system_stats_div_container a.text-white:focus {
  color: #e6e6e6 !important;
}

.system_stats_div_container .text-muted {
  color: #8898aa !important;
}

@media print {
  *,
  *::before,
  *::after {
    box-shadow: none !important;
    text-shadow: none !important;
  }
  
  .system_stats_div_container a:not(.btn) {
    text-decoration: underline;
  }
  
  .system_stats_div_container p,
  .system_stats_div_container h2 {
    orphans: 3;
    widows: 3;
  }
  
  .system_stats_div_container h2 {
    page-break-after: avoid;
  }
  
  @ page {
    size: a3;
  }
  
  body {
    min-width: 992px !important;
  }
}

figcaption,
main {
  display: block;
}

main {
  overflow: hidden;
}

.bg-yellow {
  background-color: #ffd600 !important;
}

.bg-danger {
  background-color: #f5365c !important;
}
.bg-success {
  background-color: #54be28 !important;
}
.bg-warning {
  background-color: orange !important;
}
.bg-info {
  background-color: #17a2b8 !important;
}


.system_stats_div_container .icon {
  width: 5rem;
  height: 5rem;
}

.system_stats_div_container .icon i {
  font-size: 3.25rem;
}

.system_stats_div_container .icon-shape {
  display: inline-flex;
  padding: 12px;
  text-align: center;
  border-radius: 50%;
  align-items: center;
  justify-content: center;
}
.system_stats_div_container {
    /*margin: 0px 20px;*/
    /*width: -webkit-fill-available;*/
    /*width: calc(100% - 40px);*/
}
.system_stats_div_container>div {
    padding-right: 5px;
    padding-left: 5px;
}
.system_stats_div_container .card {
    background: white;
    padding: 10px;
    margin-top: 10px;
}
</style>
<div id='system_stats_div' class="text-center system_stats">
    <div id='system_stats_div_container' class='row system_stats_div_container' style="margin: 0px 0px;">
        
    </div>
</div>
<script>
$(function() {
    loadSystemStats();
});
function loadSystemStats() {
    $("#system_stats_div_container").html("<div class='ajaxloading ajaxloading8'></div>");
    processAJAXQuery(_service("dashstats","system_stats"),function(data) {
        $("#system_stats_div_container").html("");
		$.each(data.Data, function(k, row) {
		    var subtextHTML = "";
		    if(row.status=="increase") {
		        subtextHTML = `<span class="text-success mr-2"><i class="fa fa-arrow-up"></i> ${row.subtext_stats}</span>`;
		    } else if(row.status=="decrease") {
		        subtextHTML = `<span class="text-warning mr-2"><i class="fa fa-arrow-down"></i> ${row.subtext_stats}</span>`;
		    } else {
		        subtextHTML = `<span class="text-warning mr-2"> </span>`;
		    }
		    if(row.subtext==null) row.subtext = "&nbsp;";
		    
		    $("#system_stats_div_container").append(`<div class="col-xl-3 col-lg-6">
          <div class="card card-stats mb-4 mb-xl-0">
            <div class="card-body">
                <div class="icon icon-shape bg-warning text-white rounded-circle shadow pull-left">
                    <i class="${row.icon} fa-3x"></i>
                </div>
                <div class="col">
                  <h3 class="card-title text-uppercase text-muted mb-0">${row.title}</h3>
                  <span class="h2 font-weight-bold mb-0">${row.stats}</span>
                </div>
                <p class="mt-3 mb-0 text-muted text-sm">
                    ${subtextHTML}
                    <span class="text-nowrap">${row.subtext}</span>
                </p>
            </div>
          </div>
        </div>`);
		});
	},"json");
}
</script>