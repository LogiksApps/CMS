<!-- Navigation -->
<nav class="navbar navbar-default navbar-static-top noselect" role="navigation" style="margin-bottom: 0">
	<div class="navbar-header navbar-header-brand">
        <div class='pull-left thumbnail'><img src='{loadMedia("/media/logos/logiks.png")}' class='photo' /></div>
        <a class="navbar-brand">{$APPS_NAME} {$APPS_VERS}</a>
    </div>
    
    <button id="leftMenuOpen" class="pull-left btn btn-default" style="margin-top: 8px;margin-right: 5px;height: 35px;padding-top: 5px;outline: none;"><i class="fi fi-rr-bars-staggered"></i></button>
    
    <div class="navbar-header" title='Switch Between Available Sites' style='float: left;background: transparent;'>
        <div class="btn-group site-select-form">
            <button type="button" class="btn btn-default show-sidebar-menu">
          		<span class="fa fa-bars"></span>
            </button>
						
            <button type="button" class="btn btn-default dropdown-toggle btn-sitedropmenu" data-toggle="dropdown" aria-expanded="false">
                {$PAGE.forSite}
                <span class="caret"></span>
            </button>
            
			<ul class="dropdown-menu" role="menu">
				{foreach from=$PAGE.siteList item=site}
						<li><a href='{$site.url}'>{$site.title} Site</a></li>
				{/foreach}
			</ul>

            <!--<a href='{$WEBROOT}?site={$PAGE.forSite}' target='_blank' type="button" class="btn btn-default" title='Preview Site'>-->
            <!--    <i class='fa fa-rocket' style='margin: 3px;'></i></a>-->
            <a href='#' type="button" class="btn btn-default" title='Code Search' onclick="openCodeSearch(this)"><i class='fa-light fa-magnifying-glass' style='margin: 3px;'></i></a>
        </div>
        <a href='#' type="button" class="btn btn-default btn-action btn-open-file" title='Open File'><i class='fa-light fa-folder-open' style='margin: 3px;'></i></a>
        
        <div class="dropdown btn-dropdown">
            <button type="button" class="btn btn-success btn-action btn-run dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" onclick="loadStudioTools()" >
                    <i class='fa-light fa-play' style='margin: 3px;'></i>
                    <span class="caret"></span>
            </button>
            
            <ul id='runToolsMenu' class="dropdown-menu runtools-menu" role="menu">
    			<li><a href='{$WEBROOT}?site={$PAGE.forSite}' target='_blank' type="button" class="btn btn-default" title='Preview Site'><i class='fa fa-rocket' style='margin: 3px;'></i> Preview</a></li>
    			<div class='submenu'>
    			    <!--<li><a href='#'><i class='fa fa-star'></i> Other Tools <label class='label label-info pull-right'>2</label></a></li>-->
    			</div>
    		</ul>
		</div>
    </div>
    
    <ul id='toolsMenu' class="nav navbar-top-links navbar-right" style='text-align: right;'>
    	<li class="dropdown">
            <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                <i class="fa fa-user fa-fw"></i> <span>{$SESS_USER_NAME}</span> <i class="fa fa-caret-down"></i>
            </a>
            <ul class="dropdown-menu dropdown-user">
                <li><a href="{_link("modules/myAccounts")}"><i class="fa fa-user fa-fw"></i> My Profile</a></li>
                <li><a href="{_link("modules/myPassword")}"><i class="fa fa-user fa-fw"></i> My Password</a></li>
                <li class="divider"></li>
                <li><a class='noauto' href="{$WEBROOT}logout.php?site=#SITENAME#"><i class="fa fa-power-off fa-fw"></i> Logout</a>
                </li>
            </ul>
        </li>
    </ul>
    <div class="chatIcon rightElement">
         {pluginComponent src='logiksChat.appChat'}
    </div>
    
    <div class="searchForm pull-right hidden-xs rightElement" style="">
        {pluginComponent src='codeSearch.searchbar'}
    </div>
    <!--<div class="eStoreButton pull-right hidden-xs rightElement" style="">-->
    <!--    <button onclick="openEStore()" type="button" class="btn btn-warning btn-search"><span class="fa fa-cubes"></span> eStore</button>-->
    <!--</div>-->
    <div class="todoButton pull-right hidden-xs rightElement" style="">
        <button onclick="openCMSTodos()" type="button" class="btn btn-info btn-search">@TODO</button>
    </div>
  </div>
</nav>
