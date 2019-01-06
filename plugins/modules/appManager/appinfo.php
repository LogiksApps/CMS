<?php
if(!defined('ROOT')) exit('No direct script access allowed');

$refid=$_REQUEST['refid'];
$appInfo=fetchLogiksAppImage($refid);
//<div style='width:800px;height:300px;'>

// printArray($appInfo);
//issues_url
//forks_count
//has_wiki
//open_issues_count
//license
//default_branch

//archive_url
//https://api.github.com/repos/LogiksApps/Apps_CMS/{archive_format}{/ref}
//https://api.github.com/repos/LogiksApps/Apps_CMS/zip/
//https://github.com/LogiksApps/Apps_CMS/archive/master.zip
?>
<div>
<pre>
<?=$appInfo['description']?>
</pre>

<button class='btn btn-success pull-left' onclick="installApp('<?=$appInfo['full_name']?>')">Install App</button>
  
<a class='btn btn-primary pull-right' href='<?=$appInfo['html_url']?>' target=_blank>View Source</a>
<!-- <a class='btn btn-danger pull-right' href='<?=$appInfo['issues_url']?>' target=_blank>View Issues</a> -->
  
<div class='clearfix'></div>
</div>