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
$file = $Config['Content']['Help'] . '/' . ($url[1] ? $url[1] : 'index') . '.html';

if (file_exists($file)) {
    //Header
    $smarty->assign('controller_custom_nav', 'nav_help.tpl');
    include('header.php');

    //Help page
    $smarty->display('help.tpl');

    //Footer
    include('footer.php');
} else {
    define('ERROR_PAGE', 404);
    include("controllers/errorpage.php");
}
