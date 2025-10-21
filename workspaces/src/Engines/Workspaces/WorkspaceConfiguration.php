<?php

/**
 *    _, __,  _, _ __, _  _, _, _
 *   / \ |_) (_  | | \ | /_\ |\ |
 *   \ / |_) , ) | |_/ | | | | \|
 *    ~  ~    ~  ~ ~   ~ ~ ~ ~  ~
 *
 * Workspace configuration class
 *
 * @package     ObsidianWorkspaces
 * @subpackage  Workspaces
 * @author      Sébastien Santoro aka Dereckson <dereckson@espace-win.org>
 * @license     http://www.opensource.org/licenses/bsd-license.php BSD
 * @filesource
 */

namespace Waystone\Workspaces\Engines\Workspaces;

use Waystone\Workspaces\Engines\Framework\Context;

use ApplicationConfiguration;
use ObjectDeserializableWithContext;

/**
 * Workspace configuration class
 *
 * This class maps the workspaces table.
 */
class WorkspaceConfiguration implements ObjectDeserializableWithContext {

    /**
     * @var array applications (each element is an instance of
     *      ApplicationConfiguration)
     */
    public $applications = [];

    /**
     * @var array authentication methods for this workspace (each element is an
     *      instance of AuthenticationMethod)
     */
    public $authenticationMethods = [];

    /**
     * @var array disclaimers (each element a string)
     */
    public $disclaimers = [];

    /**
     * @var array collections (each key a string to the collection name, each
     *      value a string to the collection document type)
     */
    public $collections = [];

    /**
     * Determines if internal Obsidian Workspaces authentication can be used to
     * login on this workspace URL
     *
     * @return boolean True if a user not logged in Obsidian Workspaces going
     *                 to a workspace URL should be offered to login through
     *                 Obsidian ; otherwise, false.
     */
    public $allowInternalAuthentication = true;

    /**
     * @var string The overall custom header to prepend to the header site
     */
    public $header = '';

    /**
     * @var string The overall custom footer to append to the footer site
     */
    public $footer = '';

    /**
     * Get applications controllers binds for this workspace
     */
    public function getControllersBinds () {
        $controllers = [];
        foreach ($this->applications as $application) {
            $controllers[$application->bind] = $application;
        }

        return $controllers;
    }

    /**
     * Determines if the URL fragment matches a controller bound to it.
     *
     * @param ApplicationConfiguration $applicationConfiguration The
     *                                                           application
     *                                                           configuration
     *
     * @return boolean true if the URL fragment matches an application
     *                 controller's bind
     */
    public function hasControllerBind ($url, &$applicationConfiguration) {
        foreach ($this->applications as $application) {
            if ($application->bind == $url) {
                $applicationConfiguration = $application;

                return true;
            }
        }

        return false;
    }

    /**
     * Loads a WorkspaceConfiguration instance from an object
     *
     * @param object $data The object to deserialize
     * @param Context $context The site context
     *
     * @return WorkspaceConfiguration The deserialized instance
     */
    public static function loadFromObject ($data, $context) {
        $instance = new WorkspaceConfiguration();

        //Applications array
        if (property_exists($data, 'applications')) {
            foreach ($data->applications as $applicationData) {
                if (!property_exists($applicationData, 'name')) {
                    throw new Exception("Missing required property: application name");
                }

                $controllerClass = $applicationData->name;
                if (!class_exists($controllerClass)) {
                    trigger_error("Application controller doesn't exist: $controllerClass. If you've just added application code, update includes/autoload.php file to register your new classes.",
                        E_USER_WARNING);
                    continue;
                }
                $configurationClass = $controllerClass . 'Configuration';
                if (!class_exists($configurationClass)) {
                    $configurationClass = "ApplicationConfiguration";
                }
                $instance->applications[] =
                    $configurationClass::loadFromObject($applicationData);
            }
        }

        //Login array
        if (property_exists($data, 'login')) {
            $instance->allowInternalAuthentication = false;
            foreach ($data->login as $authData) {
                if (!property_exists($authData, 'type')) {
                    throw new Exception("Missing required property: login type");
                }

                if ($authData->type == 'internal') {
                    $instance->allowInternalAuthentication = true;
                    continue;
                }

                $class = $authData->type;
                if (!class_exists($class)) {
                    throw new Exception("Authentication method doesn't exist: $class. If you've just added authentication code, update includes/autoload.php file to register your new classes.");
                }
                $authenticationMethod = $class::loadFromObject($authData);
                $authenticationMethod->context = $context;
                $instance->authenticationMethods[] = $authenticationMethod;
            }
        }

        //Disclaimers array
        if (property_exists($data, 'disclaimers')) {
            $instance->disclaimers = $data->disclaimers;
        }

        //Collections array
        if (property_exists($data, 'collections')) {
            foreach ($data->collections as $collection) {
                if (!property_exists($collection, 'name')) {
                    throw new Exception("A collection has been declared without name in the workspace configuration.");
                }
                $name = $collection->name;
                if (!property_exists($collection, 'global')
                    || !$collection->global) {
                    $name =
                        WorkspaceConfiguration::getCollectionNameWithPrefix($context->workspace,
                            $name);
                }
                if (property_exists($collection, 'documentType')) {
                    $type = $collection->documentType;
                    if (!class_exists($type)) {
                        throw new Exception("CollectionDocument children class doesn't exist: $type. If you've just added authentication code, update includes/autoload.php file to register your new classes.");
                    }
                } else {
                    $type = null;
                }
                $instance->collections[$name] = $type;
            }
        }

        //Header string
        if (property_exists($data, 'header')) {
            $instance->header = $data->header;
        }

        //Footer string
        if (property_exists($data, 'footer')) {
            $instance->footer = $data->footer;
        }

        return $instance;
    }

    /**
     * Gets the full name of a collection, with the workspace prefix
     *
     * @param Workspace $workspace The current workspace
     * @param string $name The collection name
     *
     * @return string The full name of the collection
     */
    public static function getCollectionNameWithPrefix (
        Workspace $workspace,
        string $name
    ) {
        return $workspace->code . '-' . $name;
    }

    /**
     * Loads a WorkspaceConfiguration instance deserializing a JSON file
     */
    public static function loadFromFile ($file, $context) {
        $object = json_decode(file_get_contents($file));
        if ($object === null) {
            throw new Exception("Can't parse configuration file: "
                                . json_last_error_msg());
        }

        return self::loadFromObject($object, $context);
    }
}
