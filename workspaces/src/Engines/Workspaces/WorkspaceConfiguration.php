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

use Waystone\Workspaces\Engines\Apps\ApplicationConfiguration;
use Waystone\Workspaces\Engines\Exceptions\WorkspaceException;
use Waystone\Workspaces\Engines\Framework\Context;
use Waystone\Workspaces\Engines\Serialization\ArrayDeserializableWithContext;

use Keruald\Yaml\Parser as YamlParser;
use Keruald\Yaml\Tags\EnvTag;

use AuthenticationMethod;

use Exception;

/**
 * Workspace configuration class
 *
 * This class maps the workspaces table.
 */
class WorkspaceConfiguration implements ArrayDeserializableWithContext {

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
     * Loads a WorkspaceConfiguration instance from an array
     *
     * @param array $data The array to deserialize
     * @param mixed $context The application context
     *
     * @return WorkspaceConfiguration The deserialized instance
     * @throws WorkspaceException
     */
    public static function loadFromArray (
        array $data,
        mixed $context
    ) : self {
        $instance = new WorkspaceConfiguration();

        // Parse applications to load in the workspace
        $applications = $data["applications"] ?? [];
        foreach ($applications as $applicationData) {
            if (!array_key_exists("name", $applicationData)) {
                throw new WorkspaceException("Missing required property: application name");
            }

            $controllerClass = $applicationData["name"];
            if (!class_exists($controllerClass)) {
                trigger_error("Application controller doesn't exist: $controllerClass.",
                    E_USER_WARNING);
                continue;
            }

            $configurationClass = $controllerClass . "Configuration";
            if (!class_exists($configurationClass)) {
                $configurationClass = ApplicationConfiguration::class;
            }

            $instance->applications[] = [$configurationClass, "loadFromArray"]($applicationData);
        }

        // Parse custom authentication methods for this workspace
        if (array_key_exists("login", $data)) {
            $instance->allowInternalAuthentication = false;
            foreach ($data["login"] as $authData) {
                if ($authData["type"] == "internal") {
                    $instance->allowInternalAuthentication = true;
                    continue;
                }

                $auth = self::loadAuthenticationMethod($authData, $context);
                $instance->authenticationMethods[] = $auth;
            }
        }

        // Parse collections the workspace applications can access
        $collections = $data->collections ?? [];
        foreach ($collections as $collection) {
            if (!property_exists($collection, 'name')) {
                throw new WorkspaceException("A collection has been declared without name in the workspace configuration.");
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
                    throw new WorkspaceException("CollectionDocument children class doesn't exist: $type. If you've just added authentication code, update includes/autoload.php file to register your new classes.");
                }
            } else {
                $type = null;
            }
            $instance->collections[$name] = $type;
        }

        // Customization
        $instance->disclaimers = $data->disclaimers ?? [];
        $instance->header = $data["header"] ?? "";
        $instance->footer = $data["footer"] ?? "";

        return $instance;
    }

    private static function loadAuthenticationMethod (
        array $authData,
        Context $context,
    ) : AuthenticationMethod {
        if (!array_key_exists("type", $authData)) {
            throw new WorkspaceException("Missing required property: login type");
        }

        $class = $authData["type"];
        if (!class_exists($class)) {
            throw new WorkspaceException("Authentication method doesn't exist: $class.");
        }

        try {
            $authenticationMethod = $class::loadFromArray($authData);
            $authenticationMethod->context = $context;
        } catch (Exception $ex) {
            throw new WorkspaceException(
                "Can't load authentication method: " . $ex->getMessage(), 0, $ex
            );
        }

        return $authenticationMethod;
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
        $object = json_decode(file_get_contents($file), true);
        if ($object === null) {
            throw new Exception("Can't parse configuration file: "
                                . json_last_error_msg());
        }

        return self::loadFromArray($object, $context);
    }

    /**
     * @throws WorkspaceException
     */
    public static function loadFromYamlFile (
        string  $file,
        Context $context
    ) : self {
        $parser = new YamlParser();
        $parser->withTagClass(EnvTag::class);

        try {
            $value = $parser->parseFile($file);
        }
        catch (Exception $ex) {
            throw new WorkspaceException("Can't parse configuration file: "
                                         . $ex->getMessage(), 0, $ex);
        }

        return self::loadFromArray($value, $context);
    }
}
