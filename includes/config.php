<?php

/**
 *    _, __,  _, _ __, _  _, _, _
 *   / \ |_) (_  | | \ | /_\ |\ |
 *   \ / |_) , ) | |_/ | | | | \|
 *    ~  ~    ~  ~ ~   ~ ~ ~ ~  ~
 *
 * Configuration file
 *
 * This file will contain your site/application settings. Ideally, you should
 * make this file autogenerable by a setup process.
 *
 * @package     ObsidianWorkspaces
 * @subpackage  Keruald
 * @author      Sébastien Santoro aka Dereckson <dereckson@espace-win.org>
 * @license     http://www.opensource.org/licenses/bsd-license.php BSD
 * @filesource
 *
 */

////////////////////////////////////////////////////////////////////////////////
///                                                                          ///
/// I. SQL configuration                                                     ///
///                                                                          ///
////////////////////////////////////////////////////////////////////////////////

//SQL configuration
$Config['sql']['product'] = 'MySQL';    //Only MySQL is currently implemented
$Config['sql']['host'] = 'localhost';
$Config['sql']['username'] = 'obsidian';
$Config['sql']['password'] = 'obsidian';
$Config['sql']['database'] = 'obsidian';

//SQL tables
$prefix = '';
define('TABLE_SESSIONS', $prefix . 'sessions');
define('TABLE_USERS', $prefix . 'users');
define('TABLE_PERMISSIONS', $prefix . 'permissions');
define('TABLE_UGROUPS', $prefix . 'users_groups');
define('TABLE_UGROUPS_MEMBERS', $prefix . 'users_groups_members');
define('TABLE_WORKSPACES', $prefix . 'workspaces');

//TODO: you can add here your own tables and views

////////////////////////////////////////////////////////////////////////////////
///                                                                          ///
/// II. Site configuration                                                   ///
///                                                                          ///
////////////////////////////////////////////////////////////////////////////////

//TODO: you can add here settings like default site theme or the app title.

//Dates
date_default_timezone_set("UTC");

//Secret key, used for some verification hashes in URLs (e.g. xhr calls)
//or forms.
$Config['SecretKey'] = 'Replace this by a secret key, like AdYN}"p/+D.U]M^MC&-Q~KFthXZCT*g<V:dL.@{Mt-Di1mEA\&~_Eh\I\WA';

//When reading files, buffer size
define('BUFFER_SIZE', 4096);

////////////////////////////////////////////////////////////////////////////////
///                                                                          ///
/// III. Script URLs                                                         ///
///                                                                          ///
////////////////////////////////////////////////////////////////////////////////

/*
 * The following settings give your script/application URL.
 *
 * Without mod_rewrite:
 *
 *   Subdirectory:
 *     - $Config['SiteURL'] = 'http://www.yourdomain.tld/application/index.php';
 *     - $Config['BaseURL'] = '/application/index.php';
 *
 *   Root directory:
 *     - $Config['SiteURL'] = 'http://www.yourdomain.tld/index.php';
 *     - $Config['BaseURL'] = '/index.php';
 *
 * With mod_rewrite:
 *
 *   Subdirectory:
 *     - $Config['SiteURL'] = 'http://www.yourdomain.tld/application';
 *     - $Config['BaseURL'] = '/application';
 *
 *     In .htaccess or your vhost definition:
 *       RewriteEngine On
 *       RewriteBase /application/
 *       RewriteCond %{REQUEST_FILENAME} !-f
 *       RewriteCond %{REQUEST_FILENAME} !-d
 *       RewriteRule . /application/index.php [L]
 *
 *   Root directory:
 *     - $Config['SiteURL'] = 'http://www.yourdomain.tld';
 *     - $Config['BaseURL'] = '';
 *
 *     In .htaccess or your vhost definition:
 *       RewriteEngine On
 *       RewriteBase /
 *       RewriteCond %{REQUEST_FILENAME} !-f
 *       RewriteCond %{REQUEST_FILENAME} !-d
 *       RewriteRule . /index.php [L]
 *
 *
 * If you don't want to specify the server domain, you can use get_server_url:
 *      $Config['SiteURL'] = get_server_url() . '/application';
 *      $Config['SiteURL'] = get_server_url();
 *
 * !!! No trailing slash !!!
 *
 */

$Config['SiteURL'] = get_server_url();
$Config['BaseURL'] = '';

//xmlHttpRequest callbacks URL
$Config['DoURL'] = $Config['SiteURL'] . "/do.php";

////////////////////////////////////////////////////////////////////////////////
///                                                                          ///
/// IV. Static content                                                       ///
///                                                                          ///
////////////////////////////////////////////////////////////////////////////////

//Where the static content is located?
//Static content = 4 directories: js, css, img and content
//On default installation, those directories are at site root.
//To improve site performance, you can use a CDN for that.
//
//Recommanded setting: $Config['StaticContentURL'] = $Config['SiteURL'];
//Or if this is the site root: $Config['StaticContentURL'] = '';
//With CoralCDN: $Config['StaticContentURL'] =  . '.nyud.net';
//
$Config['StaticContentURL'] = '';
//$Config['StaticContentURL'] = get_server_url() . '.nyud.net';

//Content directories
$Config['Content']['Cache'] = 'content/cache';
$Config['Content']['Help'] = 'content/help';
$Config['Content']['Workspaces'] = 'content/workspaces';

//ImageMagick paths
//Be careful on Windows platform convert could match the NTFS convert command.
$Config['ImageMagick']['convert']   = 'convert';
$Config['ImageMagick']['mogrify']   = 'mogrify';
$Config['ImageMagick']['composite'] = 'composite';
$Config['ImageMagick']['identify']  = 'identify';

////////////////////////////////////////////////////////////////////////////////
///                                                                          ///
/// V. Caching                                                               ///
///                                                                          ///
////////////////////////////////////////////////////////////////////////////////

/*
 * Some data (Smarty, OpenID and sessions) are cached in the cache directory.
 *
 * Security tip: you can move this cache directory outside the webserver tree.
 */
define('CACHE_DIR', 'cache');

/*
 * Furthermore, you can also enable a cache engine, like memcached, to store
 * data from heavy database queries, or frequently accessed stuff.
 *
 * To use memcached:
 *    - $Config['cache']['engine'] = 'memcached';
 *    - $Config['cache']['server'] = 'localhost';
 *    - $Config['cache']['port']   = 11211;
 *
 * To disable cache:
 *    - $Config['cache']['engine'] = 'void';
 *    (or don't write nothing at all)
 */
$Config['cache']['engine'] = 'void';

////////////////////////////////////////////////////////////////////////////////
///                                                                          ///
/// VI. Sessions                                                              ///
///                                                                          ///
////////////////////////////////////////////////////////////////////////////////

//If you want to use a common table of sessions / user handling
//with several websites, specify a different resource id for each site.
$Config['ResourceID'] = 32;
