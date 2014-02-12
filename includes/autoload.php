<?php

/**
 *    _, __,  _, _ __, _  _, _, _
 *   / \ |_) (_  | | \ | /_\ |\ |
 *   \ / |_) , ) | |_/ | | | | \|
 *    ~  ~    ~  ~ ~   ~ ~ ~ ~  ~
 *
 * Classes and interfaces auto loader
 *
 * @package     ObsidianWorkspaces
 * @filesource
 */

/**
 * This SPL autoloader method is called when a class or an interface can't be loaded.
 */
function obsidian_autoload ($name) {
    $dir = dirname(__DIR__);

    ///
    /// Applications
    ///

    if ($name == 'DocumentsApplication') { require $dir . '/apps/documents/DocumentsApplication.php'; return true; }
    if ($name == 'DocumentsApplicationConfiguration') { require $dir . '/apps/documents/DocumentsApplicationConfiguration.php'; return true; }

    if ($name == 'HelloWorldApplication') { require $dir . '/apps/helloworld/HelloWorldApplication.php'; return true; }

    if ($name == 'MediaWikiMirrorApplication') { require $dir . '/apps/mediawikimirror/MediaWikiMirrorApplication.php'; return true; }
    if ($name == 'MediaWikiMirrorApplicationConfiguration') { require $dir . '/apps/mediawikimirror/MediaWikiMirrorApplicationConfiguration.php'; return true; }

    if ($name == 'StaticContentApplication') { require $dir . '/apps/staticcontent/StaticContentApplication.php'; return true; }
    if ($name == 'StaticContentApplicationConfiguration') { require $dir . '/apps/staticcontent/StaticContentApplicationConfiguration.php'; return true; }

    ///
    /// Core controllers
    ///

    if ($name == 'ErrorPageController') { require $dir . '/controllers/errorpage.php'; return true; }
    if ($name == 'FooterController') { require $dir . '/controllers/footer.php'; return true; }
    if ($name == 'HeaderController') { require $dir . '/controllers/header.php'; return true; }
    if ($name == 'HomepageController') { require $dir . '/controllers/home.php'; return true; }

    ///
    /// Keruald and Obsidian Workspaces libraries
    ///

    if ($name == 'Events') { require $dir . '/includes/Events.php'; return true; }
    if ($name == 'LoadableWithContext') { require $dir . '/includes/LoadableWithContext.php'; return true; }
    if ($name == 'ObjectDeserializable') { require $dir . '/includes/ObjectDeserializable.php'; return true; }
    if ($name == 'ObjectDeserializableWithContext') { require $dir . '/includes/ObjectDeserializable.php'; return true; }

    if ($name == 'Application') { require $dir . '/includes/apps/Application.php'; return true; }
    if ($name == 'ApplicationConfiguration') { require $dir . '/includes/apps/ApplicationConfiguration.php'; return true; }
    if ($name == 'ApplicationContext') { require $dir . '/includes/apps/ApplicationContext.php'; return true; }

    if ($name == 'AddToGroupUserAction') { require $dir . '/includes/auth/AddToGroupUserAction.php'; return true; }
    if ($name == 'AuthenticationMethod') { require $dir . '/includes/auth/AuthenticationMethod.php'; return true; }
    if ($name == 'AzharProvider') { require $dir . '/includes/auth/AzharProvider.php'; return true; }
    if ($name == 'GivePermissionUserAction') { require $dir . '/includes/auth/GivePermissionUserAction.php'; return true; }
    if ($name == 'UserAction') { require $dir . '/includes/auth/UserAction.php'; return true; }

    if ($name == 'Cache') { require $dir . '/includes/cache/cache.php'; return true; }
    if ($name == 'CacheMemcached') { require $dir . '/includes/cache/memcached.php'; return true; }
    if ($name == 'CacheVoid') { require $dir . '/includes/cache/void.php'; return true; }

    if ($name == 'Collection') { require $dir . '/includes/collection/Collection.php'; return true; }
    if ($name == 'CollectionDocument') { require $dir . '/includes/collection/CollectionDocument.php'; return true; }
    if ($name == 'FilesCollection') { require $dir . '/includes/collection/FilesCollection.php'; return true; }
    if ($name == 'MongoDBCollection') { require $dir . '/includes/collection/MongoDBCollection.php'; return true; }
    if ($name == 'MongoDBCollectionIterator') { require $dir . '/includes/collection/MongoDBCollectionIterator.php'; return true; }
    if ($name == 'MySQLCollection') { require $dir . '/includes/collection/MySQLCollection.php'; return true; }
    if ($name == 'SQLiteCollection') { require $dir . '/includes/collection/SQLiteCollection.php'; return true; }
    if ($name == 'SQLCollection') { require $dir . '/includes/collection/SQLCollection.php'; return true; }

    if ($name == 'Context') { require $dir . '/includes/controller/Context.php'; return true; }
    if ($name == 'Controller') { require $dir . '/includes/controller/Controller.php'; return true; }
    if ($name == 'RunnableWithContext') { require $dir . '/includes/controller/RunnableWithContext.php'; return true; }

    if ($name == 'Database') { require $dir . '/includes/database/Database.php'; return true; }
    if ($name == 'DatabaseException') { require $dir . '/includes/database/DatabaseException.php'; return true; }
    if ($name == 'DatabaseResult') { require $dir . '/includes/database/DatabaseResult.php'; return true; }
    if ($name == 'EmptyDatabaseResult') { require $dir . '/includes/database/EmptyDatabaseResult.php'; return true; }
    if ($name == 'MySQLDatabase') { require $dir . '/includes/database/MySQLDatabase.php'; return true; }
    if ($name == 'MySQLDatabaseResult') { require $dir . '/includes/database/MySQLDatabaseResult.php'; return true; }

    if ($name == 'Language') { require $dir . '/includes/i18n/Language.php'; return true; }
    if ($name == 'Message') { require $dir . '/includes/i18n/Message.php'; return true; }
    if ($name == 'TextFileMessage') { require $dir . '/includes/i18n/TextFileMessage.php'; return true; }

    if ($name == 'Disclaimer') { require $dir . '/includes/objects/Disclaimer.php'; return true; }
    if ($name == 'Permission') { require $dir . '/includes/objects/Permission.php'; return true; }
    if ($name == 'User') { require $dir . '/includes/objects/user.php'; return true; }
    if ($name == 'UserGroup') { require $dir . '/includes/objects/usergroup.php'; return true; }

    if ($name == 'Workspace') { require $dir . '/includes/workspaces/Workspace.php'; return true; }
    if ($name == 'WorkspaceConfiguration') { require $dir . '/includes/workspaces/WorkspaceConfiguration.php'; return true; }

    return false;
}

spl_autoload_register('obsidian_autoload');
