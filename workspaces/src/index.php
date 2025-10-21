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

use Keruald\Database\Database;
use Waystone\Workspaces\Engines\Errors\ErrorHandling;

////////////////////////////////////////////////////////////////////////////////
///
/// Initialization
///

//Keruald and Obsidian Workspaces libraries

include('includes/core.php');

//Prepares the site context
$context = new Context();
$context->config = $Config;
$context->db = $db = Database::load($Config["sql"]);
$context->session = Session::load();
$context->url = get_current_url_fragments();
$context->initializeTemplateEngine($context->config['Theme']);

//Loads language files
Language::initialize();
Language::load($context)->configLoad('core.conf');

//Loads workspace
try {
    if (Workspace::is_workspace($context->url[0])) {
        $context->workspace = Workspace::fromCode(array_shift($context->url));
        $context->workspace->loadConfiguration($context);
    }
} catch (Exception $ex) {
    ErrorHandling::messageAndDie(GENERAL_ERROR, $ex->getMessage(), Language::get('CantLoadWorkspace'));
}

//Handles login or logout
include("includes/login.php");

//Gets current user information
$context->user = $context->session->get_logged_user();

////////////////////////////////////////////////////////////////////////////////
///
/// Serves the requested page
///

//If the user isn't logged in (is anonymous), prints login/invite page & dies.
if ($context->user->id == ANONYMOUS_USER) {
    //Anonymous user
    include('controllers/anonymous.php');
    exit;
}

//If a workspace has been selected, ensures the current logged in user has access to it.
if ($context->workspace && !$context->workspace->userCanAccess($context->user)) {
    ErrorHandling::messageAndDie(HACK_ERROR, "You don't have access to this workspace.", 'Access control');
}

$controller = count($context->url) > 0 ? $context->url[0] : '';
switch ($controller) {
    case '':
        //Calls homepage controller
        HomepageController::run($context);
        break;

    case 'help':
    case 'reports':
        //Calls requested controller
        include("controllers/$controller.php");
        break;

    default:
        //Current workspace application controller?
        if ($context->workspace != null) {
            $workspaceConfig =  $context->workspace->configuration;
            $applicationConfiguration = null;
            if ($workspaceConfig != null && $workspaceConfig->hasControllerBind($controller, $applicationConfiguration)) {
                //Runs controller
                $controllerClass = $applicationConfiguration->name;
                $appContext = ApplicationContext::loadFromContext($context, $applicationConfiguration);
                $controllerClass::run($appContext);
                break;
            }
        }

        //Not a workspace, nor a controller toponym
        ErrorPageController::show($context, 404);
        exit;
}
