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

namespace Waystone\Workspaces\Engines\Apps;

use Waystone\Workspaces\Engines\Workspaces\WorkspaceConfiguration;

use Message;
use ObjectDeserializable;

/**
 * Application configuration class
 *
 * This class describes an application configuration.
 *
 * It can be serialized into a workspace.conf application entry
 */
class ApplicationConfiguration implements ObjectDeserializable {

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
     * @var string[] The collections to use. Keys ares collections roles, values
     *      collections names.
     */
    public $useCollections = [];

    /**
     * Loads a WorkspaceConfiguration instance from an object
     *
     * @param object $data The object to deserialize
     *
     * @return WorkspaceConfiguration The deserialized instance
     */
    public static function loadFromObject ($data) {
        $instance = new ApplicationConfiguration();

        //Applications array
        foreach ($data as $key => $value) {
            switch ($key) {
                case 'nav':
                    $instance->nav = new Message($value);
                    break;

                case 'useCollections':
                    foreach ($data->useCollections as $role => $name) {
                        $instance->useCollections[$role] = $name;
                    }
                    break;

                default:
                    $instance->$key = $value;
            }
        }

        return $instance;
    }
}
