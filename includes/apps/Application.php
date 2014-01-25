<?php

/**
 *    _, __,  _, _ __, _  _, _, _
 *   / \ |_) (_  | | \ | /_\ |\ |
 *   \ / |_) , ) | |_/ | | | | \|
 *    ~  ~    ~  ~ ~   ~ ~ ~ ~  ~
 *
 * Application class
 *
 * @package     ObsidianWorkspaces
 * @subpackage  Apps
 * @author      Sébastien Santoro aka Dereckson <dereckson@espace-win.org>
 * @license     http://www.opensource.org/licenses/bsd-license.php BSD
 * @filesource
 */

/**
 * Application class
 *
 * This class describes an application
 */
class Application extends Controller {
    /**
     * @var string the application name
     */
    public static $name;
    
    /**
     * @var ApplicationContext the currenct application context
     */
    public $context;
}