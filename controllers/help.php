<?php

/**
 *    _, __,  _, _ __, _  _, _, _
 *   / \ |_) (_  | | \ | /_\ |\ |
 *   \ / |_) , ) | |_/ | | | | \|
 *    ~  ~    ~  ~ ~   ~ ~ ~ ~  ~
 *
 * Controller
 *
 * @package     ObsidianWorkspaces
 * @subpackage  Controllers
 * @author      Sébastien Santoro aka Dereckson <dereckson@espace-win.org>
 * @license     http://www.opensource.org/licenses/bsd-license.php BSD
 * @filesource
 *
 */

//
// HTML output
//
$file  = $Config['Content']['Help'] . DIRECTORY_SEPARATOR;
$file .= (count($context->url) > 1) ? $context->url[1] : 'index';
$file .= '.html';

if (!file_exists($file)) {
    ErrorPageController::show($context, 404);
    exit;
}

//Header
$context->templateEngine->assign('controller_custom_nav', 'nav_help.tpl');
HeaderController::run($context);

//Help page
$context->templateEngine->assign('help_file', $file);
$context->templateEngine->display('help.tpl');

//Footer
FooterController::run($context);
