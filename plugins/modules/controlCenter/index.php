<?php
if(!defined('ROOT')) exit('No direct script access allowed');

include_once __DIR__."/api.php";
loadNodeEnvironment();

loadModule("pages");

printPageComponent(false,[
		"toolbar"=>[
		    "refreshPage"=>["icon"=>"<i class='fa fa-refresh'></i>"],
		    ['type'=>"bar"],
		    
		    "clearOutput"=>["icon"=>"<i class='fa fa-broom'></i>"],
		    ['type'=>"bar"],
		    
		    "viewScript"=>["icon"=>"<i class='fa fa-code'></i>", "title"=>"Script", "class"=>"on_script hidden"],
		    "editScript"=>["icon"=>"<i class='fa fa-pencil'></i>", "title"=>"Edit", "class"=>"on_script on_editor hidden"],
		    
		    "runScript"=>["icon"=>"<i class='fa fa-play'></i>", "title"=>"Run", "class"=>"on_script hidden"],
		    
		    
		    //"loadServerStats"=>["icon"=>"<i class='fa fa-tachometer-alt'></i>"],
		    //['type'=>"bar"],
		    "restartNodeServer"=>["icon"=>"<i class='fa fa-redo'></i>", "title"=> "Restart", "tips"=>"Restart Node Server", "align"=>"right"],
// 			"db"=>["title"=>"DB","align"=>"right","class"=>"active"],
// 			"fs"=>["title"=>"FS","align"=>"right"],
// 			"log"=>["title"=>"ERROR-LOG","align"=>"right"],
// 			"msg"=>["title"=>"MSG","align"=>"right"],
// 			"cache"=>["title"=>"CACHE","align"=>"right"],

			
			
			// 
			// "trash"=>["icon"=>"<i class='fa fa-trash'></i>"],
		],
		"sidebar"=> "pageSidebar",
		"contentArea"=>"pageContentArea"
	]);

echo _css(["controlCenter"]);
echo _js(["controlCenter", "chart"]);

function pageSidebar() {
    return "<ul id='script-list' class='list-group script-list'></ul>";
}

function pageContentArea() {
	return "<div class='container-fluid container-card' style='height: 100%;'>
	    <div class='row' style='height:100%;'>
	        <div class='col-md-8' style='padding: 0px 5px;'>
	            <div class='panel panel-default'>
                  <div class='panel-heading hide hidden d-none'>Output Results</div>
                  <div class='panel-body output_results'>
                    <pre></pre>
                  </div>
                </div>
	        </div>
	        <div class='col-md-4' style='padding: 0px;'>
	            <div class='panel panel-default text-center' style='padding-top:10px;'>
                    <div id='canvas-holder' style='width: 120px;height:120px;margin:auto;margin-top:5px;display: inline-block;margin: 0px 20px;'>
                        <canvas id='disk_capacity'></canvas>
                        <h5 style='margin: 0px;display: inline-block;'>Disk Usage</h5>
                    </div>
                    <div id='canvas-holder2' style='width: 120px;height:120px;margin:auto;margin-top:5px;display: inline-block;margin: 0px 20px;'>
                        <canvas id='ram_usage'></canvas>
                        <h5 style='margin: 0px;display: inline-block;'>RAM Usage</h5>
                    </div>
                </div>
                <div class='panel panel-default text-center'>
                </div>
	            <div class='panel panel-default'>
                  <div class='panel-heading'>Server Stats <i class='fa fa-refresh pull-right reload_server_stats'></i></div>
                  <div class='panel-body' style='padding: 2px;height: 60%;overflow: auto;'>
                    <table class='table' style='margin-bottom: 0px;'>
                        <tbody id='server_stats'>
                        </tbody>
                    </table>
                  </div>
                </div>
	        </div>
	    </div>
	</div>";
}
?>
<style>
.list-group-item label {
    font-size: 12px !important;
}
.list-group-item span.label {
    font-size: 8px !important;
    float: right;
    padding: 0.2em 0.4em 0.3em;
}
.list-group-item input[type=radio], .list-group-item input[type=checkbox] {
    float: left;
    margin-right: 3px;
    margin-top: 2px;
}
.code_text {
    width:100%;height:50%;min-height:400px;
    background: black;
    color: white;
    padding: 2px;
}
</style>
<script>
window.chartColors = {
	red: 'rgb(255, 99, 132)',
	orange: 'rgb(255, 159, 64)',
	yellow: 'rgb(255, 205, 86)',
	green: 'rgb(75, 192, 192)',
	blue: 'rgb(54, 162, 235)',
	purple: 'rgb(153, 102, 255)',
	grey: 'rgb(201, 203, 207)'
};
var script_running = false;
var server_connected = false;
var server_stats = {};
$(function() {
    $(".script-list").delegate("input[type=radio]", "change", function() {
        if($(".script-list input:checked").length>0) {
            $(".on_script").removeClass("hidden");
        } else {
            $(".on_script").addClass("hidden");
        }
        if($(".script-list input:checked").data("group")=="root") {
            $(".on_editor").addClass("hidden");
        }
        
        $(".script-list li.active").removeClass("active");
        $(this).closest("li").addClass("active");
    });
    $("body").delegate(".save_script_source", "click", function() {
        saveScript();
    })
        
    $(".script-list").delegate("li[data-src]", "click", function() {
        // $(".script-list li.active").removeClass("active");
        // $(this).addClass("active");
        // var src = $(this).data("src");
        // runControlScript(src, this);
    })
    
    $(".reload_server_stats").click(loadServerStats);
    
    runConnectionTest();
});
function refreshPage() {
    window.location.reload();
}
function runScript(btn) {
    var src = $(".script-list input:checked").data("src");
    if(src==null) {
        lgksToast("Script not selected");
        return;
    }
    runControlScript(src, btn);
}
function viewScript() {
    var src = $(".script-list input:checked").data("src");
    if(src==null) {
        lgksToast("Script not selected");
        return;
    }
    lgksOverlayURL(_service("controlCenter", "view_script")+"&src="+src, "View Script", function() {
        
    }, {});
}
function editScript() {
    var src = $(".script-list input:checked").data("src");
    if(src==null) {
        lgksToast("Script not selected");
        return;
    }
    parent.openLinkFrame("Script-"+src, _service("controlCenter", "edit_script")+"&src="+src, true);
}
function saveScript() {
    var srcData = $("#script_source").val();
    var script = $("#script_source").data("src");
    
    processAJAXPostQuery(_service("controlCenter", "save_script"), `src=${script}&text=`+srcData, function(data) {
        lgksToast(data);
    });
}
function deleteScript() {
    var src = $(".script-list input:checked").data("src");
    if(src==null) {
        lgksToast("Script not selected");
        return;
    }
    
}
function createScript() {
    
}
function runConnectionTest() {
    server_connected = false;
    $(".output_results>pre").html("Connecting To Server ...");
    processAJAXQuery(_service("controlCenter", "test"), function(data) {
        if(data.Data) {
            $(".output_results>pre").append("\nServer Connected Successfully");
            loadServerStats();
            server_connected = true;
        } else {
            server_connected = false;
            $(".output_results>pre").append("\nFailed to connect to server");
            $("#server_stats").html("<h4 align=center>Server Not Connected</h4>");
        }
        loadScriptList();
    }, "json");
}
function restartNodeServer() {
    var ans = confirm("Are you Sure, This will restart NODEJS Server?");
    if(ans) {
        $(".output_results>pre").append("<hr>Restarting Server");
        processAJAXQuery(_service("controlCenter", "restart"), function(data) {
            $(".output_results>pre").append("\nChecking in 3 secs, Will auto connect after restart<hr>\n\n");
            
            setTimeout(runConnectionTest, 1000);
        });
    }
}
function loadServerStats() {
    $("#server_stats").html("<div class='ajaxloading ajaxloading3'></div>");
    processAJAXQuery(_service("controlCenter", "stats"), function(data) {
        if(data.Data.length<=0) {
            $("#server_stats").html("<h4 align=center>Server Not Connected</h4>");
        } else {
            $("#server_stats").html("");
            $.each(data.Data, function(k, v) {
                $("#server_stats").append(`<tr><th style='text-transform: uppercase;'>${k}</th><td align=left>${v}</td></tr>`);
            });
            server_stats = data.Data
            renderCharts();
        }
    }, "json");
}

function loadScriptList() {
    $("#script-list").html("<div class='ajaxloading ajaxloading3'></div>");
    processAJAXQuery(_service("controlCenter", "list_scripts"), function(data) {
        $("#script-list").html("");
        $.each(data.Data, function(scrpt, info) {
            var lbl = ".";
            if(scrpt.indexOf(".sh")>0) lbl = "SH";
            else if(scrpt.indexOf(".js")>0) lbl = "JS";
            
            var title= (scrpt.replace(/_/g,' ').replace('.sh', '').replace('.js', '')).toUpperCase();
            var clz = "info";
            if(info.group=="root") clz = "danger";
            
            if(server_connected)
                $("#script-list").append(`<li data-src='${scrpt}' class='list-group-item'><label><input data-src='${scrpt}' data-group='${info.group}' type='radio' name='scriptSelector' /> ${title}</label> <span class='label label-${clz}'>${lbl}</span></li>`);
            else
                $("#script-list").append(`<li data-src='${scrpt}' class='list-group-item disabled'>${title} <span class='label label-${clz}'>${lbl}</span></li>`);
        });
    }, "json");
}
function runControlScript(src) {
    if(script_running) {
        lgksToast("You can only run one script at a time.");
        return;
    }
    $(".output_results>pre").append("<hr>Running Script : "+ src);
    
    processAJAXPostQuery(_service("controlCenter", "form_script"), "src="+src, function(data) {
        if(data.Data) {
            lgksToast("Script Forms Not Yet Supported");
            
            //To be removed after form rendering is ready
            script_running = true;
            processAJAXPostQuery(_service("controlCenter", "run_script"), "src="+src, function(dataRaw) {
                $(".output_results>pre").append("\n"+dataRaw);
                script_running = false;
                $(".output_results").scrollTop($(".output_results>pre").height());
            });
        } else {
            script_running = true;
            processAJAXPostQuery(_service("controlCenter", "run_script"), "src="+src, function(dataRaw) {
                $(".output_results>pre").append("\n"+dataRaw);
                script_running = false;
                $(".output_results").scrollTop($(".output_results>pre").height());
            });
        }
    }, "json");
}
function clearOutput() {
    $(".output_results>pre").html("");
}

function renderCharts() {
    renderChartDiskCapacity();
    renderChartRamUsage();
}

function renderChartDiskCapacity() {
    if(server_stats.DISK_CAPACITY==null) {
        $("#disk_capacity").closest(".panel").hide();
        return;
    }
    var v1 = parseInt(server_stats.DISK_CAPACITY);
    
    var data1 = {
            labels: [
                "Used Disk",
                "Available Disk",
                //"Not-Available",
            ],
            datasets: [{
                data: [
                    v1,
                    (100-v1),
                ],
                //borderColor: window.chartColors.red,
                backgroundColor: [
                    window.chartColors.red,
                    window.chartColors.green,
                    window.chartColors.blue,
                    window.chartColors.orange,
                    window.chartColors.yellow,
                ],
                fill: false,
                label: 'Disk Capacity'
            }],
        };
        
    var ctx = document.getElementById("disk_capacity").getContext("2d");
    window.myPie = new Chart(ctx, {
        type: 'doughnut',
        data: data1,
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false,
                    position: 'bottom'
                }
            }
        }
    });
}

function renderChartRamUsage() {
    if(server_stats.MEM_TOTAL==null) {
        $("#ram_usage").closest(".panel").hide();
        return;
    }
    var v1 = parseInt(server_stats.MEM_TOTAL);
    var v2 = parseInt(server_stats.MEM_FREE);
    var v3 = parseInt(server_stats.MEM_PROCESS);
    
    var data1 = {
            labels: [
                "Free RAM",
                // "Process RAM",
                "Used RAM",
                //"Not-Available",
            ],
            datasets: [{
                data: [
                    v2,
                    // v3,
                    (v1-v2),
                ],
                //borderColor: window.chartColors.red,
                backgroundColor: [
                    //window.chartColors.red,
                    //window.chartColors.green,
                    // window.chartColors.yellow,
                    window.chartColors.purple,
                    window.chartColors.grey,
                ],
                fill: false,
                label: 'RAM Usage'
            }],
        };
        
    var ctx = document.getElementById("ram_usage").getContext("2d");
    window.myPie = new Chart(ctx, {
        type: 'doughnut',
        data: data1,
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false,
                    position: 'bottom'
                }
            }
        }
    });
}

function renderChartsOld() {
    var data1 = {
            labels: [
                "Red",
                "Orange",
                "Yellow",
                "Green",
                "Blue"
            ],
            datasets: [{
                data: [
                    2,
                    3,
                    2,
                    4,
                    5,
                ],
                //borderColor: window.chartColors.red,
                backgroundColor: [
                    window.chartColors.red,
                    window.chartColors.orange,
                    window.chartColors.yellow,
                    window.chartColors.green,
                    window.chartColors.blue,
                ],
                fill: false,
                label: 'Dataset 1'
            }],
        };
    
    var ctx = document.getElementById("chart1").getContext("2d");
    window.myPie = new Chart(ctx, {
        type: 'doughnut',
        data: data1,
        options: {
            responsive: true,
            legend: {
                display: false
            }
        }
    });
    
    var ctx = document.getElementById("chart2").getContext("2d");
    window.myPie = new Chart(ctx, {
        type: 'bar',
        data: data1,
        options: {
            responsive: true,
            legend: false
        }
    });
    
    var ctx = document.getElementById("chart3").getContext("2d");
    window.myPie = new Chart(ctx, {
        type: 'line',
        data: data1,
        options: {
            responsive: true,
            legend: false
        }
    });
}
</script>