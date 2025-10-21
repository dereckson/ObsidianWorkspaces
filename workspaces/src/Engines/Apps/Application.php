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

namespace Waystone\Workspaces\Engines\Apps;

use Waystone\Workspaces\Engines\Controller\Controller;
use Waystone\Workspaces\Engines\Workspaces\WorkspaceConfiguration;

use Collection;

use Exception;

/**
 * Application class
 *
 * This class describes an application
 */
abstract class Application extends Controller {

    /**
     * @var string The application name
     */
    public static $name;

    /**
     * @var ApplicationContext The current application context
     */
    public $context;

    /**
     * @var Collection[] The collections, keys as collections roles and values as
     *      collections names.
     */
    public $collections = [];

    /**
     * Initializes the controller resources
     */
    public function initialize () {
        $this->collections = $this->loadCollections();
    }

    /**
     * Loads the collection
     */
    private function loadCollections () : array {
        $loadedCollections = [];

        $workspaceCollections =
            $this->context->workspace->configuration->collections;

        foreach (
            $this->context->configuration->useCollections as $role => $name
        ) {
            if (!array_key_exists($name, $workspaceCollections)) {
                $name =
                    WorkspaceConfiguration::getCollectionNameWithPrefix($this->context->workspace,
                        $name);
                if (!array_key_exists($name, $workspaceCollections)) {
                    throw new Exception("Collection not found: $name");
                }
            }
            $loadedCollections[$role] =
                Collection::load($name, $workspaceCollections[$name]);
        }

        return $loadedCollections;
    }
}
