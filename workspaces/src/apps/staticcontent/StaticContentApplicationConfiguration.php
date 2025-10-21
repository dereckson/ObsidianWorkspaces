<?php

/**
 *    _, __,  _, _ __, _  _, _, _
 *   / \ |_) (_  | | \ | /_\ |\ |
 *   \ / |_) , ) | |_/ | | | | \|
 *    ~  ~    ~  ~ ~   ~ ~ ~ ~  ~
 *
 * Static content application configuration class
 *
 * @package     ObsidianWorkspaces
 * @subpackage  StaticContent
 * @author      Sébastien Santoro aka Dereckson <dereckson@espace-win.org>
 * @license     http://www.opensource.org/licenses/bsd-license.php BSD
 * @filesource
 */

use Waystone\Workspaces\Engines\Apps\ApplicationConfiguration;

/**
 * Static content application configuration class
 */
class StaticContentApplicationConfiguration extends ApplicationConfiguration {
    /**
     * @var string The path to the files to serve
     */
    public $path;
}
