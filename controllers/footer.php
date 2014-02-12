<?php

/**
 *    _, __,  _, _ __, _  _, _, _
 *   / \ |_) (_  | | \ | /_\ |\ |
 *   \ / |_) , ) | |_/ | | | | \|
 *    ~  ~    ~  ~ ~   ~ ~ ~ ~  ~
 *
 * Controller for footer (called on every regular page)
 *
 * @package     ObsidianWorkspaces
 * @subpackage  Controllers
 * @author      Sébastien Santoro aka Dereckson <dereckson@espace-win.org>
 * @license     http://www.opensource.org/licenses/bsd-license.php BSD
 * @filesource
 *
 */

/**
 * Footer controller
 */
class FooterController extends Controller {

    /**
     * Handles controller request
     */
    public function handleRequest () {
        $smarty = $this->context->templateEngine;

        if ($this->context->workspace !== null) {
            $workspace = $this->context->workspace;

            //Gets custom footer
            if ($workspace->configuration->footer != '') {
                try {
                    $customFooter = file_get_contents($workspace->configuration->footer);
                    $smarty->assign('custom_workspace_footer', $customFooter);
                } catch (ErrorException $ex) {
                    //TODO: log the $ex->getMessage() or a generic message we can't open $workspace->configuration->footer
                    //The error code is easy to recover: HTTP request failed! HTTP/1.1 404 Not Found
                }
            }
        }

        $smarty->display('footer.tpl');
    }

}
