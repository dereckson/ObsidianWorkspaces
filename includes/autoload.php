<?php

/**
 *    _, __,  _, _ __, _  _, _, _
 *   / \ |_) (_  | | \ | /_\ |\ |
 *   \ / |_) , ) | |_/ | | | | \|
 *    ~  ~    ~  ~ ~   ~ ~ ~ ~  ~
 *
 * Classes auto loader
 *
 * @package     ObsidianWorkspaces
 * @filesource
 */

/**
 * This magic method is called when a class can't be loaded
 */
function obsidian_autoload ($className) {
    //Core controllers
    if ($className == 'HeaderController') { require './controllers/header.php'; return true; }
    if ($className == 'FooterController') { require './controllers/footer.php'; return true; }
    if ($className == 'HomepageController') { require './controllers/home.php'; return true; }

    //Keruald and Obsidian Workspaces Libraries
    if ($className == 'Application') { require './includes/apps/Application.php'; return true; }
    if ($className == 'ApplicationContext') { require './includes/apps/ApplicationContext.php'; return true; }

    if ($className == 'Cache') { require './includes/cache/cache.php'; return true; }
    if ($className == 'CacheMemcached') { require './includes/cache/memcached.php'; return true; }
    if ($className == 'CacheVoid') { require './includes/cache/void.php'; return true; }

    if ($className == 'Context') { require './includes/controller/Context.php'; return true; }
    if ($className == 'Controller') { require './includes/controller/Controller.php'; return true; }

    if ($className == 'User') { require './includes/objects/user.php'; return true; }
    if ($className == 'UserGroup') { require './includes/objects/usergroup.php'; return true; }
    if ($className == 'Workspace') { require './includes/objects/workspace.php'; return true; }

    return false;
}

spl_autoload_register('obsidian_autoload');
