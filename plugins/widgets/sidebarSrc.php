<?php
if(!defined('ROOT')) exit('No direct script access allowed');

// echo getApp_PROPS("APPS_ROUTER");
if(!isset($_COOKIE["LOGIKCMS-SIDEBAR-MODE"])) $_COOKIE["LOGIKCMS-SIDEBAR-MODE"] = "modern";

// echo $_COOKIE["LOGIKCMS-SIDEBAR-MODE"];

if(CMS_SITENAME=="cms") {
    include_once __DIR__."/sidebarFiles1.php";
} else {
    if($_COOKIE["LOGIKCMS-SIDEBAR-MODE"]=="source") {
        include_once __DIR__."/sidebarFiles1.php";
    } else {
        include_once __DIR__."/sidebarFiles2.php";
    }
}

?>
<style>
#sidebarFiles .switch {
  position: relative;
  display: inline-block;
  width: 60px;
  height: 34px;
}

/* Hide default HTML checkbox */
#sidebarFiles .switch input {
  opacity: 0;
  width: 0;
  height: 0;
}

/* The slider */
#sidebarFiles .slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #ccc;
  -webkit-transition: .4s;
  transition: .4s;
}

#sidebarFiles .slider:before {
  position: absolute;
  content: "";
  height: 26px;
  width: 26px;
  left: 4px;
  bottom: 4px;
  background-color: white;
  -webkit-transition: .4s;
  transition: .4s;
}

#sidebarFiles input:checked + .slider {
  background-color: #2196F3;
}

#sidebarFiles input:focus + .slider {
  box-shadow: 0 0 1px #2196F3;
}

#sidebarFiles input:checked + .slider:before {
  -webkit-transform: translateX(26px);
  -ms-transform: translateX(26px);
  transform: translateX(26px);
}

/* Rounded sliders */
#sidebarFiles .slider.round {
  border-radius: 34px;
}
#sidebarFiles .slider.round:before {
  border-radius: 50%;
}

/*sidebarToggleModes*/
.sidebarToggleModes {
    position: fixed;
    bottom: 0px;
    left: 0px;
    width: 250px;
    text-align: center;
}
.text-span-left {
    float: left;
    text-align: center;
    padding-top: 6px;
    padding-left: 5px;
    font-size: 16px;
    font-weight: bold;
}
.text-span-right {
    float: right;
    text-align: center;
    padding-top: 6px;
    padding-right: 5px;
    font-size: 16px;
    font-weight: bold;
}
</style>
<?php
if(CMS_SITENAME!="cms") {
?>
<div class='sidebarToggleModes'>
    <span class='text-span-left'>Modern</span>
    <label class="switch">
      <input type="checkbox" name='sidebarModeToggle' onchange='changeSidebarMode(this)' <?=($_COOKIE["LOGIKCMS-SIDEBAR-MODE"]=="source"?"checked":"")?> >
      <span class="slider"></span>
    </label>
    <span class='text-span-right'>Source</span>
</div>
<script>
$(function() {
    //
});
function changeSidebarMode(src) {
    lgksLoader("Switching Source Mode");
    // alert($(src).is(":checked"));
    if($(src).is(":checked")) {
        $.cookie("LOGIKCMS-SIDEBAR-MODE", "source");
    } else {
        $.cookie("LOGIKCMS-SIDEBAR-MODE", "mordern");
    }
    setTimeout(function() {
        document.location.reload();
    }, 300);
} 
</script>
<?php
}
?>