<?php

/**
 *    _, __,  _, _ __, _  _, _, _
 *   / \ |_) (_  | | \ | /_\ |\ |
 *   \ / |_) , ) | |_/ | | | | \|
 *    ~  ~    ~  ~ ~   ~ ~ ~ ~  ~
 *
 * Controller class
 *
 * @package     ObsidianWorkspaces
 * @subpackage  Controller
 * @author      Sébastien Santoro aka Dereckson <dereckson@espace-win.org>
 * @license     http://www.opensource.org/licenses/bsd-license.php BSD
 * @filesource
 */

/**
 * Controller class
 *
 * This class describes a controller
 */
class Controller {
    /**
     * @var string the application name
     */
    public static $name;

    /**
     * @var Context the site context
     */
    public $context;

    /**
     * Initializes a new instance of the controller with the specified context and handle request
     */
    public static function Run (Context $context) {
        $controller = new static;
        $controller->context = $context;
        $controller->handleRequest();
    }
}