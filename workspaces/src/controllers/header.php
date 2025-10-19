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
     * Handles controller request
     */
    public function handleRequest () {
        //Gets header resources
        $workspaces = $this->context->user->get_workspaces();

        //HTML output
        $smarty = $this->context->templateEngine;
        $smarty->assign('current_username', $this->context->user->name);
        $smarty->assign('workspaces', $workspaces);
        $smarty->assign('workspaces_count', count($workspaces));

        if ($this->context->workspace === null) {
            $smarty->assign('root_url', get_url());
        } else {
            $workspace = $this->context->workspace;
            $workspace_url = get_url($workspace->code);

            $smarty->assign('current_workspace', $workspace);
            $smarty->assign('current_workspace_url', $workspace_url);

            //Gets custom header
            if ($workspace->configuration->header != '') {
                $customHeader = file_get_contents($workspace->configuration->header);
                $smarty->assign('custom_workspace_header', $customHeader);
            }

            //Gets navigation
            $nav = [];
            $binds = $workspace->configuration->getControllersBinds();
            foreach ($binds as $applicationConfig) {
                if ($applicationConfig->nav !== null) {
                    $nav[] = [
                        'link' => $applicationConfig->nav->__toString(),
                        'url' => $applicationConfig->bind,
                        'icon' => $applicationConfig->icon
                    ];
                }
            }
            $smarty->assign('current_workspace_nav', $nav);
        }

        $smarty->display('header.tpl');
        define('HEADER_PRINTED', true);
    }
}
