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
$file = $Config['Content']['Help'] . '/' . ($context->url[1] ? $context->url[1] : 'index') . '.html';

if (file_exists($file)) {
    //Header
    $smarty->assign('controller_custom_nav', 'nav_help.tpl');
    HeaderController::run($context);

    //Help page
    $smarty->assign('help_file', $file);
    $smarty->display('help.tpl');

    //Footer
    FooterController::run($context);
} else {
    define('ERROR_PAGE', 404);
    include("controllers/errorpage.php");
}
