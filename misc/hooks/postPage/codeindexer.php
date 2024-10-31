<?php
if(!defined('ROOT')) exit('No direct script access allowed');


if(!isset($_SESSION['CODE_INDEX_CHECK']) && PAGE=="home") {
    $_SESSION['CODE_INDEX_CHECK'] = true;
    ?>
    <script>$(function() {processAJAXQuery(_service("codeIndexer", "check"))});</script>
    <?php
}
?>