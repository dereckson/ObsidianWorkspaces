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

    if ($name == 'Document') { require $dir . '/apps/documents/Document.php'; return true; }
    if ($name == 'DocumentsApplication') { require $dir . '/apps/documents/DocumentsApplication.php'; return true; }
    if ($name == 'DocumentsApplicationConfiguration') { require $dir . '/apps/documents/DocumentsApplicationConfiguration.php'; return true; }
    if ($name == 'DocumentType') { require $dir . '/apps/documents/DocumentType.php'; return true; }

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

    if ($name == 'Disclaimer') { require $dir . '/includes/objects/Disclaimer.php'; return true; }

    return false;
}

spl_autoload_register('obsidian_autoload');
