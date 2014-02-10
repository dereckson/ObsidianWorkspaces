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
abstract class Controller implements RunnableWithContext {
    /**
     * @var string the application name
     */
    public static $name;

    /**
     * @var Context the site context
     */
    public $context;

    /**
     * Initializes the controller resources
     */
    public function initialize () { }

    /**
     * Handles a web request
     */
    public abstract function handleRequest();

    /**
     * Loads a new instance of a Controller class
     *
     * @param Context the site or application context
     * @return Controller an instance of the class inheriting from Controller
     */
    public static function load (Context $context) {
        $controller = new static;
        $controller->context = $context;
        $controller->initialize();
        return $controller;
    }

    /**
     * Initializes a new instance of the controller with the specified context and handle request
     *
     * @param Context the site or application context
     */
    public static function run (Context $context) {
        static::load($context)->handleRequest();
    }
}
