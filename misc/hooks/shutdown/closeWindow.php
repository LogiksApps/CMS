<?php
if(strpos(strtolower($_SERVER['HTTP_USER_AGENT']), "electron")) return;

if(session_check() && PAGE=="home") {
?>
<script>
window.onbeforeunload = function (e) {
    e = e || window.event;

    // For IE and Firefox prior to version 4
    if (e) {
        e.returnValue = 'Do you want to leave this site?';
    }

    // For Safari
    return 'Do you want to leave this site?';
};
</script>
<?php
}
?>