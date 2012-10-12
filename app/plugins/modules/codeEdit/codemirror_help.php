<?php
$helpCmds=array();
$helpCmds["Ctrl-F / Cmd-F"]="Start Searching";
$helpCmds["Ctrl-G / Cmd-G"]="Find Next";
$helpCmds["Shift-Ctrl-G / Shift-Cmd-G"]="Find Previous";
$helpCmds["Shift-Ctrl-F / Cmd-Option-F"]="Replace";
$helpCmds["Ctrl-Space / Cmd-Space"]="Autocomplete Help Popup";
$helpCmds["Ctrl-Q / Cmd-Q"]="Invoke Fold Functionality";
$helpCmds["Ctrl-Z / Cmd-Z"]="Undo";
$helpCmds["Ctrl-Y / Cmd-Y"]="Redo";
?>

<div style='width:500px;'>
<table width=100% cellpadding=2 cellspacing=0 border=0>
<?php
	foreach($helpCmds as $a=>$b) {
		echo "<tr><th align=left>$a</th><td>$b</td></tr>";
	}
?>
</table>
</div>
