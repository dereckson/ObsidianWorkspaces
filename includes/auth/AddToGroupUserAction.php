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

/**
 * User action to add an user into a group
 */
class AddToGroupUserAction extends UserAction implements ObjectDeserializable {
    /**
     * @var UserGroup The group to add the user to
     */
    public $group;

    /**
     * @var boolean Determines if the target user has to be added to the group in the quality of admin
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
     * Loads a AddToGroupUserAction instance from an object.
     *
     * @param object $data The object to deserialize
     * @return AddToGroupUserAction The deserialized instance
     */
    public static function loadFromObject ($data) {
        $instance = new AddToGroupUserAction();
        $instance->group = UserGroup::fromCode($data->code);
        $instance->isAdmin = ($data->isAdmin == true);
        return $instance;
    }
}
