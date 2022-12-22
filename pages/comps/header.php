<?php
if(!defined('ROOT')) exit('No direct script access allowed');

if(!isset($_REQUEST['forSite'])) $_REQUEST['forSite'] = "";

_pageConfig("forSite",$_REQUEST['forSite']);
_pageConfig("siteList",_session("siteList"));
?>