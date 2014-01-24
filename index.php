<?php

/**
 *    _, __,  _, _ __, _  _, _, _
 *   / \ |_) (_  | | \ | /_\ |\ |
 *   \ / |_) , ) | |_/ | | | | \|
 *    ~  ~    ~  ~ ~   ~ ~ ~ ~  ~
 *
 * Main web application entry point
 *
 * @package     ObsidianWorkspaces
 * @subpackage  Controllers
 * @author      Sébastien Santoro aka Dereckson <dereckson@espace-win.org>
 * @license     http://www.opensource.org/licenses/bsd-license.php BSD
 * @filesource
 * 
 */

////////////////////////////////////////////////////////////////////////////////
///
/// Initialization
///

//Keruald and Obsidian Workspaces libraries
include('includes/core.php');
include('includes/cache/cache.php');
include('includes/objects/workspace.php');

////////////////////////////////////////////////////////////////////////////////
///
/// Session
///

//Starts a new session or recovers current session
$Session = Session::load();

//Handles login or logout
include("includes/login.php");

//Gets current user information
$CurrentUser = $Session->get_logged_user();

////////////////////////////////////////////////////////////////////////////////
///
/// Template/L10n engine
///

define('THEME', 'bluegray');

require('includes/smarty/Smarty.class.php');
$smarty = new Smarty();
$current_dir = dirname(__FILE__);
$smarty->template_dir = $current_dir . '/skins/' . THEME;

$smarty->compile_dir = $Config['Content']['Cache'] . '/compiled';
$smarty->cache_dir = $Config['Content']['Cache'];
$smarty->config_dir = $current_dir;

$smarty->config_vars['StaticContentURL'] = $Config['StaticContentURL'];

//Loads language files
initialize_lang();
lang_load('core.conf');

////////////////////////////////////////////////////////////////////////////////
///
/// Serves the requested page
///

$url = get_current_url_fragments();

//If the user isn't logged in (is anonymous), prints login/invite page & dies.
if ($CurrentUser->id == ANONYMOUS_USER) {
    //Anonymous user
    include('controllers/anonymous.php');
    exit;
}

//Workspace
if (Workspace::is_workspace($url[0])) {
    $workspace = new Workspace($url[0]);
    $controller = $url[1];
} else {
    $controller = $url[0];
}

switch ($controller) {
    case '':
        //Calls homepage controller
        include("controllers/home.php");
        break;

    case 'help':
    case 'reports':
        //Calls requested controller
        include("controllers/$controller.php");
        break;

    default:
        //Not a workspace, nor a controller toponomy
        define('ERROR_PAGE', 404);
        include("controllers/errorpage.php");
        break;
}
