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

namespace Waystone\Workspaces\Engines\Framework;

use Keruald\Database\DatabaseEngine;
use Smarty\Smarty;
use Waystone\Workspaces\Engines\Users\User;
use Waystone\Workspaces\Engines\Users\UserRepository;
use Waystone\Workspaces\Engines\Workspaces\WorkSpace;

/**
 * Context class
 *
 * This class describes the site context.
 */
class Context {

    /**
     * @var ?WorkSpace the workspace currently enabled
     */
    public ?WorkSpace $workspace = null;

    /**
     * @var DatabaseEngine the database
     */
    public DatabaseEngine $db;

    /**
     * @var array the configuration
     */
    public array $config;

    /**
     * @var UserRepository the users already loaded from database
     */
    public UserRepository $userRepository;

    public Resources $resources;

    /**
     * @var ?User the user currently logged in
     */
    public ?User $user = null;

    /**
     * @var Session the current session
     */
    public Session $session;

    /**
     * @var string[] the URL fragments
     */
    public array $url;

    /**
     * @var Smarty the template engine
     */
    public Smarty $templateEngine;

    ///
    /// Helper methods
    ///

    /**
     * Gets application root directory
     *
     * @return string|false the application root directory
     */
    public function getApplicationRootDirectory () : string|false {
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
