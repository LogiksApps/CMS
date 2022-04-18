<ul id="sidebarTab" class="nav nav-tabs nav-justified" data-tabs="tabs">
    {if $SIDEBAR_OPEN_APPS}
        <li role="presentation"><a href="#sidebarControls" data-toggle="tab"><i class="fa fa-cubes fa-fw"></i>&nbsp;CMS</a></li>
        <li role="presentation" class="active"><a href="#sidebarApps" data-toggle="tab"><i class="fa fa-object-group fa-fw"></i>&nbsp;APP</a></li>
    {else}
        <li role="presentation" class="active"><a href="#sidebarControls" data-toggle="tab"><i class="fa fa-cubes fa-fw"></i>&nbsp;CMS</a></li>
        <li role="presentation"><a href="#sidebarApps" data-toggle="tab"><i class="fa fa-object-group fa-fw"></i>&nbsp;APP</a></li>
    {/if}
    
    {if $SIDEBAR_FILES}
    <li role="presentation"><a href="#sidebarFiles" data-toggle="tab"><i class="fa fa-folder fa-fw"></i>&nbsp;SRC</a></li>
    {/if}
</ul>
<div id="sidebarPane" class="tab-content noselect">
    {if $SIDEBAR_OPEN_APPS}
        <div id='sidebarControls' class='tab-pane'>
            {widget src='sidebarMenu'}
        </div>
        <div id='sidebarApps' class='tab-pane active'>
            {widget src='sidebarApps'}
        </div>
    {else}
        <div id='sidebarControls' class='tab-pane active'>
            {widget src='sidebarMenu'}
        </div>
        <div id='sidebarApps' class='tab-pane'>
            {widget src='sidebarApps'}
        </div>
    {/if}
    {if $SIDEBAR_FILES}
        <div id='sidebarFiles' class='tab-pane'>
            {widget src='sidebarSrc'}
        </div>
    {/if}
</div>
