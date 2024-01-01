{$PAGE.DOCTYPE}

<html {$PAGE.HTML_ATTRIBUTES}>
  <head {$PAGE.HEAD_ATTRIBUTES}>
    <title>{$APPS_TITLE}</title>
    {$PAGE.FAVICON}
    
    <!-- start: META -->
    <meta name='description' content='{$PAGE.DESCRIPTION}' />
	<meta name='keywords' content='{$PAGE.KEYWORDS}' />
	<meta name='robots' content='{$PAGE.ROBOTS}' />
	
	<!-- end: META -->
	
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta name="viewport" content="initial-scale=0.8, minimum-scale=0.5, maximum-scale=1.5, user-scalable=yes, minimal-ui" id='viewport-meta'>
	<meta name="msapplication-tap-highlight" content="no">
	<meta name="google" value="notranslate">
	<meta id="win8Icon" name="msapplication-TileImage" content="">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-title" content="">
	<meta name="apple-mobile-web-app-status-bar-style" content="black">
	
	{$PAGE.CSS}
	{$PAGE.JS_PRELOAD}
	
	{logikscripts}
	
	{pluginComponent src='perspectives.headers'}
	
	{hook src='postHTMLHead'}
  </head>
  <body {$PAGE.BODY_CLASS} {$PAGE.BODY_ATTRIBUTES} >
  	<div id="wrapper" class='wrapper'>
  		<div id='header'>
		{component src='header'}
		</div>
		<div id='sidebar'>
	    {component src='sidebar'}
	    </div>
	    <div id="page-wrapper">
	        {viewpage}
	    </div>
	</div>
	{hook src='postHTMLBody'}
  </body>
	{$PAGE.JS_POSTLOAD}
</html>
{hook src='postHTML'}
