<?php

/**
 *    _, __,  _, _ __, _  _, _, _
 *   / \ |_) (_  | | \ | /_\ |\ |
 *   \ / |_) , ) | |_/ | | | | \|
 *    ~  ~    ~  ~ ~   ~ ~ ~ ~  ~
 *
 * Application context class
 *
 * @package     ObsidianWorkspaces
 * @subpackage  Controller
 * @author      Sébastien Santoro aka Dereckson <dereckson@espace-win.org>
 * @license     http://www.opensource.org/licenses/bsd-license.php BSD
 * @filesource
 */

/**
 * Context class
 *
 * This class describes the site context.
 */
class Context {
    /**
     * @var WorkSpace the workspace currently enabled
     */
    public $workspace;

    /**
     * @var User the user currently logged in
     */
    public $user;

    /**
     * @var Session the current session
     */
    public $session;

    /**
     * @var Array the URL fragments
     */
    public $url;

    /**
     * @var Smarty the template engine
     */
    public $templateEngine;
}
