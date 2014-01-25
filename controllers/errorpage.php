<?php

/**
 *    _, __,  _, _ __, _  _, _, _
 *   / \ |_) (_  | | \ | /_\ |\ |
 *   \ / |_) , ) | |_/ | | | | \|
 *    ~  ~    ~  ~ ~   ~ ~ ~ ~  ~
 *
 * Controller for error pages
 *
 * @package     ObsidianWorkspaces
 * @subpackage  Controllers
 * @author      Sébastien Santoro aka Dereckson <dereckson@espace-win.org>
 * @license     http://www.opensource.org/licenses/bsd-license.php BSD
 * @filesource
 *
 */

//
// Common variables
//
$smarty->assign("URL_HOME", get_url());

//
// HTML output
//

switch (ERROR_PAGE) {
    case 404:
        header("HTTP/1.0 404 Not Found");
        $smarty->display("errors/404.tpl");
        break;

    default:
        die("Unknown error page: " . ERROR_PAGE);
}
