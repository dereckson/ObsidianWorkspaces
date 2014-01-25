<?php

/**
 *    _, __,  _, _ __, _  _, _, _
 *   / \ |_) (_  | | \ | /_\ |\ |
 *   \ / |_) , ) | |_/ | | | | \|
 *    ~  ~    ~  ~ ~   ~ ~ ~ ~  ~
 *
 * Application configuration class
 *
 * @package     ObsidianWorkspaces
 * @subpackage  Apps
 * @author      Sébastien Santoro aka Dereckson <dereckson@espace-win.org>
 * @license     http://www.opensource.org/licenses/bsd-license.php BSD
 * @filesource
 */

/**
 * Application configuration class
 *
 * This class describes an application configuration.
 *
 * It can be serialized into a workspace.conf application entry
 */
class ApplicationConfiguration {
    /**
     * @var string The URL the application is binded to, without initial slash.
     */
    public $bind;
    
    /**
     * @var Message The navigation entry
     */
    public $nav;

    /**
     * @var string The application icon name
     */
    public $icon;

    /**
     * Loads a WorkspaceConfiguration instance from an object
     *
     * @param object $data The object to deserialize
     * @return WorkspaceConfiguration The deserialized instance
     */
    public static function loadFromObject ($data) {
        $instance = new ApplicationConfiguration();

        //Applications array
        foreach ($data as $key => $value) {
            if ($key == "nav") {
                $instance->nav = new Message($value);
            } else {
                $instance->$key = $value;
            }
        }

        return $instance;
    }
}
