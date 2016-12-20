{$PAGE.DOCTYPE}

<html {$PAGE.HTML_ATTRIBUTES}>
  <head {$PAGE.HEAD_ATTRIBUTES}>
    
    {$PAGE.VIEWPORT}
	{$PAGE.CSS}
	{$PAGE.JS_PRELOAD}
	
	{logikscripts}
	
	{hook src='postHTMLHead'}
  </head>
  <body {$PAGE.BODY_CLASS} {$PAGE.BODY_ATTRIBUTES} >
  	<div class="container-fluid">
		{viewpage}
	</div>
	{hook src='postHTMLBody'}
  </body>
	{$PAGE.JS_POSTLOAD}
</html>
{hook src='postHTML'}
