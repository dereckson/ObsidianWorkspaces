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

 /**
  * Workspace configuration class
  *
  * This class maps the workspaces table.
  */
class WorkspaceConfiguration implements ObjectDeserializableWithContext {
    /**
     * @var Array applications (each element is an instance of ApplicationConfiguration)
     */
    public $applications = [];

    /**
     * @var Array authentication methods for this workspace (each element is an instance of AuthenticationMethod)
     */
    public $authenticationMethods = [];

    /**
     * Determines if internal Obsidian Workspaces authentication can be used to login on this workspace URL
     *
     * @return boolean True if an user not logged in Obsidian Workspaces going to a workspace URL should be offered to login through Obsidian ; otherwise, false.
     */
    public $allowInternalAuthentication = true;

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
     * Determines if the URL fragment matches a controller binded to it.
     *
     * @param ApplicationConfiguration $applicationConfiguration The application configuration
     * @return boolean true if the URL fragment matches an application controller's bind
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
     * @param Context The site context
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
                    trigger_error("Application controller doesn't exist: $controllerClass. If you've just added application code, update includes/autoload.php file to register your new classes.", E_USER_WARNING);
                    continue;
                }
                $configurationClass = $controllerClass . 'Configuration';
                if (!class_exists($configurationClass)) {
                    $configurationClass = "ApplicationConfiguration";
                }
                $instance->applications[] = $configurationClass::loadFromObject($applicationData);
            }
        }

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

        return $instance;
    }

    /**
     * Loads a WorkspaceConfiguration instance deserializing a JSON file
     */
    public static function loadFromFile ($file, $context) {
        return self::loadFromObject(
            json_decode(file_get_contents($file)),
            $context
        );
    }
}
