<?php

/**
 *    _, __,  _, _ __, _  _, _, _
 *   / \ |_) (_  | | \ | /_\ |\ |
 *   \ / |_) , ) | |_/ | | | | \|
 *    ~  ~    ~  ~ ~   ~ ~ ~ ~  ~
 *
 * MediaWiki mirror application class
 *
 * @package     ObsidianWorkspaces
 * @subpackage  MediaWikiMirror
 * @author      Sébastien Santoro aka Dereckson <dereckson@espace-win.org>
 * @license     http://www.opensource.org/licenses/bsd-license.php BSD
 * @filesource
 */

use Waystone\Workspaces\Engines\Apps\Application;

/**
 * MediaWiki mirror application class
 */
class MediaWikiMirrorApplication extends Application {
    /**
     * Gets rendered version URL
     *
     * @return string the URL
     */
    public function getRenderUrl () {
        return
            $this->context->configuration->url[0] .
            $this->context->configuration->url[1] .
            "/index.php?action=render&title=". $this->context->configuration->page;
    }

    /**
     * Fixes links in the content
     *
     * @param string $content The page content
     * @return string The page content, with updated links
     */
    public function fixLinks ($content) {
        $fullUrl = $this->context->configuration->url[0] . $this->context->configuration->url[1];
        $content = str_replace('<a href="' . $this->context->configuration->url[1], '<a href="' . $fullUrl, $content);
        $content = str_replace(' src="' . $this->context->configuration->url[1], ' src="' . $fullUrl, $content);
        return $content;
    }

    /**
     * Handles controller request
     */
    public function handleRequest () {
        $smarty = $this->context->templateEngine;

        // Header
        $title = $this->context->configuration->page;
        $smarty->assign('PAGE_TITLE', $title);
        HeaderController::run($this->context);

        // Body
        $url = $this->getRenderUrl();
        try {
            $content = file_get_contents($url);
            $content = $this->fixLinks($content);
            $smarty->assign("Content", $content);
            $smarty->display("apps/mediawikimirror/page.tpl");
        } catch (Exception $ex) {
            $smarty->assign("alert_level", "danger");
            $smarty->assign("alert_note", $ex->getMessage());
            $smarty->display("apps/_blocks/alert.tpl");
        }

        // Footer
        FooterController::run($this->context);
    }
}
