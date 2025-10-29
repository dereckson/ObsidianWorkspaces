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

use Waystone\Workspaces\Engines\Serialization\ArrayDeserializable;

use Message;

/**
 * Application configuration class
 *
 * This class describes an application configuration.
 *
 * It can be serialized into a workspace.conf application entry
 */
class ApplicationConfiguration implements ArrayDeserializable {

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
     * Loads an ApplicationConfiguration instance from an associative array
     *
     * @param array $data The associative array to deserialize
     *
     * @return ApplicationConfiguration The deserialized instance
     */
    public static function loadFromArray (array $data) : self {
        $instance = new static;

        foreach ($data as $key => $value) {
            $instance->$key = match ($key) {
                "nav" => new Message($value),
                default => $value,
            };
        }

        return $instance;
    }
}
