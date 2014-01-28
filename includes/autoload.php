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
 * This SPL autoloader method is called when a class or an interface can't be loaded
 */
function obsidian_autoload ($name) {
    //Applications
    if ($name == 'HelloWorldApplication') { require './apps/helloworld/HelloWorldApplication.php'; return true; }

    if ($name == 'MediaWikiMirrorApplication') { require './apps/mediawikimirror/MediaWikiMirrorApplication.php'; return true; }
    if ($name == 'MediaWikiMirrorApplicationConfiguration') { require './apps/mediawikimirror/MediaWikiMirrorApplicationConfiguration.php'; return true; }

    if ($name == 'StaticContentApplication') { require './apps/staticcontent/StaticContentApplication.php'; return true; }
    if ($name == 'StaticContentApplicationConfiguration') { require './apps/staticcontent/StaticContentApplicationConfiguration.php'; return true; }

    //Core controllers
    if ($name == 'HeaderController') { require './controllers/header.php'; return true; }
    if ($name == 'FooterController') { require './controllers/footer.php'; return true; }
    if ($name == 'HomepageController') { require './controllers/home.php'; return true; }

    //Keruald and Obsidian Workspaces Libraries
    if ($name == 'ObjectDeserializable' || $name == 'ObjectDeserializableWithContext') { require './includes/ObjectDeserializable.php'; return true; }

    if ($name == 'Application') { require './includes/apps/Application.php'; return true; }
    if ($name == 'ApplicationConfiguration') { require './includes/apps/ApplicationConfiguration.php'; return true; }
    if ($name == 'ApplicationContext') { require './includes/apps/ApplicationContext.php'; return true; }

    if ($name == 'AddToGroupUserAction') { require './includes/auth/AddToGroupUserAction.php'; return true; }
    if ($name == 'AuthenticationMethod') { require './includes/auth/AuthenticationMethod.php'; return true; }
    if ($name == 'GivePermissionUserAction') { require './includes/auth/GivePermissionUserAction.php'; return true; }
    if ($name == 'UserAction') { require './includes/auth/UserAction.php'; return true; }

    if ($name == 'Cache') { require './includes/cache/cache.php'; return true; }
    if ($name == 'CacheMemcached') { require './includes/cache/memcached.php'; return true; }
    if ($name == 'CacheVoid') { require './includes/cache/void.php'; return true; }

    if ($name == 'Context') { require './includes/controller/Context.php'; return true; }
    if ($name == 'Controller') { require './includes/controller/Controller.php'; return true; }
    if ($name == 'RunnableWithContext') { require './includes/controller/RunnableWithContext.php'; return true; }

    if ($name == 'Message') { require './includes/i18n/Message.php'; return true; }

    if ($name == 'Permission') { require './includes/objects/Permission.php'; return true; }
    if ($name == 'User') { require './includes/objects/user.php'; return true; }
    if ($name == 'UserGroup') { require './includes/objects/usergroup.php'; return true; }

    if ($name == 'Workspace') { require './includes/workspaces/Workspace.php'; return true; }
    if ($name == 'WorkspaceConfiguration') { require './includes/workspaces/WorkspaceConfiguration.php'; return true; }

    return false;
}

spl_autoload_register('obsidian_autoload');
