<?php
if(!defined('ROOT')) exit('No direct script access allowed');
checkServiceSession();

handleActionMethodCalls();

function _service_system_stats() {
    loadModuleLib("logiksIDE", "api");
    return [
            [
                "title"=> "PAGES",
                "icon"=> "fas fa-file",
                "stats"=> getFileCountInDir(CMS_APPROOT."pages/defn/"),
                // "status"=> "xxx",
                // "subtext_stats"=> "3.48%",
                // "subtext"=> "Since Last Month"
            ],
            [
                "title"=> "Widgets",
                "icon"=> "fas fa-cubes",
                "stats"=> getFileCountInDir(CMS_APPROOT."plugins/widgets/") ."/". getFileCountInDir(CMS_APPROOT."pluginsDev/widgets/"),
                // "status"=> "increase",
                // "subtext_stats"=> "3.48%",
                "subtext"=> "App Widgets/Dev Widgets"
            ],
            [
                "title"=> "Modules",
                "icon"=> "fas fa-cubes",
                "stats"=> getFileCountInDir(CMS_APPROOT."plugins/modules/") ."/". getFileCountInDir(CMS_APPROOT."pluginsDev/modules/"),
                // "status"=> "increase",
                // "subtext_stats"=> "3.48%",
                "subtext"=> "App Modules/Dev Modules"
            ],
            [
                "title"=> "Tables",
                "icon"=> "fas fa-table",
                "stats"=> _db()?count(_db()->get_tableList()):0,
                // "status"=> "decrease",
                // "subtext_stats"=> "3.48%",
                "subtext"=> "App Tables"
            ],
            [
                "title"=> "Users",
                "icon"=> "fas fa-users",
                "stats"=> getDBRecordsCount("lgks_users", true, ["blocked"=>"false"]),
                // "status"=> "xxx",
                // "subtext_stats"=> "3.48%",
                "subtext"=> "Total Active Users"
            ],
            [
                "title"=> "Sessions",
                "icon"=> "fas fa-bolt",
                "stats"=> getDBRecordsCount("lgks_cache_sessions", true),
                // "status"=> "decrease",
                // "subtext_stats"=> "3.48%",
                "subtext"=> "Active User Sessions"
            ],
        ];
}

function _service_news_openlogiks() {
    return [
            [
                "avatar"=> "https://openlogiks.org/apps/home/media/images/logiks-logo.png",
                "username"=> "openlogiks",
                "timestamp"=> "Just now",
                "title"=> "Welcome to OpenLogiks",
                "text"=> "The best opensouce RAD framework fully integrated with multiple tools to make your web development experience a breeze",
            ],
            // [
            //     "avatar"=> "https://bootdey.com/img/Content/avatar/avatar1.png",
            //     "username"=> "openlogiks",
            //     "timestamp"=> "38 minutes ago",
            //     "title"=> "@Scott Sanders",
            //     "text"=> "Lorem ipsum Laborum sit laborum cillum proident dolore culpa
            //         reprehenderit qui enim labore do mollit in.",
            // ],
            // [
            //     "avatar"=> "https://bootdey.com/img/Content/avatar/avatar2.png",
            //     "username"=> "openlogiks",
            //     "timestamp"=> "2 hours ago",
            //     "title"=> "@Nina Wells",
            //     "text"=> "Lorem ipsum Laborum sit laborum cillum proident dolore culpa
            //         reprehenderit qui enim labore do mollit in.",
            // ],
        ];
}
?>