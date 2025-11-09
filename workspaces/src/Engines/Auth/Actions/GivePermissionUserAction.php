<?php

/**
 *    _, __,  _, _ __, _  _, _, _
 *   / \ |_) (_  | | \ | /_\ |\ |
 *   \ / |_) , ) | |_/ | | | | \|
 *    ~  ~    ~  ~ ~   ~ ~ ~ ~  ~
 *
 * Give permission user action class
 *
 * @package     ObsidianWorkspaces
 * @subpackage  Auth
 * @author      Sébastien Santoro aka Dereckson <dereckson@espace-win.org>
 * @license     http://www.opensource.org/licenses/bsd-license.php BSD
 * @filesource
 *
 */

namespace Waystone\Workspaces\Engines\Auth\Actions;

use Waystone\Workspaces\Engines\Auth\Permission;
use Waystone\Workspaces\Engines\Auth\UserAction;
use Waystone\Workspaces\Engines\Framework\Resources;
use Waystone\Workspaces\Engines\Serialization\ArrayDeserializable;

use Exception;
use InvalidArgumentException;
use JsonSerializable;

/**
 * User action to grant user a permission
 */
class GivePermissionUserAction extends UserAction
    implements ArrayDeserializable, JsonSerializable {

    /**
     * @var string The permission name
     */
    public $permissionName;

    /**
     * @var int The permission flag
     */
    public $permissionFlag = 1;

    /**
     * @var string The target resource type
     */
    public $resourceType;

    /**
     * @var string The target resource identifier
     */
    public $resourceIdentifier;

    /**
     * Executes the user action
     */
    public function run () {
        $id = $this->context->resources->resolveID(
            $this->resourceType,
            $this->resourceIdentifier,
        );

        if ($id->isNone()) {
            throw new Exception("Can't resolve resource "
                . $this->resourceType . " " . $this->resourceIdentifier);
        }

        $this->targetUser->setPermission(
            $this->resourceType, $id->getValue(),
            $this->permissionName, $this->permissionFlag,
        );
    }

    /**
     * Loads a GivePermissionUserAction instance from an associative array.
     *
     * @param object $data The associative array to deserialize
     *
     * @return GivePermissionUserAction The deserialized instance
     */
    public static function loadFromArray (mixed $data) : self {
        // Validate mandatory data
        if (!array_key_exists("resource", $data)) {
            throw new InvalidArgumentException("A resource property, with two mandatory type and id property is required.");
        }
        if (!array_key_exists("permission", $data)) {
            throw new InvalidArgumentException("A permission property, with a mandatory name property and a facultative flag property is required.");
        }

        $resource = $data["resource"];
        $permission = $data["permission"];

        if (!array_key_exists("name", $permission)) {
            throw new InvalidArgumentException("Permission name is required.");
        }
        if (!array_key_exists("type", $resource)) {
            throw new InvalidArgumentException("Resource type is required.");
        }
        if (!array_key_exists("id", $resource)) {
            throw new InvalidArgumentException("Resource id is required.");
        }

        // Build instance
        $instance = new GivePermissionUserAction();

        $instance->resourceType =
            Permission::getResourceTypeLetterFromCode($resource["type"]);
        $instance->resourceIdentifier = $resource["id"];
        $instance->permissionName = $permission["name"];

        if (array_key_exists("flag", $permission)) {
            $instance->permissionFlag = $permission["flag"];
        }

        return $instance;
    }


    /**
     * Serializes the object to a value that can be serialized natively by
     * json_encode().
     *
     * @return array The serializable value
     */
    public function jsonSerialize () : array {
        $type = Permission::getResourceTypeCodeFromLetter($this->resourceType);

        return [
            "resource" => [
                "type" => $type,
                "id" => $this->resourceIdentifier
            ],

            "permission" => [
                "name" => $this->permissionName,
                "flag" => $this->permissionFlag,
            ],
        ];
    }
}
