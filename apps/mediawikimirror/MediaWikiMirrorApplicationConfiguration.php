<?php

/**
 *    _, __,  _, _ __, _  _, _, _
 *   / \ |_) (_  | | \ | /_\ |\ |
 *   \ / |_) , ) | |_/ | | | | \|
 *    ~  ~    ~  ~ ~   ~ ~ ~ ~  ~
 *
 * MediaWiki mirror application configuration class
 *
 * @package     ObsidianWorkspaces
 * @subpackage  MediaWikiMirror
 * @author      Sébastien Santoro aka Dereckson <dereckson@espace-win.org>
 * @license     http://www.opensource.org/licenses/bsd-license.php BSD
 * @filesource
 */

/**
 * MediaWiki mirror application configuration class
 */
class MediaWikiMirrorApplicationConfiguration extends ApplicationConfiguration {
    /**
     * @var array The URL to the MediaWiki index.php entry point
     */
    public $url;

    /**
     * @var string The page to serve
     */
    public $page;
}
