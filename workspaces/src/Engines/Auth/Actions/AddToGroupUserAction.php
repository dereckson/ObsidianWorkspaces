<?php

/**
 *    _, __,  _, _ __, _  _, _, _
 *   / \ |_) (_  | | \ | /_\ |\ |
 *   \ / |_) , ) | |_/ | | | | \|
 *    ~  ~    ~  ~ ~   ~ ~ ~ ~  ~
 *
 * Add to group user action class
 *
 * @package     ObsidianWorkspaces
 * @subpackage  Auth
 * @author      Sébastien Santoro aka Dereckson <dereckson@espace-win.org>
 * @license     http://www.opensource.org/licenses/bsd-license.php BSD
 * @filesource
 *
 */

namespace Waystone\Workspaces\Engines\Auth\Actions;

use Waystone\Workspaces\Engines\Auth\UserAction;
use Waystone\Workspaces\Engines\Serialization\ArrayDeserializable;
use Waystone\Workspaces\Engines\Users\UserGroup;

use Exception;
use JsonSerializable;

/**
 * User action to add a user into a group
 */
class AddToGroupUserAction extends UserAction implements ArrayDeserializable, JsonSerializable {

    /**
     * @var UserGroup The group to add the user to
     */
    public $group;

    /**
     * @var boolean Determines if the target user has to be added to the group
     *     in the quality of admin
     */
    public $isAdmin;


    /**
     * Executes the user action
     */
    public function run () {
        if ($this->targetUser->isMemberOfGroup($this->group)) {
            if ($this->isAdmin) {
                //Promotes to admin if needed
                $this->targetUser->addToGroup($this->group, true);
            }
        } else {
            //Adds user to the group
            $this->targetUser->addToGroup($this->group, $this->isAdmin);
        }
    }

    /**
     * Loads an AddToGroupUserAction instance from an object.
     *
     * @param array $data The associative array to deserialize
     *
     * @return AddToGroupUserAction The deserialized instance
     * @throws Exception when the group code is not found
     */
    public static function loadFromArray (array $data) : self {
        $instance = new AddToGroupUserAction();

        $instance->group = UserGroup::fromCode($data["code"]);
        $instance->isAdmin = ($data["isAdmin"] == true);

        return $instance;
    }

    public function jsonSerialize () : array {
        return [
            "code" => $this->group->code,
            "isAdmin" => $this->isAdmin
        ];
    }
}
