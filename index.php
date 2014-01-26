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

//New application context
$context = new ApplicationContext();

////////////////////////////////////////////////////////////////////////////////
///
/// Session
///

//Starts a new session or recovers current session
$context->session = Session::load();

//Handles login or logout
include("includes/login.php");

//Gets current user information
$context->user = $context->session->get_logged_user();

////////////////////////////////////////////////////////////////////////////////
///
/// Template/L10n engine
///

define('THEME', 'bluegray');

require('includes/smarty/Smarty.class.php');
define('SMARTY_SPL_AUTOLOAD', 1);

$smarty = new Smarty();
$current_dir = dirname(__FILE__);
$smarty->template_dir = $current_dir . '/skins/' . THEME;

$smarty->compile_dir = $Config['Content']['Cache'] . '/compiled';
$smarty->cache_dir = $Config['Content']['Cache'];
$smarty->config_dir = $current_dir;

$smarty->config_vars['StaticContentURL'] = $Config['StaticContentURL'];
$context->templateEngine = $smarty;

//Loads language files
initialize_lang();
lang_load('core.conf');

////////////////////////////////////////////////////////////////////////////////
///
/// Serves the requested page
///

$context->url = get_current_url_fragments();

//If the user isn't logged in (is anonymous), prints login/invite page & dies.
if ($context->user->id == ANONYMOUS_USER) {
    //Anonymous user
    include('controllers/anonymous.php');
    exit;
}

//Workspace
if (Workspace::is_workspace($context->url[0])) {
    $context->workspace = Workspace::fromCode(array_shift($context->url));
    $context->workspace->loadConfiguration();
}

switch ($controller = $context->url[0]) {
    case '':
        //Calls homepage controller
        HomepageController::Run($context);
        break;

    case 'help':
    case 'reports':
        //Calls requested controller
        include("controllers/$controller.php");
        break;

    default:
        //Current workspace application controller?
        $workspaceConfig = $context->workspace->configuration;
        $applicationConfiguration = NULL;
        if ($workspaceConfig != NULL && $workspaceConfig->hasControllerBind($controller, $applicationConfiguration)) {
            //Run controller
            $controllerClass = $applicationConfiguration->name;
            $appContext = ApplicationContext::loadFromContext($context, $applicationConfiguration);
            $controllerClass::Run($appContext);
            break;
        }

        //Not a workspace, nor a controller toponomy
        define('ERROR_PAGE', 404);
        include("controllers/errorpage.php");
        break;
}
