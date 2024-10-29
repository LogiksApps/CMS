<?php
if(!defined('ROOT')) exit('No direct script access allowed');

if(!checkRootAccess()) {
    return;
}

loadModule("pages");

function pageContentArea() {
	return "<div class='table-responsive' style='padding-right: 6px;'><table class='table table-striped table-hover table-condensed'>
	<thead id='app1' class='hidden'><tr>
		<th width=50px>SL#</th>
		<th>Title</th>
		<th width=150px>AppName</th>
		<th width=150px>Vers</th>
		<th width=150px>Router</th>
		<th width=100px>Published</th>
		<th width=100px>Status</th>
		<th width=100px>DevMode</th>
		<th width=100px>Access</th>
		<th width=170px>-</th>
	</tr></thead>
	<thead id='app2' class='hidden'><tr>
		<th width=50px>SL#</th>
		<th>Title</th>
		<th width=150px>Package</th>
    <th width=150px>Type</th>
    <th width=150px>Price</th>
		<th width=150px>Category</th>
		<th>Descs</th>
		<th width=100px>Installed</th>
		<th width=100px>Updated</th>
		<th width=170px>-</th>
	</tr></thead>
	<tbody id='appTable'>
		<tr><td colspan=20><h2 align=center>Checking Installation ...</h2></td></tr>
	</tbody>
	</table></div>
	";
}
function pageSidebar() {
	// <form role='search'>
	//     <div class='form-group'>
	//       <input type='text' class='form-control' placeholder='Search'>
	//     </div>
	// </form>
	return "<h3 class='page-title-bold'><b>APP</b>Images <button class='btn btn-primary pull-right' onclick='relistImages()' title='Rebuild Cache'><i class='fa fa-retweet'></i></button></h3><div id='componentTree' class='componentTree list-group list-group-root well'></div>";
}

echo _css(["appManager"]);
echo _js(["appManager"]);

printPageComponent(false,[
		"toolbar"=>[
			//"searchApps"=>["title"=>"Search Apps","type"=>"search","align"=>"right"],
	
			"loadLocalApps"=>["title"=>"Installed","align"=>"right","class"=>"active"],
			"loadAppImages"=>["title"=>"New Apps","align"=>"right"],
            "loadArchived"=>["title"=>"Archived","align"=>"right"],
            "loadTrashed"=>["title"=>"Trash","align"=>"right"],
		
		    "reloadListUICache"=>["icon"=>"<i class='fa fa-retweet'></i>","tips"=>"Recache data"],
			"reloadListUI"=>["icon"=>"<i class='fa fa-refresh'></i>"],
			['type'=>"bar"],
			"uploadAppZip"=>["icon"=>"<i class='fa fa-upload'></i>","tips"=>"Create New"],
			//"rename"=>["icon"=>"<i class='fa fa-terminal'></i>","class"=>"onsidebarSelect onOnlyOneSelect","tips"=>"Rename Content"],
			//"openExternal"=>["icon"=>"<i class='fa fa-external-link'></i>","class"=>"onsidebarSelect"],
			//"preview"=>["icon"=>"<i class='fa fa-eye'></i>","class"=>"onsidebarSelect onOnlyOneSelect","tips"=>"Preview Content"],
			//
			//"removeApps"=>["icon"=>"<i class='fa fa-trash'></i>","class"=>"onsidebarSelect"],
		],
		"sidebar"=>false,
		"contentArea"=>"pageContentArea"
	]);
?>
<style>
#appTable tr td.actions {
    white-space: nowrap;
}
</style>
<script id="appRowTemplate" type="text/x-handlebars-template">
	{{#each apps}}
	<tr class='{{#if readonly}}danger{{/if}}' refid='{{appkey}}' uuid='{{uuid}}'>
		<th>{{@index}}</th>
		<td>{{title}}</td>
		<td>{{appkey}}</td>
		<td>{{vers}}</td>
		<td>{{router}}</td>
		<td>{{published}}</td>
		<td>{{status}}</td>
		<td>{{devmode}}</td>
		<td>{{access}}</td>
		<td class='actions'>
			<i class="fa fa-pencil cmdAction pull-left" cmd="editApp" appkey="{{appkey}}" title="Edit App"></i>
      <i class="fa fa-gear cmdAction pull-left" cmd="configureApp" appkey="{{appkey}}" title="Configure App"></i>
      
			<a href="{{url}}" target=_blank class="pull-left fa fa-eye" title="Preview"></a>
      
      <i class="fa fa-eraser cmdAction pull-left" cmd="flushCache" appkey="{{appkey}}" title="Purge Cache for App"></i>
      
			
			{{{actionBtns this}}}
		</td>
	</tr>
	{{/each}}
</script>
<script id="archivedRowTemplate" type="text/x-handlebars-template">
	{{#each apps}}
	<tr class='{{#if readonly}}danger{{/if}}' refid='{{appkey}}' uuid='{{uuid}}'>
		<th>{{@index}}</th>
		<td>{{title}}</td>
		<td>{{appkey}}</td>
		<td>{{vers}}</td>
		<td>{{router}}</td>
		<td>{{published}}</td>
		<td>{{status}}</td>
		<td>{{devmode}}</td>
		<td>{{access}}</td>
		<td class='actions'>
      <i class="fa fa-undo cmdAction pull-left" cmd="restoreApp" appkey="{{appkey}}" title="Restore Archived App"></i>
      
      {{{actionBtns this}}}
		</td>
	</tr>
	{{/each}}
</script>
<script id="trashedRowTemplate" type="text/x-handlebars-template">
	{{#each apps}}
	<tr class='{{#if readonly}}danger{{/if}}' refid='{{appkey}}' uuid='{{uuid}}'>
		<th>{{@index}}</th>
		<td>{{title}}</td>
		<td>{{appkey}}</td>
		<td>{{vers}}</td>
		<td>{{router}}</td>
		<td>{{published}}</td>
		<td>{{status}}</td>
		<td>{{devmode}}</td>
		<td>{{access}}</td>
		<td class='actions'>
      <i class="fa fa-undo cmdAction pull-left" cmd="restoreApp" appkey="{{appkey}}" title="Restore Archived App"></i>
      
      {{{actionBtns this}}}
		</td>
	</tr>
	{{/each}}
</script>
<script id="imageRowTemplate" type="text/x-handlebars-template">
	{{#each appimages}}
	<tr class='{{#if noinstall}}danger{{/if}}' refid='{{appkey}}'>
		<th>{{@index}}</th>
		<td>{{name}}</td>
		<td>{{package}}</td>
    <td>{{type}}</td>
    <td>
      {{pricing_type}}
    </td>
		<td>{{category}}</td>
		<td>{{descs}}</td>
		<td>{{installed}}</td>
		<td>{{release_updated}}</td>
		<td class='actions'>
      <i class="fa fa-info-circle fa-2x pull-right cmdAction pull-left" cmd="appimageInfo" appkey="{{refid}}" title="View and install this app"></i>
      
      <a href="{{homepage}}" target=_blank class="pull-left fa fa-external-link" title="Checkout homepage"></a>
      
      <div class="appinfo modal fade" role="dialog">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header" style='height: 70px;'>
              <div class="thumbnail pull-left" style="border: 0px;width: 50px;text-align: center;">
                {{# if logo_url}}
                <img src="{{logo_url}}" class="img-responsive img-rounded">
                {{else}}
                <img src="https://avatars0.githubusercontent.com/u/1589427?s=50&v=4" class="img-responsive img-rounded">
                {{/if}}
              </div>
              <button type="button" class="close" data-dismiss="modal">&times;</button>
              <a class='pull-right' href='{{homepage}}' target=_blank style='margin-top:4px;margin-right:10px;'><i class='fa fa-external-link'></i></a>
              <h4 class="modal-title pull-left" style='line-height: 47px;'>{{name}} [{{package}}]</h4>
            </div>
            <div class="modal-body">
              <p style='font-size: 14px;padding: 3px;margin: -3px;margin-bottom: 16px;'>{{{descs}}}</p>
              {{{htmltable this}}}
            </div>
            <div class="modal-footer text-right">
              <button class='btn btn-info pull-left' data-dismiss="modal">Cancel</button>
              <button class='btn btn-success' onclick='installAppImage("{{package}}")'>Install</button>
            </div>
          </div>
        </div>
      </div>
		</td>
	</tr>
	{{/each}}
</script>


<script id="imageTemplate" type="text/x-handlebars-template">
	{{#each apps}}
	<div class='list-group-item list-folder'><a href='#item-{{@index}}' data-toggle='collapse'><i class='glyphicon glyphicon-folder-close'></i>{{@key}}</a></div>
	<div class='list-group-folder collapse' id='item-{{@index}}'>
	{{#each this}}
	<div class='list-group-item list-file' title='{{descs}}' data-refid='{{refid}}' data-fullname='{{{full_name}}}' data-url='{{url}}' data-toggle='tooltip' data-placement='right'>
		<a href='#'><i class='fa fa-file'></i><span class='text'>{{name}}</span></a>
	</div>
	{{/each}}
	</div>
	{{/each}}
</script>


<div id='appForm' class="modal fade" role="dialog">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h3 style='margin: 0px;'>Install New App</h3>
        </div>
        <div class="modal-body">
          <form class="form-horizontal">
              <input type='hidden' name='refid' />
              <div class="form-group">
                <label class="control-label col-sm-2" for="appname">AppName:</label>
                <div class="col-sm-10">
                  <input type="email" class="form-control" name="appname" placeholder="App Name">
                </div>
              </div>
              <div class="form-group">
                <label class="control-label col-sm-2" for="db">DB Config:</label>
                <div class="col-sm-10">
                  <textarea name='db' class='form-control'>{
            "driver": "mysqli",
            "host": "localhost",
            "port": "",
            "database": "",
            "user": "",
            "pwd": "",
            "prefix": "do",
            "suffix": "",
            "readonly": false,
            "block": [],
            "allowSQL": true
        }</textarea>
                </div>
              </div>
            </form>
        </div>
        <div class="modal-footer text-right">
          <button class='btn btn-info pull-left' data-dismiss="modal">Cancel</button>
          <button class='btn btn-success' onclick='startAppInstallation(this)'>Start Installation</button>
        </div>
      </div>
    </div>
</div>