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

/**
 * Header controller
 */
class HeaderController extends Controller {
    /**
     * Handle controller request
     */
    public function handleRequest () {
        //Gets header resources
        $workspaces = $this->context->user->get_workspaces();

        //HTML output
        $smarty = $this->context->templateEngine;
        $smarty->assign('current_username', $context->user->name);
        $smarty->assign('workspaces', $workspaces);
        $smarty->assign('workspaces_count', count($workspaces));
        if ($this->context->workspace !== null) {
            $smarty->assign('current_workspace', $this->context->workspace);
        }

        $smarty->display('header.tpl');
        define('HEADER_PRINTED', true);
    }
}
