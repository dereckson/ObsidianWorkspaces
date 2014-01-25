<?php

/**
 *    _, __,  _, _ __, _  _, _, _
 *   / \ |_) (_  | | \ | /_\ |\ |
 *   \ / |_) , ) | |_/ | | | | \|
 *    ~  ~    ~  ~ ~   ~ ~ ~ ~  ~
 *
 * Controller for reports
 *
 * @package     ObsidianWorkspaces
 * @subpackage  Controllers
 * @author      Sébastien Santoro aka Dereckson <dereckson@espace-win.org>
 * @license     http://www.opensource.org/licenses/bsd-license.php BSD
 * @filesource
 *
 */

$template = 'reports_view';

//
// HTML output
//

//Serves header
$smarty->assign('PAGE_TITLE', "Reports");
HeaderController::run($context);

//Servers controller content
$smarty->display("$template.tpl");

//Serves footer
FooterController::run($context);
