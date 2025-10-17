<?php

/**
 *    _, __,  _, _ __, _  _, _, _
 *   / \ |_) (_  | | \ | /_\ |\ |
 *   \ / |_) , ) | |_/ | | | | \|
 *    ~  ~    ~  ~ ~   ~ ~ ~ ~  ~
 *
 * User action class
 *
 * @package     ObsidianWorkspaces
 * @subpackage  Auth
 * @author      Sébastien Santoro aka Dereckson <dereckson@espace-win.org>
 * @license     http://www.opensource.org/licenses/bsd-license.php BSD
 * @filesource
 *
 */

/**
 * User action class, to be extended to implement an action related to user
 */
abstract class UserAction {
    /**
     * @var User the target action user
     */
    public $targetUser;

    /**
     * Initializes a new instance of an UserAction object
     *
     * @param User $targetUser the target action user
     */
    public function __construct ($targetUser = NULL) {
        $this->targetUser = $targetUser;
    }

    /**
     * Executes the user action
     */
    abstract public function run ();
}
