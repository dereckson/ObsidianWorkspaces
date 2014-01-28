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

/**
 * User action to grant user a permission
 */
class GivePermissionUserAction extends UserAction implements ObjectDeserializable, JsonSerializable {
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
    public function Run () {
        if (!$id = get_resource_id($this->resourceType, $this->resourceIdentifier)) {
            throw new Exception("Can't get identifier from resource " . $this->resourceType . " " . $this->resourceIdentifier);
        }
        $this->targetUser->setPermission(
            $this->resourceType, $id,
            $this->permissionName, $this->permissionFlag
        );
    }

    /**
     * Loads a GivePermissionUserAction instance from an object.
     *
     * @param object $data The object to deserialize
     * @return GivePermissionUserAction The deserialized instance
     */
    public static function loadFromObject ($data) {
        //Checks the object contains every mandatory data
        if (!property_exists($data, 'resource')) {
            throw new InvalidArgumentException("A resource property, with two mandatory type and id property is required.");
        }
        if (!property_exists($data, 'permission')) {
            throw new InvalidArgumentException("A permission property, with a mandatory name property and a facultative flag property is required.");
        }
        if (!property_exists($data->permission, 'name')) {
            throw new InvalidArgumentException("Permission name is required.");
        }
        if (!property_exists($data->resource, 'type')) {
            throw new InvalidArgumentException("Resource type is required.");
        }
        if (!property_exists($data->resource, 'id')) {
            throw new InvalidArgumentException("Resource id is required.");
        }

        //Builds instance
        $instance = new GivePermissionUserAction();

        $instance->resourceType = Permission::getResourceTypeLetterFromCode($data->resource->type);
        $instance->resourceIdentifier = $data->resource->id;
        $instance->permissionName = $data->permission->name;
        if (property_exists($data->permission, 'flag')) {
            $instance->permissionFlag = $data->permission->flag;
        }

        return $instance;
    }

    /**
     * Serializes the object to a value that can be serialized natively by json_encode().
     *
     * @return object The serializable value
     */
    public function jsonSerialize() {
        //TODO: if you wish strict code here, we need such a class.
        $data->resource->type = Permission::getResourceTypeCodeFromLetter($this->resourceType);
        $data->resource->id = $this->resourceIdentifier;
        $data->permission->name = $this->permissionName;
        $data->permission->flag = $this->permissionFlag;
        return $data;
    }
}
