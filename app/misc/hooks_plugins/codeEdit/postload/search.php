<?php
	if(checkService("codeSearch")) {
?>
<script>
$(function() {
	$("#toolbar .right").prepend("<input type=text class='searchfield' name=searchfld id=searchfield title='Search Function' />");
	$("#searchfield").ghosttext();
	$("#searchfield").keydown(function(k) {
			if(k.keyCode==13) {
				searchFunc(this.value);
				//this.value="";
			}
		});
	$("#searchfield").autocomplete({
				minLength:1,
				source:getServiceCMD("codeSearch")+"&action=autocomplete&src=<?=$ext?>&format=json",
			});
});
function searchFunc(v) {
	l=getServiceCMD("codeSearch")+"&action=search&format=html&term="+v;
	lgksOverlayFrame(l).dialog({
				buttons:{},
				title:"Code Search",
				height:$(window).height()-80,
			});
}
</script>
<?php
		}
?>
