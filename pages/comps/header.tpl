<!-- Navigation -->
<nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
	<div class="navbar-header">
        <div class='pull-left thumbnail'><img src='{loadMedia("media/logos/logiks.png")}' class='photo' /></div>
        <a class="navbar-brand">{$APPS_NAME} {$APPS_VERS}</a>
    </div>
    <div class="navbar-header" title='Switch Between Available Sites' style='float: left;background: transparent;'>
        <div class="btn-group site-select-form">
            <button type="button" class="btn btn-default show-sidebar-menu">
          		<span class="fa fa-bars"></span>
            </button>
						
            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                {$PAGE.forSite}
                <span class="caret"></span>
            </button>
            
						<ul class="dropdown-menu" role="menu">
							{foreach from=$PAGE.siteList item=site}
									<li><a href='{$site.url}'>{$site.title} Site</a></li>
							{/foreach}
						</ul>

            <a href='{$WEBROOT}?site={$PAGE.forSite}' target='_blank' type="button" class="btn btn-default" title='Preview Site'>
                <i class='fa fa-rocket' style='margin: 3px;'></i></a>
        </div>
    </div>

    <ul id='toolsMenu' class="nav navbar-top-links navbar-right" style='text-align: right;'>
    	<li class="dropdown">
            <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                <i class="fa fa-user fa-fw"></i> {$SESS_USER_NAME} <i class="fa fa-caret-down"></i>
            </a>
            <ul class="dropdown-menu dropdown-user">
                <li><a href="{_link("modules/myAccounts")}"><i class="fa fa-user fa-fw"></i> My Profile</a></li>
                <li><a href="{_link("modules/myPassword")}"><i class="fa fa-user fa-fw"></i> My Password</a></li>
                <li class="divider"></li>
                <li><a class='noauto' href="{$WEBROOT}logout.php?site=cms"><i class="fa fa-sign-out fa-fw"></i> Logout</a>
                </li>
            </ul>
        </li>
    </ul>
    
    <div class="searchForm pull-right hidden-xs" style="width: 350px;height: 49px;padding-top: 8px;">
        {pluginComponent src='codeSearch.searchbar'}
    </div>
  </div>
</nav>