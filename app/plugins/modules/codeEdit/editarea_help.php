<?php
$helpCmds=array();
$helpCmds["Ctrl-F / Cmd-F"]="Start Search,Replace";
$helpCmds["Ctrl-H / Cmd-H"]="Toggle Syntax Coloring";
$helpCmds["Ctrl-G / Cmd-G"]="Go To Line";
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

