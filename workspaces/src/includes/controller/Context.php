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
 * @subpackage  Controller
 * @author      Sébastien Santoro aka Dereckson <dereckson@espace-win.org>
 * @license     http://www.opensource.org/licenses/bsd-license.php BSD
 * @filesource
 */

use Smarty\Smarty;

/**
 * Context class
 *
 * This class describes the site context.
 */
class Context {
    /**
     * @var WorkSpace the workspace currently enabled
     */
    public $workspace;

    /**
     * @var Database the database instance
     */
    public $db;

    /**
     * @var array the configuration
     */
    public $config;

    /**
     * @var User the user currently logged in
     */
    public $user;

    /**
     * @var Session the current session
     */
    public $session;

    /**
     * @var Array the URL fragments
     */
    public $url;

    /**
     * @var Smarty the template engine
     */
    public $templateEngine;

    ///
    /// Helper methods
    ///

    /**
     * Gets application root directory
     *
     * @return string the application root directory
     */
    public function getApplicationRootDirectory() {
        return getcwd();
    }

    ///
    /// Templates
    ///

    /**
     * Initializes the template engine
     *
     * @param string $theme the theme for the templates
     */
    public function initializeTemplateEngine (string $theme) : void {
        $smarty = new Smarty();

        $current_dir = static::getApplicationRootDirectory();
        $smarty
            ->setTemplateDir("$current_dir/skins/$theme")
            ->setCacheDir($this->config["Content"]["Cache"])
            ->setCompileDir($this->config["Content"]["Cache"] . "/compiled")
            ->setConfigDir($current_dir);

        $smarty->config_vars += [
            "StaticContentURL" => $this->config["StaticContentURL"],
        ];

        $this->templateEngine = $smarty;
    }
}
