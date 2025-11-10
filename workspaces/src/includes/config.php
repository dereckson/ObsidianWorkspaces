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

use Keruald\Cache\Engines\CacheVoid;
use Keruald\Database\Engines\MySQLiEngine;
use Keruald\OmniTools\HTTP\Requests\Request;

////////////////////////////////////////////////////////////////////////////////
///                                                                          ///
/// I. SQL configuration                                                     ///
///                                                                          ///
////////////////////////////////////////////////////////////////////////////////

//SQL configuration

$Config['sql']['engine'] = MySQLiEngine::class;
$Config['sql']['host'] = $_ENV["DB_HOST"] ?? 'localhost';
$Config['sql']['username'] = $_ENV["DB_USER"] ?? 'obsidian';
$Config['sql']['password'] = $_ENV["DB_PASSWORD"] ?? 'obsidian';
$Config['sql']['database'] = $_ENV["DB_NAME"] ?? 'obsidian';
$Config['sql']['fetch_mode'] = MYSQLI_BOTH;
$Config['sql']['dontThrowExceptions'] = true;

//SQL tables
$prefix = '';
define('TABLE_PERMISSIONS', $prefix . 'permissions');
define('TABLE_USERS', $prefix . 'users');
define('TABLE_USERS_AUTH', $prefix . 'users_auth');
define('TABLE_UGROUPS', $prefix . 'users_groups');
define('TABLE_UGROUPS_MEMBERS', $prefix . 'users_groups_members');
define('TABLE_SESSIONS', $prefix . 'sessions');
define('TABLE_WORKSPACES', $prefix . 'workspaces');

////////////////////////////////////////////////////////////////////////////////
///                                                                          ///
/// II. Site configuration                                                   ///
///                                                                          ///
////////////////////////////////////////////////////////////////////////////////

//Dates
date_default_timezone_set("UTC");

//Secret key, used for some verification hashes in URLs (e.g. xhr calls)
//or forms.
$Config['SecretKey'] = 'Replace this by a secret key, like AdYN}"p/+D.U]M^MC&-Q~KFthXZCT*g<V:dL.@{Mt-Di1mEA\&~_Eh\I\WA';

//When reading files, buffer size
const BUFFER_SIZE = 4096;

//Site theme
$Config['Theme'] = 'bluegray';

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

$Config['SiteURL'] = Request::getServerURL();
$Config['BaseURL'] = '';

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
$Config['Content']['Disclaimers'] = 'content/disclaimers';

/*
 * The following settings configure your document storage engine.
 *
 * To use MongoDB:
 *
 *     $Config['DocumentStorage'] = [
 *         'Type' => 'MongoDB',
 *         'Host' => 'mymongoinstance.domain.tld',
 *         'Port' => 27017,
 *         'Database' => 'obsidian'
 *     ];
 *
 * To use MongoDB, and authenticate with a username and a password:
 *
 *     $Config['DocumentStorage'] = [
 *         'Type' => 'MongoDB',
 *         'Host' => 'mymongoinstance.domain.tld',
 *         'Port' => 27017,
 *         'Database' => 'obsidian',
 *         'Username' => 'yourusername',
 *         'Password' => 'yourpassword'
 *     ];
 *
 * To connect to MongoDB with SSL, use the same syntax and add a SSL context as 'SSL' parameter.
 * Documentation about SSL context is located at the following PHP documentation URL:
 * http://www.php.net/manual/en/context.ssl.php
 *
 *     $Config['DocumentStorage'] = [
 *         'Type' => 'MongoDB',
 *         'Host' => 'mymongoinstance.domain.tld',
 *         'Port' => 27017,
 *         'Database' => 'obsidian',
 *         'SSL' => [
 *             'cafile' => '/path/to/CAcertificate.crt',
 *             'local_cert' => '/path/to/yourcertificate.pem',
 *             'verify_peer' => true,
 *             'allow_self_signed' => false,
 *             'CN_match' => 'the server certificate expected CN'
 *         ]
 *     ];
 *
 *
 * If you don't want to deploy a MongoDB server, you can use either MySQL
 * or SQLite 3 if you need concurrency, either plain text files if you're
 * the only user as a fallback.
 *
 *
 * For MySQL, it uses the same connection as the main application.
 *
 *     $Config['DocumentStorage'] = [
 *         'Type' => 'MySQL',
 *         'Table' => $prefix . 'collections',
 *     ];
 *
 * Engine will automatically intialize the database if the file hasn't been found.
 *
 * You can also store the table in another database with the db.table syntax:
 *
 *     $Config['DocumentStorage'] = [
 *         'Type' => 'MySQL',
 *         'Table' => 'obsidian_data.collections',
 *     ];
 *
 *
 * To use SQLite 3:
 *
 *     $Config['DocumentStorage'] = [
 *         'Type' => 'SQLite',
 *         'File' => 'content/collections.db',
 *     ];
 *
 * Engine will automatically intialize the database if the file hasn't been found.
 *
 *
 * To use file storage, create a folder and gives it as path parameter:
 *
 *     $Config['DocumentStorage'] = [
 *         'Type' => 'Files',
 *         'Path' => 'content/collections',
 *     ];
 *
 */
$Config['DocumentStorage'] = [
    'Type' => 'MongoDB',
    'Host' => 'localhost',
    'Port' => 27017
];

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
const CACHE_DIR = 'cache';

/*
 * Furthermore, you can also enable a cache engine, like memcached, to store
 * data from heavy database queries, or frequently accessed stuff.
 *
 * To use memcached:
 *    - $Config['cache']['engine'] = CacheMemcached::class;
 *    - $Config['cache']['server'] = 'localhost';
 *    - $Config['cache']['port']   = 11211;
 *
 * To disable cache:
 *    - $Config['cache']['engine'] = CacheVoid::class;
 *    (or omit the cache key)
 */
$Config['cache']['engine'] = CacheVoid::class;

////////////////////////////////////////////////////////////////////////////////
///                                                                          ///
/// VI. Sessions                                                              ///
///                                                                          ///
////////////////////////////////////////////////////////////////////////////////

//If you want to use a common table of sessions / user handling
//with several websites, specify a different resource id for each site.
$Config['ResourceID'] = 32;
