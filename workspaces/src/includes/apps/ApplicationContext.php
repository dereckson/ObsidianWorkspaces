<?php

/**
 *    _, __,  _, _ __, _  _, _, _
 *   / \ |_) (_  | | \ | /_\ |\ |
 *   \ / |_) , ) | |_/ | | | | \|
 *    ~  ~    ~  ~ ~   ~ ~ ~ ~  ~
 *
 * Application context class
 *
 * @package     ObsidianWorkspaces
 * @subpackage  Apps
 * @author      Sébastien Santoro aka Dereckson <dereckson@espace-win.org>
 * @license     http://www.opensource.org/licenses/bsd-license.php BSD
 * @filesource
 */

use Waystone\Workspaces\Engines\Framework\Context;

/**
 * Application context class
 *
 * This class describes an application context, in addition to the site context.
 */
class ApplicationContext extends Context {
    /**
     * @var ApplicationConfiguration the application configuration
     */
    public $configuration;

    /**
     * Initializes a new ApplicationContext instance from a Context instance
     *
     * @param Context $sourceContext The source context
     * @param ApplicationConfiguration The application configuration (facultative)
     */
    public static function loadFromContext ($sourceContext, $applicationConfiguration = NULL) {
        $applicationContext = new ApplicationContext();

        $applicationContext->workspace = $sourceContext->workspace;
        $applicationContext->user = $sourceContext->user;
        $applicationContext->session = $sourceContext->session;
        $applicationContext->url = $sourceContext->url;
        $applicationContext->templateEngine = $sourceContext->templateEngine;

        if ($applicationConfiguration !== NULL) {
            $applicationContext->configuration = $applicationConfiguration;
        }

        return $applicationContext;
    }
}
