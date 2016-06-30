--
-- MySQL 5.6.30
-- Thu, 30 Jun 2016 19:54:26 +0000
--


INSERT INTO `lgks_links` (`id`, `menuid`, `title`, `mode`, `category`, `menugroup`, `class`, `target`, `link`, `iconpath`, `tips`, `site`, `device`, `privilege`, `weight`, `onmenu`, `blocked`, `rules`, `creator`, `dtoc`, `dtoe`) VALUES 
('101', 'default', 'Site Manager', '*', '', '/', '', '', '#', 'fa fa-folder', 'Manage the various components required for running a site', 'cms', '*', '*', '50', 'true', 'false', '', 'root', '0000-00-00 00:00:00', '2016-03-04 02:04:18'),
('102', 'default', 'Data Controls', '*', '', '/', '', '', '#', '', 'Logiks Data Modules', 'cms', '*', '*', '51', 'true', 'false', '', 'root', '0000-00-00 00:00:00', '2016-03-04 02:04:18'),
('103', 'default', 'Media Manager', '*', '', '/', '', '', '#', '', 'Explore various assets, media and userdata', 'cms', '*', '*', '52', 'true', 'false', '', 'root', '0000-00-00 00:00:00', '2016-03-04 02:04:18'),
('104', 'default', 'Configurations', '*', '', '/', '', '', '#', '', 'Manage Global and Default Configurations', 'cms', '*', '*', '53', 'true', 'false', '', 'root', '0000-00-00 00:00:00', '2016-03-04 02:04:18'),
('106', 'default', 'Security Manager', '*', '', '/', '', '', '#', '', 'Manage All Security Related Issues', 'cms', '*', '*', '54', 'true', 'false', '', 'root', '0000-00-00 00:00:00', '2016-03-04 02:04:18'),
('107', 'default', 'Maintainance', '*', '', '/', '', '', '#', '', 'Install/Update Plugins, Themes, etc', 'cms', '*', '*', '55', 'true', 'false', '', 'root', '0000-00-00 00:00:00', '2016-03-04 02:04:18'),
('108', 'default', 'Privilege & Roles', '*', '', '/', '', '', '#', '', 'Privilege and roles for the site', 'cms', '*', '*', '56', 'true', 'false', '', 'root', '0000-00-00 00:00:00', '2016-03-04 02:04:18'),
('109', 'default', 'Site Reports', '*', '', '/', '', '', '#', '', 'Site Wide Reports', 'cms', '*', '*', '57', 'true', 'false', '', 'root', '0000-00-00 00:00:00', '2016-03-04 02:04:18'),
('112', 'default', 'Advanced Tools', '*', '', '/', '', '', '#', '', 'Advanced tools for developers', 'cms', '*', '*', '100', 'true', 'false', '', 'root', '0000-00-00 00:00:00', '2016-03-04 02:04:18'),
('114', 'default', 'Power Tools', '*', '', '/', '', '', '#', '', 'Super User Tools, required by root of the user', 'cms', '*', '*', '190', 'true', 'false', '', 'root', '0000-00-00 00:00:00', '2016-03-04 02:04:18'),
('119', 'default', 'CMS Help', '*', '', '/', '', '', '#', '', 'CMS Help contents', 'cms', '*', '*', '500', 'true', 'false', '', 'root', '0000-00-00 00:00:00', '2016-03-04 02:04:18');

INSERT INTO `lgks_links` (`id`, `menuid`, `title`, `mode`, `category`, `menugroup`, `class`, `target`, `link`, `iconpath`, `tips`, `site`, `device`, `privilege`, `weight`, `onmenu`, `blocked`, `rules`, `creator`, `dtoc`, `dtoe`) VALUES 
('120', 'default', 'Page Manager', '*', '', '101', '', '', 'modules/pageManager', '', 'Manage pages for your site', 'cms', '*', '*', '0', 'true', 'false', 'module#pageManager', 'root', '0000-00-00 00:00:00', '2016-03-04 02:04:18'),
('123', 'default', 'DBMS Manager', '*', '', '112', '', '', 'modules/dbEdit', '', 'Manage DBMS from within CMS', 'cms', '*', '*', '0', 'true', 'false', 'module#dbEdit', 'root', '0000-00-00 00:00:00', '2016-03-04 02:04:18'),
('169', 'default', 'CMS Branding', '*', '', '104', '', '', 'modules/settingsCMS', '', 'Brand and own your own CMS', 'cms', '*', '*', '500', 'true', 'false', 'module#settingsCMS', 'root', '0000-00-00 00:00:00', '2016-03-04 02:04:18'),
('128', 'default', 'User Manager', '*', '', '108', '', '', 'modules/credsMaster', '', 'Manage users and roles', 'cms', '*', '*', '0', 'true', 'false', 'module#credsMaster', 'root', '0000-00-00 00:00:00', '2016-03-04 02:04:18'),
('124', 'default', 'Core Settings', '*', '', '104', '', '', 'modules/settingsCore', '', 'Manage Core Configurations', 'cms', 'pc', 'e5d9dee0892c9f474a174d3bfffb7810', '0', 'true', 'false', 'module#settingsCore', 'root', '0000-00-00 00:00:00', '2016-03-04 02:04:18'),
('125', 'default', 'System Settings', '*', '', '104', '', '', 'modules/settingsJSON', '', 'Manage System Settings', 'cms', '*', '*', '0', 'true', 'false', 'module#settingsJSON', 'root', '0000-00-00 00:00:00', '2016-03-04 02:04:18'),
('165', 'default', 'RepoCenter', '*', '', '107', '', '', 'modules/repoCenter', '', 'Download and install new features from Repo Center', 'cms', '*', '*', '0', 'true', 'false', 'module#repoCenter', 'root', '0000-00-00 00:00:00', '2016-03-04 02:04:18'),
('129', 'default', 'Permissions Control', '*', '', '108', '', '', 'modules/credsRoles', '', 'Check and manage User permissions', 'cms', '*', '*', '0', 'true', 'false', 'module#credsRoles', 'root', '0000-00-00 00:00:00', '2016-03-04 02:04:18'),
('166', 'default', 'App Configurations', '*', '', '104', '', '', 'modules/settingsApps', '', 'Manage Current App Related Settings', 'cms', '*', '*', '0', 'true', 'false', 'module#settingsApps', 'root', '0000-00-00 00:00:00', '2016-03-04 02:04:18'),
('167', 'default', 'App Features', '*', '', '104', '', '', 'modules/settingsPlugins', '', 'Manage Plugin Related Options', 'cms', '*', '*', '0', 'true', 'false', 'module#settingsPlugins', 'root', '0000-00-00 00:00:00', '2016-03-04 02:04:18'),
('170', 'default', 'Log Book', '*', '', '109', '', '', 'modules/logBook', '', 'Central Place to look into all the logs', 'cms', '*', '*', '0', 'true', 'false', 'module#logBook', 'root', '0000-00-00 00:00:00', '2016-03-04 02:04:18'),
('168', 'default', 'App Domain Map', '*', '', '112', '', '', 'modules/settingsDomainmap', '', 'Update Domain-APP map', 'cms', '*', 'e5d9dee0892c9f474a174d3bfffb7810', '0', 'true', 'false', 'module#settingsDomainmap', 'root', '0000-00-00 00:00:00', '2016-03-04 02:04:18');