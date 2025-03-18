<?php
if(!defined('ROOT')) exit('No direct script access allowed');

$actions = [];
switch(getAppType()) {
    case "logiks-webapp":
        break;
    case "logiks-nodejs":
        break;
    case "logiks-react":
        break;
    default:
        
}

?>
<div id='studioRunner' class='studioRunner'>
    <?php
        if($actions) {
            foreach($actions as $k=>$conf) {
                
            }
        } else {
            echo "<h3 align=center>No Actions are yet supported for <u>".getAppType()."</u></h3>";
        }
    ?>
</div>
<script>
function runScript(src) {
    $("#studioRunner").append(`<div class='ajaxloading ajaxloading5>Running ...<div>'`);
    processAJAXPostQuery(_service("controlCenter", "run_script"), "src="+src, function(dataRaw) {
                $("#studioRunner").append(`<pre>${dataRaw}</pre>`);
            });
}

// function runControlScript(src) {
//     if(script_running) {
//         lgksToast("You can only run one script at a time.");
//         return;
//     }
//     $(".output_results>pre").append("<hr>Running Script : "+ src);
    
//     processAJAXPostQuery(_service("controlCenter", "form_script"), "src="+src, function(data) {
//         if(data.Data) {
//             lgksToast("Script Forms Not Yet Supported");
            
//             //To be removed after form rendering is ready
//             script_running = true;
//             processAJAXPostQuery(_service("controlCenter", "run_script"), "src="+src, function(dataRaw) {
//                 $(".output_results>pre").append("\n"+dataRaw);
//                 script_running = false;
//                 $(".output_results").scrollTop($(".output_results>pre").height());
//             });
//         } else {
//             script_running = true;
//             processAJAXPostQuery(_service("controlCenter", "run_script"), "src="+src, function(dataRaw) {
//                 $(".output_results>pre").append("\n"+dataRaw);
//                 script_running = false;
//                 $(".output_results").scrollTop($(".output_results>pre").height());
//             });
//         }
//     }, "json");
// }
</script>