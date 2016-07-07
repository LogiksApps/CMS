<ul id="sidebarTab" class="nav nav-tabs nav-justified" data-tabs="tabs">
    <li role="presentation" class="active"><a href="#sidebarControls" data-toggle="tab"><i class="fa fa-cubes fa-fw"></i>&nbsp;CMS</a></li>
    <!-- <li role="presentation"><a href="#sidebarPages" data-toggle="tab"><i class="fa fa-sitemap fa-fw"></i>&nbsp;Pages</a></li> -->
    <li role="presentation"><a href="#sidebarFiles" data-toggle="tab"><i class="fa fa-folder fa-fw"></i>&nbsp;Files</a></li>
</ul>
<div id="sidebarPane" class="tab-content">
    <div id='sidebarControls' class='tab-pane active'>
        {widget src='sidebarMenu'}
    </div>
    <!-- <div id='sidebarPages' class='tab-pane'>
        {widget src='sidebarPages'}
    </div> -->
    <div id='sidebarFiles' class='tab-pane'>
        {widget src='sidebarFiles'}
    </div>
</div>
