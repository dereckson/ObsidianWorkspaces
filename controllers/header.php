<?php

/**
 *    _, __,  _, _ __, _  _, _, _
 *   / \ |_) (_  | | \ | /_\ |\ |
 *   \ / |_) , ) | |_/ | | | | \|
 *    ~  ~    ~  ~ ~   ~ ~ ~ ~  ~
 *
 * Controller for header (called on every regular page)
 *
 * @package     ObsidianWorkspaces
 * @subpackage  Controllers
 * @author      Sébastien Santoro aka Dereckson <dereckson@espace-win.org>
 * @license     http://www.opensource.org/licenses/bsd-license.php BSD
 * @filesource
 *
 */

//
// Gets header resources
//

$workspaces = $CurrentUser->get_workspaces();
$workspaces_count = count($workspaces);

//
// HTML output
//

//Assigns header information
$smarty->assign('current_username', $CurrentUser->name);
if (isset($workspace)) {
    $smarty->assign('current_workspace', $workspace);
}
$smarty->assign('workspaces', $workspaces);
$smarty->assign('workspaces_count', $workspaces_count);

//Prints the template
$smarty->display('header.tpl');

/**
 * This constant indicates the header have been printed
 */
define('HEADER_PRINTED', true);
