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

$session = Session::load();

////////////////////////////////////////////////////////////////////////////////
///
/// Template/L10n engine
///

define('THEME', 'bluegray');

////////////////////////////////////////////////////////////////////////////////
///
/// Session and context
///

//Prepares the site context
$context = new ApplicationContext();
$context->session = $session;;
$context->url = get_current_url_fragments();
$context->config = $Config;
$context->initializeTemplateEngine();

//Loads language files
$smarty = $context->templateEngine;
Language::initialize();
Language::load($context)->configLoad('core.conf');

if (Workspace::is_workspace($context->url[0])) {
    $context->workspace = Workspace::fromCode(array_shift($context->url));
    $context->workspace->loadConfiguration($context);
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

switch ($controller = $context->url[0]) {
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
        $workspaceConfig = $context->workspace->configuration;
        $applicationConfiguration = NULL;
        if ($workspaceConfig != NULL && $workspaceConfig->hasControllerBind($controller, $applicationConfiguration)) {
            //Runs controller
            $controllerClass = $applicationConfiguration->name;
            $appContext = ApplicationContext::loadFromContext($context, $applicationConfiguration);
            $controllerClass::run($appContext);
            break;
        }

        //Not a workspace, nor a controller toponomy
        ErrorPageController::show($context, 404);
        exit;
}
