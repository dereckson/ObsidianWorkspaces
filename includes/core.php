<?php

/**
 *    _, __,  _, _ __, _  _, _, _
 *   / \ |_) (_  | | \ | /_\ |\ |
 *   \ / |_) , ) | |_/ | | | | \|
 *    ~  ~    ~  ~ ~   ~ ~ ~ ~  ~
 *
 * Core global functions
 *
 * @package     ObsidianWorkspaces
 * @subpackage  Keruald
 * @author      Sébastien Santoro aka Dereckson <dereckson@espace-win.org>
 * @license     http://www.opensource.org/licenses/bsd-license.php BSD
 * @filesource
 *
 */

////////////////////////////////////////////////////////////////////////////////
///                                                                          ///
/// Configures PHP and loads site-wide used libraries                        ///
///                                                                          ///
////////////////////////////////////////////////////////////////////////////////

//Errors management
include_once("error.php");
error_reporting(E_ALL);

$minorRecoverableErrors = E_NOTICE | E_USER_NOTICE | E_DEPRECATED | E_USER_DEPRECATED;
set_error_handler('throwExceptionErrorHandler', E_ALL ^ $minorRecoverableErrors);

//Loads global functions
include_once("GlobalFunctions.php"); //Global functions

//Loads configuration
if (isset($_SERVER) && array_key_exists('OBSIDIAN_CONFIG', $_SERVER)) {
    $configFile = $_SERVER['OBSIDIAN_CONFIG'];
    if (file_exists($configFile)) {
        include_once($configFile);
        unset($configFile);
    } else {
        die("You've specified a custom configuration file path in the environment, but this file doesn't exist: $configFile");
    }
} else {
    include_once("config.php");
}

//Loads libraries
include_once("session.php");           //Sessions handler
include_once("autoload.php");         //Autoloader for needed classes
