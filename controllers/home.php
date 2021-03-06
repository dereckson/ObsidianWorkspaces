<?php

/**
 *    _, __,  _, _ __, _  _, _, _
 *   / \ |_) (_  | | \ | /_\ |\ |
 *   \ / |_) , ) | |_/ | | | | \|
 *    ~  ~    ~  ~ ~   ~ ~ ~ ~  ~
 *
 * Controller for homepage content
 *
 * @package     ObsidianWorkspaces
 * @subpackage  Controllers
 * @author      Sébastien Santoro aka Dereckson <dereckson@espace-win.org>
 * @license     http://www.opensource.org/licenses/bsd-license.php BSD
 * @filesource
 *
 */

/**
 * Homepage controller
 */
class HomepageController extends Controller {
    /**
     * Handles controller request
     */
    public function handleRequest () {
        $smarty = $this->context->templateEngine;
        $workspace = $this->context->workspace;

        if ($workspace == null) {
            //We need a list of workspaces to allow user
            //to select the one he wishes to access.
            //The header has already grabbed it for us.
            if (array_key_exists('workspaces', $smarty->tpl_vars)) {
                $workspaces = $smarty->tpl_vars['workspaces']->value;
            } else {
                $workspaces = $this->context->user->get_workspaces();
                $smarty->assign('workspaces', $workspaces);
            }

            switch (count($workspaces)) {
                case 0:
                    //No workspace error message
                    $smarty->assign('PAGE_TITLE', Language::get("Home"));
                    $template = "home_noworkspace.tpl";
                    break;

                case 1:
                    //Autoselect workspace
                    $this->context->workspace = $workspaces[0];
                    $workspace = $workspaces[0];
                    $this->context->workspace->loadConfiguration($this->context);
                    break;

                default:
                    //Select workspace template
                    $smarty->assign('PAGE_TITLE', Language::get("PickWorkspace"));
                    $template = "home_pickworkspace.tpl";
            }
        }

        if ($workspace != null) {
            $smarty->assign('PAGE_TITLE', $workspace->name);
            $template = "home_workspace.tpl";

            if (count($workspace->configuration->disclaimers)) {
                $disclaimers = [];
                foreach ($workspace->configuration->disclaimers as $disclaimer) {
                    $disclaimers[] = Disclaimer::get($disclaimer);
                }
                $smarty->assign('disclaimers', $disclaimers);
            }
        }

        //Serves header
        HeaderController::run($this->context);

        //Serves relevant template
        $smarty->display($template);

        //Serves footer
        FooterController::run($this->context);
    }
}
