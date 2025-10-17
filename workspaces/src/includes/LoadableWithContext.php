<?php

/**
 *    _, __,  _, _ __, _  _, _, _
 *   / \ |_) (_  | | \ | /_\ |\ |
 *   \ / |_) , ) | |_/ | | | | \|
 *    ~  ~    ~  ~ ~   ~ ~ ~ ~  ~
 *
 * LoadableWithContext interface
 *
 * @package     ObsidianWorkspaces
 * @subpackage  Keruald
 * @author      Sébastien Santoro aka Dereckson <dereckson@espace-win.org>
 * @license     http://www.opensource.org/licenses/bsd-license.php BSD
 * @filesource
 */

/**
 * LoadableWithContext interface
 *
 * Objects implementing this interface can initialize themselves and
 * execute a task they represent, with a context as parameter.
 */
interface LoadableWithContext {
    /**
     * Initializes a new instance of the object with the specified context and handle request
     *
     * @return object The loaded instance of the object.
     */
    public static function load (Context $context);
}
