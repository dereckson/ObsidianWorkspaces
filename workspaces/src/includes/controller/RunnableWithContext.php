<?php

/**
 *    _, __,  _, _ __, _  _, _, _
 *   / \ |_) (_  | | \ | /_\ |\ |
 *   \ / |_) , ) | |_/ | | | | \|
 *    ~  ~    ~  ~ ~   ~ ~ ~ ~  ~
 *
 * RunnableWithContext interface
 *
 * @package     ObsidianWorkspaces
 * @subpackage  Controller
 * @author      Sébastien Santoro aka Dereckson <dereckson@espace-win.org>
 * @license     http://www.opensource.org/licenses/bsd-license.php BSD
 * @filesource
 */

/**
 * RunnableWithContext interface
 *
 * Objects implementing this interface can initialize themselves and
 * execute a task they represent, with a context as parameter.
 */
interface RunnableWithContext extends LoadableWithContext {
    /**
     * Initializes a new instance of the object with the specified context and handle request
     */
    public static function run (Context $context);
}
